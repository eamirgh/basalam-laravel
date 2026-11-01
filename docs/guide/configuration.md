# Configuration

The published configuration file resides in `config/basalam.php`.

```php
return [
    // Personal Access Token or fallback
    'token' => env('BASALAM_TOKEN', env('BASALAM_API_KEY')),

    // Default Vendor ID (optional, handy for single-vendor integrations)
    'vendor_id' => env('BASALAM_VENDOR_ID'),

    // OAuth 2.0 Credentials (for multi-tenant or public apps)
    'client_id' => env('BASALAM_CLIENT_ID'),
    'client_secret' => env('BASALAM_CLIENT_SECRET'),
    'redirect_uri' => env('BASALAM_REDIRECT_URI'),

    // Webhook Secret for signature validation
    'webhook_secret' => env('BASALAM_WEBHOOK_SECRET'),

    // Base API URLs
    'gateway_url' => env('BASALAM_GATEWAY_URL', 'https://openapi.basalam.com'),
    'core_url' => env('BASALAM_CORE_URL', 'https://core.basalam.com'),
    'auth_url' => env('BASALAM_AUTH_URL', 'https://auth.basalam.com'),
    'sso_url' => env('BASALAM_SSO_URL', 'https://basalam.com/accounts/sso'),

    // HTTP Client settings
    'timeout' => (int) env('BASALAM_TIMEOUT', 30),
    'retry' => [
        'times' => (int) env('BASALAM_RETRY_TIMES', 0),
        'sleep' => (int) env('BASALAM_RETRY_SLEEP', 100),
    ],
];
```

## Environment Variables (.env)

```env
# Single-vendor / Script access
BASALAM_TOKEN=eyJ0eXAiOi...
BASALAM_VENDOR_ID=78910

# OAuth 2.0 Public App
BASALAM_CLIENT_ID=your_client_id
BASALAM_CLIENT_SECRET=your_client_secret
BASALAM_REDIRECT_URI=https://your-domain.com/basalam/callback

# Webhook verification
BASALAM_WEBHOOK_SECRET=your_random_webhook_secret

# Network tuning
BASALAM_TIMEOUT=30
BASALAM_RETRY_TIMES=2
BASALAM_RETRY_SLEEP=200
```
