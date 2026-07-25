<?php

namespace Modules\Procurement\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Compact peso format for the dashboard summaries: ₱9.6k, ₱1.2m, ₱1b.
     * Amounts under 1,000 are shown in full (₱950).
     */
    private function compactPeso($value): string
    {
        $value = (float) $value;
        $abs = abs($value);

        [$divisor, $suffix] = match (true) {
            $abs >= 1_000_000_000 => [1_000_000_000, 'b'],
            $abs >= 1_000_000 => [1_000_000, 'm'],
            $abs >= 1_000 => [1_000, 'k'],
            default => [1, ''],
        };

        $scaled = $value / $divisor;
        $formatted = $suffix === ''
            ? number_format($scaled, 0)
            : rtrim(rtrim(number_format($scaled, 1), '0'), '.'); // 9.6k, 1k (not 1.0k)

        return '₱'.$formatted.$suffix;
    }

    /**
     * Requisitions live on the external Order Fulfillment / Manufacturing
     * databases — the same source the Requisitions page reads. Counting
     * procurement.requisitions (which is empty) always returned 0, which is why
     * the dashboard card read "0".
     *
     * @return array{total:int, pending:int}
     */
    private function externalRequisitionCounts(): array
    {
        $total = 0;
        $pending = 0;

        foreach (['order_fulfillment', 'inventory'] as $connectionName) {
            try {
                $connection = DB::connection($connectionName);
                $schema = $connection->getSchemaBuilder();

                if (! $schema->hasTable('requisitions')) {
                    continue;
                }

                $total += (int) $connection->table('requisitions')->count();

                if ($schema->hasColumn('requisitions', 'status')) {
                    $pending += (int) $connection->table('requisitions')
                        ->whereIn('status', ['Pending', 'pending'])
                        ->count();
                } else {
                    // No status column (Order Fulfillment): those rows surface
                    // as Pending on the Requisitions page.
                    $pending += (int) $connection->table('requisitions')->count();
                }
            } catch (\Throwable $e) {
                // Skip broken or unavailable external connections.
            }
        }

        return ['total' => $total, 'pending' => $pending];
    }

    /**
     * Lightweight JSON counts polled by the dashboard + sidebar so cards and
     * nav badges update live (no page refresh). Every query is wrapped so a
     * missing table/column/connection can never turn this into a 500.
     */
    public function liveStats(Request $request)
    {
        $db = DB::connection('procurement');
        $clientId = (int) session('employee_client_id');
        $rootTesting = config('nexora.root_admin_module_testing') && $request->user()?->role === 'root_admin';

        $table = function (string $name) use ($db, $clientId, $rootTesting) {
            $query = $db->table($name);
            if (! $rootTesting) {
                $query->where($name.'.client_id', $clientId);
            }

            return $query;
        };

        $safe = function (callable $cb): int {
            try {
                return (int) $cb();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        // Requisitions live on the external Order Fulfillment / Manufacturing
        // databases (same source the Requisitions page and sidebar badge use),
        // never on the procurement connection.
        $requisitionCounts = $this->externalRequisitionCounts();

        return response()->json([
            'cards' => [
                'activePos' => $safe(fn () => $table('purchase_orders')->count()),
                'suppliers' => $safe(fn () => $table('suppliers')->where('status', 'active')->count()),
                'requisitions' => $requisitionCounts['total'],
                'deliveries' => $safe(fn () => $table('deliveries')->count()),
            ],
            'badges' => [
                'purchaseOrders' => $safe(fn () => $table('purchase_orders')->where('status', 'pending')->count()),
                'requisitions' => $requisitionCounts['pending'],
                'deliveries' => $safe(fn () => $table('deliveries')->whereIn('status', ['pending', 'scheduled', 'intransit'])->count()),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $db = DB::connection('procurement');
        $clientId = (int) session('employee_client_id');
        $rootTesting = config('nexora.root_admin_module_testing') && $request->user()?->role === 'root_admin';

        $table = function (string $name) use ($db, $clientId, $rootTesting) {
            $query = $db->table($name);

            if (! $rootTesting) {
                $query->where($name.'.client_id', $clientId);
            }

            return $query;
        };

        $poCount = $table('purchase_orders')->count();
        $poStatusBreakdown = $table('purchase_orders')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $supplierCount = $table('suppliers')->where('status', 'active')->count();
        // From the external requisition sources, not procurement.requisitions.
        $requisitionCount = $this->externalRequisitionCounts()['total'];
        $deliveryCount = $table('deliveries')->count();
        $pendingDeliveries = $table('deliveries')
            ->whereIn('status', ['pending', 'scheduled', 'intransit'])
            ->count();

        $recentPOs = $table('purchase_orders')
            ->select('id', 'po_number', 'supplier_id', 'qty', 'amount', 'status', 'priority', 'order_date', 'expected_delivery_date', 'item', 'brand')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $supplierIds = $recentPOs->pluck('supplier_id')->filter()->unique()->all();
        $suppliersMap = $supplierIds
            ? $table('suppliers')->whereIn('id', $supplierIds)->pluck('name', 'id')->all()
            : [];

        $recentDeliveries = $table('deliveries')
            ->select('id', 'shipment_number', 'purchase_order_id', 'supplier_id', 'status', 'delivery_date', 'estimated_arrival', 'actual_arrival', 'carrier')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $deliverySupplierIds = $recentDeliveries->pluck('supplier_id')->filter()->unique()->all();
        $deliverySuppliersMap = $deliverySupplierIds
            ? $table('suppliers')->whereIn('id', $deliverySupplierIds)->pluck('name', 'id')->all()
            : [];

        // "Spend by Category" is split per ordered item's category
        // (purchase_order_items.category). Until the schema migration adds that
        // column, fall back to each PO's primary category stored in `brand`.
        $hasItemCategory = Schema::connection('procurement')
            ->hasColumn('purchase_order_items', 'category');

        // Spend figures only count POs that represent committed money. A PO that
        // is still Pending, or was Rejected/Cancelled, must not contribute to
        // total spend, category spend, or top-supplier spend.
        $uncountedStatuses = ['pending', 'rejected', 'cancelled'];

        if ($hasItemCategory) {
            $spendByCategoryAll = $table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->select('purchase_order_items.category as category', DB::raw('SUM(purchase_order_items.amount) as total'))
                ->whereNotNull('purchase_order_items.category')
                ->where('purchase_order_items.category', '!=', '')
                ->whereNotIn('purchase_orders.status', $uncountedStatuses)
                ->groupBy('purchase_order_items.category')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row) {
                    $row->formatted_total = $this->compactPeso($row->total);

                    return $row;
                })
                ->values();
        } else {
            $spendByCategoryAll = $table('purchase_orders')
                ->select('brand as category', DB::raw('SUM(amount) as total'))
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->whereNotIn('status', $uncountedStatuses)
                ->groupBy('brand')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row) {
                    $row->formatted_total = $this->compactPeso($row->total);

                    return $row;
                })
                ->values();
        }

        // Top 5 for the compact dashboard panel; the full list feeds the
        // "View all" modal.
        $spendByCategory = $spendByCategoryAll->take(5)->values();

        $totalSpend = $table('purchase_orders')
            ->whereNotIn('status', $uncountedStatuses)
            ->sum('amount');

        $topSuppliers = $table('purchase_orders')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('suppliers.id', 'suppliers.name', DB::raw('SUM(purchase_orders.amount) as total_spend'))
            ->whereNotIn('purchase_orders.status', $uncountedStatuses)
            ->when(! $rootTesting, fn ($query) => $query->where('suppliers.client_id', $clientId))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_spend')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $row->formatted_total_spend = $this->compactPeso($row->total_spend);

                return $row;
            });

        return view('procurement::pages.dashboard', [
            'poCount' => $poCount,
            'poStatusBreakdown' => $poStatusBreakdown,
            'supplierCount' => $supplierCount,
            'requisitionCount' => $requisitionCount,
            'deliveryCount' => $deliveryCount,
            'pendingDeliveries' => $pendingDeliveries,
            'recentPOs' => $recentPOs,
            'suppliersMap' => $suppliersMap,
            'recentDeliveries' => $recentDeliveries,
            'deliverySuppliersMap' => $deliverySuppliersMap,
            'spendByCategory' => $spendByCategory,
            'spendByCategoryAll' => $spendByCategoryAll,
            'totalSpend' => $totalSpend,
            'totalSpendFormatted' => $this->compactPeso($totalSpend),
            'topSuppliers' => $topSuppliers,
            'lowStockAlerts' => collect(),
        ]);
    }
}
