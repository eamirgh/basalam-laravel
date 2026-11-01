<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface VendorContract
{
    public function getId(): int|string;

    public function getIdentifier(): ?string;

    public function getTitle(): string;

    public function getOwnerId(): ?int;

    public function getStatus(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
