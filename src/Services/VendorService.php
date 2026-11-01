<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\DTOs\VendorData;
use Eamirgh\Basalam\Http\ApiClient;

class VendorService
{
    public function __construct(
        protected ApiClient $client,
        protected ApiClient $coreClient,
    ) {
    }

    /**
     * Get vendor details by ID
     */
    public function get(int|string $vendorId): VendorData
    {
        $response = $this->client->get("/v1/vendors/{$vendorId}");
        $data = $response['data'] ?? $response;

        return VendorData::fromArray($data);
    }

    /**
     * Update vendor information
     *
     * @param array<string, mixed> $data
     */
    public function update(int|string $vendorId, array $data): VendorData
    {
        $response = $this->client->patch("/v1/vendors/{$vendorId}", $data);
        $data = $response['data'] ?? $response;

        return VendorData::fromArray($data);
    }

    /**
     * Update vendor online/offline status
     *
     * @return array<string, mixed>
     */
    public function updateStatus(int|string $vendorId, string $status): array
    {
        return $this->client->patch("/v1/vendors/{$vendorId}/status", [
            'status' => $status,
        ]);
    }

    /**
     * Get shipping methods configured for this vendor
     *
     * @return array<string, mixed>
     */
    public function getShippingMethods(int|string $vendorId): array
    {
        return $this->coreClient->get("/v3/vendors/{$vendorId}/shipping-methods");
    }

    /**
     * Get vendor addresses
     *
     * @return array<string, mixed>
     */
    public function getAddresses(): array
    {
        return $this->client->get('/v1/vendors/me/addresses');
    }

    /**
     * Get vendor carrier options
     *
     * @return array<string, mixed>
     */
    public function getCarriers(): array
    {
        return $this->client->get('/v1/shipping/vendor-carriers');
    }

    /**
     * Request change of preparation days
     *
     * @return array<string, mixed>
     */
    public function createPreparationDayRequest(int|string $vendorId, int $days, ?string $description = null): array
    {
        return $this->coreClient->post("/v3/vendors/{$vendorId}/preparation-day-request", [
            'preparation_days' => $days,
            'description' => $description,
        ]);
    }

    /**
     * Get batch jobs submitted for vendor
     *
     * @return array<string, mixed>
     */
    public function getBatchJobs(int|string $vendorId): array
    {
        return $this->client->get("/v1/vendors/{$vendorId}/batch-jobs");
    }
}
