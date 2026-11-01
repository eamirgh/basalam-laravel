<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\DTOs\CartData;
use Eamirgh\Basalam\Http\ApiClient;

class CartService
{
    public function __construct(
        protected ApiClient $client,
    ) {
    }

    /**
     * Get active basket/cart
     */
    public function get(bool $refresh = false): CartData
    {
        $response = $this->client->get('/v1/baskets', [
            'refresh' => $refresh ? 1 : 0,
        ]);

        $data = $response['data'] ?? $response;

        return CartData::fromArray($data);
    }

    /**
     * Get stock and price status of a product variation in active basket
     *
     * @return array<string, mixed>
     */
    public function getProductStatus(int|string $productId): array
    {
        return $this->client->get("/v1/baskets/products/{$productId}/status");
    }

    /**
     * Apply coupon to active basket
     *
     * @return array<string, mixed>
     */
    public function addCoupon(string $couponCode): array
    {
        return $this->client->post('/v2/basket/pro-coupons', [
            'coupon_code' => $couponCode,
        ]);
    }

    /**
     * Remove coupon from basket
     *
     * @return array<string, mixed>
     */
    public function removeCoupon(string $couponCode): array
    {
        return $this->client->delete('/v2/basket/pro-coupons', [
            'coupon_code' => $couponCode,
        ]);
    }
}
