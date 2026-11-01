# Basalam Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eamirgh/basalam-laravel.svg?style=flat-square)](https://packagist.org/packages/eamirgh/basalam-laravel)
[![Documentation](https://img.shields.io/badge/docs-GitHub%20Pages-blue.svg?style=flat-square)](https://eamirgh.github.io/basalam-laravel/)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eamirgh/basalam-laravel/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/eamirgh/basalam-laravel/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/eamirgh/basalam-laravel.svg?style=flat-square)](https://packagist.org/packages/eamirgh/basalam-laravel)
[![License](https://img.shields.io/packagist/l/eamirgh/basalam-laravel.svg?style=flat-square)](https://github.com/eamirgh/basalam-laravel/blob/master/LICENSE)

A comprehensive, TDD-backed Laravel package for interacting with the **Basalam API** ([Gateway](https://developers.basalam.com/docs/api/gateway) and [Core](https://developers.basalam.com/docs/api/core)).

Built directly on Laravel's native HTTP Client with **interface contracts** for zero SDK dependencies and seamless Eloquent model integration.

---

## Highlights

- **Zero SDK Dependencies**: Direct communication via Laravel HTTP Client (`Http::fake()`, retries, pools).
- **Interface Contract Architecture**: Implement `ProductContract`, `CartContract`, `OrderContract` on your own Eloquent models.
- **Multi-Version Compatibility**: Supports **PHP 8.2, 8.3, 8.4** across **Laravel 11, 12, and 13+**.
- **Unified Services**:
  - `Basalam::products()` — Manage products, variations, search, price history.
  - `Basalam::cart()` / `Basalam::basket()` — Active basket, line items, coupons, item status.
  - `Basalam::orders()` — Customer orders, order items, packaging hints, order stats.
  - `Basalam::parcels()` — Vendor shipping parcels, preparation state, postal tracking code registration.
  - `Basalam::vendors()` — Vendor profiles, online/offline status, carrier shipping methods.
  - `Basalam::users()` — Current user profile, bank accounts, mobile OTP verification.
  - `Basalam::files()` — Direct media upload for product photos (`product.photo`).
  - `Basalam::categories()` — Taxonomy trees, category attributes.
  - `Basalam::webhooks()` — HMAC-SHA256 signature verification & endpoint registration.
  - `Basalam::auth()` — OAuth 2.0 Authorization Code flow, PAT, and Client Credentials.
- **Multi-Tenant / Scoped Execution**: Switch tokens or vendor scope per-request using `Basalam::withToken($token)` and `Basalam::withVendor($id)`.

---

## Installation

```bash
composer require eamirgh/basalam-laravel
```

Publish configuration:

```bash
php artisan vendor:publish --tag=basalam-config
```

---

## Configuration

Set your credentials in `.env`:

```env
# Personal Access Token (for internal tools or single-vendor management)
BASALAM_TOKEN=eyJ0eXAiOi...
BASALAM_VENDOR_ID=78910

# OAuth 2.0 (for public or multi-user applications)
BASALAM_CLIENT_ID=your_client_id
BASALAM_CLIENT_SECRET=your_client_secret
BASALAM_REDIRECT_URI=https://your-domain.com/basalam/callback

# Webhook Secret
BASALAM_WEBHOOK_SECRET=your_secret_key
```

---

## Interface Contracts & Eloquent Integration

Instead of transforming your data into vendor-specific structures, implement package contracts on your Eloquent models:

```php
namespace App\Models;

use Eamirgh\Basalam\Contracts\ProductContract;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements ProductContract
{
    public function getId(): int|string { return $this->id; }
    public function getTitle(): string { return $this->name; }
    public function getPrice(): int { return (int) $this->price; }
    public function getPrimaryPrice(): ?int { return (int) $this->price; }
    public function getStock(): int { return (int) $this->inventory; }
    public function getSku(): ?string { return $this->sku; }
    public function getWeight(): ?int { return (int) $this->weight_grams; }
    public function getPackageWeight(): ?int { return (int) ($this->weight_grams + 50); }
    public function getPreparationDays(): ?int { return 2; }
    public function getBrief(): ?string { return $this->short_description; }
    public function getDescription(): ?string { return $this->description; }
    public function getVendorId(): ?int { return $this->vendor_id; }
    public function getStatus(): ?string { return 'available'; }
    public function getPhotoId(): int|string|null { return $this->basalam_photo_id; }
    public function getPhotoUrl(): ?string { return $this->image_url; }
    public function getPhotos(): array { return [$this->getPhotoId()]; }
    public function getVariations(): array { return []; }
}
```

Now, pass your model directly to any service method:

```php
use App\Models\Product;
use Eamirgh\Basalam\Facades\Basalam;

$product = Product::find(1);

// Direct contract-based creation
$basalamProduct = Basalam::products()->create(vendorId: 78910, product: $product);

// Direct contract-based update
Basalam::products()->update(productId: $basalamProduct->getId(), product: $product);
```

---

## Basic Usage

### 1. Products

```php
use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\DTOs\CreateProductData;

// Get product
$product = Basalam::products()->get(24018670);

// Upload photo and create product
$photoId = Basalam::files()->uploadProductPhoto(storage_path('app/tea.jpg'));

$newProduct = Basalam::products()->create(78910, new CreateProductData(
    name: 'چای بهاره لاهیجان',
    photo: $photoId,
    primaryPrice: 250000,
    stock: 20,
    sku: 'TEA-01'
));

// Update variation price and stock
Basalam::products()->updateVariation(
    productId: $newProduct->getId(),
    variationId: 10023,
    data: ['price' => 260000, 'stock' => 15]
);
```

### 2. Orders & Shipping Parcels

```php
use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\DTOs\OrderFilter;
use Eamirgh\Basalam\DTOs\OrderParcelFilter;

// List customer orders
$orders = Basalam::orders()->list(new OrderFilter(status: 'paid'));

// List vendor parcels awaiting shipment
$parcels = Basalam::parcels()->list(new OrderParcelFilter(vendorId: 78910, status: 'ready_to_send'));

// Register parcel as posted with tracking code
Basalam::parcels()->setPosted(
    parcelId: 98765,
    trackingCode: '109876543210987654321098',
    carrier: 'پست پیشتاز'
);
```

### 3. Shopping Cart

```php
// Retrieve active basket
$cart = Basalam::cart()->get();

echo $cart->getPayableAmount();
echo $cart->getItemsCount();

// Apply promotional coupon
Basalam::cart()->addCoupon('OFFER2026');
```

### 4. Dynamic Multi-Tenant Execution

```php
$userToken = auth()->user()->basalam_token;

// Requests executed in isolation with this token
$products = Basalam::withToken($userToken)
    ->products()
    ->listByVendor($vendorId);
```

### 5. Webhook Signature Verification

```php
public function handleWebhook(Request $request)
{
    $valid = Basalam::webhooks()->verifySignature(
        payload: $request->getContent(),
        signature: (string) $request->header('X-Basalam-Signature')
    );

    if (!$valid) {
        abort(403);
    }

    // Process event...
}
```

---

## Testing

Run the test suite via PHPUnit:

```bash
composer test
# or
./vendor/bin/phpunit
```

Mock API calls in your own tests via `Http::fake()` or `Basalam::fake()`:

```php
use Eamirgh\Basalam\Facades\Basalam;
use Illuminate\Support\Facades\Http;

public function test_vendor_sync(): void
{
    Http::fake([
        'https://openapi.basalam.com/v1/products/123' => Http::response([
            'id' => 123,
            'title' => 'محصول تستی',
            'price' => 100000,
        ], 200),
    ]);

    $product = Basalam::products()->get(123);
    $this->assertSame('محصول تستی', $product->getTitle());
}
```

---

## Documentation

Full interactive documentation is built with VitePress and available in the `docs` folder.

To run locally:

```bash
npm install
npm run docs:dev
```

---

## License

The MIT License (MIT). See [License File](LICENSE) for more information.
