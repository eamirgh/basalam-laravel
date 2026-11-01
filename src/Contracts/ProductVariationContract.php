<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface ProductVariationContract
{
    public function getId(): int|string;

    public function getProductId(): int|string|null;

    public function getSku(): ?string;

    public function getPrice(): int;

    public function getStock(): int;

    /**
     * @return array<string, mixed>
     */
    public function getProperties(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
