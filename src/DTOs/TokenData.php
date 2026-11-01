<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class TokenData implements Arrayable, Jsonable, JsonSerializable
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType = 'Bearer',
        public readonly ?int $expiresIn = null,
        public readonly ?string $refreshToken = null,
        public readonly ?int $createdAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: (string) ($data['access_token'] ?? $data['accessToken'] ?? $data['token'] ?? ''),
            tokenType: (string) ($data['token_type'] ?? $data['tokenType'] ?? 'Bearer'),
            expiresIn: isset($data['expires_in']) ? (int) $data['expires_in'] : (isset($data['expiresIn']) ? (int) $data['expiresIn'] : null),
            refreshToken: isset($data['refresh_token']) ? (string) $data['refresh_token'] : (isset($data['refreshToken']) ? (string) $data['refreshToken'] : null),
            createdAt: isset($data['created_at']) ? (int) $data['created_at'] : time(),
        );
    }

    public function isExpired(): bool
    {
        if ($this->expiresIn === null || $this->createdAt === null) {
            return false;
        }

        // 60-second buffer
        return (time() >= ($this->createdAt + $this->expiresIn - 60));
    }

    public function getAuthorizationHeader(): string
    {
        return trim("{$this->tokenType} {$this->accessToken}");
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'refresh_token' => $this->refreshToken,
            'created_at' => $this->createdAt,
        ];
    }

    /**
     * @param int $options
     */
    public function toJson($options = 0): string
    {
        return (string) json_encode($this->toArray(), $options);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
