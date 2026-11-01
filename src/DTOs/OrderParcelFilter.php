<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Illuminate\Contracts\Support\Arrayable;

class OrderParcelFilter implements Arrayable
{
    public function __construct(
        public ?int $page = 1,
        public ?int $perPage = 20,
        public int|string|null $orderId = null,
        public int|string|null $vendorId = null,
        public ?string $status = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->page !== null) {
            $params['page'] = $this->page;
        }
        if ($this->perPage !== null) {
            $params['per_page'] = $this->perPage;
        }
        if ($this->orderId !== null) {
            $params['order_id'] = $this->orderId;
        }
        if ($this->vendorId !== null) {
            $params['vendor_id'] = $this->vendorId;
        }
        if ($this->status !== null) {
            $params['status'] = $this->status;
        }

        return $params;
    }
}
