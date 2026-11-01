# Webhooks

Basalam can notify your application in real-time when important events occur, such as when an order is paid or parcel status changes.

The `WebhookService` handles signature verification and webhook registration.

---

## 1. Verifying Webhook Signatures

All incoming webhook requests from Basalam should be validated using your configured `webhook_secret`.

### Controller Implementation

```php
namespace App\Http\Controllers;

use Eamirgh\Basalam\Facades\Basalam;
use Illuminate\Http\Request;

class BasalamWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Basalam-Signature') ?? $request->header('X-Signature');

        // Verify HMAC SHA-256 signature
        if (! Basalam::webhooks()->verifySignature($payload, (string) $signature)) {
            return response()->json(['error' => 'Invalid signature.'], 403);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        match ($event) {
            'order.created' => $this->handleOrderCreated($data),
            'parcel.posted' => $this->handleParcelPosted($data),
            'product.updated' => $this->handleProductUpdated($data),
            default => null,
        };

        return response()->json(['status' => 'acknowledged']);
    }

    protected function handleOrderCreated(array $data): void
    {
        // Process new order...
    }

    protected function handleParcelPosted(array $data): void
    {
        // Update local parcel status...
    }

    protected function handleProductUpdated(array $data): void
    {
        // Sync product stock or price...
    }
}
```

### Route Definition

Register your webhook route in `routes/api.php`:

```php
use App\Http\Controllers\BasalamWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/basalam', [BasalamWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
```

---

## 2. Managing Webhook Endpoints via API

### Register a Webhook

```php
use Eamirgh\Basalam\Facades\Basalam;

$webhook = Basalam::webhooks()->create(
    url: 'https://myapp.com/api/webhooks/basalam',
    events: [
        'order.created',
        'order.paid',
        'parcel.created',
        'parcel.posted',
    ],
    secret: config('basalam.webhook_secret')
);
```

### List Webhooks

```php
$webhooks = Basalam::webhooks()->list();
```

### Delete a Webhook

```php
Basalam::webhooks()->delete(webhookId: 105);
```
