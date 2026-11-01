<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

interface ProductContract
{
    public function getId(): int|string;

    public function getTitle(): string;

    public function getPrice(): int;

    public function getPrimaryPrice(): ?int;

    public function getStock(): int;

    public function getSku(): ?string;

    public function getWeight(): ?int;

    public function getPackageWeight(): ?int;

    public function getPreparationDays(): ?int;

    public function getBrief(): ?string;

    public function getDescription(): ?string;

    public function getVendorId(): ?int;

    public function getStatus(): ?string;

    public function getPhotoId(): int|string|null;

    public function getPhotoUrl(): ?string;

    /**
     * @return array<int, mixed>
     */
    public function getPhotos(): array;

    /**
     * @return array<int, ProductVariationContract>
     */
    public function getVariations(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
