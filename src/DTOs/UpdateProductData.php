<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\ProductContract;
use Illuminate\Contracts\Support\Arrayable;

class UpdateProductData implements Arrayable
{
    /**
     * @param array<int, int|string>|null $photos
     * @param array<string, mixed> $extra
     */
    public function __construct(
        public ?string $name = null,
        public int|string|null $photo = null,
        public ?array $photos = null,
        public ?string $brief = null,
        public ?string $description = null,
        public ?int $preparationDays = null,
        public ?int $weight = null,
        public ?int $packageWeight = null,
        public ?int $primaryPrice = null,
        public ?int $stock = null,
        public ?string $sku = null,
        public ?bool $isWholesale = null,
        public ?int $categoryId = null,
        public array $extra = [],
    ) {
    }

    public static function fromContract(ProductContract $contract): self
    {
        return new self(
            name: $contract->getTitle(),
            photo: $contract->getPhotoId(),
            photos: $contract->getPhotos(),
            brief: $contract->getBrief(),
            description: $contract->getDescription(),
            preparationDays: $contract->getPreparationDays(),
            weight: $contract->getWeight(),
            packageWeight: $contract->getPackageWeight(),
            primaryPrice: $contract->getPrimaryPrice() ?? $contract->getPrice(),
            stock: $contract->getStock(),
            sku: $contract->getSku(),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: isset($data['name']) ? (string) $data['name'] : (isset($data['title']) ? (string) $data['title'] : null),
            photo: $data['photo'] ?? null,
            photos: isset($data['photos']) && is_array($data['photos']) ? $data['photos'] : null,
            brief: isset($data['brief']) ? (string) $data['brief'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            preparationDays: isset($data['preparation_days']) ? (int) $data['preparation_days'] : null,
            weight: isset($data['weight']) ? (int) $data['weight'] : null,
            packageWeight: isset($data['package_weight']) ? (int) $data['package_weight'] : null,
            primaryPrice: isset($data['primary_price']) ? (int) $data['primary_price'] : (isset($data['price']) ? (int) $data['price'] : null),
            stock: isset($data['stock']) ? (int) $data['stock'] : null,
            sku: isset($data['sku']) ? (string) $data['sku'] : null,
            isWholesale: isset($data['is_wholesale']) ? (bool) $data['is_wholesale'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            extra: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }
        if ($this->photo !== null) {
            $payload['photo'] = $this->photo;
        }
        if ($this->photos !== null) {
            $payload['photos'] = $this->photos;
        }
        if ($this->brief !== null) {
            $payload['brief'] = $this->brief;
        }
        if ($this->description !== null) {
            $payload['description'] = $this->description;
        }
        if ($this->preparationDays !== null) {
            $payload['preparation_days'] = $this->preparationDays;
        }
        if ($this->weight !== null) {
            $payload['weight'] = $this->weight;
        }
        if ($this->packageWeight !== null) {
            $payload['package_weight'] = $this->packageWeight;
        }
        if ($this->primaryPrice !== null) {
            $payload['primary_price'] = $this->primaryPrice;
        }
        if ($this->stock !== null) {
            $payload['stock'] = $this->stock;
        }
        if ($this->sku !== null) {
            $payload['sku'] = $this->sku;
        }
        if ($this->isWholesale !== null) {
            $payload['is_wholesale'] = $this->isWholesale;
        }
        if ($this->categoryId !== null) {
            $payload['category_id'] = $this->categoryId;
        }

        return $payload;
    }
}
