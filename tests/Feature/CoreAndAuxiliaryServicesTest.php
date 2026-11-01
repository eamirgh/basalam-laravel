<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class CoreAndAuxiliaryServicesTest extends TestCase
{
    public function test_vendor_service_operations(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/vendors/78910' => Http::response([
                'id' => 78910,
                'title' => 'غرفه بهارستان',
                'identifier' => 'baharestan',
                'status' => 'active',
            ], 200),
            'https://core.basalam.com/v3/vendors/78910/shipping-methods' => Http::response([
                ['id' => 1, 'name' => 'پست پیشتاز'],
            ], 200),
            'https://openapi.basalam.com/v1/vendors/78910/status' => Http::response([
                'status' => 'online',
            ], 200),
        ]);

        $vendor = Basalam::vendors()->get(78910);
        $this->assertSame(78910, $vendor->getId());
        $this->assertSame('غرفه بهارستان', $vendor->getTitle());

        $shipping = Basalam::vendors()->getShippingMethods(78910);
        $this->assertCount(1, $shipping);

        $status = Basalam::vendors()->updateStatus(78910, 'online');
        $this->assertSame('online', $status['status']);
    }

    public function test_user_service_operations(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/users/me' => Http::response([
                'id' => 1234,
                'name' => 'کاربر تستی',
                'vendor' => [
                    'id' => 78910,
                    'title' => 'غرفه تستی',
                ],
            ], 200),
            'https://core.basalam.com/v3/users/1234/bank-information' => Http::response([
                ['id' => 10, 'iban' => 'IR123456789012345678901234'],
            ], 200),
        ]);

        $user = Basalam::users()->me();
        $this->assertSame(1234, $user->getId());
        $this->assertSame('کاربر تستی', $user->getName());
        $this->assertSame(78910, $user->getVendor()->getId());

        $banks = Basalam::users()->getBankAccounts(1234);
        $this->assertCount(1, $banks);
    }

    public function test_category_service_operations(): void
    {
        Http::fake([
            'https://core.basalam.com/v3/categories/1/attributes' => Http::response([
                'attributes' => [
                    ['id' => 5, 'name' => 'وزن خالص'],
                ],
            ], 200),
            'https://core.basalam.com/v3/categories*' => Http::response([
                ['id' => 1, 'title' => 'مواد غذایی'],
            ], 200),
        ]);

        $categories = Basalam::categories()->list();
        $this->assertCount(1, $categories);

        $attrs = Basalam::categories()->getAttributes(1);
        $this->assertArrayHasKey('attributes', $attrs);
    }

    public function test_webhook_signature_verification(): void
    {
        $payload = json_encode(['event' => 'order.created', 'id' => 101]);
        $secret = 'super-secret-key';
        $signature = hash_hmac('sha256', $payload, $secret);

        $isValid = Basalam::webhooks()->verifySignature($payload, $signature, $secret);
        $this->assertTrue($isValid);

        $isInvalid = Basalam::webhooks()->verifySignature($payload, 'wrong-signature', $secret);
        $this->assertFalse($isInvalid);
    }

    public function test_file_upload_service(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/files' => Http::response([
                'id' => 238300331,
                'file_name' => 'sample.jpg',
            ], 200),
        ]);

        $photoId = Basalam::files()->uploadProductPhoto('fake-binary-content', 'sample.jpg');
        $this->assertSame(238300331, $photoId);
    }
}
