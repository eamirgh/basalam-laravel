<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface UserContract
{
    public function getId(): int|string;

    public function getName(): ?string;

    public function getMobile(): ?string;

    public function getHashId(): ?string;

    public function getVendor(): ?VendorContract;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
