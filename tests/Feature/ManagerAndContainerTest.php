<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\BasalamManager;
use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ManagerAndContainerTest extends TestCase
{
    public function test_service_provider_binds_manager_and_facade(): void
    {
        $manager = $this->app->make('basalam');

        $this->assertInstanceOf(BasalamManager::class, $manager);
        $this->assertSame($manager, Basalam::getFacadeRoot());
        $this->assertSame(78910, Basalam::getVendorId());
    }

    public function test_manager_immutable_token_cloning(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/users/me' => Http::response(['id' => 777], 200),
        ]);

        $customManager = Basalam::withToken('user-isolated-token');

        $this->assertNotSame(Basalam::getFacadeRoot(), $customManager);

        $customManager->users()->me();

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer user-isolated-token');
        });
    }

    public function test_manager_with_vendor(): void
    {
        $scoped = Basalam::withVendor(99001);

        $this->assertSame(99001, $scoped->getVendorId());
        $this->assertSame(78910, Basalam::getVendorId());
    }
}
