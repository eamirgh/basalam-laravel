<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\ProductVariationContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ProductVariationData implements ProductVariationContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<string, mixed> $properties
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly int|string|null $productId = null,
        public readonly ?string $sku = null,
        public readonly int $price = 0,
        public readonly int $stock = 0,
        public readonly array $properties = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            productId: $data['product_id'] ?? $data['productId'] ?? null,
            sku: $data['sku'] ?? null,
            price: (int) ($data['price'] ?? 0),
            stock: (int) ($data['stock'] ?? 0),
            properties: (array) ($data['properties'] ?? $data['attributes'] ?? []),
            raw: $data,
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getProductId(): int|string|null
    {
        return $this->productId;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return array<string, mixed>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'properties' => $this->properties,
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
