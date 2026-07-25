<?php

namespace App\Services\Departments;

use App\Models\ProcurementOrder;

/**
 * Computes Procurement KPIs from procurement_orders.
 */
class ProcurementService
{
    /**
     * @return array<string, mixed>
     */
    public function getSnapshot(): array
    {
        return [
            'open_orders' => $this->openOrdersCount(),
            'open_orders_value' => $this->openOrdersValue(),
            'expedited_orders' => $this->expeditedOrdersCount(),
            'orders_by_status' => $this->ordersByStatus(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getKpiSummaryForAi(): array
    {
        return [
            'open_orders' => $this->openOrdersCount(),
            'open_orders_value' => $this->openOrdersValue(),
            'expedited_orders' => $this->expeditedOrdersCount(),
        ];
    }

    public function openOrdersCount(): int
    {
        return ProcurementOrder::open()->count();
    }

    public function openOrdersValue(): float
    {
        return (float) ProcurementOrder::open()->sum('total_cost');
    }

    public function expeditedOrdersCount(): int
    {
        return ProcurementOrder::where('expedited', true)->open()->count();
    }

    /**
     * @return array<string, int>
     */
    public function ordersByStatus(): array
    {
        return ProcurementOrder::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}
