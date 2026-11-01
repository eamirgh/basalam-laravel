<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\DTOs\OrderParcelData;
use Eamirgh\Basalam\DTOs\OrderParcelFilter;
use Eamirgh\Basalam\Http\ApiClient;

class ParcelService
{
    public function __construct(
        protected ApiClient $client,
    ) {
    }

    /**
     * List parcels for vendor orders
     *
     * @param OrderParcelFilter|array<string, mixed> $filter
     * @return array<int, OrderParcelData>
     */
    public function list(OrderParcelFilter|array $filter = []): array
    {
        $query = $filter instanceof OrderParcelFilter ? $filter->toArray() : $filter;
        $response = $this->client->get('/v1/vendor-parcels', $query);

        $items = $response['data'] ?? $response['parcels'] ?? $response['results'] ?? $response;

        $parcels = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $parcels[] = OrderParcelData::fromArray($item);
                }
            }
        }

        return $parcels;
    }

    /**
     * Get single parcel details
     */
    public function get(int|string $parcelId): OrderParcelData
    {
        $response = $this->client->get("/v1/vendor-parcels/{$parcelId}");
        $data = $response['data'] ?? $response;

        return OrderParcelData::fromArray($data);
    }

    /**
     * Set parcel status to under preparation
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function setPreparation(int|string $parcelId, array $payload = []): array
    {
        return $this->client->post("/v1/vendor-parcels/{$parcelId}/set-preparation", $payload);
    }

    /**
     * Set parcel status to posted/shipped with postal tracking code
     *
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    public function setPosted(
        int|string $parcelId,
        string $trackingCode,
        ?string $carrier = null,
        array $extra = [],
    ): array {
        $payload = array_merge([
            'tracking_code' => $trackingCode,
        ], $extra);

        if ($carrier !== null) {
            $payload['carrier'] = $carrier;
            $payload['shipping_method'] = $carrier;
        }

        return $this->client->post("/v1/vendor-parcels/{$parcelId}/set-posted", $payload);
    }
}
