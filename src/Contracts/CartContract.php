<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface CartContract
{
    public function getId(): int|string|null;

    /**
     * @return array<int, CartItemContract>
     */
    public function getItems(): array;

    public function getTotalAmount(): int;

    public function getDiscountAmount(): int;

    public function getPayableAmount(): int;

    public function getCouponCode(): ?string;

    public function getItemsCount(): int;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
