<?php

namespace Modules\OrderFulfillment\Helpers;

use Carbon\Carbon;

/**
 * Order priority calculators.
 */
class OrderPriority
{
    /**
     * Used by dashboard.blade.php (was getOrderPriority()).
     * NEW on day 0, LOW after 1 day, MEDIUM after 2 days, HIGH after 3+ days.
     */
    public static function dashboard(?string $createdAt): array
    {
        if (!$createdAt) {
            return ['label' => 'NEW', 'class' => 'tag-new'];
        }

        $daysOld = round(Carbon::parse($createdAt)->floatDiffInDays(Carbon::now()));

        if ($daysOld >= 3) {
            return ['label' => 'HIGH', 'class' => 'tag-high'];
        } elseif ($daysOld == 2) {
            return ['label' => 'MEDIUM', 'class' => 'tag-medium'];
        } elseif ($daysOld == 1) {
            return ['label' => 'LOW', 'class' => 'tag-low'];
        }

        return ['label' => 'NEW', 'class' => 'tag-new'];
    }

    /**
     * Used by order.blade.php (was getPriority()).
     * LOW for 0-1 days old, MEDIUM at 2 days, HIGH at 3+ days.
     */
    public static function order(?string $createdAt): array
    {
        if (!$createdAt) {
            return ['label' => 'LOW', 'class' => 'priority-low'];
        }

        $daysOld = round(Carbon::parse($createdAt)->floatDiffInDays(Carbon::now()));

        if ($daysOld >= 3) {
            return ['label' => 'HIGH', 'class' => 'priority-high'];
        }

        if ($daysOld == 2) {
            return ['label' => 'MEDIUM', 'class' => 'priority-medium'];
        }

        return ['label' => 'LOW', 'class' => 'priority-low'];
    }

    /**
     * Used by packing.blade.php (was getPackingPriority()).
     * Same day thresholds as order(), but shorter labels ('Low'/'Med'/'High')
     * plus a 'key' used by the front-end JS lookup.
     */
    public static function packing(?string $createdAt): array
    {
        if (!$createdAt) {
            return ['label' => 'Low', 'class' => 'priority-low', 'key' => 'Low'];
        }

        $daysOld = round(Carbon::parse($createdAt)->floatDiffInDays(Carbon::now()));

        if ($daysOld >= 3) {
            return ['label' => 'High', 'class' => 'priority-high', 'key' => 'High'];
        } elseif ($daysOld == 2) {
            return ['label' => 'Med', 'class' => 'priority-med', 'key' => 'Med'];
        }

        return ['label' => 'Low', 'class' => 'priority-low', 'key' => 'Low'];
    }
}
