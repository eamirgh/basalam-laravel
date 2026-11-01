<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\Http\ApiClient;

class WebhookService
{
    public function __construct(
        protected ApiClient $client,
        protected ?string $defaultSecret = null,
    ) {
    }

    /**
     * Verify HMAC SHA256 webhook signature
     */
    public function verifySignature(
        string $payload,
        string $signature,
        ?string $secret = null,
    ): bool {
        $secretKey = $secret ?? $this->defaultSecret;
        if (empty($secretKey)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secretKey);

        return hash_equals($expected, $signature);
    }

    /**
     * List registered webhooks
     *
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->client->get('/v1/webhooks');
    }

    /**
     * Register a new webhook endpoint
     *
     * @param array<int, string> $events
     * @return array<string, mixed>
     */
    public function create(string $url, array $events, ?string $secret = null): array
    {
        return $this->client->post('/v1/webhooks', [
            'url' => $url,
            'events' => $events,
            'secret' => $secret ?? $this->defaultSecret,
        ]);
    }

    /**
     * Delete webhook
     *
     * @return array<string, mixed>
     */
    public function delete(int|string $webhookId): array
    {
        return $this->client->delete("/v1/webhooks/{$webhookId}");
    }
}
