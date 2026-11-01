<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\Contracts\ProductContract;
use Eamirgh\Basalam\DTOs\CreateProductData;
use Eamirgh\Basalam\DTOs\ProductData;
use Eamirgh\Basalam\DTOs\ProductVariationData;
use Eamirgh\Basalam\DTOs\UpdateProductData;
use Eamirgh\Basalam\Http\ApiClient;

class ProductService
{
    public function __construct(
        protected ApiClient $client,
    ) {
    }

    /**
     * Get product details by product ID
     */
    public function get(int|string $productId): ProductData
    {
        $response = $this->client->get("/v1/products/{$productId}");

        return ProductData::fromArray($response);
    }

    /**
     * List products with pagination and filters
     *
     * @param array<string, mixed> $query
     * @return array<int, ProductData>
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get('/v1/products', $query);
        $items = $response['data'] ?? $response['products'] ?? $response['results'] ?? $response;

        $products = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $products[] = ProductData::fromArray($item);
                }
            }
        }

        return $products;
    }

    /**
     * List all products for a specific vendor
     *
     * @param array<string, mixed> $query
     * @return array<int, ProductData>
     */
    public function listByVendor(int|string $vendorId, array $query = []): array
    {
        $response = $this->client->get("/v1/vendors/{$vendorId}/products", $query);
        $items = $response['data'] ?? $response['products'] ?? $response['results'] ?? $response;

        $products = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $products[] = ProductData::fromArray($item);
                }
            }
        }

        return $products;
    }

    /**
     * Create a new product for vendor
     */
    public function create(
        int|string $vendorId,
        CreateProductData|ProductContract|array $product,
    ): ProductData {
        $payload = match (true) {
            $product instanceof CreateProductData => $product->toArray(),
            $product instanceof ProductContract => CreateProductData::fromContract($product)->toArray(),
            default => CreateProductData::fromArray($product)->toArray(),
        };

        $response = $this->client->post("/v1/vendors/{$vendorId}/products", $payload);

        return ProductData::fromArray($response);
    }

    /**
     * Update an existing product
     */
    public function update(
        int|string $productId,
        UpdateProductData|ProductContract|array $product,
    ): ProductData {
        $payload = match (true) {
            $product instanceof UpdateProductData => $product->toArray(),
            $product instanceof ProductContract => UpdateProductData::fromContract($product)->toArray(),
            default => UpdateProductData::fromArray($product)->toArray(),
        };

        $response = $this->client->patch("/v1/products/{$productId}", $payload);

        return ProductData::fromArray($response);
    }

    /**
     * Update a single product variation (price, stock, SKU)
     *
     * @param array<string, mixed> $data
     */
    public function updateVariation(
        int|string $productId,
        int|string $variationId,
        array $data,
    ): ProductVariationData {
        $response = $this->client->patch("/v1/products/{$productId}/variations/{$variationId}", $data);

        return ProductVariationData::fromArray($response);
    }

    /**
     * Get shelves that this product belongs to
     *
     * @return array<string, mixed>
     */
    public function getShelves(int|string $productId): array
    {
        return $this->client->get("/v1/products/{$productId}/shelves");
    }

    /**
     * Get price history for a product
     *
     * @return array<string, mixed>
     */
    public function getPriceHistory(int|string $productId): array
    {
        return $this->client->get("/v1/products/{$productId}/price-history");
    }

    /**
     * Search products
     *
     * @param array<string, mixed> $criteria
     * @return array<string, mixed>
     */
    public function search(array $criteria): array
    {
        return $this->client->post('/v1/products/search', $criteria);
    }
}
