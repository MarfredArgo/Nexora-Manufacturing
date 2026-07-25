<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        try {
            $rows = DB::connection('inventory')
                ->table('requisitions')
                ->where('requested_by', session('employee_name'))
                ->orderByDesc('created_at')
                ->get();

            $grouped = $rows->groupBy(function ($r) {
                preg_match('/\[batch:([^\]]+)\]/', $r->notes ?? '', $m);
                return $m[1] ?? $r->req_id;
            })->map(function ($group) {
                $first = $group->first();
                $first->quantity = $group->sum('quantity');
                $first->item_count = $group->count();
                $first->part_name = $group->count() > 1
                    ? $first->part_name . ' <span class="multi-count">+' . ($group->count() - 1) . ' more</span>'
                    : $first->part_name;
                return $first;
            })->values();

            $total = $grouped->count();
            $pending = $grouped->where('status', 'Pending')->count();

            $perPage = 10;
            $page = request()->get('page', 1);
            $offset = ($page - 1) * $perPage;
            $requests = new \Illuminate\Pagination\LengthAwarePaginator(
                $grouped->slice($offset, $perPage)->values(),
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } catch (\Exception $e) {
            $requests = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $total = 0;
            $pending = 0;
        }

        return view('inventory::requests', [
            'requests' => $requests,
            'pendingCount' => $pending,
            'totalCount' => $total,
            'activePage' => 'requests',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:restock,replacement',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:restock,replacement',
            'items.*.part_name' => 'required|string|max:150',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:1000',
            'items.*.item_id' => 'nullable|integer',
            'items.*.defect_id' => 'nullable|integer',
        ]);

        $rows = [];
        $names = [];
        $year = now()->format('Y');
        $reqSeq = DB::connection('inventory')->table('requisitions')
            ->whereYear('created_at', $year)
            ->count() + 1;
        $batchId = 'BATCH-' . $year . '-' . str_pad((string) $reqSeq, 4, '0', STR_PAD_LEFT);

        foreach ($validated['items'] as $i => $item) {
            $priority = 'Low';

            if ($item['type'] === 'restock' && !empty($item['item_id'])) {
                $invItem = \Modules\Inventory\Models\Item::find($item['item_id']);
                if (!$invItem) {
                    return back()->withInput()->withErrors(['items.' . $i . '.item_id' => 'Item not found.']);
                }
                $item['part_name'] = $invItem->name;
                $priority = $invItem->status === 'Out of Stock' ? 'High' : 'Low';
            }

            if ($item['type'] === 'replacement' && !empty($item['defect_id'])) {
                $defect = \Modules\Inventory\Models\Defect::find($item['defect_id']);
                if (!$defect || $defect->status !== 'Open') {
                    return back()->withInput()->withErrors(['items.' . $i . '.defect_id' => 'Selected defect is no longer open.']);
                }
                $item['part_name'] = $defect->part_name;
            }

            $reqId = 'REQ-' . $year . '-' . str_pad((string) ($reqSeq + $i), 4, '0', STR_PAD_LEFT);

            $structuredNotes = $item['notes'] ?? '';
            $structuredNotes = '[batch:' . $batchId . '] ' . $structuredNotes;
            if ($item['type'] === 'restock' && !empty($item['item_id'])) {
                $structuredNotes = '[item_id:' . $item['item_id'] . '] ' . $structuredNotes;
            } elseif (!empty($item['defect_id'])) {
                $structuredNotes = '[defect_id:' . $item['defect_id'] . '] ' . $structuredNotes;
            }

            $rows[] = [
                'client_id' => session('employee_client_id', 0),
                'req_id' => $reqId,
                'part_name' => $item['part_name'],
                'quantity' => $item['quantity'],
                'department' => 'Inventory',
                'requested_by' => session('employee_name', 'Unknown'),
                'type' => $item['type'],
                'notes' => $structuredNotes,
                'priority' => $priority,
                'date_requested' => now()->toDateString(),
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $names[] = $item['part_name'];
        }

        try {
            DB::connection('inventory')->table('requisitions')->insert($rows);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['submit' => 'Failed to submit request. The procurement system is currently unavailable. Please try again later.']);
        }

        $count = count($rows);
        return redirect()->route('inventory.requests')
            ->with('success', "{$count} item(s) submitted to Procurement: " . implode(', ', $names));
    }

    public function restockItems()
    {
        $items = \Modules\Inventory\Models\Item::with('stockLevels')
            ->where(function ($q) {
                $q->lowStock()->orWhere(function ($sq) {
                    $sq->whereIn('items.id', function ($sub) {
                        $sub->select('item_id')
                            ->from('stock_levels')
                            ->groupBy('item_id')
                            ->havingRaw('COALESCE(SUM(stock - reserved_quantity), 0) <= 0');
                    });
                });
            })
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'total_available' => $item->total_available,
                'status' => $item->status,
            ])
            ->sortBy('status')
            ->values();

        return response()->json($items);
    }

    public function replacementItems()
    {
        $items = \Modules\Inventory\Models\Defect::where('status', 'Open')->get()->map(fn ($d) => [
            'id' => $d->id,
            'part_name' => $d->part_name,
            'quantity' => $d->quantity,
            'source' => $d->source,
        ]);

        return response()->json($items);
    }
}
