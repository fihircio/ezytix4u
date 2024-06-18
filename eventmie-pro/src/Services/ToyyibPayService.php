<?php

namespace Classiebit\Eventmie\Services;

use GuzzleHttp\Client; // Assuming Guzzle is the HTTP client used

class ToyyibPayService
{
    protected $client;
    protected $_callback_url;
    protected $categoryCode;
    protected $userSecretKey;
    protected $toyyibpay_uri;

    public function __construct($settings = [])
    {
        // Initialize the Guzzle HTTP client
        $this->client = new Client();

        // Store category code and user secret key
        $this->categoryCode = $settings['toyyibpay_code'];
        $this->userSecretKey = $settings['toyyibpay_secret_key'];
        $this->toyyibpay_uri = $settings['toyyibpay_redirect_uri'];

        // Set your callback URL (use the route name)
        $this->_callback_url = route('eventmie.bookings_toyyibpay_callback');
    }

    // Create a new ToyyibPay payment
    public function createPayment($order = [], $currency = 'MYR', $booking = [])
    {
        // Debugging output
        //dd('createPayment parameters:', $order, $currency, $booking);

        try {
            $customer = $booking[0];

            $params = [
                'categoryCode'        => $this->categoryCode,
                'userSecretKey'       => $this->userSecretKey,
                'billName'            => $order['product_title'],
                'billDescription'     => $order['product_title'].' ('.$order['price_title'].')',
                'billPriceSetting'    => 1,
                'billPayorInfo'       => 1,
                'billAmount'          => (int) ($order['price'] * 100),
                'billReturnUrl'       => $this->_callback_url,
                'billCallbackUrl'     => $this->_callback_url,
                'billExternalReferenceNo' => $order['order_number'],
                'billTo'              => $customer['customer_name'],
                'billEmail'           => $customer['customer_email'],
                'billPhone'           => $customer['customer_phone'] ?? '',
                'billPaymentChannel'  => 2,
            ];

            // Debugging output
            //dd('createBill parameters:', $params);

            // Attempt to create the bill
            $response = $this->client->post($this->toyyibpay_uri . '/index.php/api/createBill', [
                'form_params' => $params
            ]);

            $responseData = json_decode($response->getBody(), true);

            // Debugging output
           //dd('createBill response:', $responseData);

            if (isset($responseData['error'])) {
                return ['error' => $responseData['error']['message'], 'status' => false];
            }

            if (!isset($responseData[0]['BillCode'])) {
                // Handle missing BillCode
                return ['error' => 'BillCode not found in response', 'status' => false];
            }

            $billCode = $responseData[0]['BillCode'];

            // Run the bill
            //$runBillResponse = $this->runBill($billCode, $params);

            if (isset($runBillResponse['error'])) {
                return ['error' => $runBillResponse['error'], 'status' => false];
            }

            // Generate the payment link
            $paymentLink = $this->billPaymentLink($billCode);

           // dd('payment link response:', $paymentLink);

            // After generating the payment link
            return ['url' => $paymentLink, 'billCode' => $billCode, 'status' => true];
        } catch (\Throwable $th) {
            // Handle any exceptions and print the error message
           // dd('Exception:', $th->getMessage());
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }

     // Toyyibpay Run Bill
     public function runBill($billCode, $billObject)
     {
        $url = $this->toyyibpay_uri . '/index.php/api/runBill';

         //dd('Bill response:', $billCode, $billObject);
 
         $data = [
             'form_params' => [
                 'userSecretKey' => $this->userSecretKey,
                 'billBankID' => $billObject['billBankID'] ?? '',
                 'billCode' => $billCode,
                 'billpaymentAmount' => $billObject['billAmount'],
                 'billpaymentPayorName' => $billObject['billTo'],
                 'billpaymentPayorPhone' => $billObject['billPhone'] ?? '',
                 'billpaymentPayorEmail' => $billObject['billEmail'],
             ]
         ];

         // dd('Bill response:', $data);
 
         $response = $this->client->post($url, $data);
         return json_decode($response->getBody(), true);
     }
 
     // Toyyibpay Get Payment Link
     public function billPaymentLink($billCode)
     {
        return $this->toyyibpay_uri . '/' . $billCode;
     }

     public function verifyPaymentStatus($billCode)
     {
         $params = [
             'userSecretKey' => $this->userSecretKey, // Your secret key
             'billCode' => $billCode,
         ];
     
         // Use a POST request for getBillTransactions
         $response = $this->client->post($this->toyyibpay_uri . '/index.php/api/getBillTransactions', [
             'form_params' => $params, 
         ]);
     
        $responseData = json_decode($response->getBody(), true);
     
         //dd('Verify Payment Status:', $responseData); // For debugging
     
         // Check for errors
         if (isset($responseData['error'])) {
            return ['error' => $responseData['error']['message'], 'status' => false]; // Return the error message 
         }
         return $responseData; 
         // Check if there's a bill payment status
         if (isset($responseData[0]['billpaymentStatus'])) {
             // Assuming 'billpaymentStatus' is the status key
             return $responseData[0]['billpaymentStatus']; 
         } else {
             return 'unknown'; // Unable to determine payment status
         }
     }
}
