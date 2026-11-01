<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\OrderContract;
use Eamirgh\Basalam\Contracts\OrderItemContract;
use Eamirgh\Basalam\Contracts\OrderParcelContract;
use Eamirgh\Basalam\Contracts\UserContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class OrderData implements OrderContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<int, OrderItemContract> $items
     * @param array<int, OrderParcelContract> $parcels
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly ?string $hashId = null,
        public readonly int $amount = 0,
        public readonly int $payableAmount = 0,
        public readonly ?string $status = null,
        public readonly ?UserContract $customer = null,
        public readonly array $items = [],
        public readonly array $parcels = [],
        public readonly ?string $paidAt = null,
        public readonly ?string $createdAt = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $hashId = isset($data['hash_id']) ? (string) $data['hash_id'] : (isset($data['hashId']) ? (string) $data['hashId'] : null);
        $amount = (int) ($data['amount'] ?? 0);
        $payableAmount = (int) ($data['payable_amount'] ?? $data['payable'] ?? $amount);

        $status = null;
        if (is_string($data['status'] ?? null)) {
            $status = $data['status'];
        } elseif (is_array($data['status'] ?? null) && isset($data['status']['name'])) {
            $status = (string) $data['status']['name'];
        }

        $customer = null;
        if (! empty($data['customer'])) {
            if ($data['customer'] instanceof UserContract) {
                $customer = $data['customer'];
            } elseif (is_array($data['customer'])) {
                $customer = UserData::fromArray($data['customer']);
            }
        }

        $items = [];
        $rawItems = $data['items'] ?? $data['order_items'] ?? [];
        if (is_array($rawItems)) {
            foreach ($rawItems as $item) {
                if ($item instanceof OrderItemContract) {
                    $items[] = $item;
                } elseif (is_array($item)) {
                    $items[] = OrderItemData::fromArray($item);
                }
            }
        }

        $parcels = [];
        $rawParcels = $data['parcels'] ?? $data['vendor_parcels'] ?? [];
        if (is_array($rawParcels)) {
            foreach ($rawParcels as $parcel) {
                if ($parcel instanceof OrderParcelContract) {
                    $parcels[] = $parcel;
                } elseif (is_array($parcel)) {
                    $parcels[] = OrderParcelData::fromArray($parcel);
                }
            }
        }

        return new self(
            id: $id,
            hashId: $hashId,
            amount: $amount,
            payableAmount: $payableAmount,
            status: $status,
            customer: $customer,
            items: $items,
            parcels: $parcels,
            paidAt: isset($data['paid_at']) ? (string) $data['paid_at'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            raw: $data,
        );
    }

    public static function fromContract(OrderContract $contract): self
    {
        if ($contract instanceof self) {
            return $contract;
        }

        return new self(
            id: $contract->getId(),
            hashId: $contract->getHashId(),
            amount: $contract->getAmount(),
            payableAmount: $contract->getPayableAmount(),
            status: $contract->getStatus(),
            customer: $contract->getCustomer(),
            items: $contract->getItems(),
            parcels: $contract->getParcels(),
            paidAt: $contract->getPaidAt(),
            createdAt: $contract->getCreatedAt(),
            raw: $contract->toArray(),
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getHashId(): ?string
    {
        return $this->hashId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPayableAmount(): int
    {
        return $this->payableAmount;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getCustomer(): ?UserContract
    {
        return $this->customer;
    }

    /**
     * @return array<int, OrderItemContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, OrderParcelContract>
     */
    public function getParcels(): array
    {
        return $this->parcels;
    }

    public function getPaidAt(): ?string
    {
        return $this->paidAt;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'hash_id' => $this->hashId,
            'amount' => $this->amount,
            'payable_amount' => $this->payableAmount,
            'status' => $this->status,
            'customer' => $this->customer?->toArray(),
            'items' => array_map(fn (OrderItemContract $item) => $item->toArray(), $this->items),
            'parcels' => array_map(fn (OrderParcelContract $parcel) => $parcel->toArray(), $this->parcels),
            'paid_at' => $this->paidAt,
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
