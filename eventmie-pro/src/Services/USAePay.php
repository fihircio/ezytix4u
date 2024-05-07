<?php

namespace Classiebit\Eventmie\Services;

use Illuminate\Support\Facades\Http;

class USAePay
{    
    /**
     * sourceKey
     *
     * @var mixed
     */
    protected $sourceKey, $pin, $url, $authKey;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->sourceKey = setting('apps.usaepay_sourceKey');  //'_2ll6SsTj7ZIkB8DTKC0sPu58ZRayM1l';
        $this->pin = setting('apps.usaepay_pin'); //'99999';
        $this->url = empty(setting('apps.usaepay_production_mode')) ? 'https://sandbox.usaepay.com/api/v2/transactions' : 'https://usaepay.com/api/v2/transactions';
    }
    
    /**
     * createTransaction
     *
     * @param  mixed $order
     * @param  mixed $currency
     * @return void
     */
    public function createTransaction($order = [], $currency = 'USD')
    {
        $flag = [];

        try {
            $payment_method = session('payment_method');

            $this->setAuthentication($this->sourceKey, $this->pin);

            $response = Http::withHeaders([
                'Authorization' => "Basic $this->authKey"
            ])->post($this->url, [
                "command" => "cc:sale",
                "amount" => $order['price'],
                "currency" => $currency,

                "creditcard" => [
                    "cardholder" => $payment_method['cardName'],
                    "number" => $payment_method['cardNumber'], //"4000100011112224",
                    "expiration" => $payment_method['cardMonth'] . substr($payment_method['cardYear'], -2),
                    "cvc" => $payment_method['cvc'],

                ],
            ]);
            $response = $response->json();
            
            if($response['result'] != 'Approved')
                throw new \Exception(__('eventmie-pro::em.payment').' '.__('eventmie-pro::em.failed'));

            $flag['transaction_id']    = $response['key'];
            $flag['payer_reference']   = $response['refnum'];
            $flag['message']           = $response['result'];
            $flag['status']            = true;
        
            return $flag;


        } catch (\Throwable $th) {

        }

        $flag['status'] = false;

        return $flag;
    }
    
    /**
     * setAuthentication
     *
     * @param  mixed $api_key
     * @param  mixed $api_pin
     * @return void
     */
    protected function setAuthentication($api_key, $api_pin)
    {
        $seed = substr(hash('sha256', rand()), 10, 25);
        $clear = $api_key . $seed . $api_pin;
        $hash = "s2/" . $seed . "/" . hash('sha256', $clear);
        $this->authKey =  base64_encode("$api_key:$hash");
    }
    
    /**
     * isUSAePay
     *
     * @return void
     */
    public function isUSAePay()
    {
        if(!empty(setting('apps.usaepay_sourceKey')) && !empty(setting('apps.usaepay_pin')))
            return true;

        return false;
    }
}
