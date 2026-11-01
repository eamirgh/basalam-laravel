<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\DTOs\ItemFilter;
use Eamirgh\Basalam\DTOs\OrderData;
use Eamirgh\Basalam\DTOs\OrderFilter;
use Eamirgh\Basalam\DTOs\OrderItemData;
use Eamirgh\Basalam\Http\ApiClient;

class OrderService
{
    public function __construct(
        protected ApiClient $client,
    ) {
    }

    /**
     * List customer orders
     *
     * @param OrderFilter|array<string, mixed> $filter
     * @return array<int, OrderData>
     */
    public function list(OrderFilter|array $filter = []): array
    {
        $query = $filter instanceof OrderFilter ? $filter->toArray() : $filter;
        $response = $this->client->get('/v1/customer-orders', $query);

        $items = $response['data'] ?? $response['orders'] ?? $response['results'] ?? $response;

        $orders = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $orders[] = OrderData::fromArray($item);
                }
            }
        }

        return $orders;
    }

    /**
     * Get single customer order details
     */
    public function get(int|string $orderId): OrderData
    {
        $response = $this->client->get("/v1/customer-orders/{$orderId}");
        $data = $response['data'] ?? $response;

        return OrderData::fromArray($data);
    }

    /**
     * List order items across customer orders
     *
     * @param ItemFilter|array<string, mixed> $filter
     * @return array<int, OrderItemData>
     */
    public function getItems(ItemFilter|array $filter = []): array
    {
        $query = $filter instanceof ItemFilter ? $filter->toArray() : $filter;
        $response = $this->client->get('/v1/customer-orders/items', $query);

        $items = $response['data'] ?? $response['items'] ?? $response['results'] ?? $response;

        $orderItems = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $orderItems[] = OrderItemData::fromArray($item);
                }
            }
        }

        return $orderItems;
    }

    /**
     * Get single order item details
     */
    public function getItem(int|string $itemId): OrderItemData
    {
        $response = $this->client->get("/v1/customer-orders/items/{$itemId}");
        $data = $response['data'] ?? $response;

        return OrderItemData::fromArray($data);
    }

    /**
     * Get hints for packaging parcels in an order
     *
     * @return array<string, mixed>
     */
    public function getParcelHints(int|string $orderId): array
    {
        return $this->client->get("/v1/customer-orders/{$orderId}/parcel-hints");
    }

    /**
     * Get order statistics
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function getStats(array $params = []): array
    {
        return $this->client->get('/v1/orders/stats', $params);
    }
}
