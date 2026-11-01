<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\DTOs\ItemFilter;
use Eamirgh\Basalam\DTOs\OrderFilter;
use Eamirgh\Basalam\DTOs\OrderParcelFilter;
use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class OrderAndParcelServiceTest extends TestCase
{
    public function test_list_orders(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/customer-orders*' => Http::response([
                'data' => [
                    [
                        'id' => 1001,
                        'amount' => 300000,
                        'payable_amount' => 290000,
                        'status' => 'paid',
                    ],
                    [
                        'id' => 1002,
                        'amount' => 450000,
                        'payable_amount' => 450000,
                        'status' => 'processing',
                    ],
                ],
            ], 200),
        ]);

        $orders = Basalam::orders()->list(new OrderFilter(page: 1, perPage: 10, status: 'paid'));

        $this->assertCount(2, $orders);
        $this->assertSame(1001, $orders[0]->getId());
        $this->assertSame(300000, $orders[0]->getAmount());
    }

    public function test_get_single_order_details(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/customer-orders/5001' => Http::response([
                'id' => 5001,
                'hash_id' => 'ORD-5001-X',
                'amount' => 600000,
                'items' => [
                    [
                        'id' => 881,
                        'product_id' => 1234,
                        'title' => 'محصول سفارشی',
                        'quantity' => 2,
                        'price' => 300000,
                    ],
                ],
            ], 200),
        ]);

        $order = Basalam::orders()->get(5001);

        $this->assertSame(5001, $order->getId());
        $this->assertSame('ORD-5001-X', $order->getHashId());
        $this->assertCount(1, $order->getItems());
        $this->assertSame('محصول سفارشی', $order->getItems()[0]->getTitle());
    }

    public function test_get_order_items(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/customer-orders/items*' => Http::response([
                'data' => [
                    [
                        'id' => 11,
                        'product_id' => 22,
                        'title' => 'آیتم یک',
                        'quantity' => 1,
                        'price' => 50000,
                    ],
                ],
            ], 200),
        ]);

        $items = Basalam::orders()->getItems(new ItemFilter(productId: 22));

        $this->assertCount(1, $items);
        $this->assertSame(11, $items[0]->getId());
    }

    public function test_list_vendor_parcels(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendor-parcels*' => Http::response([
                'data' => [
                    [
                        'id' => 9001,
                        'order_id' => 5001,
                        'vendor_id' => 78910,
                        'status' => 'ready_to_send',
                    ],
                ],
            ], 200),
        ]);

        $parcels = Basalam::parcels()->list(new OrderParcelFilter(vendorId: 78910));

        $this->assertCount(1, $parcels);
        $this->assertSame(9001, $parcels[0]->getId());
        $this->assertSame('ready_to_send', $parcels[0]->getStatus());
    }

    public function test_set_parcel_preparation_and_posted(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendor-parcels/9001/set-preparation' => Http::response(['success' => true], 200),
            'https://openapi.basalam.com/v1/vendor-parcels/9001/set-posted' => Http::response(['success' => true], 200),
        ]);

        $prepRes = Basalam::parcels()->setPreparation(9001);
        $this->assertTrue($prepRes['success']);

        $postRes = Basalam::parcels()->setPosted(9001, 'POSTAL-TRACK-12345', 'پیشتاز');
        $this->assertTrue($postRes['success']);

        Http::assertSent(function ($request) {
            if ($request->url() === 'https://openapi.basalam.com/v1/vendor-parcels/9001/set-posted') {
                $data = $request->data();

                return $data['tracking_code'] === 'POSTAL-TRACK-12345' &&
                    $data['carrier'] === 'پیشتاز';
            }

            return true;
        });
    }
}
