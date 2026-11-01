# Vendors & Core Services

The `VendorService`, `UserService`, and `CategoryService` coordinate directly with **Basalam Gateway** and **Basalam Core Service** (`core.basalam.com`).

---

## 1. Vendor Operations

Access vendor management via `Basalam::vendors()`:

```php
use Eamirgh\Basalam\Facades\Basalam;

// 1. Get vendor details
$vendor = Basalam::vendors()->get(vendorId: 78910);
echo $vendor->getTitle();
echo $vendor->getIdentifier();

// 2. Toggle vendor store status (online / offline)
Basalam::vendors()->updateStatus(vendorId: 78910, status: 'online');

// 3. Get shipping methods from Basalam Core
$shippingMethods = Basalam::vendors()->getShippingMethods(vendorId: 78910);

// 4. Get vendor addresses
$addresses = Basalam::vendors()->getAddresses();

// 5. Request preparation days change
Basalam::vendors()->createPreparationDayRequest(
    vendorId: 78910,
    days: 3,
    description: 'درخواست افزایش زمان آماده‌سازی به دلیل حجم سفارشات'
);
```

---

## 2. User & Bank Accounts

Access user profile and banking details via `Basalam::users()`:

```php
// Current authenticated user
$user = Basalam::users()->me();

echo "User: {$user->getName()} (ID: {$user->getId()})";
if ($vendor = $user->getVendor()) {
    echo "Associated Vendor: {$vendor->getTitle()} (ID: {$vendor->getId()})";
}

// Bank accounts (Core API)
$bankAccounts = Basalam::users()->getBankAccounts(userId: $user->getId());

// Mobile verification OTP
Basalam::users()->requestMobileVerification(userId: $user->getId(), mobile: '09120000000');
Basalam::users()->confirmMobileVerification(userId: $user->getId(), code: '123456');
```

---

## 3. Categories & Attribute Specs

Access taxonomy metadata via `Basalam::categories()`:

```php
// List categories
$categories = Basalam::categories()->list();

// Get specific category attributes for product creation
$attributes = Basalam::categories()->getAttributes(categoryId: 142);
```
