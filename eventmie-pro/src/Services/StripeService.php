<?php

namespace Classiebit\Eventmie\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Account;
use Stripe\AccountLink;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $publicKey;
    protected $secretKey;
    protected $webhookSecret;
    protected $mode;

    /**
     * Attempt to manually load Stripe SDK if it's not found in the autoloader.
     * This is useful for environments where composer dump-autoload cannot be run.
     */
    protected function loadSDK()
    {
        if (!class_exists(\Stripe\Stripe::class)) {
            $path = base_path('vendor/stripe/stripe-php/init.php');
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }

    public function __construct()
    {
        $this->loadSDK();
        $this->publicKey     = setting('apps.stripe_public_key');
        $this->secretKey     = setting('apps.stripe_secret_key');
        $this->webhookSecret = setting('apps.stripe_webhook_secret');
        $this->mode          = setting('apps.stripe_mode') ?? 'sandbox';

        if ($this->secretKey && class_exists(\Stripe\Stripe::class)) {
            \Stripe\Stripe::setApiKey($this->secretKey);
        }
    }

    protected function isSDKAvailable($class = \Stripe\Stripe::class)
    {
        $this->loadSDK();
        return class_exists($class);
    }

    /**
     * Create Stripe Checkout Session for payment
     */
    public function createCheckoutSession($order, $currency, $booking)
    {
        if (!$this->isSDKAvailable(\Stripe\Checkout\Session::class)) {
            return ['status' => false, 'error' => 'Stripe SDK (Checkout) not found on server.'];
        }

        try {
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $order['product_title'],
                        ],
                        'unit_amount' => $order['price'] * 100, // Amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('eventmie.bookings_stripe_callback') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('eventmie.events_index'),
                'metadata' => [
                    'order_number' => $order['order_number'],
                ],
            ]);

            return [
                'status' => true,
                'url' => $checkout_session->url,
                'session_id' => $checkout_session->id
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify Stripe Payment
     */
    public function verifyPayment($sessionId)
    {
        if (!$this->isSDKAvailable(\Stripe\Checkout\Session::class)) {
            return ['status' => false, 'error' => 'Stripe SDK (Session) not found on server.'];
        }

        try {
            $session = Session::retrieve($sessionId);
            if ($session->payment_status === 'paid') {
                return [
                    'status' => true,
                    'transaction_id' => $session->payment_intent,
                    'message' => 'Stripe Payment Successful',
                    'payer_reference' => $session->customer_details->email ?? '',
                    'order_number' => $session->metadata->order_number ?? ''
                ];
            }
            return ['status' => false, 'message' => 'Payment not completed'];
        } catch (\Exception $e) {
            Log::error('Stripe Verification Error: ' . $e->getMessage());
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create Stripe Connect Account and Link
     */
    public function createConnectLink($user)
    {
        if (!$this->isSDKAvailable(\Stripe\Account::class) || !$this->isSDKAvailable(\Stripe\AccountLink::class)) {
            return ['status' => false, 'error' => 'Stripe SDK (Connect) not found on server.'];
        }

        try {
            if (!$user->stripe_account_id) {
                $account = \Stripe\Account::create([
                    'type' => 'express',
                    'email' => $user->email,
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                ]);
                $user->stripe_account_id = $account->id;
                $user->save();
            }

            $account_link = \Stripe\AccountLink::create([
                'account' => $user->stripe_account_id,
                'refresh_url' => route('eventmie.stripe_connect_refresh'),
                'return_url' => route('eventmie.stripe_connect_return'),
                'type' => 'account_onboarding',
            ]);

            return [
                'status' => true,
                'url' => $account_link->url
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Connect Error: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check Stripe Account Status
     */
    public function checkAccountStatus($accountId)
    {
        if (!$this->isSDKAvailable(\Stripe\Account::class)) {
            return false;
        }

        try {
            $account = \Stripe\Account::retrieve($accountId);
            return $account->details_submitted;
        } catch (\Exception $e) {
            Log::error('Stripe Account Status Error: ' . $e->getMessage());
            return false;
        }
    }
}
