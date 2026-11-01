# Cart & Baskets

The `CartService` (also accessible as `Basalam::basket()`) handles shopping basket operations on Basalam Gateway (`/v1/baskets`) and Order Service (`/v2/basket`).

```php
use Eamirgh\Basalam\Facades\Basalam;

$cart = Basalam::cart();
// or
$basket = Basalam::basket();
```

---

## 1. Retrieve Active Basket

Fetch the authenticated user's current shopping cart:

```php
$cart = Basalam::cart()->get(refresh: false);

echo "Basket ID: {$cart->getId()}\n";
echo "Total Amount: {$cart->getTotalAmount()}\n";
echo "Discount: {$cart->getDiscountAmount()}\n";
echo "Payable Amount: {$cart->getPayableAmount()}\n";
echo "Items Count: {$cart->getItemsCount()}\n";

foreach ($cart->getItems() as $item) {
    echo "- {$item->getTitle()}: {$item->getQuantity()} x {$item->getUnitPrice()}\n";
}
```

---

## 2. Check Product Status in Basket

Before final invoice checkout, verify stock and pricing availability:

```php
$status = Basalam::cart()->getProductStatus(productId: 24018670);

if ($status['is_available'] ?? false) {
    echo "Product is in stock!";
}
```

---

## 3. Apply & Remove Coupons

Apply a promotional coupon code:

```php
$response = Basalam::cart()->addCoupon('SPRING2026');
```

Remove an applied coupon code:

```php
$response = Basalam::cart()->removeCoupon('SPRING2026');
```
