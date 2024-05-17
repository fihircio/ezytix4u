<?php 

namespace Classiebit\Eventmie\Services;
use Billplz\Laravel\Billplz as BillplzClient;
use Billplz\Client;

class BillplzService
{
    protected $billplz;
    protected $_callback_url;

    public function __construct($settings = [])
    {
        $this->billplz = new BillplzClient(
            $settings['billplz_secret_key'],
            $settings['billplz_sandbox'] ? 'staging' : 'live' 
        );

        // Set your callback URL (use the route name)
        $this->_callback_url = route('eventmie.bookings_billplz_callback');
    }

    // Create a new Billplz bill
    public function createBill($order = [], $currency = 'MYR', $customer = [])
    {   
        try {
            $response = $this->billplz->bill()->create(
                  
                setting('apps.billplz_collection_id'), // Replace with your actual collection ID 
                $customer['email'],
                $customer['name'],
                (int) ($order['price'] * 100), // amount in cents
                $this->_callback_url,
                $order['product_title'].' ('.$order['price_title'].')',
                [
                    'redirect_url' => $this->_callback_url
                ]
            );
            dd($response);

            if (isset($response['error'])) 
            {
                return ['error' => $response['error']['message'], 'status' => false];
            }

            // Return the Billplz payment page URL 
            return ['url' => $response['url'], 'status' => true];

        } catch (\Throwable $th) {
            return ['error' => $th->getMessage(), 'status' => false];
        }
    }
}