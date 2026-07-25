<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $warehouses = Warehouse::where('status', 'active')->get();
        $movements = StockMovement::with(['item', 'warehouse'])->orderByDesc('created_at')->take(7)->get();

        $totalItems = $items->count();
        $totalStockUnits = StockLevel::sum(DB::raw('GREATEST(stock - reserved_quantity, 0)'));
        $lowStockAlerts = StockLevel::where(function ($q) {
            $q->whereColumn('stock', '<=', 'reserved_quantity')
              ->orWhere(function ($q2) {
                  $q2->where('reorder_threshold', '>', 0)
                     ->whereRaw('(stock - reserved_quantity) <= reorder_threshold');
              });
        })->count();

        $criticalAlerts = StockLevel::with(['item', 'warehouse'])
            ->whereHas('item')
            ->whereHas('warehouse')
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'reserved_quantity')
                  ->orWhere(function ($q2) {
                      $q2->where('reorder_threshold', '>', 0)
                         ->whereRaw('(stock - reserved_quantity) <= reorder_threshold');
                  });
            })
            ->orderByRaw("CASE WHEN stock <= reserved_quantity THEN 0 ELSE 1 END")
            ->take(10)
            ->get()
            ->map(function ($stockLevel) {
                $available = $stockLevel->stock - $stockLevel->reserved_quantity;

                return [
                    'name' => $stockLevel->item->name,
                    'warehouse' => $stockLevel->warehouse->name,
                    'type' => $available <= 0 ? 'out_of_stock' : 'low_stock',
                    'on_hand' => max(0, $available),
                    'threshold' => $stockLevel->reorder_threshold ?? 0,
                ];
            });

        $trendData = $this->getTrendData('this_week');

        $warehouseDistribution = $warehouses->map(function ($w) {
            return [
                'name' => $w->name,
                'total' => $w->stockLevels()->sum(DB::raw('GREATEST(stock - reserved_quantity, 0)')),
            ];
        });

        // Merge transfer movements (stored as two rows: from & to) into a single display row.
        $mergedMovements = $movements->groupBy(function ($m) {
            return $m->type === 'transfer'
                ? ($m->item_id . '|' . ($m->reference ?? ''))
                : ('__single__|' . $m->id);
        })->map(function ($group) {
            $base = $group->sortByDesc('created_at')->first();

            if ($base->type !== 'transfer') {
                return $base;
            }

            $from = $group->sortBy('warehouse_id')->first();
            $to = $group->sortByDesc('warehouse_id')->first();

            $fromName = $from?->warehouse?->name ?? 'Deleted';
            $toName = $to?->warehouse?->name ?? 'Deleted';

            // Attach display-only value used by index.blade.php (expects a string in ['warehouse']).
            $base->transfer_warehouses_display = $fromName . ' → ' . $toName;

            return $base;
        })->sortByDesc('created_at')->values();

        $recentMovements = $mergedMovements->values()->map(function ($m) {
            return [
                'type' => $m->type,
                'item_name' => $m->item->name,
                'quantity' => $m->quantity,
                'warehouse' => $m->type === 'transfer'
                    ? ($m->transfer_warehouses_display ?? ($m->warehouse?->name ?? 'Deleted'))
                    : ($m->warehouse?->name ?? 'Deleted'),
                'reference' => $m->reference,
                'date' => $m->created_at->format('M d, Y h:i A'),
            ];
        });

        return view('inventory::index', compact(
            'totalItems', 'totalStockUnits', 'lowStockAlerts',
            'criticalAlerts', 'trendData',
            'warehouseDistribution', 'recentMovements'
        ))->with('activePage', 'dashboard');
    }

    public function trendData(Request $request)
    {
        $period = $request->input('period', 'this_week');
        return response()->json($this->getTrendData($period));
    }

    private function getTrendData(string $period): array
    {
        switch ($period) {
            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                break;
            default:
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
        }

        $days = [];
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        $movements = StockMovement::whereBetween('created_at', [$start, $end])->get();

        $labels = [];
        $inbound = [];
        $outbound = [];
        $adjustments = [];
        $transfers = [];

        foreach ($days as $day) {
            $labels[] = $day->format('M d');
            $dayMovements = $movements->filter(fn ($m) => $m->created_at->isSameDay($day));
            $inbound[] = $dayMovements->whereIn('type', ['inbound'])->sum('quantity');
            $outbound[] = $dayMovements->whereIn('type', ['outbound'])->sum('quantity');
            $adjustments[] = $dayMovements->whereIn('type', ['adjustment'])->sum(fn ($m) => abs($m->quantity));
            
            // For transfers, count each unique transfer only once (they're stored as two records)
            $transferMovements = $dayMovements->where('type', 'transfer');
            $uniqueTransfers = $transferMovements->groupBy('reference')->map(fn ($group) => $group->first());
            $transfers[] = $uniqueTransfers->sum('quantity');
        }

        return [
            'labels' => $labels,
            'inbound' => $inbound,
            'outbound' => $outbound,
            'adjustments' => $adjustments,
            'transfers' => $transfers,
        ];
    }
}

