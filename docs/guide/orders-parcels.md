# Orders & Parcels

Order processing in Basalam involves customer orders and vendor parcels (shipping packages).

Access via:

```php
use Eamirgh\Basalam\Facades\Basalam;

$orders = Basalam::orders();
$parcels = Basalam::parcels();
```

---

## 1. List Customer Orders

Use `OrderFilter` for structured filtering and pagination:

```php
use Eamirgh\Basalam\DTOs\OrderFilter;
use Eamirgh\Basalam\Facades\Basalam;

$filter = new OrderFilter(
    page: 1,
    perPage: 15,
    status: 'paid',
    sort: 'DESC'
);

$orders = Basalam::orders()->list($filter);

foreach ($orders as $order) {
    echo "Order #{$order->getId()} ({$order->getHashId()}): {$order->getPayableAmount()} Toman\n";
    echo "Status: {$order->getStatus()}\n";
    echo "Customer: {$order->getCustomer()?->getName()} ({$order->getCustomer()?->getMobile()})\n";
}
```

---

## 2. Get Single Order Details

```php
$order = Basalam::orders()->get(orderId: 54321);

// Line items
foreach ($order->getItems() as $item) {
    echo "- {$item->getTitle()}: {$item->getQuantity()} x {$item->getPrice()} Toman\n";
}

// Shipping parcels
foreach ($order->getParcels() as $parcel) {
    echo "Parcel #{$parcel->getId()}: Status {$parcel->getStatus()}\n";
}
```

---

## 3. Order Items Search & Hints

Retrieve specific customer order items:

```php
use Eamirgh\Basalam\DTOs\ItemFilter;

$items = Basalam::orders()->getItems(new ItemFilter(
    productId: 24018670,
    status: 'paid'
));
```

Get parcel packing hints:

```php
$hints = Basalam::orders()->getParcelHints(orderId: 54321);
```

---

## 4. Vendor Parcels & Logistics

As a vendor, shipments are tracked in parcels.

### List Vendor Parcels

```php
use Eamirgh\Basalam\DTOs\OrderParcelFilter;

$parcels = Basalam::parcels()->list(new OrderParcelFilter(
    vendorId: 78910,
    status: 'ready_to_send'
));

foreach ($parcels as $parcel) {
    echo "Parcel #{$parcel->getId()} for Order #{$parcel->getOrderId()}\n";
}
```

### Update Parcel Preparation Status

Mark that parcel packaging has begun:

```php
Basalam::parcels()->setPreparation(parcelId: 98765);
```

### Mark Parcel as Shipped with Postal Tracking Code

Once shipped via Iran Post or courier, register the tracking code:

```php
Basalam::parcels()->setPosted(
    parcelId: 98765,
    trackingCode: '109876543210987654321098',
    carrier: 'پست پیشتاز'
);
```
