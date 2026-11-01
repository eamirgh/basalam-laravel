<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\UserContract;
use Eamirgh\Basalam\Contracts\VendorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class UserData implements UserContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly ?string $name = null,
        public readonly ?string $mobile = null,
        public readonly ?string $hashId = null,
        public readonly ?VendorContract $vendor = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $vendor = null;
        if (! empty($data['vendor'])) {
            if ($data['vendor'] instanceof VendorContract) {
                $vendor = $data['vendor'];
            } elseif (is_array($data['vendor'])) {
                $vendor = VendorData::fromArray($data['vendor']);
            }
        }

        return new self(
            id: $data['id'] ?? 0,
            name: isset($data['name']) ? (string) $data['name'] : (isset($data['username']) ? (string) $data['username'] : null),
            mobile: isset($data['mobile']) ? (string) $data['mobile'] : null,
            hashId: isset($data['hash_id']) ? (string) $data['hash_id'] : (isset($data['hashId']) ? (string) $data['hashId'] : null),
            vendor: $vendor,
            raw: $data,
        );
    }

    public static function fromContract(UserContract $contract): self
    {
        if ($contract instanceof self) {
            return $contract;
        }

        return new self(
            id: $contract->getId(),
            name: $contract->getName(),
            mobile: $contract->getMobile(),
            hashId: $contract->getHashId(),
            vendor: $contract->getVendor(),
            raw: $contract->toArray(),
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function getHashId(): ?string
    {
        return $this->hashId;
    }

    public function getVendor(): ?VendorContract
    {
        return $this->vendor;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'hash_id' => $this->hashId,
            'vendor' => $this->vendor?->toArray(),
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
