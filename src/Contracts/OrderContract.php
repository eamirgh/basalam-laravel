<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface OrderContract
{
    public function getId(): int|string;

    public function getHashId(): ?string;

    public function getAmount(): int;

    public function getPayableAmount(): int;

    public function getStatus(): ?string;

    public function getCustomer(): ?UserContract;

    /**
     * @return array<int, OrderItemContract>
     */
    public function getItems(): array;

    /**
     * @return array<int, OrderParcelContract>
     */
    public function getParcels(): array;

    public function getPaidAt(): ?string;

    public function getCreatedAt(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
