<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Facades;

use Closure;
use Eamirgh\Basalam\BasalamManager;
use Eamirgh\Basalam\DTOs\TokenData;
use Eamirgh\Basalam\Services\AuthService;
use Eamirgh\Basalam\Services\BasketService;
use Eamirgh\Basalam\Services\CartService;
use Eamirgh\Basalam\Services\CategoryService;
use Eamirgh\Basalam\Services\FileUploadService;
use Eamirgh\Basalam\Services\OrderService;
use Eamirgh\Basalam\Services\ParcelService;
use Eamirgh\Basalam\Services\ProductService;
use Eamirgh\Basalam\Services\UserService;
use Eamirgh\Basalam\Services\VendorService;
use Eamirgh\Basalam\Services\WebhookService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static BasalamManager withToken(string|TokenData|Closure $token)
 * @method static BasalamManager withVendor(int|string $vendorId)
 * @method static ?int getVendorId()
 * @method static AuthService auth()
 * @method static ProductService products()
 * @method static CartService cart()
 * @method static BasketService basket()
 * @method static OrderService orders()
 * @method static ParcelService parcels()
 * @method static VendorService vendors()
 * @method static UserService users()
 * @method static FileUploadService files()
 * @method static CategoryService categories()
 * @method static WebhookService webhooks()
 * @method static void fake(array|Closure $callback = [])
 *
 * @see BasalamManager
 */
class Basalam extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'basalam';
    }
}
