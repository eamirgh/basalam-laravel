<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\DTOs\CreateProductData;
use Eamirgh\Basalam\DTOs\UpdateProductData;
use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\Tests\Fixtures\AppProduct;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ProductServiceTest extends TestCase
{
    public function test_get_product_by_id(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/products/24018670' => Http::response([
                'id' => 24018670,
                'title' => 'پیراهن مردانه کتان',
                'price' => 350000,
                'primary_price' => 350000,
                'stock' => 14,
                'sku' => 'SHIRT-COTTON-01',
                'vendor' => [
                    'id' => 78910,
                    'title' => 'پوشاک بهار',
                ],
            ], 200),
        ]);

        $product = Basalam::products()->get(24018670);

        $this->assertSame(24018670, $product->getId());
        $this->assertSame('پیراهن مردانه کتان', $product->getTitle());
        $this->assertSame(350000, $product->getPrice());
        $this->assertSame(14, $product->getStock());
        $this->assertSame(78910, $product->getVendorId());
    }

    public function test_list_vendor_products(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendors/78910/products*' => Http::response([
                'data' => [
                    [
                        'id' => 101,
                        'name' => 'محصول اول',
                        'price' => 100000,
                        'stock' => 5,
                    ],
                    [
                        'id' => 102,
                        'name' => 'محصول دوم',
                        'price' => 200000,
                        'stock' => 10,
                    ],
                ],
            ], 200),
        ]);

        $products = Basalam::products()->listByVendor(78910);

        $this->assertCount(2, $products);
        $this->assertSame('محصول اول', $products[0]->getTitle());
        $this->assertSame('محصول دوم', $products[1]->getTitle());
    }

    public function test_create_product_from_dto(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendors/78910/products' => Http::response([
                'id' => 999111,
                'title' => 'چای لاهیجان بهاره',
                'price' => 220000,
                'stock' => 25,
            ], 201),
        ]);

        $createDto = new CreateProductData(
            name: 'چای لاهیجان بهاره',
            photo: 554433,
            primaryPrice: 220000,
            stock: 25,
            sku: 'TEA-LAH-01'
        );

        $created = Basalam::products()->create(78910, $createDto);

        $this->assertSame(999111, $created->getId());
        $this->assertSame('چای لاهیجان بهاره', $created->getTitle());

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['name'] === 'چای لاهیجان بهاره' &&
                $data['primary_price'] === 220000 &&
                $data['sku'] === 'TEA-LAH-01';
        });
    }

    public function test_create_product_directly_from_app_contract(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendors/78910/products' => Http::response([
                'id' => 888777,
                'title' => 'عسل آویشن طبیعی',
                'price' => 450000,
                'stock' => 8,
            ], 201),
        ]);

        $appProduct = new AppProduct([
            'title' => 'عسل آویشن طبیعی',
            'price' => 450000,
            'stock' => 8,
            'sku' => 'HONEY-THYME',
            'photo_id' => 443322,
        ]);

        $created = Basalam::products()->create(78910, $appProduct);

        $this->assertSame(888777, $created->getId());
        $this->assertSame('عسل آویشن طبیعی', $created->getTitle());

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['name'] === 'عسل آویشن طبیعی' &&
                $data['photo'] === 443322;
        });
    }

    public function test_update_product(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/products/888777' => Http::response([
                'id' => 888777,
                'title' => 'عسل آویشن بهاره',
                'price' => 480000,
                'stock' => 15,
            ], 200),
        ]);

        $updateDto = new UpdateProductData(
            name: 'عسل آویشن بهاره',
            primaryPrice: 480000,
            stock: 15
        );

        $updated = Basalam::products()->update(888777, $updateDto);

        $this->assertSame(480000, $updated->getPrice());
        $this->assertSame('عسل آویشن بهاره', $updated->getTitle());
    }

    public function test_update_product_variation(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/products/100/variations/200' => Http::response([
                'id' => 200,
                'product_id' => 100,
                'price' => 195000,
                'stock' => 10,
                'sku' => 'VAR-100-200',
            ], 200),
        ]);

        $variation = Basalam::products()->updateVariation(100, 200, [
            'price' => 195000,
            'stock' => 10,
        ]);

        $this->assertSame(200, $variation->getId());
        $this->assertSame(195000, $variation->getPrice());
        $this->assertSame(10, $variation->getStock());
    }
}
