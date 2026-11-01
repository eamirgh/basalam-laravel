<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Eamirgh\Basalam\Contracts\ProductContract;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

class CreateProductData implements Arrayable
{
    /**
     * @param array<int, int|string> $photos
     * @param array<string, mixed> $extra
     */
    public function __construct(
        public string $name,
        public int|string $photo,
        public array $photos = [],
        public ?string $brief = null,
        public ?string $description = null,
        public int $preparationDays = 3,
        public int $weight = 100,
        public ?int $packageWeight = null,
        public int $primaryPrice = 0,
        public int $stock = 1,
        public ?string $sku = null,
        public bool $isWholesale = false,
        public ?int $categoryId = null,
        public array $extra = [],
    ) {
        if (empty($this->photos)) {
            $this->photos = [$this->photo];
        }

        if ($this->packageWeight === null) {
            $this->packageWeight = $this->weight + 50;
        }
    }

    /**
     * Build CreateProductData from any object implementing ProductContract
     */
    public static function fromContract(ProductContract $contract): self
    {
        $photoId = $contract->getPhotoId();
        if ($photoId === null) {
            $photos = $contract->getPhotos();
            $photoId = ! empty($photos[0]) ? (is_array($photos[0]) ? ($photos[0]['id'] ?? 0) : $photos[0]) : 0;
        }

        $photoList = [];
        foreach ($contract->getPhotos() as $p) {
            $photoList[] = is_array($p) ? ($p['id'] ?? $p) : $p;
        }

        if (empty($photoList) && $photoId) {
            $photoList = [$photoId];
        }

        return new self(
            name: $contract->getTitle(),
            photo: $photoId,
            photos: $photoList,
            brief: $contract->getBrief(),
            description: $contract->getDescription(),
            preparationDays: $contract->getPreparationDays() ?? 3,
            weight: $contract->getWeight() ?? 100,
            packageWeight: $contract->getPackageWeight(),
            primaryPrice: $contract->getPrice() ?: ($contract->getPrimaryPrice() ?? 0),
            stock: $contract->getStock(),
            sku: $contract->getSku(),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['name']) && empty($data['title'])) {
            throw new InvalidArgumentException('Product name/title is required.');
        }

        $name = (string) ($data['name'] ?? $data['title']);
        $photo = $data['photo'] ?? (! empty($data['photos'][0]) ? $data['photos'][0] : 0);
        $photos = ! empty($data['photos']) && is_array($data['photos']) ? $data['photos'] : [$photo];

        return new self(
            name: $name,
            photo: $photo,
            photos: $photos,
            brief: isset($data['brief']) ? (string) $data['brief'] : (isset($data['summary']) ? (string) $data['summary'] : null),
            description: isset($data['description']) ? (string) $data['description'] : null,
            preparationDays: (int) ($data['preparation_days'] ?? 3),
            weight: (int) ($data['weight'] ?? 100),
            packageWeight: isset($data['package_weight']) ? (int) $data['package_weight'] : null,
            primaryPrice: (int) ($data['primary_price'] ?? $data['price'] ?? 0),
            stock: (int) ($data['stock'] ?? 1),
            sku: isset($data['sku']) ? (string) $data['sku'] : null,
            isWholesale: (bool) ($data['is_wholesale'] ?? false),
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            extra: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'photo' => $this->photo,
            'photos' => $this->photos,
            'brief' => $this->brief ?? mb_substr(strip_tags((string) $this->description), 0, 150),
            'description' => $this->description ?? $this->brief ?? '',
            'preparation_days' => $this->preparationDays,
            'weight' => $this->weight,
            'package_weight' => $this->packageWeight,
            'primary_price' => $this->primaryPrice,
            'stock' => $this->stock,
            'is_wholesale' => $this->isWholesale,
        ];

        if ($this->sku !== null) {
            $payload['sku'] = $this->sku;
        }

        if ($this->categoryId !== null) {
            $payload['category_id'] = $this->categoryId;
        }

        return $payload;
    }
}
