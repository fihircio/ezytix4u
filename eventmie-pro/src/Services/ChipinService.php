<?php 

namespace Classiebit\Eventmie\Services;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChipinService
{
    protected $guzzleClient;
    protected $brandId;
    protected $apiKey;
    protected $baseUrl;
    protected $callbackUrl;
    protected $environment;

    public function __construct($settings = [])
    {
        $this->brandId = $settings['chipin_brand_id'] ?? null;
        $this->apiKey = $settings['chipin_api_key'] ?? null;
        $this->environment = $settings['chipin_environment'] ?? 'sandbox';
        
        // Set base URL based on environment
        $this->baseUrl = $this->environment === 'production' 
            ? 'https://gate.chip-in.asia/api/v1' 
            : 'https://gate.chip-in.asia/api/v1'; // Using same URL for sandbox

        // Add detailed logging for initialization
        Log::info('Chipin Service Initialization', [
            'environment' => app()->environment(),
            'chipin_environment' => $this->environment,
            'brandId' => $this->brandId,
            'apiKey_length' => strlen($this->apiKey),
        ]);

        // Configure Guzzle client with timeout settings
        $this->guzzleClient = new GuzzleClient([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
            'verify' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]
        ]);

        $this->callbackUrl = url('/bookings/chipin/callback');
    }

    /**
     * Create a payment with Chipin
     *
     * @param array $order
     * @param string $currency
     * @param array $booking
     * @return array
     */
    public function createPayment($order = [], $currency = 'MYR', $booking = [])
    {
        try {
            $startTime = microtime(true);
            
            if (empty($this->brandId) || empty($this->apiKey)) {
                 return ['error' => 'Chipin configuration missing', 'status' => false];
            }

            $customer = $booking[0] ?? [];
            $amountInCents = (int) (round((float) $order['price'], 2) * 100);

            // Construct ClientDetails
            $client = [
                'email' => $customer['customer_email'] ?? 'test@example.com',
                'phone' => $customer['customer_phone'] ?? '',
                'full_name' => $customer['customer_name'] ?? 'Guest',
                // Add missing required fields per Chipin API documentation
                'first_name' => explode(' ', $customer['customer_name'] ?? 'Guest')[0] ?? 'Guest',
                'address' => $customer['customer_address'] ?? 'Default Address',
                'city' => $customer['customer_city'] ?? 'Default City',
                'country' => $customer['customer_country'] ?? 'MY',
                'postcode' => $customer['customer_postcode'] ?? '12345',
            ];

            // Construct Product (Single aggregated product for the order)
            // Sanitizing name to remove special characters that might cause issues
            $productName = str_replace(['|', '(', ')'], '', $order['product_title'] . ' ' . $order['price_title']);
            $productName = mb_substr(trim($productName), 0, 250);

            $product = [
                'name' => $productName,
                'price' => $amountInCents,
                'quantity' => 1,
            ];

            // Construct PurchaseDetails
            $purchaseDetails = [
                'products' => [$product],
                'currency' => $currency,
                'total' => $amountInCents, // Some Chipin API versions require total amount explicitly
                'notes' => 'Order #' . $order['order_number'],
            ];

            // Construct Root Purchase Object
            // ISSUE: Chipin may or may not replace {payment_id} placeholder in redirect URLs
            // To be safe, include the placeholder so if it does replace, we get the ID
            $paymentData = [
                'brand_id' => $this->brandId,
                'client' => $client,
                'purchase' => $purchaseDetails,
                'success_redirect' => $this->callbackUrl . '?payment_id={payment_id}', // Include placeholder for potential replacement
                'failure_redirect' => $this->callbackUrl . '?status=failed&payment_id={payment_id}', // Include placeholder for potential replacement
                'success_callback' => route('eventmie.bookings_chipin_overview_callback'), // Use dedicated webhook endpoint
                'reference' => (string) $order['order_number'],
                // Add explicit payment_id to be returned in response for our reference
                'return_payment_id' => true,
            ];

            Log::info('Chipin Create Payment Parameters (Refactored)', $paymentData);

            $requestStartTime = microtime(true);
            
            try {
                // Make API call to create payment
                // FIXED: Based on 405 error, the correct endpoint is /purchases/ not /purchases/create/
                $response = $this->guzzleClient->post($this->baseUrl . '/purchases/', [
                    'json' => $paymentData
                ]);

                $requestEndTime = microtime(true);
                
                $responseData = json_decode($response->getBody(), true);

                Log::info('Chipin Create Payment Response', [
                    'status_code' => $response->getStatusCode(),
                    'response' => $responseData,
                    'duration' => $requestEndTime - $requestStartTime
                ]);

                // Check if response indicates success (status created/true or status code 200/201)
                $isSuccess = ($response->getStatusCode() === 200 || $response->getStatusCode() === 201)
                             && (isset($responseData['status']) && ($responseData['status'] === 'created' || $responseData['status'] === true));
                
                if ($isSuccess) {
                    return [
                        'url' => $responseData['checkout_url'] ?? null,
                        'payment_id' => $responseData['id'] ?? null,
                        'status' => true
                    ];
                }

                // Better error extraction for Chipin's nested error structure
                $errorMsg = $responseData['message'] ?? 'Unknown error';
                if (isset($responseData['response']['__all__'][0]['message'])) {
                    $errorMsg = $responseData['response']['__all__'][0]['message'];
                } elseif (isset($responseData['error']['message'])) {
                    $errorMsg = $responseData['error']['message'];
                }

                return ['error' => 'Failed to create payment: ' . $errorMsg, 'status' => false];
                
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $responseBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null;
                Log::error('Chipin Request Error', [
                    'message' => $e->getMessage(),
                    'response' => $responseBody
                ]);
                return ['error' => 'Request error: ' . ($responseBody['message'] ?? $e->getMessage()), 'status' => false];
            }
            
        } catch (\Throwable $th) {
            Log::error('Chipin Create Payment Exception', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }

    /**
     * Verify payment status with Chipin
     *
     * @param string $paymentId
     * @return array
     */
    public function verifyPaymentStatus($paymentId)
    {
        try {
            Log::info('Verifying Chipin payment status', ['payment_id' => $paymentId]);

            // Correct endpoint from Chipin docs: GET /purchases/{id}/
            $endpoint = 'https://gate.chip-in.asia/api/v1/purchases/' . $paymentId . '/';
            
            Log::info('Trying Chipin endpoint', ['endpoint' => $endpoint]);
            
            $response = $this->guzzleClient->get($endpoint);
            
            if ($response->getStatusCode() === 200) {
                $paymentData = json_decode($response->getBody(), true);
                
                Log::info('Chipin payment verification successful', [
                    'payment_id' => $paymentId,
                    'endpoint' => $endpoint,
                    'status' => $paymentData['status'] ?? 'unknown',
                    'payment_data_keys' => array_keys($paymentData)
                ]);
                
                // Extract payment amount from purchase.total or payment.amount
                $amount = 0;
                if (isset($paymentData['purchase']['total'])) {
                    $amount = $paymentData['purchase']['total'];
                } elseif (isset($paymentData['payment']['amount'])) {
                    $amount = $paymentData['payment']['amount'];
                }
                
                return [
                    [
                        'paymentStatus' => $paymentData['status'] ?? 'unknown',
                        'paymentId' => $paymentData['id'] ?? $paymentId,
                        'paymentAmount' => $amount,
                        'paymentCurrency' => $paymentData['purchase']['currency'] ?? 'MYR',
                        'orderNo' => $paymentData['reference'] ?? '',
                        'paidAt' => $paymentData['payment']['paid_on'] ?? null,
                    ]
                ];
            } else {
                Log::error('Chipin payment verification failed - non-200 response', [
                    'payment_id' => $paymentId,
                    'endpoint' => $endpoint,
                    'status_code' => $response->getStatusCode(),
                    'response_body' => $response->getBody()->getContents()
                ]);
            }

            return ['error' => 'Failed to retrieve payment data', 'status' => false];
            
        } catch (\Throwable $th) {
            Log::error('Chipin payment verification exception', [
                'payment_id' => $paymentId,
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'trace' => $th->getTraceAsString()
            ]);
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }


    /**
     * Verify webhook signature from Chipin
     *
     * @param array $data
     * @return bool
     */
    public function verifyWebhook(array $data)
    {
        Log::info('Received Chipin webhook data', ['data' => $data]);

        // For sandbox mode or test environments, bypass signature verification
        if ($this->environment === 'sandbox' || app()->environment('local', 'testing')) {
            Log::info('Sandbox/Development mode: bypassing signature verification');
            return true;
        }

        // Extract signature from headers (Chipin sends signature in X-Chipin-Signature header)
        $signature = null;
        $timestamp = null;
        
        // Check for signature in headers first (preferred method)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $signature = $headers['X-Chipin-Signature'] ?? $headers['x-chipin-signature'] ?? null;
            $timestamp = $headers['X-Chipin-Timestamp'] ?? $headers['x-chipin-timestamp'] ?? null;
        }
        
        // Fallback to data if not in headers
        if (!$signature) {
            $signature = $data['signature'] ?? $data['chipin_signature'] ?? null;
            $timestamp = $data['timestamp'] ?? $data['chipin_timestamp'] ?? null;
        }

        if (!$signature) {
            Log::error('Chipin webhook verification failed: Signature not found');
            return false;
        }

        Log::info('Extracted signature components', [
            'signature' => $signature,
            'timestamp' => $timestamp
        ]);

        // Remove signature and timestamp from data for verification
        $dataForVerification = $data;
        unset($dataForVerification['signature']);
        unset($dataForVerification['chipin_signature']);
        unset($dataForVerification['timestamp']);
        unset($dataForVerification['chipin_timestamp']);

        Log::info('Data for verification', ['data' => $dataForVerification]);

        try {
            // According to Chipin docs, signature is created by:
            // HMAC-SHA256(timestamp + "." + payload, secret_key)
            $payload = json_encode($dataForVerification, JSON_UNESCAPED_SLASHES);
            $message = $timestamp . '.' . $payload;
            $expectedSignature = hash_hmac('sha256', $message, $this->apiKey);
            $isValid = hash_equals($expectedSignature, $signature);
            
            Log::info('Signature verification result', [
                'is_valid' => $isValid,
                'timestamp' => $timestamp,
                'payload' => $payload,
                'message' => $message,
                'expected_signature' => $expectedSignature,
                'received_signature' => $signature
            ]);
            
            return $isValid;
            
        } catch (\Throwable $th) {
            Log::error('Chipin webhook verification failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Check if Chipin is properly configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->brandId) && !empty($this->apiKey);
    }

    /**
     * Get Chipin configuration status
     *
     * @return array
     */
    public function getConfigStatus()
    {
        return [
            'configured' => $this->isConfigured(),
            'brand_id' => !empty($this->brandId),
            'api_key' => !empty($this->apiKey),
            'environment' => $this->environment,
        ];
    }
}
