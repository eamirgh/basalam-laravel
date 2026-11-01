<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Http;

use Closure;
use Eamirgh\Basalam\DTOs\TokenData;
use Eamirgh\Basalam\Exceptions\AuthenticationException;
use Eamirgh\Basalam\Exceptions\AuthorizationException;
use Eamirgh\Basalam\Exceptions\BasalamException;
use Eamirgh\Basalam\Exceptions\NotFoundException;
use Eamirgh\Basalam\Exceptions\ServerException;
use Eamirgh\Basalam\Exceptions\ValidationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    /**
     * @var string|TokenData|Closure():(string|TokenData|null)|null
     */
    protected string|TokenData|Closure|null $token = null;

    public function __construct(
        protected string $baseUrl,
        string|TokenData|Closure|null $token = null,
        protected int $timeout = 30,
        protected int $retryTimes = 0,
        protected int $retrySleep = 100,
        /** @var array<string, string> */
        protected array $defaultHeaders = [],
    ) {
        $this->token = $token;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setToken(string|TokenData|Closure|null $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function setRetry(int $times, int $sleep = 100): self
    {
        $this->retryTimes = $times;
        $this->retrySleep = $sleep;

        return $this;
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        $url = $this->buildUrl($path);
        $response = $this->createRequest()->get($url, $query);

        return $this->handleResponse($response);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function post(string $path, array $data = []): array
    {
        $url = $this->buildUrl($path);
        $response = $this->createRequest()->post($url, $data);

        return $this->handleResponse($response);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function patch(string $path, array $data = []): array
    {
        $url = $this->buildUrl($path);
        $response = $this->createRequest()->patch($url, $data);

        return $this->handleResponse($response);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function put(string $path, array $data = []): array
    {
        $url = $this->buildUrl($path);
        $response = $this->createRequest()->put($url, $data);

        return $this->handleResponse($response);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function delete(string $path, array $data = []): array
    {
        $url = $this->buildUrl($path);
        $response = $this->createRequest()->delete($url, $data);

        return $this->handleResponse($response);
    }

    /**
     * Upload a file with multipart/form-data
     *
     * @param mixed $contents
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function attach(
        string $path,
        string $name,
        mixed $contents,
        ?string $filename = null,
        array $data = [],
    ): array {
        $url = $this->buildUrl($path);
        $request = $this->createRequest()->asMultipart();

        $request->attach($name, $contents, $filename);

        $response = $request->post($url, $data);

        return $this->handleResponse($response);
    }

    protected function buildUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    protected function resolveToken(): ?string
    {
        $token = $this->token;

        if ($token instanceof Closure) {
            $token = $token();
        }

        if ($token instanceof TokenData) {
            return $token->accessToken;
        }

        return is_string($token) && $token !== '' ? $token : null;
    }

    protected function createRequest(): PendingRequest
    {
        $request = Http::timeout($this->timeout)
            ->acceptJson()
            ->withHeaders(array_merge([
                'X-Client-Info' => json_encode(['name' => 'basalam-laravel', 'version' => '1.0.0']),
            ], $this->defaultHeaders));

        if ($resolvedToken = $this->resolveToken()) {
            $request->withToken($resolvedToken);
        }

        if ($this->retryTimes > 0) {
            $request->retry($this->retryTimes, $this->retrySleep);
        }

        return $request;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws BasalamException
     */
    protected function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return (array) $response->json();
        }

        $status = $response->status();
        $body = (array) ($response->json() ?? []);
        $message = $body['message'] ?? $body['detail'] ?? $body['error'] ?? "Basalam API error with status {$status}";

        if (is_array($message)) {
            $message = json_encode($message);
        }

        $errors = (array) ($body['errors'] ?? $body['detail'] ?? []);

        throw match ($status) {
            401 => new AuthenticationException($message, $status, $body, $errors),
            403 => new AuthorizationException($message, $status, $body, $errors),
            404 => new NotFoundException($message, $status, $body, $errors),
            422 => new ValidationException($message, $status, $body, $errors),
            500, 502, 503, 504 => new ServerException($message, $status, $body, $errors),
            default => new BasalamException($message, $status, $body, $errors),
        };
    }
}
