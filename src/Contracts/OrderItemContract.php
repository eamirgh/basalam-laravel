<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface OrderItemContract
{
    public function getId(): int|string;

    public function getOrderId(): int|string|null;

    public function getProductId(): int|string;

    public function getVariationId(): int|string|null;

    public function getTitle(): string;

    public function getQuantity(): int;

    public function getPrice(): int;

    public function getDiscount(): int;

    public function getTotal(): int;

    public function getVendorId(): ?int;

    public function getStatus(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
