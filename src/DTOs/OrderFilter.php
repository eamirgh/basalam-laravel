<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\DTOs;

use Illuminate\Contracts\Support\Arrayable;

class OrderFilter implements Arrayable
{
    public function __construct(
        public ?int $page = 1,
        public ?int $perPage = 20,
        public ?string $status = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?int $customerId = null,
        public ?int $vendorId = null,
        public ?string $sort = 'DESC',
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
        if ($this->status !== null) {
            $params['status'] = $this->status;
        }
        if ($this->fromDate !== null) {
            $params['from_date'] = $this->fromDate;
        }
        if ($this->toDate !== null) {
            $params['to_date'] = $this->toDate;
        }
        if ($this->customerId !== null) {
            $params['customer_id'] = $this->customerId;
        }
        if ($this->vendorId !== null) {
            $params['vendor_id'] = $this->vendorId;
        }
        if ($this->sort !== null) {
            $params['sort'] = $this->sort;
        }

        return $params;
    }
}
