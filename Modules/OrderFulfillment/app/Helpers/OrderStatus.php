<?php

namespace Modules\OrderFulfillment\Helpers;

/**
 * Single source of truth for order/shipment status TEXT.
 *
 * Every stage an order can be in — from the moment it's placed to the
 * moment it's delivered (or cancelled/delayed along the way) — lives
 * in one place. dashboard.blade.php, order.blade.php and
 * shipping.blade.php all call OrderStatus::label() instead of keeping
 * their own copy of the wording, so the three tabs can never drift
 * out of sync with each other again (e.g. "READY_TO_SHIP" showing as
 * raw DB text on one page and "READY FOR DELIVERY" on another).
 *
 * Colors are intentionally handled separately, per page, because each
 * blade already has its own badge markup (.tag-*, .status-*,
 * .status-tag.tag-*, .status-pill.tag-*). What matters is that the
 * *tier* a status maps to is the same everywhere:
 *
 *   tier "new"       -> NEW
 *   tier "packing"    -> PACKING, READY_TO_SHIP
 *   tier "shipped"     -> SHIPPED
 *   tier "transit"      -> OUT_FOR_DELIVERY
 *   tier "delivered"     -> DELIVERED
 *   tier "cancelled"      -> CANCELLED, DELAYED
 *
 * Keep this list and the tier map below in sync if a new status is
 * ever introduced.
 */
class OrderStatus
{
    private static array $labels = [
        'NEW'              => 'NEW',
        'PACKING'          => 'PACKING',
        'READY_TO_SHIP'    => 'READY FOR DELIVERY',
        'SHIPPED'          => 'SHIPPED',
        'OUT_FOR_DELIVERY' => 'OUT FOR DELIVERY',
        'DELIVERED'        => 'DELIVERED',
        'DELAYED'          => 'DELAYED',
        'CANCELLED'        => 'CANCELLED',
    ];

    /**
     * The color "tier" each status belongs to. Every blade's own
     * class map (tag-*, status-*, etc.) should route these tiers to
     * identical hex values — see the CSS comments left in each file.
     */
    private static array $tiers = [
        'NEW'              => 'new',
        'PACKING'          => 'packing',
        'READY_TO_SHIP'    => 'packing',
        'SHIPPED'          => 'shipped',
        'OUT_FOR_DELIVERY' => 'transit',
        'DELIVERED'        => 'delivered',
        'DELAYED'          => 'cancelled',
        'CANCELLED'        => 'cancelled',
    ];

    public static function label(?string $status): string
    {
        $key = strtoupper((string) $status);
        return self::$labels[$key] ?? strtoupper(str_replace('_', ' ', $key));
    }

    public static function tier(?string $status): string
    {
        $key = strtoupper((string) $status);
        return self::$tiers[$key] ?? 'new';
    }
}
