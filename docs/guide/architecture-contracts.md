# Interface Contracts & DTOs

A core architectural strength of `eamirgh/basalam-laravel` is that **your application does not need to convert its domain models into proprietary SDK structures**.

Instead, the package provides interfaces (`ProductContract`, `CartContract`, `CartItemContract`, `OrderContract`, etc.). Any Eloquent Model or custom DTO that implements these contracts can be passed directly into Basalam operations.

## Available Contracts

| Interface | Purpose | Package Default DTO |
|---|---|---|
| `ProductContract` | Product data schema & photo/variation mapping | `ProductData` |
| `ProductVariationContract` | Product variation (size, color, SKU, price) | `ProductVariationData` |
| `CartContract` | Active shopping basket & discounts | `CartData` |
| `CartItemContract` | Single line-item in shopping basket | `CartItemData` |
| `OrderContract` | Customer order, status, financial totals | `OrderData` |
| `OrderItemContract` | Purchased item line in order | `OrderItemData` |
| `OrderParcelContract` | Shipping parcel & tracking info | `OrderParcelData` |
| `VendorContract` | Vendor profile & store metadata | `VendorData` |
| `UserContract` | Customer / vendor user entity | `UserData` |

---

## Example: Eloquent Product Integration

Implement `ProductContract` directly on your app's `App\Models\Product`:

```php
namespace App\Models;

use Eamirgh\Basalam\Contracts\ProductContract;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements ProductContract
{
    protected $guarded = [];

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return (int) $this->sale_price;
    }

    public function getPrimaryPrice(): ?int
    {
        return (int) ($this->original_price ?? $this->sale_price);
    }

    public function getStock(): int
    {
        return (int) $this->inventory_count;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getWeight(): ?int
    {
        return (int) $this->weight_grams;
    }

    public function getPackageWeight(): ?int
    {
        return (int) ($this->weight_grams + 50);
    }

    public function getPreparationDays(): ?int
    {
        return $this->lead_time_days ?? 2;
    }

    public function getBrief(): ?string
    {
        return $this->short_description;
    }

    public function getDescription(): ?string
    {
        return $this->full_description;
    }

    public function getVendorId(): ?int
    {
        return $this->vendor_id;
    }

    public function getStatus(): ?string
    {
        return $this->is_active ? 'available' : 'unavailable';
    }

    public function getPhotoId(): int|string|null
    {
        return $this->basalam_photo_id;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->featured_image_url;
    }

    public function getPhotos(): array
    {
        return [$this->getPhotoId()];
    }

    public function getVariations(): array
    {
        return [];
    }
}
```

### Passing Your Model Directly to Basalam

Now, you can create or sync products on Basalam without converting arrays:

```php
use App\Models\Product;
use Eamirgh\Basalam\Facades\Basalam;

$product = Product::find(1);

// Direct contract-based creation!
$basalamProduct = Basalam::products()->create(
    vendorId: 78910,
    product: $product
);

// Direct contract-based update!
Basalam::products()->update(
    productId: $basalamProduct->getId(),
    product: $product
);
```

---

## Example: Eloquent Cart Integration

Implement `CartContract` on your shopping basket:

```php
namespace App\Models;

use Eamirgh\Basalam\Contracts\CartContract;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model implements CartContract
{
    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getItems(): array
    {
        return $this->items->all(); // each implements CartItemContract
    }

    public function getTotalAmount(): int
    {
        return (int) $this->subtotal;
    }

    public function getDiscountAmount(): int
    {
        return (int) $this->discount_total;
    }

    public function getPayableAmount(): int
    {
        return (int) $this->grand_total;
    }

    public function getCouponCode(): ?string
    {
        return $this->applied_coupon;
    }

    public function getItemsCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }
}
```

## Using Standalone DTOs

If your application prefers plain objects without Eloquent interfaces, instantiate the package DTOs directly:

```php
use Eamirgh\Basalam\DTOs\CreateProductData;

$dto = new CreateProductData(
    name: 'زعفران ۱ مثقال',
    photo: 238300331,
    primaryPrice: 350000,
    stock: 20,
    sku: 'ZAF-01'
);

Basalam::products()->create(78910, $dto);
```
