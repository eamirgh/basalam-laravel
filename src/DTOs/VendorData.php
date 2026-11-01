<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\VendorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class VendorData implements VendorContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly string $title,
        public readonly ?string $identifier = null,
        public readonly ?int $ownerId = null,
        public readonly ?string $status = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $status = null;
        if (is_string($data['status'] ?? null)) {
            $status = $data['status'];
        } elseif (is_array($data['status'] ?? null) && isset($data['status']['name'])) {
            $status = (string) $data['status']['name'];
        }

        return new self(
            id: $data['id'] ?? 0,
            title: (string) ($data['title'] ?? $data['name'] ?? ''),
            identifier: isset($data['identifier']) ? (string) $data['identifier'] : null,
            ownerId: isset($data['owner_id']) ? (int) $data['owner_id'] : (isset($data['user_id']) ? (int) $data['user_id'] : null),
            status: $status,
            raw: $data,
        );
    }

    public static function fromContract(VendorContract $contract): self
    {
        if ($contract instanceof self) {
            return $contract;
        }

        return new self(
            id: $contract->getId(),
            title: $contract->getTitle(),
            identifier: $contract->getIdentifier(),
            ownerId: $contract->getOwnerId(),
            status: $contract->getStatus(),
            raw: $contract->toArray(),
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'identifier' => $this->identifier,
            'owner_id' => $this->ownerId,
            'status' => $this->status,
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
