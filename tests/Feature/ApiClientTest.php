<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Feature;

use Eamirgh\Basalam\DTOs\TokenData;
use Eamirgh\Basalam\Exceptions\AuthenticationException;
use Eamirgh\Basalam\Exceptions\AuthorizationException;
use Eamirgh\Basalam\Exceptions\NotFoundException;
use Eamirgh\Basalam\Exceptions\ServerException;
use Eamirgh\Basalam\Exceptions\ValidationException;
use Eamirgh\Basalam\Http\ApiClient;
use Eamirgh\Basalam\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ApiClientTest extends TestCase
{
    public function test_api_client_sends_bearer_token_and_headers(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/test' => Http::response(['success' => true], 200),
        ]);

        $client = new ApiClient(
            baseUrl: 'https://openapi.basalam.com',
            token: 'test-token-123'
        );

        $res = $client->get('/v1/test');

        $this->assertTrue($res['success']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer test-token-123') &&
                $request->hasHeader('Accept', 'application/json') &&
                $request->hasHeader('X-Client-Info');
        });
    }

    public function test_api_client_resolves_closure_token_and_token_data(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/dynamic' => Http::response(['ok' => true], 200),
        ]);

        $tokenDto = new TokenData(accessToken: 'dto-token-abc');

        $client = new ApiClient(
            baseUrl: 'https://openapi.basalam.com',
            token: fn () => $tokenDto
        );

        $client->get('/v1/dynamic');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer dto-token-abc');
        });
    }

    public function test_api_client_maps_401_to_authentication_exception(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(401);

        Http::fake([
            'https://openapi.basalam.com/v1/unauth' => Http::response(['message' => 'Unauthenticated.'], 401),
        ]);

        $client = new ApiClient('https://openapi.basalam.com');
        $client->get('/v1/unauth');
    }

    public function test_api_client_maps_403_to_authorization_exception(): void
    {
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionCode(403);

        Http::fake([
            'https://openapi.basalam.com/v1/forbidden' => Http::response(['message' => 'Forbidden scope.'], 403),
        ]);

        $client = new ApiClient('https://openapi.basalam.com');
        $client->get('/v1/forbidden');
    }

    public function test_api_client_maps_404_to_not_found_exception(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionCode(404);

        Http::fake([
            'https://openapi.basalam.com/v1/not-found' => Http::response(['message' => 'Resource not found.'], 404),
        ]);

        $client = new ApiClient('https://openapi.basalam.com');
        $client->get('/v1/not-found');
    }

    public function test_api_client_maps_422_to_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);

        Http::fake([
            'https://openapi.basalam.com/v1/invalid' => Http::response([
                'message' => 'Validation error',
                'errors' => ['name' => ['The name field is required.']],
            ], 422),
        ]);

        $client = new ApiClient('https://openapi.basalam.com');
        $client->post('/v1/invalid', []);
    }

    public function test_api_client_maps_500_to_server_exception(): void
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionCode(500);

        Http::fake([
            'https://openapi.basalam.com/v1/error' => Http::response(['message' => 'Internal server error'], 500),
        ]);

        $client = new ApiClient('https://openapi.basalam.com');
        $client->get('/v1/error');
    }

    public function test_multipart_file_attachment(): void
    {
        Http::fake([
            'https://openapi.basalam.com/v1/files' => Http::response([
                'id' => 999888,
                'file_name' => 'photo.jpg',
            ], 200),
        ]);

        $client = new ApiClient('https://openapi.basalam.com');
        $res = $client->attach(
            path: '/v1/files',
            name: 'file',
            contents: 'fake-image-bytes',
            filename: 'photo.jpg',
            data: ['file_type' => 'product.photo']
        );

        $this->assertSame(999888, $res['id']);

        Http::assertSent(function ($request) {
            return $request->isMultipart();
        });
    }
}
