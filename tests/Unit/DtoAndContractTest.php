<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Unit;

use Eamirgh\Basalam\Contracts\CartContract;
use Eamirgh\Basalam\Contracts\OrderContract;
use Eamirgh\Basalam\Contracts\ProductContract;
use Eamirgh\Basalam\DTOs\CartData;
use Eamirgh\Basalam\DTOs\CartItemData;
use Eamirgh\Basalam\DTOs\CreateProductData;
use Eamirgh\Basalam\DTOs\OrderData;
use Eamirgh\Basalam\DTOs\OrderItemData;
use Eamirgh\Basalam\DTOs\OrderParcelData;
use Eamirgh\Basalam\DTOs\ProductData;
use Eamirgh\Basalam\DTOs\ProductVariationData;
use Eamirgh\Basalam\DTOs\TokenData;
use Eamirgh\Basalam\DTOs\UpdateProductData;
use Eamirgh\Basalam\DTOs\UserData;
use Eamirgh\Basalam\DTOs\VendorData;
use Eamirgh\Basalam\Tests\Fixtures\AppCart;
use Eamirgh\Basalam\Tests\Fixtures\AppOrder;
use Eamirgh\Basalam\Tests\Fixtures\AppProduct;
use PHPUnit\Framework\TestCase;

class DtoAndContractTest extends TestCase
{
    public function test_token_data_serialization_and_expiry(): void
    {
        $token = new TokenData(
            accessToken: 'sample-token',
            tokenType: 'Bearer',
            expiresIn: 3600,
            refreshToken: 'refresh-token',
            createdAt: time() - 3550, // 50s left -> within 60s buffer -> expired
        );

        $this->assertSame('sample-token', $token->accessToken);
        $this->assertSame('Bearer sample-token', $token->getAuthorizationHeader());
        $this->assertTrue($token->isExpired());

        $freshToken = TokenData::fromArray([
            'access_token' => 'fresh-token',
            'expires_in' => 7200,
        ]);

        $this->assertFalse($freshToken->isExpired());
        $this->assertSame('fresh-token', $freshToken->accessToken);
        $this->assertArrayHasKey('access_token', $freshToken->toArray());
    }

    public function test_product_data_from_array_and_contract_integration(): void
    {
        $data = [
            'id' => 12345,
            'title' => 'زعفران ممتاز قائنات',
            'price' => 1500000,
            'primary_price' => 1800000,
            'stock' => 20,
            'sku' => 'ZAF-001',
            'weight' => 50,
            'photo' => [
                'id' => 999,
                'original' => 'https://img.basalam.com/zaf.jpg',
            ],
            'vendor' => [
                'id' => 78910,
                'title' => 'غرفه زعفران',
            ],
            'variations' => [
                [
                    'id' => 555,
                    'sku' => 'ZAF-001-1G',
                    'price' => 1500000,
                    'stock' => 10,
                ],
            ],
        ];

        $product = ProductData::fromArray($data);

        $this->assertInstanceOf(ProductContract::class, $product);
        $this->assertSame(12345, $product->getId());
        $this->assertSame('زعفران ممتاز قائنات', $product->getTitle());
        $this->assertSame(1500000, $product->getPrice());
        $this->assertSame(1800000, $product->getPrimaryPrice());
        $this->assertSame(20, $product->getStock());
        $this->assertSame('ZAF-001', $product->getSku());
        $this->assertSame(78910, $product->getVendorId());
        $this->assertSame('https://img.basalam.com/zaf.jpg', $product->getPhotoUrl());
        $this->assertCount(1, $product->getVariations());
        $this->assertSame(555, $product->getVariations()[0]->getId());

        // Test custom app model implementing ProductContract
        $appProduct = new AppProduct([
            'id' => 888,
            'title' => 'عسل طبیعی کوهستان',
            'price' => 450000,
            'stock' => 5,
            'sku' => 'HONEY-888',
            'photo_id' => 1234,
        ]);

        $adapted = ProductData::fromContract($appProduct);
        $this->assertSame(888, $adapted->getId());
        $this->assertSame('عسل طبیعی کوهستان', $adapted->getTitle());
        $this->assertSame(450000, $adapted->getPrice());
        $this->assertSame('HONEY-888', $adapted->getSku());
    }

