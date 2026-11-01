<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\Http\ApiClient;

class CategoryService
{
    public function __construct(
        protected ApiClient $coreClient,
    ) {
    }

    /**
     * List product categories
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function list(array $query = []): array
    {
        return $this->coreClient->get('/v3/categories', $query);
    }

    /**
     * Get category details
     *
     * @return array<string, mixed>
     */
    public function get(int|string $categoryId): array
    {
        return $this->coreClient->get("/v3/categories/{$categoryId}");
    }

    /**
     * Get attributes specification for category
     *
     * @return array<string, mixed>
     */
    public function getAttributes(int|string $categoryId): array
    {
        return $this->coreClient->get("/v3/categories/{$categoryId}/attributes");
    }
}
