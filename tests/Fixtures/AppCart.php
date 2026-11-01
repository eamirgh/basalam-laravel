<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Fixtures;

use Eamirgh\Basalam\Contracts\CartContract;
use Eamirgh\Basalam\Contracts\CartItemContract;

class AppCart implements CartContract
{
    /**
     * @param array<int, CartItemContract> $items
     */
    public function __construct(
        protected int|string|null $id = 'cart-1',
        protected array $items = [],
        protected int $totalAmount = 500000,
        protected int $discountAmount = 50000,
        protected int $payableAmount = 450000,
        protected ?string $couponCode = 'SPRING2026',
    ) {
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function getDiscountAmount(): int
    {
        return $this->discountAmount;
    }

    public function getPayableAmount(): int
    {
        return $this->payableAmount;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function getItemsCount(): int
    {
        return count($this->items);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'items' => array_map(fn (CartItemContract $item) => $item->toArray(), $this->items),
            'total_amount' => $this->totalAmount,
            'discount_amount' => $this->discountAmount,
            'payable_amount' => $this->payableAmount,
            'coupon_code' => $this->couponCode,
        ];
    }
}
