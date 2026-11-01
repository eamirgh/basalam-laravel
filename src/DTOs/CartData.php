<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\CartContract;
use Eamirgh\Basalam\Contracts\CartItemContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class CartData implements CartContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<int, CartItemContract> $items
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string|null $id = null,
        public readonly array $items = [],
        public readonly int $totalAmount = 0,
        public readonly int $discountAmount = 0,
        public readonly int $payableAmount = 0,
        public readonly ?string $couponCode = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $items = [];
        $rawItems = $data['items'] ?? $data['products'] ?? $data['basket_items'] ?? [];

        if (is_array($rawItems)) {
            foreach ($rawItems as $item) {
                if ($item instanceof CartItemContract) {
                    $items[] = $item;
                } elseif (is_array($item)) {
                    $items[] = CartItemData::fromArray($item);
                }
            }
        }

        $totalAmount = (int) ($data['total_amount'] ?? $data['total'] ?? $data['amount'] ?? 0);
        $discountAmount = (int) ($data['discount_amount'] ?? $data['discount'] ?? 0);
        $payableAmount = (int) ($data['payable_amount'] ?? $data['payable'] ?? ($totalAmount - $discountAmount));
        $couponCode = isset($data['coupon_code']) ? (string) $data['coupon_code'] : (isset($data['coupon']) ? (string) $data['coupon'] : null);

        // Fallback calculations if amounts are 0 and items exist
        if ($totalAmount === 0 && ! empty($items)) {
            foreach ($items as $item) {
                $totalAmount += $item->getTotalPrice();
                $discountAmount += $item->getDiscount();
            }
            $payableAmount = $totalAmount - $discountAmount;
        }

        return new self(
            id: $id,
            items: $items,
            totalAmount: $totalAmount,
            discountAmount: $discountAmount,
            payableAmount: $payableAmount,
            couponCode: $couponCode,
            raw: $data,
        );
    }

    public static function fromContract(CartContract $contract): self
    {
        if ($contract instanceof self) {
            return $contract;
        }

        return new self(
            id: $contract->getId(),
            items: $contract->getItems(),
            totalAmount: $contract->getTotalAmount(),
            discountAmount: $contract->getDiscountAmount(),
            payableAmount: $contract->getPayableAmount(),
            couponCode: $contract->getCouponCode(),
            raw: $contract->toArray(),
        );
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    /**
     * @return array<int, CartItemContract>
     */
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
        $count = 0;
        foreach ($this->items as $item) {
            $count += $item->getQuantity();
        }

        return $count;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'items' => array_map(fn (CartItemContract $item) => $item->toArray(), $this->items),
            'total_amount' => $this->totalAmount,
            'discount_amount' => $this->discountAmount,
            'payable_amount' => $this->payableAmount,
            'coupon_code' => $this->couponCode,
            'items_count' => $this->getItemsCount(),
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
