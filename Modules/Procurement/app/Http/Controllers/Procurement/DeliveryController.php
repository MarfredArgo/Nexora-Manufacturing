<?php

namespace Modules\Procurement\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\Delivery;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Supplier;
use Modules\Procurement\Services\RequisitionStatusWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class DeliveryController extends Controller
{
    private function table(string $name)
    {
        $query = DB::connection('procurement')->table($name);

        if (! (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin')) {
            $query->where($name.'.client_id', (int) session('employee_client_id'));
        }

        return $query;
    }

    private function nextAvailableShipmentNumber(string $requestedNumber): string
    {
        if (! preg_match('/^(.*?)(\d+)$/', $requestedNumber, $matches)) {
            return $requestedNumber . '-' . now()->format('YmdHis');
        }

        $prefix = $matches[1];
        $width = strlen($matches[2]);
        $highestSequence = Delivery::query()
            ->where('shipment_number', 'like', $prefix . '%')
            ->pluck('shipment_number')
            ->map(function (string $number) use ($prefix): int {
                return preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $number, $parts)
                    ? (int) $parts[1]
                    : 0;
            })
            ->max() ?? 0;

        return $prefix . str_pad($highestSequence + 1, $width, '0', STR_PAD_LEFT);
    }

    private function createDeliveryWithUniqueShipmentNumber(array $attributes): Delivery
    {
        if (Delivery::query()->where('shipment_number', $attributes['shipment_number'])->exists()) {
            $attributes['shipment_number'] = $this->nextAvailableShipmentNumber($attributes['shipment_number']);
        }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                return Delivery::create($attributes);
            } catch (QueryException $exception) {
                $message = $exception->getMessage();

                if (! str_contains($message, 'deliveries_shipment_number_key')
                    && ! str_contains($message, 'shipment_number')) {
                    throw $exception;
                }

                $attributes['shipment_number'] = $this->nextAvailableShipmentNumber($attributes['shipment_number']);
            }
        }

        throw new \RuntimeException('Unable to allocate a unique shipment number.');
    }

    public function index(): View
    {
        // Raw tenant-scoped query with joins so the table gets flat
        // supplier_name / po_number columns (the Blade reads $d->supplier_name
        // and $d->po_number; the eager-loaded model relations don't expose
        // those, which is why supplier and PO showed as "—").
        $deliveries = $this->table('deliveries')
            ->leftJoin('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->leftJoin('purchase_orders', 'deliveries.purchase_order_id', '=', 'purchase_orders.id')
            ->select('deliveries.*', 'suppliers.name as supplier_name', 'purchase_orders.po_number as po_number')
            ->orderBy('deliveries.delivery_date')
            ->get();

        $counts = [
            'all' => $deliveries->count(),
            'pending' => $deliveries->where('status', 'pending')->count(),
            'shipment' => $deliveries->where('status', 'shipment')->count(),
            'intransit' => $deliveries->whereIn('status', ['intransit', 'shipment'])->count(),
            'delivered' => $deliveries->where('status', 'delivered')->count(),
            'complete' => $deliveries->whereIn('status', ['complete', 'delivered'])->count(),
            'delayed' => $deliveries->where('status', 'delayed')->count(),
        ];

        // Next shipment sequence, derived from the highest existing
        // "SHP-#####" number, so the "+ Log Delivery" form always
        // auto-fills the true next number instead of a hardcoded guess.
        // Only the trailing number group — never the year — so "SHP-2026-0231"
        // yields 231, not 20260231.
        $nextShipmentSeq = ($deliveries->pluck('shipment_number')
            ->map(fn ($n) => preg_match('/(\d+)$/', (string) $n, $m) ? (int) $m[1] : 0)
            ->max() ?? 0) + 1;

        return view('procurement::pages.deliveries', compact('deliveries', 'counts', 'nextShipmentSeq'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dr' => ['required', 'string', 'max:255'],
            'po' => ['required', 'string', 'max:255'],
            'supplier' => ['required', 'string', 'max:255'],
            'delDate' => ['required', 'date'],
            'items' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:intransit,delayed'],
            'remarks' => ['nullable', 'string'],
            'carrier' => ['nullable', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'integer'],
        ]);

        $warehouse = null;
        if (! empty($data['warehouse_id'])) {
            $warehouse = \Modules\Inventory\Models\Warehouse::query()
                ->whereKey((int) $data['warehouse_id'])
                ->where('status', 'active')
                ->first();
        }

        $purchaseOrder = PurchaseOrder::where('po_number', $data['po'])->first();

        // Server-side guard: a PO can only be logged in Deliveries once it is
        // Approved (or already Processing from a prior delivery). Pending,
        // Rejected and Cancelled POs must be rejected here, not just hidden in
        // the UI.
        if ($purchaseOrder && ! in_array(strtolower((string) $purchaseOrder->status), ['approved', 'processing'], true)) {
            return response()->json([
                'message' => 'Only approved purchase orders can be logged in deliveries.',
            ], 422);
        }

        $supplier = Supplier::where('name', $data['supplier'])->first();

        if (! $supplier) {
            $supplier = Supplier::create([
                'name' => $data['supplier'],
                'contact_person' => 'Pending',
                'email' => null,
                'phone' => null,
                'address' => null,
                'category' => 'General Procurement',
                'status' => 'active',
            ]);
        }

        $delivery = $this->createDeliveryWithUniqueShipmentNumber([
            'client_id' => (int) session('employee_client_id'),
            'shipment_number' => $data['dr'],
            'purchase_order_id' => $purchaseOrder?->id,
            'supplier_id' => $supplier->id,
            'status' => $data['status'] ?? 'intransit',
            'qty' => (int) $data['qty'],
            'qty_expected' => (int) $data['qty'],
            'items' => $data['items'],
            'remarks' => $data['remarks'] ?? null,
            'carrier' => $data['carrier'] ?? null,
            'delivery_date' => $data['delDate'],
            'estimated_arrival' => $purchaseOrder?->expected_delivery_date,
            // The selected warehouse's name is stored in the deliver_to_warehouse
            // column so the shipment record shows where it's being delivered.
            'deliver_to_warehouse' => $warehouse?->name,
        ]);

        if ($purchaseOrder) {
            // Logging a delivery moves the PO to Processing. The linked
            // requisition's status is derived from the PO status when the
            // Requisitions page renders (see RequisitionController@index), so it
            // does not need to be written here.
            $purchaseOrder->update(['status' => 'processing']);
        }

        return response()->json([
            'success' => true,
            'data' => $delivery,
            'shipment_number' => $delivery->shipment_number,
            'delete_url' => route('procurement.deliveries.destroy', $delivery),
        ], 201);
    }

    public function update(Request $request, Delivery $delivery): JsonResponse
    {
        $data = $request->validate([
            'ship' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:pending,scheduled,intransit,delayed,delivered,completed,cancelled'],
            'carrier' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);

        $purchaseOrder = ($data['po'] ?? null)
            ? PurchaseOrder::where('po_number', $data['po'])->first()
            : $delivery->purchaseOrder;
        $supplier = ($data['supplier'] ?? null)
            ? Supplier::where('name', $data['supplier'])->first()
            : $delivery->supplier;

        if (($data['supplier'] ?? null) && ! $supplier) {
            $supplier = Supplier::create([
                'name' => $data['supplier'],
                'contact_person' => 'Pending',
                'email' => null,
                'phone' => null,
                'address' => null,
                'category' => 'General Procurement',
                'status' => 'active',
            ]);
        }

        $status = strtolower((string) ($data['status'] ?? $delivery->status));
        $stageMap = [
            'pending' => 0,
            'scheduled' => 0,
            'intransit' => 2,
            'delayed' => 2,
            'delivered' => 3,
            'completed' => 4,
            'cancelled' => 0,
        ];

        $updateData = [
            'stage' => $stageMap[$status] ?? 0,
            'status' => $status,
        ];

        if ($data['ship'] ?? null) {
            $updateData['shipment_number'] = $data['ship'];
        }
        if ($data['po'] ?? null) {
            $updateData['purchase_order_id'] = $purchaseOrder?->id;
        }
        if ($data['supplier'] ?? null) {
            $updateData['supplier_id'] = $supplier?->id;
        }
        if ($data['date'] ?? null) {
            $updateData['delivery_date'] = $data['date'];
        }
        $remarks = $data['note'] ?? $data['remarks'] ?? null;
        if ($remarks !== null) {
            $updateData['remarks'] = $remarks;
        }

        $delivery->update($updateData);

        // Cascade the shipment status back to the parent PO. The linked
        // requisition's status is derived from the PO status at render time
        // (RequisitionController@index), so updating the PO is enough.
        //   intransit / delayed -> PO Processing
        //   delivered           -> PO Delivered
        //   completed           -> PO Completed
        $poStatusFromDelivery = [
            'intransit' => 'processing',
            'delayed' => 'processing',
            'delivered' => 'delivered',
            'completed' => 'completed',
        ];
        if ($purchaseOrder && isset($poStatusFromDelivery[$status])) {
            $purchaseOrder->update(['status' => $poStatusFromDelivery[$status]]);
        }

        // Completing the shipment completes the requisition behind the PO
        // (Processing -> Completed). Best-effort: a source without a status
        // column simply reports not-ok and is ignored.
        $requisitionStatus = null;
        if ($purchaseOrder && $status === 'completed' && ! empty($purchaseOrder->requisition_reference)) {
            $transition = (new RequisitionStatusWriter)->transitionByReference(
                $purchaseOrder->requisition_reference,
                RequisitionStatusWriter::COMPLETED
            );
            $requisitionStatus = $transition['ok'] ? $transition['status'] : null;
        }

        return response()->json([
            'success' => true,
            'data' => $delivery,
            'requisition_status' => $requisitionStatus,
        ]);
    }

    public function destroy(Delivery $delivery): JsonResponse
    {
        $delivery->delete();

        return response()->json(['success' => true]);
    }
}
