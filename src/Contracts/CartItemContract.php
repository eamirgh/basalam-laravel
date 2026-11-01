<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface CartItemContract
{
    public function getId(): int|string;

    public function getProductId(): int|string;

    public function getVariationId(): int|string|null;

    public function getTitle(): string;

    public function getQuantity(): int;

    public function getUnitPrice(): int;

    public function getTotalPrice(): int;

    public function getDiscount(): int;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
