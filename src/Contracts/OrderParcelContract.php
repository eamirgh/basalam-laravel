<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface OrderParcelContract
{
    public function getId(): int|string;

    public function getOrderId(): int|string|null;

    public function getVendorId(): int|string|null;

    public function getStatus(): ?string;

    public function getTrackingCode(): ?string;

    public function getShippingMethod(): ?string;

    /**
     * @return array<int, OrderItemContract>
     */
    public function getItems(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
