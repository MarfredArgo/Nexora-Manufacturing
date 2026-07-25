<?php

namespace Modules\Manufacturing\Services;

use Modules\Manufacturing\Models\WorkOrder;
use Carbon\Carbon;

class DueDateService
{
    private const DEFAULT_DAYS       = 7;
    private const EXTRA_DAYS         = 3;
    private const CAPACITY_THRESHOLD = 70;

    // ── Due date calc ────────────────────────────────────────────────────────
    // Default: order date + 7 days. +3 extra days if 70+ orders already fall
    // in that window (queue congestion).
    public function calculate(?Carbon $orderDate = null): Carbon
    {
        $orderDate = ($orderDate ?? now())->copy();

        $windowStart = $orderDate->copy()->startOfDay();
        $windowEnd   = $orderDate->copy()->addDays(self::DEFAULT_DAYS)->endOfDay();

        $ordersInLine = WorkOrder::whereNotNull('due_date')
            ->whereBetween('due_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->count();

        $days = $ordersInLine >= self::CAPACITY_THRESHOLD
            ? self::DEFAULT_DAYS + self::EXTRA_DAYS
            : self::DEFAULT_DAYS;

        return $orderDate->copy()->addDays($days);
    }
}
