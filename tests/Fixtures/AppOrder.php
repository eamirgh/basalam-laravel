<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Fixtures;

use Eamirgh\Basalam\Contracts\OrderContract;
use Eamirgh\Basalam\Contracts\OrderItemContract;
use Eamirgh\Basalam\Contracts\OrderParcelContract;
use Eamirgh\Basalam\Contracts\UserContract;

class AppOrder implements OrderContract
{
    /**
     * @param array<int, OrderItemContract> $items
     * @param array<int, OrderParcelContract> $parcels
     */
    public function __construct(
        protected int|string $id = 998877,
        protected ?string $hashId = 'order-hash-123',
        protected int $amount = 850000,
        protected int $payableAmount = 800000,
        protected ?string $status = 'paid',
        protected ?UserContract $customer = null,
        protected array $items = [],
        protected array $parcels = [],
        protected ?string $paidAt = '2026-05-15 10:00:00',
        protected ?string $createdAt = '2026-05-15 09:30:00',
    ) {
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

    public function getItems(): array
    {
        return $this->items;
    }

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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'hash_id' => $this->hashId,
            'amount' => $this->amount,
            'payable_amount' => $this->payableAmount,
            'status' => $this->status,
            'customer' => $this->customer?->toArray(),
            'paid_at' => $this->paidAt,
            'created_at' => $this->createdAt,
        ];
    }
}
