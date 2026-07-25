<?php

namespace Modules\Procurement\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Warehouse;
use Modules\Procurement\Services\RequisitionStatusWriter;

class PurchaseOrderController extends Controller
{
    private function table(string $name)
    {
        $query = DB::connection('procurement')->table($name);

        if (! (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin')) {
            $query->where($name.'.client_id', (int) session('employee_client_id'));
        }

        return $query;
    }

    /**
     * Load each PO's item rows (added when multi-item PO support was
     * introduced), grouped by purchase_order_id, in the JSON shape the
     * frontend chips/cascading selects expect: {name, qty, unitPrice}.
     */
    private function itemsGroupedByPurchaseOrder($purchaseOrderIds)
    {
        if (empty($purchaseOrderIds)) {
            return collect();
        }

        return DB::connection('procurement')->table('purchase_order_items')
            ->whereIn('purchase_order_id', $purchaseOrderIds)
            ->orderBy('id')
            ->get()
            ->groupBy('purchase_order_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'name' => $row->name,
                        'qty' => (int) $row->qty,
                        'unitPrice' => (float) $row->unit_price,
                    ];
                })->values();
            });
    }

    /**
     * Detect a unique-constraint violation (e.g. a duplicate po_number),
     * regardless of which database driver raised it.
     */
    private function isDuplicateKeyException(\Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, 'duplicate key')
            || str_contains($message, 'Unique violation')
            || str_contains($message, 'SQLSTATE[23505]')
            || str_contains($message, 'UNIQUE constraint failed');
    }

    /**
     * The next clean, sequential PO number: PO-{year}-{0001}. Derived from the
     * highest existing number for the year, ignoring any legacy suffixed ones
     * (e.g. "PO-2026-0002-20260724..."), so numbers stay short and orderly.
     */
    private function nextPurchaseOrderNumber(): string
    {
        $year = now()->year;
        $prefix = "PO-{$year}-";

        // Global uniqueness (the po_number UNIQUE constraint is not per-tenant),
        // so this is intentionally not client-scoped.
        $highest = DB::connection('procurement')->table('purchase_orders')
            ->where('po_number', 'like', $prefix.'%')
            ->pluck('po_number')
            ->map(function (string $number) use ($prefix): int {
                // Only "PO-YYYY-####" exactly — no trailing junk.
                return preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $number, $m) ? (int) $m[1] : 0;
            })
            ->max() ?? 0;

        return $prefix.str_pad($highest + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Insert the purchase order. The po_number is always assigned server-side
     * from nextPurchaseOrderNumber(), so the browser's in-memory guess (which
     * resets each page load and used to collide, then get an ugly timestamp
     * suffix appended) is ignored. On the rare concurrent-insert collision we
     * simply recompute the next number and retry.
     */
    private function insertPurchaseOrder(array $insert): int
    {
        $attempts = 0;
        $currentInsert = $insert;
        $currentInsert['po_number'] = $this->nextPurchaseOrderNumber();

        while ($attempts < 5) {
            try {
                return DB::connection('procurement')->table('purchase_orders')->insertGetId($currentInsert);
            } catch (\Throwable $e) {
                if ($this->isDuplicateKeyException($e)) {
                    $currentInsert['po_number'] = $this->nextPurchaseOrderNumber();
                    $attempts++;
                    continue;
                }

                throw $e;
            }
        }

        throw new \RuntimeException('Unable to save purchase order after retrying.');
    }

    /**
     * Purchase Orders list page (filters, sortable table, add PO modal).
     */
    public function index(Request $request)
    {
        $purchaseOrders = $this->table('purchase_orders')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('purchase_orders.*', 'suppliers.name as supplier_name')
            ->orderBy('purchase_orders.created_at', 'desc')
            ->get();

        // Item breakdown per PO (name/qty/price), used for the View modal's
        // item chips — same "products as chips" pattern used on Suppliers.
        $poItemsByOrder = $this->itemsGroupedByPurchaseOrder($purchaseOrders->pluck('id')->all());

        $suppliers = $this->table('suppliers')
            ->orderBy('created_at', 'desc')
            ->get();

        $warehouses = Warehouse::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'address']);

        $statusCounts = $this->table('purchase_orders')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->mapWithKeys(function ($total, $status) {
                return [strtolower(str_replace([' ', '_'], '-', $status ?? 'pending')) => $total];
            });

        return view('procurement::pages.purchase-orders', compact('purchaseOrders', 'poItemsByOrder', 'suppliers', 'warehouses', 'statusCounts'));
    }

    public function approved(Request $request)
    {
        $approvedPurchaseOrders = $this->table('purchase_orders')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('purchase_orders.*', 'suppliers.name as supplier_name')
            ->where('purchase_orders.status', 'approved')
            ->orderBy('purchase_orders.order_date', 'desc')
            ->get();

        // Attach each PO's item breakdown so the Log Delivery modal can show
        // the purchased items as chips instead of a free-text Item field.
        $itemsByOrder = $this->itemsGroupedByPurchaseOrder($approvedPurchaseOrders->pluck('id')->all());
        $approvedPurchaseOrders->each(function ($po) use ($itemsByOrder) {
            $po->items = $itemsByOrder->get($po->id, collect())->values();
        });

        return response()->json($approvedPurchaseOrders);
    }

    /**
     * Handle the "+ New PO" modal submit (submitAddPO in app-forms.js).
     *
     * A PO can now carry multiple item rows (supplier -> category -> item
     * cascading select per row in the modal). `items` is a JSON-encoded array
     * of {category, name, qty, unitPrice, amount}; the legacy single-item
     * columns (item, brand, unit_price, qty) are kept in sync from that array
     * so every place that still reads them (tables, filters, dashboard "Spend
     * by Brand") keeps working: `item` becomes a joined item-name summary,
     * `brand` holds the first row's category, `qty` is the summed quantity,
     * and `amount` is the summed total.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'po' => 'required|string|max:50',
            'supplier' => 'required|string|max:150',
            'category' => 'nullable|string|max:100',
            'items' => 'required|string',
            'priority' => 'nullable|string|max:20',
            'expected' => 'nullable|date',
            'createdBy' => 'nullable|string|max:150',
            'remarks' => 'nullable|string',
            'reqRef' => 'nullable|string|max:50',
            'warehouse_id' => 'nullable|integer',
        ]);

        $items = $this->sanitizeItemRows($validated['items']);
        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one item with a category, item, and quantity.',
            ]);
        }

        // Only an Approved requisition may become a purchase order. Enforced
        // here so a Pending/Rejected requisition can't be converted even if the
        // button were reachable. (statusOfReference() returns null when the
        // source stores no status — nothing to enforce in that case.)
        $requisitionReference = $validated['reqRef'] ?? null;
        $statusWriter = new RequisitionStatusWriter;
        if (! empty($requisitionReference)) {
            $requisitionStatus = $statusWriter->statusOfReference($requisitionReference);
            if ($requisitionStatus !== null && strcasecmp($requisitionStatus, RequisitionStatusWriter::APPROVED) !== 0) {
                throw ValidationException::withMessages([
                    'reqRef' => sprintf(
                        'Only approved requisitions can be converted to a purchase order (%s is "%s").',
                        $requisitionReference,
                        $requisitionStatus
                    ),
                ]);
            }
        }

        $totalQty = (int) array_sum(array_column($items, 'qty'));
        $totalAmount = (float) array_sum(array_column($items, 'amount'));
        $primaryCategory = $validated['category'] ?? ($items[0]['category'] ?? '');
        $itemSummary = implode(', ', array_column($items, 'name'));

        // Warehouse is optional (the module matches nexora, which chooses the
        // warehouse on the delivery, not the PO). Only look one up when a
        // warehouse_id was actually provided.
        $warehouse = null;
        if (! empty($validated['warehouse_id'])) {
            $warehouse = Warehouse::query()
                ->whereKey((int) $validated['warehouse_id'])
                ->where('status', 'active')
                ->first();
        }

        $supplier = $this->table('suppliers')->where('name', $validated['supplier'])->first();
        $supplierId = $supplier?->id;

        if (! $supplierId) {
            $supplierId = DB::connection('procurement')->table('suppliers')->insertGetId([
                'client_id' => (int) session('employee_client_id'),
                'name' => $validated['supplier'],
                'contact_person' => 'Auto-imported',
                'email' => 'auto@example.com',
                'phone' => 'N/A',
                'address' => 'Auto-imported',
                'brand' => $primaryCategory ?: null,
                'status' => 'active',
                'product_items' => '[]',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $insert = [
            'client_id' => (int) session('employee_client_id'),
            'po_number' => $validated['po'],
            'supplier_id' => $supplierId,
            'qty' => $totalQty,
            'amount' => $totalAmount,
            'status' => 'pending',
            'priority' => strtolower($validated['priority'] ?? 'normal'),
            'order_date' => now()->toDateString(),
            'expected_delivery_date' => $validated['expected'] ?? null,
            'created_by' => $validated['createdBy'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'item' => $itemSummary,
            'brand' => $primaryCategory ?: null,
            'unit_price' => (float) ($items[0]['unit_price'] ?? 0),
            'requisition_reference' => $validated['reqRef'] ?? null,
            'warehouse_id' => $warehouse?->id,
            'delivery_address' => $warehouse?->address,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $poId = $this->insertPurchaseOrder($insert);
        $savedPoNumber = $this->table('purchase_orders')->where('id', $poId)->value('po_number');

        // Per-item category is only stored once the schema migration has added
        // the column; guard so PO creation still works if it hasn't run yet.
        $hasItemCategory = \Illuminate\Support\Facades\Schema::connection('procurement')
            ->hasColumn('purchase_order_items', 'category');

        foreach ($items as $item) {
            // Best-effort link back to the supplier's catalog row (for
            // reporting); the PO item row is still fully self-contained
            // (name/qty/price) if no match is found.
            $supplierProductId = DB::connection('procurement')->table('supplier_products')
                ->where('supplier_id', $supplierId)
                ->where('name', $item['name'])
                ->value('id');

            $itemInsert = [
                'client_id' => (int) session('employee_client_id'),
                'purchase_order_id' => $poId,
                'supplier_product_id' => $supplierProductId,
                'name' => $item['name'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($hasItemCategory) {
                $itemInsert['category'] = $item['category'] ?? null;
            }

            DB::connection('procurement')->table('purchase_order_items')->insert($itemInsert);
        }

        // Creating the PO moves its requisition Approved -> Processing.
        $requisitionStatus = null;
        if (! empty($requisitionReference)) {
            $transition = $statusWriter->transitionByReference($requisitionReference, RequisitionStatusWriter::PROCESSING);
            $requisitionStatus = $transition['ok'] ? $transition['status'] : null;
        }

        $validated['po'] = $savedPoNumber;
        $validated['item'] = $itemSummary;
        $validated['qty'] = $totalQty;
        $validated['amount'] = $totalAmount;
        $validated['category'] = $primaryCategory;
        $validated['items'] = $items;

        return response()->json([
            'status' => 'ok',
            'data' => $validated,
            'id' => $poId,
            'po_number' => $savedPoNumber,
            'requisition_status' => $requisitionStatus,
        ]);
    }

    /**
     * Decode + validate the item-rows JSON from the PO modal: drops any row
     * missing a name or a positive quantity, and recomputes each row's amount
     * server-side (never trusts the client's math).
     */
    private function sanitizeItemRows(string $itemsJson): array
    {
        $decoded = json_decode($itemsJson, true);
        if (! is_array($decoded)) {
            return [];
        }

        $rows = [];
        foreach ($decoded as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $qty = (int) ($row['qty'] ?? 0);
            if ($name === '' || $qty <= 0) {
                continue;
            }

            $unitPrice = (float) ($row['unitPrice'] ?? 0);
            $rows[] = [
                'category' => trim((string) ($row['category'] ?? '')),
                'name' => $name,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'amount' => round($qty * $unitPrice, 2),
            ];
        }

        return $rows;
    }

    public function update(Request $request, $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|max:20',
            'amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? null;
        if ($status !== null) {
            $status = strtolower(trim($status));
            $allowed = ['pending', 'approved', 'rejected', 'cancelled', 'processing', 'completed', 'delivered'];
            if (!in_array($status, $allowed, true)) {
                $status = null;
            }
        }

        $existing = $this->table('purchase_orders')->where('id', $purchaseOrder)->first();

        if (! $existing) {
            abort(404, 'Purchase order not found for this client.');
        }

        // Enforce the PO status lifecycle server-side (not just by hiding
        // buttons). Only these transitions are legal:
        //   pending    -> approved | rejected | cancelled
        //   approved   -> processing | delivered | completed   (via deliveries)
        //   processing -> delivered | completed                (via deliveries)
        //   delivered  -> completed
        //   rejected / cancelled / completed -> (terminal)
        // Cancelling is only possible while Pending.
        $current = strtolower(trim((string) ($existing->status ?? 'pending')));
        if ($status !== null && $status !== $current) {
            $transitions = [
                'pending' => ['approved', 'rejected', 'cancelled', 'processing'],
                'approved' => ['processing', 'delivered', 'completed'],
                'processing' => ['delivered', 'completed'],
                'delivered' => ['completed'],
                'rejected' => [],
                'cancelled' => [],
                'completed' => [],
            ];

            if (! in_array($status, $transitions[$current] ?? [], true)) {
                return response()->json([
                    'status' => 'error',
                    'message' => sprintf('Cannot change purchase order from "%s" to "%s".', $current, $status),
                ], 422);
            }
        }

        $this->table('purchase_orders')->where('id', $purchaseOrder)->update([
            'status' => $status ?? DB::raw('status'),
            'amount' => $validated['amount'] ?? DB::raw('amount'),
            'remarks' => $validated['remarks'] ?? DB::raw('remarks'),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'ok', 'purchase_order_id' => (int) $purchaseOrder]);
    }

    public function destroy($purchaseOrder)
    {
        $this->table('purchase_orders')->where('id', $purchaseOrder)->delete();

        return response()->json(['status' => 'ok']);
    }
}
