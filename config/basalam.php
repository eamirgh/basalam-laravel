<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Basalam API Credentials
    |--------------------------------------------------------------------------
    |
    | For private tools and direct vendor management, you can use a Personal
    | Access Token (PAT) generated from the Basalam Developer Panel.
    |
    */
    'token' => env('BASALAM_TOKEN', env('BASALAM_API_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Default Vendor ID
    |--------------------------------------------------------------------------
    |
    | When managing products or vendor parcels for a single vendor,
    | setting a default vendor ID saves you from passing it every time.
    |
    */
    'vendor_id' => env('BASALAM_VENDOR_ID'),

    /*
    |--------------------------------------------------------------------------
    | OAuth 2.0 Client Credentials (Authorization Code & Client Credentials)
    |--------------------------------------------------------------------------
    |
    | If you are building a public app or integrating with user authorization,
    | configure your client credentials obtained from developers.basalam.com.
    |
    */
    'client_id' => env('BASALAM_CLIENT_ID'),
    'client_secret' => env('BASALAM_CLIENT_SECRET'),
    'redirect_uri' => env('BASALAM_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Secret key used to verify incoming webhook payloads from Basalam.
    |
    */
    'webhook_secret' => env('BASALAM_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    |
    | Basalam routes Gateway, Core and Auth through distinct microservice
    | domains. You can override these in testing or mock environments.
    |
    */
    'gateway_url' => env('BASALAM_GATEWAY_URL', 'https://openapi.basalam.com'),
    'core_url' => env('BASALAM_CORE_URL', 'https://core.basalam.com'),
    'auth_url' => env('BASALAM_AUTH_URL', 'https://auth.basalam.com'),
    'sso_url' => env('BASALAM_SSO_URL', 'https://basalam.com/accounts/sso'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds and automatic retry configuration for network requests.
    |
    */
    'timeout' => (int) env('BASALAM_TIMEOUT', 30),
    'retry' => [
        'times' => (int) env('BASALAM_RETRY_TIMES', 0),
        'sleep' => (int) env('BASALAM_RETRY_SLEEP', 100),
    ],
];
