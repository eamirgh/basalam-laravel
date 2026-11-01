<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\OrderItemContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class OrderItemData implements OrderItemContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $productId,
        public readonly int|string|null $orderId = null,
        public readonly int|string|null $variationId = null,
        public readonly string $title = '',
        public readonly int $quantity = 1,
        public readonly int $price = 0,
        public readonly int $discount = 0,
        public readonly int $total = 0,
        public readonly ?int $vendorId = null,
        public readonly ?string $status = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $price = (int) ($data['price'] ?? $data['unit_price'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? $data['count'] ?? 1);
        $discount = (int) ($data['discount'] ?? 0);
        $total = (int) ($data['total'] ?? $data['total_price'] ?? (($price * $quantity) - $discount));

        $status = null;
        if (is_string($data['status'] ?? null)) {
            $status = $data['status'];
        } elseif (is_array($data['status'] ?? null) && isset($data['status']['name'])) {
            $status = (string) $data['status']['name'];
        }

        $vendorId = null;
        if (isset($data['vendor_id'])) {
            $vendorId = (int) $data['vendor_id'];
        } elseif (isset($data['vendor']['id'])) {
            $vendorId = (int) $data['vendor']['id'];
        }

        return new self(
            id: $data['id'] ?? 0,
            productId: $data['product_id'] ?? $data['productId'] ?? 0,
            orderId: $data['order_id'] ?? $data['orderId'] ?? null,
            variationId: $data['variation_id'] ?? $data['variationId'] ?? null,
            title: (string) ($data['title'] ?? $data['name'] ?? $data['product_name'] ?? ''),
            quantity: $quantity,
            price: $price,
            discount: $discount,
            total: $total,
            vendorId: $vendorId,
            status: $status,
            raw: $data,
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getOrderId(): int|string|null
    {
        return $this->orderId;
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

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getVendorId(): ?int
    {
        return $this->vendorId;
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
            'order_id' => $this->orderId,
            'product_id' => $this->productId,
            'variation_id' => $this->variationId,
            'title' => $this->title,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'total' => $this->total,
            'vendor_id' => $this->vendorId,
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
