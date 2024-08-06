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

    public function __construct($settings = [])
    {
        $this->apiKey = $settings['billplz_secret_key'];
        $this->xSignatureKey = $settings['billplz_xsignature'];
        $this->collectionId = $settings['billplz_app_id'];

        // Add logging
        Log::info('Billplz Settings', [
            'apiKey' => $this->apiKey,
            'xSignatureKey' => $this->xSignatureKey,
            'collectionId' => $this->collectionId,
            'redirectUri' => $settings['billplz_redirect_uri']
        ]);

        $this->client = Client::make($this->apiKey, $settings['billplz_redirect_uri']);
        
        // Check if we're in a development environment
        if (app()->environment('local', 'development')) {
            $this->client->useSandbox();
        }

        $this->signature = new Signature($this->xSignatureKey, ['x_signature']);

        $this->callbackUrl = route('eventmie.bookings_billplz_callback');
    }

    public function createPayment($order = [], $currency = 'MYR', $booking = [])
    {
        try {
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

            $response = $this->client->bill()->create(
                $billplzParams['collection_id'],
                $billplzParams['email'],
                $billplzParams['mobile'],
                $billplzParams['name'],
                $billplzParams['amount'],
                $billplzParams['callback_url'],
                $billplzParams['description'],
                [
                    'redirect_url' => $billplzParams['redirect_url'],
                    'reference_1_label' => $billplzParams['reference_1_label'],
                    'reference_1' => $billplzParams['reference_1'],
                ]
            );

            // Log the response from Billplz
            Log::info('Billplz Create Bill Response', $response->toArray());

            if ($response->isSuccessful()) {
                return [
                    'url' => $response->toArray()['url'],
                    'billCode' => $response->toArray()['id'],
                    'status' => true
                ];
            }

            return ['error' => 'Failed to create bill', 'status' => false];
        } catch (\Throwable $th) {
            // Log any exceptions
            Log::error('Billplz Create Bill Exception', ['message' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }

    public function verifyPaymentStatus($billCode)
    {
        try {
            $response = $this->client->bill()->get($billCode);
            
            if ($response->isSuccessful()) {
                $billData = $response->toArray();
                //dd($billData);
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