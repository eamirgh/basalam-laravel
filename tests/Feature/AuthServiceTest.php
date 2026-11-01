<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\Facades\Basalam;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class AuthServiceTest extends TestCase
{
    public function test_get_authorization_url(): void
    {
        $url = Basalam::auth()->getAuthorizationUrl(
            redirectUri: 'https://myapp.com/callback',
            scopes: ['vendor.product.read', 'customer.order.read'],
            state: 'xyz123'
        );

        $this->assertStringContainsString('https://basalam.com/accounts/sso?', $url);
        $this->assertStringContainsString('client_id=test-client-id', $url);
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fmyapp.com%2Fcallback', $url);
        $this->assertStringContainsString('scope=vendor.product.read+customer.order.read', $url);
        $this->assertStringContainsString('state=xyz123', $url);
    }

    public function test_exchange_authorization_code_for_token(): void
    {
        Http::fake([
            'https://auth.basalam.com/oauth/token' => Http::response([
                'access_token' => 'mock-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 31622400,
                'refresh_token' => 'mock-refresh-token',
            ], 200),
        ]);

        $token = Basalam::auth()->exchangeCode('auth-code-12345');

        $this->assertSame('mock-access-token', $token->accessToken);
        $this->assertSame('mock-refresh-token', $token->refreshToken);
        $this->assertSame(31622400, $token->expiresIn);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'https://auth.basalam.com/oauth/token' &&
                $data['grant_type'] === 'authorization_code' &&
                $data['code'] === 'auth-code-12345' &&
                $data['client_id'] === 'test-client-id';
        });
    }

    public function test_refresh_token(): void
    {
        Http::fake([
            'https://auth.basalam.com/oauth/token' => Http::response([
                'access_token' => 'renewed-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 31622400,
                'refresh_token' => 'renewed-refresh-token',
            ], 200),
        ]);

        $token = Basalam::auth()->refreshToken('old-refresh-token');

        $this->assertSame('renewed-access-token', $token->accessToken);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['grant_type'] === 'refresh_token' &&
                $data['refresh_token'] === 'old-refresh-token';
        });
    }

    public function test_client_credentials_grant(): void
    {
        Http::fake([
            'https://auth.basalam.com/oauth/token' => Http::response([
                'access_token' => 'machine-token',
                'token_type' => 'Bearer',
                'expires_in' => 86400,
            ], 200),
        ]);

        $token = Basalam::auth()->clientCredentials(['wallet.read', 'wallet.write']);

        $this->assertSame('machine-token', $token->accessToken);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['grant_type'] === 'client_credentials' &&
                $data['scope'] === 'wallet.read wallet.write';
        });
    }

    public function test_whoami_endpoint(): void
    {
        Http::fake([
            'https://auth.basalam.com/whoami' => Http::response([
                'id' => 123456,
                'name' => 'غرفه‌دار نمونه',
                'mobile' => '09121234567',
                'hash_id' => 'abc1234',
            ], 200),
        ]);

        $user = Basalam::auth()->whoami();

        $this->assertSame(123456, $user->getId());
        $this->assertSame('غرفه‌دار نمونه', $user->getName());
        $this->assertSame('09121234567', $user->getMobile());
    }
}
