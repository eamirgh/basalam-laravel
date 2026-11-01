<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\CartItemContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class CartItemData implements CartItemContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $productId,
        public readonly int|string|null $variationId = null,
        public readonly string $title = '',
        public readonly int $quantity = 1,
        public readonly int $unitPrice = 0,
        public readonly int $totalPrice = 0,
        public readonly int $discount = 0,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $unitPrice = (int) ($data['unit_price'] ?? $data['price'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? $data['count'] ?? 1);
        $discount = (int) ($data['discount'] ?? 0);
        $totalPrice = (int) ($data['total_price'] ?? $data['total'] ?? (($unitPrice * $quantity) - $discount));

        return new self(
            id: $data['id'] ?? $data['product_id'] ?? 0,
            productId: $data['product_id'] ?? $data['productId'] ?? 0,
            variationId: $data['variation_id'] ?? $data['variationId'] ?? null,
            title: (string) ($data['title'] ?? $data['name'] ?? ''),
            quantity: $quantity,
            unitPrice: $unitPrice,
            totalPrice: $totalPrice,
            discount: $discount,
            raw: $data,
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getProductId(): int|string
    {
        return $this->productId;
    }

    public function getVariationId(): int|string|null
    {
        return $this->variationId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'variation_id' => $this->variationId,
            'title' => $this->title,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total_price' => $this->totalPrice,
            'discount' => $this->discount,
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
