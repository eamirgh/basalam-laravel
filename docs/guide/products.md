# Products & Variations

The `ProductService` provides complete access to product management on Basalam Gateway (`/v1/products` and `/v1/vendors/{vendor_id}/products`).

Access it via:

```php
use Eamirgh\Basalam\Facades\Basalam;

$products = Basalam::products();
```

---

## 1. Get Product Details

```php
$product = Basalam::products()->get(productId: 24018670);

echo $product->getTitle();        // "پیراهن مردانه کتان"
echo $product->getPrice();        // 350000 (Toman)
echo $product->getStock();        // 14
echo $product->getSku();          // "SHIRT-COTTON-01"
echo $product->getPhotoUrl();     // "https://..."
```

---

## 2. Listing Vendor Products

```php
$vendorId = 78910;

$products = Basalam::products()->listByVendor($vendorId, [
    'page' => 1,
    'per_page' => 20,
]);

foreach ($products as $product) {
    echo "{$product->getTitle()}: {$product->getPrice()} Toman\n";
}
```

---

## 3. Creating a Product

Adding a product requires:
1. Uploading the primary product photo to get a `photo_id` (via `Basalam::files()`).
2. Sending the product payload.

### Method A: Using `CreateProductData` DTO

```php
use Eamirgh\Basalam\DTOs\CreateProductData;
use Eamirgh\Basalam\Facades\Basalam;

// 1. Upload photo first
$photoId = Basalam::files()->uploadProductPhoto(
    file: storage_path('app/photos/tea.jpg'),
    filename: 'tea.jpg'
);

// 2. Create product DTO
$dto = new CreateProductData(
    name: 'چای بهاره لاهیجان ۵۰۰ گرمی',
    photo: $photoId,
    photos: [$photoId],
    brief: 'چای سیاه طبیعی ممتاز با عطر و طعم اصیل ایرانی',
    description: 'توضیحات تکمیلی محصول و شرایط نگهداری...',
    preparationDays: 2,
    weight: 500,
    packageWeight: 550,
    primaryPrice: 280000,
    stock: 30,
    sku: 'TEA-LAH-500'
);

$product = Basalam::products()->create(vendorId: 78910, product: $dto);

echo "Created product ID: {$product->getId()}";
```

### Method B: Passing Your Eloquent Model Directly

If your `Product` model implements `Eamirgh\Basalam\Contracts\ProductContract`:

```php
$myProduct = App\Models\Product::find(42);

$created = Basalam::products()->create(
    vendorId: 78910,
    product: $myProduct
);
```

---

## 4. Updating a Product

Update specific attributes without touching the rest:

```php
use Eamirgh\Basalam\DTOs\UpdateProductData;

$update = new UpdateProductData(
    primaryPrice: 295000,
    stock: 25
);

$updatedProduct = Basalam::products()->update(productId: 24018670, product: $update);
```

Or pass your updated Eloquent model directly:

```php
$myProduct->sale_price = 295000;
$myProduct->save();

Basalam::products()->update(
    productId: 24018670,
    product: $myProduct
);
```

---

## 5. Variations Management

Update pricing or stock on specific product variations:

```php
$variation = Basalam::products()->updateVariation(
    productId: 24018670,
    variationId: 10023,
    data: [
        'price' => 310000,
        'stock' => 8,
        'sku' => 'SHIRT-COTTON-L-BLUE',
    ]
);

echo "Updated variation stock: {$variation->getStock()}";
```

---

## 6. Search Products

Query Basalam marketplace products by search criteria:

```php
$results = Basalam::products()->search([
    'query' => 'زعفران قائنات',
    'limit' => 10,
]);
```