    public function test_create_and_update_product_data_from_contract(): void
    {
        $appProduct = new AppProduct([
            'title' => 'روغن زیتون فرابکر',
            'price' => 320000,
            'stock' => 12,
            'sku' => 'OLIVE-01',
            'photo_id' => 77711,
            'weight' => 800,
            'package_weight' => 950,
            'preparation_days' => 2,
            'brief' => 'روغن زیتون طبیعی رودبار',
            'description' => 'توضیحات کامل محصول',
        ]);

        $createDto = CreateProductData::fromContract($appProduct);
        $payload = $createDto->toArray();

        $this->assertSame('روغن زیتون فرابکر', $payload['name']);
        $this->assertSame(77711, $payload['photo']);
        $this->assertSame([77711], $payload['photos']);
        $this->assertSame(320000, $payload['primary_price']);
        $this->assertSame(12, $payload['stock']);
        $this->assertSame('OLIVE-01', $payload['sku']);
        $this->assertSame(800, $payload['weight']);
        $this->assertSame(950, $payload['package_weight']);

        $updateDto = UpdateProductData::fromContract($appProduct);
        $updatePayload = $updateDto->toArray();
        $this->assertSame('روغن زیتون فرابکر', $updatePayload['name']);
        $this->assertSame(320000, $updatePayload['primary_price']);
    }

    public function test_cart_and_cart_item_contract_integration(): void
    {
        $item = CartItemData::fromArray([
            'id' => 1,
            'product_id' => 101,
            'title' => 'کتاب برنامه‌نویسی',
            'quantity' => 2,
            'unit_price' => 100000,
            'discount' => 10000,
        ]);

        $this->assertSame(101, $item->getProductId());
        $this->assertSame(2, $item->getQuantity());
        $this->assertSame(100000, $item->getUnitPrice());
        $this->assertSame(190000, $item->getTotalPrice());

        $cart = CartData::fromArray([
            'id' => 99,
            'items' => [$item],
            'coupon_code' => 'OFF20',
        ]);

        $this->assertInstanceOf(CartContract::class, $cart);
        $this->assertSame(190000, $cart->getTotalAmount());
        $this->assertSame(10000, $cart->getDiscountAmount());
        $this->assertSame(180000, $cart->getPayableAmount());
        $this->assertSame('OFF20', $cart->getCouponCode());
        $this->assertSame(2, $cart->getItemsCount());

        $appCart = new AppCart(id: 'app-cart-77', items: [$item]);
        $convertedCart = CartData::fromContract($appCart);
        $this->assertSame('app-cart-77', $convertedCart->getId());
        $this->assertCount(1, $convertedCart->getItems());
    }

    public function test_order_and_parcel_contract_integration(): void
    {
        $orderData = [
            'id' => 3001,
            'hash_id' => 'HSH-3001',
            'amount' => 500000,
            'payable_amount' => 480000,
            'status' => ['name' => 'paid'],
            'customer' => [
                'id' => 10,
                'name' => 'رضا حسینی',
                'mobile' => '09120000000',
            ],
            'items' => [
                [
                    'id' => 701,
                    'product_id' => 123,
                    'title' => 'محصول نمونه',
                    'quantity' => 1,
                    'price' => 500000,
                ],
            ],
            'parcels' => [
                [
                    'id' => 901,
                    'order_id' => 3001,
                    'vendor_id' => 78910,
                    'tracking_code' => 'POST123456789',
                    'shipping_method' => 'پیشتاز',
                    'status' => 'posted',
                ],
            ],
        ];

        $order = OrderData::fromArray($orderData);

        $this->assertInstanceOf(OrderContract::class, $order);
        $this->assertSame(3001, $order->getId());
        $this->assertSame('HSH-3001', $order->getHashId());
        $this->assertSame(500000, $order->getAmount());
        $this->assertSame('paid', $order->getStatus());
        $this->assertNotNull($order->getCustomer());
        $this->assertSame('رضا حسینی', $order->getCustomer()->getName());
        $this->assertCount(1, $order->getItems());
        $this->assertSame(701, $order->getItems()[0]->getId());
        $this->assertCount(1, $order->getParcels());
        $this->assertSame('POST123456789', $order->getParcels()[0]->getTrackingCode());

        // Test custom app order implementation
        $appOrder = new AppOrder(id: 4002, hashId: 'APP-4002', amount: 900000);
        $convertedOrder = OrderData::fromContract($appOrder);
        $this->assertSame(4002, $convertedOrder->getId());
        $this->assertSame('APP-4002', $convertedOrder->getHashId());
    }

    public function test_user_and_vendor_data(): void
    {
        $userData = UserData::fromArray([
            'id' => 501,
            'name' => 'علی احمدی',
            'mobile' => '09350000000',
            'hash_id' => 'USR-501',
            'vendor' => [
                'id' => 200,
                'title' => 'فروشگاه علی',
                'identifier' => 'ali-store',
                'status' => ['name' => 'active'],
            ],
        ]);

        $this->assertSame(501, $userData->getId());
        $this->assertSame('علی احمدی', $userData->getName());
        $this->assertNotNull($userData->getVendor());
        $this->assertSame(200, $userData->getVendor()->getId());
        $this->assertSame('فروشگاه علی', $userData->getVendor()->getTitle());
        $this->assertSame('active', $userData->getVendor()->getStatus());
    }
}
