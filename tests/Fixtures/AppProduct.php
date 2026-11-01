<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests\Fixtures;

use Eamirgh\Basalam\Contracts\ProductContract;

class AppProduct implements ProductContract
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        protected array $attributes,
    ) {
    }

    public function getId(): int|string
    {
        return $this->attributes['id'] ?? 101;
    }

    public function getTitle(): string
    {
        return $this->attributes['title'] ?? 'Custom App Product';
    }

    public function getPrice(): int
    {
        return (int) ($this->attributes['price'] ?? 250000);
    }

    public function getPrimaryPrice(): ?int
    {
        return (int) ($this->attributes['primary_price'] ?? $this->getPrice());
    }

    public function getStock(): int
    {
        return (int) ($this->attributes['stock'] ?? 15);
    }

    public function getSku(): ?string
    {
        return $this->attributes['sku'] ?? 'APP-SKU-999';
    }

    public function getWeight(): ?int
    {
        return (int) ($this->attributes['weight'] ?? 350);
    }

    public function getPackageWeight(): ?int
    {
        return (int) ($this->attributes['package_weight'] ?? 400);
    }

    public function getPreparationDays(): ?int
    {
        return (int) ($this->attributes['preparation_days'] ?? 2);
    }

    public function getBrief(): ?string
    {
        return $this->attributes['brief'] ?? 'Short summary from app';
    }

    public function getDescription(): ?string
    {
        return $this->attributes['description'] ?? 'Full description from app';
    }

    public function getVendorId(): ?int
    {
        return isset($this->attributes['vendor_id']) ? (int) $this->attributes['vendor_id'] : 78910;
    }

    public function getStatus(): ?string
    {
        return $this->attributes['status'] ?? 'available';
    }

    public function getPhotoId(): int|string|null
    {
        return $this->attributes['photo_id'] ?? 98765432;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->attributes['photo_url'] ?? 'https://example.com/app-photo.jpg';
    }

    public function getPhotos(): array
    {
        return $this->attributes['photos'] ?? [$this->getPhotoId()];
    }

    public function getVariations(): array
    {
        return $this->attributes['variations'] ?? [];
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
