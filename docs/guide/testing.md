# Testing & Mocking

`eamirgh/basalam-laravel` is built strictly on top of Laravel's native HTTP Client. Therefore, **no external network requests ever need to hit Basalam during unit or feature tests**.

You can use `Http::fake()` or `Basalam::fake()`.

---

## 1. Using `Http::fake()`

Mock exact Basalam Gateway or Core URLs:

```php
namespace Tests\Feature;

use Eamirgh\Basalam\Facades\Basalam;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductSyncTest extends TestCase
{
    public function test_it_creates_product_on_basalam(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendors/78910/products' => Http::response([
                'id' => 999111,
                'title' => 'محصول آزمایشی',
                'price' => 150000,
                'stock' => 10,
            ], 201),
        ]);

        $created = Basalam::products()->create(78910, [
            'name' => 'محصول آزمایشی',
            'photo' => 12345,
            'primary_price' => 150000,
            'stock' => 10,
        ]);

        $this->assertSame(999111, $created->getId());
        $this->assertSame('محصول آزمایشی', $created->getTitle());

        Http::assertSent(function ($request) {
            return $request->url() === 'https://openapi.basalam.com/v1/vendors/78910/products' &&
                   $request['primary_price'] === 150000;
        });
    }
}
```

---

## 2. Testing Exception Scenarios

Verify your application's error handling for 401, 403, 404, or 422:

```php
use Eamirgh\Basalam\Exceptions\AuthenticationException;
use Eamirgh\Basalam\Exceptions\ValidationException;

public function test_handles_validation_error(): void
{
    $this->expectException(ValidationException::class);

    Http::fake([
        'https://openapi.basalam.com/v1/vendors/78910/products' => Http::response([
            'message' => 'The name field is required.',
            'errors' => ['name' => ['The name field is required.']],
        ], 422),
    ]);

    Basalam::products()->create(78910, [
        'name' => '',
    ]);
}
```

---

## 3. Testing Your Model Contracts

Ensure your Eloquent models correctly satisfy the package contracts:

```php
namespace Tests\Unit;

use App\Models\Product;
use Eamirgh\Basalam\Contracts\ProductContract;
use Eamirgh\Basalam\DTOs\ProductData;
use Tests\TestCase;

class ProductContractTest extends TestCase
{
    public function test_product_model_implements_contract(): void
    {
        $product = Product::factory()->create([
            'name' => 'عسل طبیعی',
            'sale_price' => 200000,
        ]);

        $this->assertInstanceOf(ProductContract::class, $product);

        $dto = ProductData::fromContract($product);
        $this->assertSame('عسل طبیعی', $dto->getTitle());
        $this->assertSame(200000, $dto->getPrice());
    }
}
```
