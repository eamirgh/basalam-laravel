<?php

declare(strict_types=1);

namespace Eamirgh\Basalam;

use Closure;
use Eamirgh\Basalam\DTOs\TokenData;
use Eamirgh\Basalam\Http\ApiClient;
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
use Illuminate\Support\Facades\Http;

class BasalamManager
{
    /**
     * @var string|TokenData|Closure():(string|TokenData|null)|null
     */
    protected string|TokenData|Closure|null $token = null;

    protected ?int $vendorId = null;

    protected ?ApiClient $gatewayClient = null;

    protected ?ApiClient $coreClient = null;

    protected ?ApiClient $authClient = null;

    protected ?AuthService $authService = null;

    protected ?ProductService $productService = null;

    protected ?CartService $cartService = null;

    protected ?BasketService $basketService = null;

    protected ?OrderService $orderService = null;

    protected ?ParcelService $parcelService = null;

    protected ?VendorService $vendorService = null;

    protected ?UserService $userService = null;

    protected ?FileUploadService $fileUploadService = null;

    protected ?CategoryService $categoryService = null;

    protected ?WebhookService $webhookService = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        protected array $config = [],
    ) {
        $this->token = $config['token'] ?? $config['api_key'] ?? null;
        $this->vendorId = isset($config['vendor_id']) ? (int) $config['vendor_id'] : null;
    }

    /**
     * Return a cloned manager with a different access token
     *
     * @param string|TokenData|Closure():(string|TokenData|null) $token
     */
    public function withToken(string|TokenData|Closure $token): self
    {
        $clone = clone $this;
        $clone->token = $token;
        $clone->gatewayClient = null;
        $clone->coreClient = null;
        $clone->authClient = null;
        $clone->resetServices();

        return $clone;
    }

    /**
     * Return a cloned manager with a default vendor ID
     */
    public function withVendor(int|string $vendorId): self
    {
        $clone = clone $this;
        $clone->vendorId = (int) $vendorId;

        return $clone;
    }

    public function getVendorId(): ?int
    {
        return $this->vendorId;
    }

    public function gatewayClient(): ApiClient
    {
        if ($this->gatewayClient === null) {
            $this->gatewayClient = new ApiClient(
                baseUrl: (string) ($this->config['gateway_url'] ?? 'https://openapi.basalam.com'),
                token: $this->token,
                timeout: (int) ($this->config['timeout'] ?? 30),
                retryTimes: (int) ($this->config['retry']['times'] ?? 0),
                retrySleep: (int) ($this->config['retry']['sleep'] ?? 100),
            );
        }

        return $this->gatewayClient;
    }

    public function coreClient(): ApiClient
    {
        if ($this->coreClient === null) {
            $this->coreClient = new ApiClient(
                baseUrl: (string) ($this->config['core_url'] ?? 'https://core.basalam.com'),
                token: $this->token,
                timeout: (int) ($this->config['timeout'] ?? 30),
                retryTimes: (int) ($this->config['retry']['times'] ?? 0),
                retrySleep: (int) ($this->config['retry']['sleep'] ?? 100),
            );
        }

        return $this->coreClient;
    }

    public function authClient(): ApiClient
    {
        if ($this->authClient === null) {
            $this->authClient = new ApiClient(
                baseUrl: (string) ($this->config['auth_url'] ?? 'https://auth.basalam.com'),
                token: $this->token,
                timeout: (int) ($this->config['timeout'] ?? 30),
                retryTimes: (int) ($this->config['retry']['times'] ?? 0),
                retrySleep: (int) ($this->config['retry']['sleep'] ?? 100),
            );
        }

        return $this->authClient;
    }

    public function auth(): AuthService
    {
        if ($this->authService === null) {
            $this->authService = new AuthService(
                client: $this->authClient(),
                clientId: isset($this->config['client_id']) ? (string) $this->config['client_id'] : null,
                clientSecret: isset($this->config['client_secret']) ? (string) $this->config['client_secret'] : null,
                redirectUri: isset($this->config['redirect_uri']) ? (string) $this->config['redirect_uri'] : null,
                ssoUrl: (string) ($this->config['sso_url'] ?? 'https://basalam.com/accounts/sso'),
            );
        }

        return $this->authService;
    }

    public function products(): ProductService
    {
        return $this->productService ??= new ProductService($this->gatewayClient());
    }

    public function cart(): CartService
    {
        return $this->cartService ??= new CartService($this->gatewayClient());
    }

    public function basket(): BasketService
    {
        return $this->basketService ??= new BasketService($this->gatewayClient());
    }

    public function orders(): OrderService
    {
        return $this->orderService ??= new OrderService($this->gatewayClient());
    }

    public function parcels(): ParcelService
    {
        return $this->parcelService ??= new ParcelService($this->gatewayClient());
    }

    public function vendors(): VendorService
    {
        return $this->vendorService ??= new VendorService($this->gatewayClient(), $this->coreClient());
    }

    public function users(): UserService
    {
        return $this->userService ??= new UserService($this->gatewayClient(), $this->coreClient());
    }

    public function files(): FileUploadService
    {
        return $this->fileUploadService ??= new FileUploadService($this->gatewayClient());
    }

    public function categories(): CategoryService
    {
        return $this->categoryService ??= new CategoryService($this->coreClient());
    }

    public function webhooks(): WebhookService
    {
        return $this->webhookService ??= new WebhookService(
            $this->gatewayClient(),
            isset($this->config['webhook_secret']) ? (string) $this->config['webhook_secret'] : null,
        );
    }

    /**
     * Fake HTTP responses for testing
     *
     * @param array<string, mixed>|Closure $callback
     */
    public static function fake(array|Closure $callback = []): void
    {
        Http::fake($callback);
    }

    protected function resetServices(): void
    {
        $this->authService = null;
        $this->productService = null;
        $this->cartService = null;
        $this->basketService = null;
        $this->orderService = null;
        $this->parcelService = null;
        $this->vendorService = null;
        $this->userService = null;
        $this->fileUploadService = null;
        $this->categoryService = null;
        $this->webhookService = null;
    }
}
