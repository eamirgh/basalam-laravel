<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\OrderItemContract;
use Eamirgh\Basalam\Contracts\OrderParcelContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class OrderParcelData implements OrderParcelContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<int, OrderItemContract> $items
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly int|string|null $orderId = null,
        public readonly int|string|null $vendorId = null,
        public readonly ?string $status = null,
        public readonly ?string $trackingCode = null,
        public readonly ?string $shippingMethod = null,
        public readonly array $items = [],
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

        $items = [];
        if (! empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if ($item instanceof OrderItemContract) {
                    $items[] = $item;
                } elseif (is_array($item)) {
                    $items[] = OrderItemData::fromArray($item);
                }
            }
        }

        return new self(
            id: $data['id'] ?? 0,
            orderId: $data['order_id'] ?? $data['orderId'] ?? null,
            vendorId: $data['vendor_id'] ?? $data['vendorId'] ?? null,
            status: $status,
            trackingCode: isset($data['tracking_code']) ? (string) $data['tracking_code'] : (isset($data['barcode']) ? (string) $data['barcode'] : null),
            shippingMethod: isset($data['shipping_method']) ? (string) $data['shipping_method'] : (isset($data['carrier']) ? (string) $data['carrier'] : null),
            items: $items,
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

    public function getVendorId(): int|string|null
    {
        return $this->vendorId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    public function getShippingMethod(): ?string
    {
        return $this->shippingMethod;
    }

    /**
     * @return array<int, OrderItemContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'vendor_id' => $this->vendorId,
            'status' => $this->status,
            'tracking_code' => $this->trackingCode,
            'shipping_method' => $this->shippingMethod,
            'items' => array_map(fn (OrderItemContract $item) => $item->toArray(), $this->items),
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
