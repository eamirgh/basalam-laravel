<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class CartAndBasketServiceTest extends TestCase
{
    public function test_get_active_basket(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/baskets*' => Http::response([
                'id' => 9999,
                'total_amount' => 450000,
                'discount_amount' => 50000,
                'payable_amount' => 400000,
                'coupon_code' => 'DISCOUNT10',
                'items' => [
                    [
                        'id' => 1,
                        'product_id' => 123,
                        'title' => 'محصول شماره یک',
                        'quantity' => 2,
                        'unit_price' => 225000,
                        'total_price' => 450000,
                    ],
                ],
            ], 200),
        ]);

        $cart = Basalam::cart()->get();

        $this->assertSame(9999, $cart->getId());
        $this->assertSame(450000, $cart->getTotalAmount());
        $this->assertSame(50000, $cart->getDiscountAmount());
        $this->assertSame(400000, $cart->getPayableAmount());
        $this->assertSame('DISCOUNT10', $cart->getCouponCode());
        $this->assertCount(1, $cart->getItems());
        $this->assertSame(2, $cart->getItemsCount());

        // Test basket alias
        $basket = Basalam::basket()->get();
        $this->assertSame(9999, $basket->getId());
    }

    public function test_coupon_management(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v2/basket/pro-coupons' => Http::response(['success' => true], 200),
        ]);

        $addRes = Basalam::cart()->addCoupon('OFFER2026');
        $this->assertTrue($addRes['success']);

        $removeRes = Basalam::cart()->removeCoupon('OFFER2026');
        $this->assertTrue($removeRes['success']);
    }

    public function test_product_basket_status(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/baskets/products/123/status' => Http::response([
                'product_id' => 123,
                'is_available' => true,
                'current_stock' => 5,
            ], 200),
        ]);

        $status = Basalam::cart()->getProductStatus(123);
        $this->assertTrue($status['is_available']);
        $this->assertSame(5, $status['current_stock']);
    }
}
