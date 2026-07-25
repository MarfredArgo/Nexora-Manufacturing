<?php

namespace App\Services\Departments;

use App\Models\FulfillmentOrder;
use App\Models\FulfillmentPackingMaterial;
use App\Models\FulfillmentShipment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Computes Order Fulfillment KPIs from data synced from the Order
 * Fulfillment department's database (via `sync:fulfillment`).
 *
 * As of the initial sync, their system has orders but no shipment
 * history yet — so fulfillment rate / delayed shipments genuinely
 * can't be computed. Methods return null in that case rather than a
 * fabricated number; the caller decides how to render "no data yet".
 */
class FulfillmentService
{
    public function pendingOrdersCount(): int
    {
        return FulfillmentOrder::where('status', 'NEW')->count();
    }

    public function totalOrdersCount(): int
    {
        return FulfillmentOrder::count();
    }

    public function totalShipmentsCount(): int
    {
        return FulfillmentShipment::count();
    }

    /**
     * Percentage of shipments delivered on or before their due date.
     * Returns null if there's no shipment data yet to compute from.
     */
    public function fulfillmentRatePercent(): ?float
    {
        $total = FulfillmentShipment::count();

        if ($total === 0) {
            return null;
        }

        $onTime = FulfillmentShipment::whereColumn('source_updated_at', '<=', 'due_date')->count();

        return round(($onTime / $total) * 100, 2);
    }

    /**
     * Count of shipments still not delivered past their due date.
     * Returns null if there's no shipment data yet.
     */
    public function delayedShipmentsCount(): ?int
    {
        if (FulfillmentShipment::count() === 0) {
            return null;
        }

        return FulfillmentShipment::where('due_date', '<', now())
            ->whereNotIn('status', ['Delivered', 'Completed'])
            ->count();
    }

    public function lowStockPackingMaterialsCount(): int
    {
        return FulfillmentPackingMaterial::lowStock()->count();
    }

    /**
     * @return array<int, array{name: string, stock_qty: int, low_stock_threshold: int}>
     */
    public function lowStockPackingMaterials(): array
    {
        return FulfillmentPackingMaterial::lowStock()
            ->orderBy('stock_qty')
            ->get()
            ->map(fn(FulfillmentPackingMaterial $item) => [
                'name' => $item->name,
                'stock_qty' => $item->stock_qty,
                'low_stock_threshold' => $item->low_stock_threshold,
            ])
            ->toArray();
    }

    /**
     * Orders that have actually been fulfilled (as opposed to all orders
     * regardless of status). This is the source of truth for "units sold"
     * and product revenue — replaces the old SalesService/sales_orders logic.
     */
    protected function fulfilledOrdersQuery()
    {
        return FulfillmentOrder::whereRaw('LOWER(status) = ?', ['fulfilled']);
    }

    /**
     * Source query for the "Products Driving Growth" table's units/revenue
     * numbers. Prefers strictly-fulfilled orders (the correct long-term
     * source of truth). But if the synced dataset doesn't have any
     * fulfilled orders yet (e.g. everything is still sitting at "NEW"
     * because fulfillment hasn't started), it falls back to counting all
     * orders regardless of status — so the widget reflects real demand
     * instead of sitting empty. This re-tightens back to fulfilled-only
     * automatically the moment fulfilled orders start appearing in the
     * synced data, with no code change needed.
     */
    protected function growthMetricsOrdersQuery()
    {
        return $this->fulfilledOrdersCount() > 0
            ? $this->fulfilledOrdersQuery()
            : FulfillmentOrder::query();
    }

    /**
     * Total units sold across all fulfilled orders, all time.
     */
    public function totalUnitsSold(): int
    {
        return (int) $this->fulfilledOrdersQuery()->sum('qty');
    }

    /**
     * Count of orders that have reached "Fulfilled" status.
     */
    public function fulfilledOrdersCount(): int
    {
        return $this->fulfilledOrdersQuery()->count();
    }

    /**
     * Month-over-month change in total order volume (all statuses),
     * comparing the trailing 30 days against the 30 days before that.
     * Returns null when there's no prior-period data to compare against,
     * rather than fabricating a 0% change.
     */
    public function ordersMonthOverMonthChangePercent(): ?float
    {
        $now = Carbon::now();
        $currentStart = $now->copy()->subDays(30);
        $previousStart = $now->copy()->subDays(60);

        $previous = FulfillmentOrder::whereBetween('source_created_at', [$previousStart, $currentStart])->count();

        if ($previous === 0) {
            return null;
        }

        $current = FulfillmentOrder::where('source_created_at', '>=', $currentStart)->count();

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Highest-selling products over the trailing 30 days, with their
     * prior-30-day units for the "vs Last 30 Days" comparison and revenue
     * (qty x selling price, summed from the synced `amount` column).
     * Uses fulfilled-only orders once that data exists (see
     * growthMetricsOrdersQuery()), falling back to all orders until then.
     *
     * @return Collection<int, array{name: string, units_sold: int, prev_units: int, revenue: float}>
     */
    public function topProductsByUnitsSold(int $limit = 10): Collection
    {
        $now = Carbon::now();
        $currentStart = $now->copy()->subDays(30);
        $previousStart = $now->copy()->subDays(60);

        $current = $this->growthMetricsOrdersQuery()
            ->where('source_created_at', '>=', $currentStart)
            ->selectRaw('product_name, SUM(qty) as units_sold, SUM(amount) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get();

        $previous = $this->growthMetricsOrdersQuery()
            ->whereBetween('source_created_at', [$previousStart, $currentStart])
            ->selectRaw('product_name, SUM(qty) as units_sold')
            ->groupBy('product_name')
            ->pluck('units_sold', 'product_name');

        return $current
            ->map(fn($row) => [
                'name' => $row->product_name,
                'units_sold' => (int) $row->units_sold,
                'prev_units' => (int) ($previous[$row->product_name] ?? 0),
                'revenue' => (float) $row->revenue,
            ])
            ->values();
    }

    /**
     * Average monthly units sold per product, based on the same order set
     * as topProductsByUnitsSold() (fulfilled-only once available, all
     * orders as a fallback), over the trailing N months. Used as the
     * denominator for Inventory's "Inventory Coverage" calculation
     * (Available Stock / Average Monthly Sales).
     *
     * @param  array<int, string>  $productNames
     * @return Collection<string, float> keyed by product name
     */
    public function averageMonthlyUnitsSoldByProduct(array $productNames, int $months = 3): Collection
    {
        if ($productNames === []) {
            return collect();
        }

        $start = Carbon::now()->subMonths($months);

        return $this->growthMetricsOrdersQuery()
            ->whereIn('product_name', $productNames)
            ->where('source_created_at', '>=', $start)
            ->selectRaw('product_name, SUM(qty) as total_units')
            ->groupBy('product_name')
            ->get()
            ->mapWithKeys(fn($row) => [$row->product_name => round(((float) $row->total_units) / $months, 2)]);
    }
}
