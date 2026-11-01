<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\ProductContract;
use Eamirgh\Basalam\Contracts\ProductVariationContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ProductData implements ProductContract, Arrayable, Jsonable, JsonSerializable
{
    /**
     * @param array<int, mixed> $photos
     * @param array<int, ProductVariationContract> $variations
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly int|string $id,
        public readonly string $title,
        public readonly int $price = 0,
        public readonly ?int $primaryPrice = null,
        public readonly int $stock = 0,
        public readonly ?string $sku = null,
        public readonly ?int $weight = null,
        public readonly ?int $packageWeight = null,
        public readonly ?int $preparationDays = null,
        public readonly ?string $brief = null,
        public readonly ?string $description = null,
        public readonly ?int $vendorId = null,
        public readonly ?string $status = null,
        public readonly int|string|null $photoId = null,
        public readonly ?string $photoUrl = null,
        public readonly array $photos = [],
        public readonly array $variations = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $title = (string) ($data['title'] ?? $data['name'] ?? '');
        $price = (int) ($data['price'] ?? $data['primary_price'] ?? 0);
        $primaryPrice = isset($data['primary_price']) ? (int) $data['primary_price'] : (isset($data['price']) ? (int) $data['price'] : null);
        $stock = (int) ($data['stock'] ?? 0);
        $sku = isset($data['sku']) ? (string) $data['sku'] : null;
        $weight = isset($data['weight']) ? (int) $data['weight'] : null;
        $packageWeight = isset($data['package_weight']) ? (int) $data['package_weight'] : null;
        $preparationDays = isset($data['preparation_days']) ? (int) $data['preparation_days'] : null;
        $brief = isset($data['brief']) ? (string) $data['brief'] : (isset($data['summary']) ? (string) $data['summary'] : null);
        $description = isset($data['description']) ? (string) $data['description'] : null;

        $vendorId = null;
        if (isset($data['vendor_id'])) {
            $vendorId = (int) $data['vendor_id'];
        } elseif (isset($data['vendor']['id'])) {
            $vendorId = (int) $data['vendor']['id'];
        }

        $status = null;
        if (is_string($data['status'] ?? null)) {
            $status = $data['status'];
        } elseif (is_array($data['status'] ?? null) && isset($data['status']['name'])) {
            $status = (string) $data['status']['name'];
        }

        $photoId = null;
        $photoUrl = null;
        if (is_numeric($data['photo'] ?? null)) {
            $photoId = $data['photo'];
        } elseif (is_array($data['photo'] ?? null)) {
            $photoId = $data['photo']['id'] ?? null;
            $photoUrl = $data['photo']['original'] ?? $data['photo']['url'] ?? $data['photo']['lg'] ?? null;
        } elseif (is_string($data['photo'] ?? null)) {
            $photoUrl = $data['photo'];
        }

        $photos = [];
        if (! empty($data['photos']) && is_array($data['photos'])) {
            $photos = $data['photos'];
        } elseif ($photoId !== null || $photoUrl !== null) {
            $photos = [$data['photo']];
        }

        $variations = [];
        if (! empty($data['variations']) && is_array($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                if ($variation instanceof ProductVariationContract) {
                    $variations[] = $variation;
                } elseif (is_array($variation)) {
                    $variations[] = ProductVariationData::fromArray($variation);
                }
            }
        }

        return new self(
            id: $id,
            title: $title,
            price: $price,
            primaryPrice: $primaryPrice,
            stock: $stock,
            sku: $sku,
            weight: $weight,
            packageWeight: $packageWeight,
            preparationDays: $preparationDays,
            brief: $brief,
            description: $description,
            vendorId: $vendorId,
            status: $status,
            photoId: $photoId,
            photoUrl: $photoUrl,
            photos: $photos,
            variations: $variations,
            raw: $data,
        );
    }

    /**
     * Build ProductData directly from any object implementing ProductContract
     */
    public static function fromContract(ProductContract $contract): self
    {
        if ($contract instanceof self) {
            return $contract;
        }

        return new self(
            id: $contract->getId(),
            title: $contract->getTitle(),
            price: $contract->getPrice(),
            primaryPrice: $contract->getPrimaryPrice(),
            stock: $contract->getStock(),
            sku: $contract->getSku(),
            weight: $contract->getWeight(),
            packageWeight: $contract->getPackageWeight(),
            preparationDays: $contract->getPreparationDays(),
            brief: $contract->getBrief(),
            description: $contract->getDescription(),
            vendorId: $contract->getVendorId(),
            status: $contract->getStatus(),
            photoId: $contract->getPhotoId(),
            photoUrl: $contract->getPhotoUrl(),
            photos: $contract->getPhotos(),
            variations: $contract->getVariations(),
            raw: $contract->toArray(),
        );
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPrimaryPrice(): ?int
    {
        return $this->primaryPrice;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function getPackageWeight(): ?int
    {
        return $this->packageWeight;
    }

    public function getPreparationDays(): ?int
    {
        return $this->preparationDays;
    }

    public function getBrief(): ?string
    {
        return $this->brief;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getVendorId(): ?int
    {
        return $this->vendorId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPhotoId(): int|string|null
    {
        return $this->photoId;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    /**
     * @return array<int, mixed>
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }

    /**
     * @return array<int, ProductVariationContract>
     */
    public function getVariations(): array
    {
        return $this->variations;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'name' => $this->title,
            'price' => $this->price,
            'primary_price' => $this->primaryPrice,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'weight' => $this->weight,
            'package_weight' => $this->packageWeight,
            'preparation_days' => $this->preparationDays,
            'brief' => $this->brief,
            'description' => $this->description,
            'vendor_id' => $this->vendorId,
            'status' => $this->status,
            'photo' => $this->photoId ?? $this->photoUrl,
            'photos' => $this->photos,
            'variations' => array_map(fn (ProductVariationContract $v) => $v->toArray(), $this->variations),
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
