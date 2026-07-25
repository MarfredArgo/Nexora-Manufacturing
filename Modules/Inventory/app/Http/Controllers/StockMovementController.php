<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\Warehouse;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['item', 'warehouse', 'performer'])->orderByDesc('created_at');

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($warehouse = $request->input('warehouse')) {
            $query->where('warehouse_id', $warehouse);
        }

        if ($reference = $request->input('reference')) {
            $query->where('reference', $reference);
        }

        if ($dateRange = $request->input('date_range')) {
            match ($dateRange) {
                'today' => $query->whereDate('created_at', today()),
                'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                default => null,
            };
        }

        if ($search = $request->input('search')) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(reference) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('item', function ($iq) use ($search) {
                      $iq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                   });
            });
        }

        $allRaw = $query->get();

        // Merge transfer movements (which are stored as two rows: from & to) into a single display row.
        // Group by item_id + reference since that's how transfer rows are created.
        $mergedMovements = collect();
        $transferGroups = $allRaw->groupBy(fn ($m) => $m->type === 'transfer'
            ? ($m->item_id . '|' . ($m->reference ?? ''))
            : ('__single__|' . $m->id)
        );

        // Both legs of a transfer are stored with the same reference and a positive
        // quantity, so direction cannot be recovered from the rows themselves.
        // Resolve it from the originating transfer record instead of guessing.
        $transferDirections = $this->transferDirections($allRaw);

        foreach ($transferGroups as $groupKey => $group) {
            // Skip empty keys
            if ($groupKey === null || $groupKey === '') {
                continue;
            }

            // Handle non-transfer movements (each has unique key)
            if (str_starts_with($groupKey, '__single__|')) {
                $mergedMovements->push($group->first());
                continue;
            }

            // Take the newest row as the base (for date / performer / item fields)
            $base = $group->sortByDesc('created_at')->first();

            $direction = $transferDirections[$base->reference] ?? null;
            $byWarehouse = $group->keyBy('warehouse_id');

            if ($direction) {
                $transferFrom = $byWarehouse[$direction->from_warehouse_id] ?? null;
                $transferTo = $byWarehouse[$direction->to_warehouse_id] ?? null;
            } else {
                $transferFrom = $group->first();
                $transferTo = $group->last();
            }

            $fromName = $transferFrom?->warehouse?->name ?? 'Deleted';
            $toName = $transferTo?->warehouse?->name ?? 'Deleted';

            // Attach display-only fields consumed by the blade.
            $base->transfer_warehouses_display = $fromName . ' → ' . $toName;
            $base->transfer_quantity_display = $base->quantity;

            $mergedMovements->push($base);
        }

        // Sort merged results newest-first
        $mergedMovements = $mergedMovements->sortByDesc('created_at')->values();

        // Paginate the merged collection so per-page count is accurate.
        $page = request()->get('page', 1);
        $perPage = 10;
        $totalAfterMerge = $mergedMovements->count();
        $displayItems = $mergedMovements->forPage($page, $perPage)->values();

        $movements = new \Illuminate\Pagination\LengthAwarePaginator(
            $displayItems,
            $totalAfterMerge,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $totals = [
            'inbound' => StockMovement::where('type', 'inbound')->sum('quantity'),
            'outbound' => StockMovement::where('type', 'outbound')->sum('quantity'),
            // Read from the transfers themselves rather than halving the movement
            // rows, which silently went fractional if a leg was ever missing.
            'transfer' => (int) StockTransfer::where('status', 'approved')->sum('quantity'),
            'adjustment' => StockMovement::where('type', 'adjustment')->sum('quantity'),
        ];
        $totals['net'] = $totals['inbound'] + $totals['outbound'] + $totals['adjustment'];

        return view('inventory::stock-movement', [
            'movements' => $movements,
            'warehouses' => Warehouse::where('status', 'active')->whereNull('deleted_at')->get(),
            'totals' => $totals,
            'activePage' => 'stock-movement',
        ]);
    }

    /**
     * Map transfer movement references (TRF-000123) back to the source and
     * destination warehouses recorded on the transfer, keyed by reference.
     */
    private function transferDirections($movements): array
    {
        $ids = $movements
            ->where('type', 'transfer')
            ->pluck('reference')
            ->filter()
            ->unique()
            ->map(fn ($reference) => (int) (explode('-', $reference)[2] ?? 0))
            ->filter()
            ->values()
            ->all();

        if (empty($ids)) {
            return [];
        }

        return StockTransfer::whereIn('id', $ids)
            ->get(['id', 'from_warehouse_id', 'to_warehouse_id', 'created_at'])
            ->keyBy(fn ($transfer) => $transfer->reference)
            ->all();
    }
}

