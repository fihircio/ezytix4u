<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

     'billplz' => [
        'key' => env('BILLPLZ_API_KEY'),
        'redirect_uri' => env('BILLPLZ_REDIRECT_URI'),
        'version' => env('BILLPLZ_VERSION', 'v4'),
        'x-signature' => env('BILLPLZ_X_SIGNATURE'),
        'sandbox' => env('BILLPLZ_SANDBOX', false),
    ],

    'toyyibpay' => [
        'client_secret' => env('TOYYIBPAY_USER_SECRET_KEY'),
        'redirect_uri' => env('TOYYIBPAY_REDIRECT_URI'),
        'sandbox' => env('TOYYIBPAY_SANDBOX', false),
        'code' => env('TOYYIBPAY_CODE'),
    ],

];
