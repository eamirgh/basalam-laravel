# Getting Started

`eamirgh/basalam-laravel` provides an expressive, idiomatic, and enterprise-grade Laravel integration with Basalam's API ecosystem ([Basalam Gateway](https://developers.basalam.com/docs/api/gateway) and [Basalam Core](https://developers.basalam.com/docs/api/core)).

## Requirements

- **PHP**: `^8.2`, `^8.3`, or `^8.4`
- **Laravel**: `11.x`, `12.x`, or `13.x+`
- **Composer** `^2.2`

## Installation

Install the package via Composer:

```bash
composer require eamirgh/basalam-laravel
```

The package automatically registers its ServiceProvider (`Eamirgh\Basalam\BasalamServiceProvider`) and Facade (`Basalam`).

## Publish Configuration

Publish the configuration file to `config/basalam.php`:

```bash
php artisan vendor:publish --tag=basalam-config
```

## Quick Verification

Set your personal access token in `.env`:

```env
BASALAM_TOKEN=your_personal_access_token_here
```

Test your connection in tinker or a route:

```php
use Eamirgh\Basalam\Facades\Basalam;

// Verify current user & vendor
$me = Basalam::users()->me();

echo "Hello {$me->getName()}! Vendor ID: {$me->getVendor()?->getId()}";
```
