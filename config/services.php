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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'credo' => [
        'base_url' => env('CREDO_BASE_URL', 'https://api.credocentral.com'),
        'public_key' => env('CREDO_PUBLIC_KEY', ''),
        'secret_key' => env('CREDO_SECRET_KEY', ''),
        'mode' => env('CREDO_PAYMENT_MODE', env('PAYMENT_MODE', 'live')),
        'callback_url' => env('CREDO_CALLBACK_URL') ?: env('CREDO_FRONTEND_CALLBACK_URL'),
    ],

    'paystack' => [
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY', ''),
        'secret_key' => env('PAYSTACK_SECRET_KEY', ''),
        'mode' => env('PAYSTACK_PAYMENT_MODE', env('PAYMENT_MODE', 'live')),
        'callback_url' => env('PAYSTACK_CALLBACK_URL') ?: env('PAYSTACK_FRONTEND_CALLBACK_URL'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'mode' => env('PAYPAL_MODE', env('PAYMENT_MODE', 'live')),
    ],

    'moneris' => [
        'store_id' => env('MONERIS_STORE_ID', ''),
        'api_token' => env('MONERIS_API_TOKEN', ''),
        'checkout_id' => env('MONERIS_CHECKOUT_ID', ''),
        'mode' => env('MONERIS_MODE', env('PAYMENT_MODE', 'live')),
    ],

    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', 'paystack'),
        'currency' => env('PORTAL_BASE_CURRENCY', 'USD'),
    ],

];
