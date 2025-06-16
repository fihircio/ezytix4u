<?php 

namespace Classiebit\Eventmie\Services;
use Billplz\Client;
use Billplz\Signature;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    protected $client;
    protected $signature;
    protected $callbackUrl;
    protected $collectionId;
    protected $apiKey;
    protected $xSignatureKey;
    protected $guzzleClient;

    public function __construct($settings = [])
    {
        $this->apiKey = $settings['billplz_secret_key'];
        $this->xSignatureKey = $settings['billplz_xsignature'];
        $this->collectionId = $settings['billplz_app_id'];

        // Add detailed logging for initialization
        Log::info('Billplz Service Initialization', [
            'environment' => app()->environment(),
            'apiKey_length' => strlen($this->apiKey),
            'xSignatureKey_length' => strlen($this->xSignatureKey),
            'collectionId' => $this->collectionId,
            'redirectUri' => $settings['billplz_redirect_uri']
        ]);

        // Configure Guzzle client with timeout settings and basic auth
        $this->guzzleClient = new GuzzleClient([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
            'verify' => true,
            'auth' => [$this->apiKey, ''], // Basic auth with API key
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->client = Client::make($this->apiKey, $settings['billplz_redirect_uri'], $this->guzzleClient);
        
        if (app()->environment('local', 'development')) {
            $this->client->useSandbox();
            Log::info('Using Billplz Sandbox environment');
        } else {
            Log::info('Using Billplz Production environment');
        }

        $this->signature = new Signature($this->xSignatureKey, ['x_signature']);
        $this->callbackUrl = route('eventmie.bookings_billplz_callback');
    }

    public function createPayment($order = [], $currency = 'MYR', $booking = [])
    {
        try {
            $startTime = microtime(true);
            Log::info('Starting Billplz payment creation', [
                'order_number' => $order['order_number'],
                'amount' => $order['price'],
                'currency' => $currency
            ]);

            $customer = $booking[0];

            $billplzParams = [
                'collection_id' => $this->collectionId,
                'email' => $customer['customer_email'],
                'mobile' => $customer['customer_phone'] ?? null,
                'name' => $customer['customer_name'],
                'amount' => $order['price'] * 100, // Amount in cents
                'callback_url' => $this->callbackUrl,
                'description' => $order['product_title'] . ' (' . $order['price_title'] . ')',
                'redirect_url' => $this->callbackUrl,
                'reference_1_label' => 'Order Number',
                'reference_1' => $order['order_number'],
            ];

            // Log the parameters we're sending to Billplz
            Log::info('Billplz Create Bill Parameters', $billplzParams);

            // Add request timing
            $requestStartTime = microtime(true);
            
            // Make direct API call using Guzzle
            $response = $this->guzzleClient->post('https://www.billplz.com/api/v3/bills', [
                'json' => $billplzParams
            ]);

            $requestEndTime = microtime(true);
            $requestDuration = $requestEndTime - $requestStartTime;

            // Log the response and timing
            Log::info('Billplz Create Bill Response', [
                'status_code' => $response->getStatusCode(),
                'response' => json_decode($response->getBody(), true),
                'request_duration' => $requestDuration,
                'total_duration' => microtime(true) - $startTime
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                return [
                    'url' => $responseData['url'],
                    'billCode' => $responseData['id'],
                    'status' => true
                ];
            }

            // Log error response
            Log::error('Billplz Create Bill Failed', [
                'status_code' => $response->getStatusCode(),
                'response' => json_decode($response->getBody(), true),
                'request_duration' => $requestDuration
            ]);

            return ['error' => 'Failed to create bill', 'status' => false];
        } catch (\Throwable $th) {
            // Enhanced error logging
            Log::error('Billplz Create Bill Exception', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
                'duration' => microtime(true) - $startTime
            ]);
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }

    public function verifyPaymentStatus($billCode)
    {
        try {
            $response = $this->guzzleClient->get("https://www.billplz.com/api/v3/bills/{$billCode}");
            
            if ($response->getStatusCode() === 200) {
                $billData = json_decode($response->getBody(), true);
                return [
                    [
                        'billpaymentStatus' => $billData['paid'] ? '1' : '0',
                        'billExternalReferenceNo' => $billData['reference_1'],
                        'billpaymentAmount' => $billData['amount'],
                        'billpaymentInvoiceNo' => $billData['id'],
                    ]
                ];
            }

            return ['error' => 'Failed to retrieve bill data', 'status' => false];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }

    public function verifyWebhook(array $data)
    {
        Log::info('Received Billplz webhook data', ['data' => $data]);

        // Extract the X-Signature from the data
        $xSignature = $data['billplz']['x_signature'] ?? null;

        if (!$xSignature) {
            Log::error('Billplz webhook verification failed: X-Signature not found in data');
            return false;
        }

        Log::info('Extracted X-Signature', ['x_signature' => $xSignature]);

        // Remove the X-Signature from the data for verification
        unset($data['billplz']['x_signature']);

        Log::info('Data for verification', ['data' => $data]);

        try {
            // Verify the signature
            $isValid = $this->signature->verify($data, $xSignature);
            Log::info('Signature verification result', ['is_valid' => $isValid]);
            return $isValid;
        } catch (\Throwable $th) {
            Log::error('Billplz webhook verification failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return false;
        }
    }
}