<?php

namespace Modules\Manufacturing\Http\Controllers;

use Modules\Manufacturing\Models\WorkOrder;
use Modules\Manufacturing\Models\Worker;
use Modules\Manufacturing\Models\QcSession;
use Modules\Manufacturing\Models\ReworkOrder;
use Modules\Manufacturing\Models\Requisition;
use Modules\Manufacturing\Services\ManufacturingDataService;
use Modules\Manufacturing\Services\BenchmarkTargetService;
use Modules\Manufacturing\Services\DueDateService;
use Modules\Manufacturing\Services\InventoryBridgeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ManufacturingController extends Controller
{
    // ── Page load ────────────────────────────────────────────────────────────
    public function index()
    {
        $data = (new ManufacturingDataService())->loadAll();

        return view('manufacturing::Manufacturing', [
            'workOrders'       => $data['workOrders'],
            'workers'          => $data['workers'],
            'benchmarkTargets' => $data['benchmarkTargets'],
            'qcSessions'       => $data['qcSessions'],
            'reworkOrders'     => $data['reworkOrders'],
            'requisitions'     => $data['requisitions'],
            'statusStyles'     => config('manufacturing.statusStyles'),
            'partStyles'       => config('manufacturing.partStyles'),
            'rangeStyles'      => config('manufacturing.rangeStyles'),
            'tempData'         => array_merge($data, [
                'statusStyles' => config('manufacturing.statusStyles'),
                'partStyles'   => config('manufacturing.partStyles'),
                'rangeStyles'  => config('manufacturing.rangeStyles'),
            ]),
        ]);
    }

    // ── Work orders ──────────────────────────────────────────────────────────
    public function updateOrder(Request $request): JsonResponse
    {
        $partChanges = (array) $request->input('partChanges', []);
        $sendToQC    = (bool)  $request->input('sendToQC', false);
        $cancelOrder = (bool)  $request->input('cancelOrder', false);

        $order = $request->filled('workOrderId')
            ? WorkOrder::with('parts')->find($request->input('workOrderId'))
            : WorkOrder::with('parts')->orderBy('due_date', 'asc')->get()->values()->get((int) $request->input('orderIndex'));
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $this->assertCanOperateWorkOrder($order);

        DB::connection('manufacturing')->transaction(function () use ($order, $partChanges, $sendToQC, $cancelOrder) {
            $partsByPosition = $order->parts->values();

            foreach ($partChanges as $position => $newStatus) {
                $part = $partsByPosition->get((int) $position);
                if (!$part) continue;
                if ($part->status === 'Sourcing' && $newStatus === 'Ready') {
                    $part->update(['status' => 'Ready']);
                }
            }

            $order->refresh()->load('parts');

            $allReady = $order->parts->every(fn ($p) => $p->status === 'Ready');
            if ($allReady && $order->status === 'Building') {
                $order->status = 'Finished';
            }

            if ($sendToQC && in_array($order->status, ['Finished', 'Building'])) {
                $order->status = 'QC Check';
            }

            if  ($cancelOrder) {
                $order->status = 'Cancelled';
            }

            $order->save();
        });

        return response()->json(['success' => true]);
    }

    // ── QC benchmark ─────────────────────────────────────────────────────────
    public function updateQC(Request $request): JsonResponse
    {
        $woId    = $request->input('woId');
        $results = $request->input('results', []);

        $order = WorkOrder::find($woId);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Work order not found.'], 404);
        }

        $this->assertCanOperateWorkOrder($order);

        if ($order->status !== 'QC Check') {
            return response()->json(['success' => false, 'message' => 'Only work orders in QC Check can be released from quality control.'], 422);
        }

        $range = $order->range ?? null;

        $targetService   = new BenchmarkTargetService();
        $allowedVerdicts = ['Pass', 'Warn', 'Fail', ''];

        $cleanResults = array_map(function ($r) use ($targetService, $range, $allowedVerdicts) {
            $checkId = (string) ($r['checkId'] ?? '');
            $value   = isset($r['value']) && $r['value'] !== null ? (float) $r['value'] : null;
            $verdict = $r['verdict'] ?? '';

            if ($verdict === '' && $value !== null) {
                $verdict = $targetService->verdictFor($checkId, $range, $value);
            }

            return [
                'checkId' => $checkId,
                'value'   => $value,
                'verdict' => in_array($verdict, $allowedVerdicts) ? $verdict : '',
                'note'    => (string) ($r['note'] ?? ''),
            ];
        }, $results);

        $flagged = array_values(array_filter($cleanResults, fn ($r) => in_array($r['verdict'], ['Warn', 'Fail'], true)));

        DB::connection('manufacturing')->transaction(function () use ($woId, $cleanResults, $range, $targetService, $order, $flagged) {
            $session = QcSession::where('wo_id', $woId)->first();
            if (!$session) {
                $session = QcSession::create(['wo_id' => $woId, 'build_type' => $range ?? 'mid-range', 'tech' => '']);
            }

            $session->results()->delete();
            foreach ($cleanResults as $r) {
                $session->results()->create([
                    'check_id' => $r['checkId'],
                    'value'    => $r['value'],
                    'verdict'  => $r['verdict'],
                    'note'     => $r['note'],
                ]);
            }

            if (count($flagged) > 0 && !ReworkOrder::where('wo_id', $woId)->exists()) {
                $targets = $targetService->targetsFor($range);

                $rwCount  = ReworkOrder::count() + 1;
                $reworkId = 'RW-' . session('employee_client_id') . '-' . str_pad((string) $rwCount, 4, '0', STR_PAD_LEFT);

                $rework = ReworkOrder::create([
                    'id'                       => $reworkId,
                    'wo_id'                    => $woId,
                    'build_name'               => $order->name ?? $woId,
                    'assigned_tech'            => $order->assigned ?? '',
                    'raised_by'                => $order->assigned ?? '',
                    'raised_date'              => now()->format('M d, Y'),
                    'status'                   => 'In Rework',
                    'priority'                 => 'Medium',
                    'notes'                    => 'Auto-created from QC benchmark flags.',
                    'escalated_to_inventory' => false,
                ]);

                foreach ($flagged as $r) {
                    $def = $targets[$r['checkId']] ?? null;
                    $rework->failedChecks()->create([
                        'check_id'   => $r['checkId'],
                        'check_name' => $def['name'] ?? $r['checkId'],
                        'verdict'    => $r['verdict'],
                        'result'     => $r['value'] !== null
                            ? number_format($r['value']) . ' ' . ($def['unit'] ?? '')
                            : '—',
                        'target'     => ($def['operator'] ?? '') . ' ' . number_format($def['target'] ?? 0) . ' ' . ($def['unit'] ?? ''),
                        'reason'     => $r['note'] ?: 'Flagged during QC benchmark',
                    ]);
                }
            }

            if ($flagged) {
                $order->update(['status' => 'Rework']);
            }
        });

        if ($flagged) {
            return response()->json([
                'success' => true,
                'status' => 'Rework',
                'message' => 'QC flagged issues. A rework order has been created.',
            ]);
        }

        if (! $cleanResults || collect($cleanResults)->contains(fn (array $result) => $result['verdict'] !== 'Pass')) {
            return response()->json([
                'success' => true,
                'status' => 'QC Check',
                'message' => 'QC results were saved. Every check must pass before this order can be released.',
            ]);
        }

        try {
            $fulfillmentOrderId = $this->releaseToFulfillment($order);
            $order->update(['status' => 'Completed']);

            return response()->json([
                'success' => true,
                'status' => 'Completed',
                'fulfillmentOrderId' => $fulfillmentOrderId,
                'message' => $fulfillmentOrderId
                    ? 'QC passed. The order is now ready for packing in Order Fulfillment.'
                    : 'QC passed. The manufacturing work order is complete.',
            ]);
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    // ── Rework ───────────────────────────────────────────────────────────────
    public function updateRework(Request $request): JsonResponse
    {
        $reworkIndex = (int) $request->input('reworkIndex');
        $rw = ReworkOrder::orderBy('id')->get()->values()->get($reworkIndex);

        if (!$rw) {
            return response()->json(['success' => false, 'message' => 'Rework order not found.'], 404);
        }

        if ($request->has('status'))     $rw->status   = $request->input('status');
        if ($request->has('priority'))   $rw->priority = $request->input('priority');
        if ($request->has('notes'))      $rw->notes    = $request->input('notes');
        if ($request->input('escalate')) $rw->escalated_to_inventory = true;
        $rw->save();

        return response()->json(['success' => true]);
    }

    public function addReworkPart(Request $request): JsonResponse
    {
        $reworkIndex = (int) $request->input('reworkIndex');
        $part        = $request->input('part', []);

        $rw = ReworkOrder::orderBy('id')->get()->values()->get($reworkIndex);
        if (!$rw) {
            return response()->json(['success' => false, 'message' => 'Rework order not found.'], 404);
        }

        $rw->requiredParts()->create([
            'name'   => (string) ($part['name']   ?? ''),
            'status' => (string) ($part['status'] ?? 'Sourcing'),
            'eta'    => $part['eta'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateReworkPart(Request $request): JsonResponse
    {
        $reworkIndex = (int) $request->input('reworkIndex');
        $partIndex   = (int) $request->input('partIndex');
        $part        = $request->input('part', []);

        $rw = ReworkOrder::with('requiredParts')->orderBy('id')->get()->values()->get($reworkIndex);
        if (!$rw) {
            return response()->json(['success' => false, 'message' => 'Rework order not found.'], 404);
        }

        $rp = $rw->requiredParts->values()->get($partIndex);
        if (!$rp) {
            return response()->json(['success' => false, 'message' => 'Part not found.'], 404);
        }

        $rp->update([
            'name'   => (string) ($part['name']   ?? ''),
            'status' => (string) ($part['status'] ?? 'Sourcing'),
            'eta'    => $part['eta'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Analytics ────────────────────────────────────────────────────────────
    public function addQcNote(Request $request): JsonResponse
    {
        $woId = $request->input('woId');
        $note = $request->input('note', '');

        $session = QcSession::where('wo_id', $woId)->first();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found.'], 404);
        }

        $session->results()->create([
            'check_id' => null,
            'value'    => null,
            'verdict'  => '',
            'note'     => $note,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Workers ──────────────────────────────────────────────────────────────
    public function addWorker(Request $request): JsonResponse
    {
        $this->assertCanManageManufacturing();

        Worker::create([
            'name'  => $request->input('name'),
            'role'  => $request->input('role'),
            'notes' => $request->input('notes', ''),
        ]);
        return response()->json(['success' => true]);
    }

    public function updateWorker(Request $request): JsonResponse
    {
        $this->assertCanManageManufacturing();

        $worker = Worker::find($request->input('id'));
        if (!$worker) {
            return response()->json(['success' => false, 'message' => 'Worker not found.'], 404);
        }
        $worker->update([
            'name'  => $request->input('name'),
            'role'  => $request->input('role'),
            'notes' => $request->input('notes', ''),
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteWorker(Request $request): JsonResponse
    {
        $this->assertCanManageManufacturing();

        $worker = Worker::find($request->input('id'));
        if (!$worker) {
            return response()->json(['success' => false, 'message' => 'Worker not found.'], 404);
        }
        $worker->delete();
        return response()->json(['success' => true]);
    }

    public function assignWorker(Request $request): JsonResponse
    {
        $this->assertCanManageManufacturing();

        $validated = $request->validate([
            'orderId' => ['required', 'string'],
            'workerId' => ['required', 'integer'],
        ]);

        $order = WorkOrder::find($validated['orderId']);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Work order not found.'], 404);
        }

        $worker = DB::connection('hr')->table('employees')
            ->where('id', $validated['workerId'])
            ->where('client_id', $order->client_id)
            ->where('approval_status', 'Active')
            ->where(function ($query): void {
                $query->whereRaw("LOWER(COALESCE(department, '')) LIKE ?", ['%production%'])
                    ->orWhereRaw("LOWER(COALESCE(position, '')) LIKE ?", ['%production%'])
                    ->orWhereRaw("LOWER(COALESCE(position, '')) LIKE ?", ['%manufacturing%']);
            })
            ->first();

        if (! $worker) {
            return response()->json(['success' => false, 'message' => 'Select an active Production Management employee.'], 422);
        }

        $name = trim(implode(' ', array_filter([$worker->first_name, $worker->middle_name, $worker->last_name, $worker->suffix])));
        $changes = [
            'assigned_employee_id' => $worker->id,
            'assigned' => $name,
        ];
        if ($order->status === 'Pending') {
            $changes['status'] = 'Building';
        }
        $order->update($changes);

        return response()->json(['success' => true, 'status' => $changes['status'] ?? $order->status]);
    }

    // ── Requisitions / inventory ─────────────────────────────────────────────
    public function sendToInventory(Request $request): JsonResponse
    {
        $woId    = $request->input('woId');
        $order   = WorkOrder::find($woId);

        // Put the defective part straight into Inventory's defect table and pull a
        // fresh unit from stock (no requisition).
        (new InventoryBridgeService())->logDefectAndPullStock(
            (string) $woId,
            (string) $request->input('partName'),
            (int) $request->input('quantity', 1),
            $order->client_id ?? null,
            (string) $request->input('requestedBy')
        );

        return response()->json(['success' => true, 'message' => 'Defective part logged to Inventory and a fresh unit pulled from stock.']);
    }

    // ── Work orders (cont.) ──────────────────────────────────────────────────
    public function cancelOrder(Request $request): JsonResponse
    {
        $order = $request->filled('workOrderId')
            ? WorkOrder::find($request->input('workOrderId'))
            : WorkOrder::orderBy('due_date', 'asc')->get()->values()->get((int) $request->input('orderIndex'));

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $this->assertCanOperateWorkOrder($order);

        if ($order->status === 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'Order is already cancelled.'], 422);
        }

        $order->update(['status' => 'Cancelled']);

        return response()->json(['success' => true]);
    }

    // ── E-commerce intake ────────────────────────────────────────────────────
    public function receiveOrderFromEcommerce(Request $request): JsonResponse
    {
        $orderDate = $request->has('orderDate')
            ? \Carbon\Carbon::parse($request->input('orderDate'))
            : now();

        $dueDate = (new DueDateService())->calculate($orderDate);

        $order = WorkOrder::create([
            'id'       => $request->input('id'),
            'name'     => $request->input('name'),
            'specs'    => $request->input('specs'),
            'status'   => $request->input('status', 'Pending'),
            'due_date' => $dueDate->toDateString(),
            'source'   => $request->input('source'),
            'fulfillment_order_id' => $request->input('fulfillmentOrderId'),
            'assigned' => $request->input('assigned'),
            'range'    => $request->input('range'),
        ]);

        foreach ($request->input('parts', []) as $part) {
            $order->parts()->create([
                'product_id' => $part['productId'] ?? null,
                'name'       => $part['name'] ?? '',
                'category'   => $part['category'] ?? '',
                'status'     => $part['status'] ?? 'Sourcing',
            ]);
        }

        return response()->json([
            'success' => true,
            'id'      => $order->id,
            'dueDate' => $dueDate->toDateString(),
        ]);
    }

    /**
     * An assigned production worker can only progress their own work order.
     * Production managers, supervisors, and quality staff retain the ability
     * to coordinate the client-wide production queue.
     */
    private function assertCanOperateWorkOrder(WorkOrder $order): void
    {
        if (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin') {
            return;
        }

        if ($this->canManageManufacturing() || $this->isQualityEmployee()) {
            return;
        }

        abort_unless(
            $order->assigned_employee_id && (int) $order->assigned_employee_id === (int) session('employee_id'),
            403,
            'You can only progress work orders assigned to you.'
        );
    }

    private function assertCanManageManufacturing(): void
    {
        if (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin') {
            return;
        }

        abort_unless($this->canManageManufacturing(), 403, 'Only a production manager or supervisor can assign staff.');
    }

    private function canManageManufacturing(): bool
    {
        $position = strtolower((string) session('employee_position'));

        return session('employee_role') === 'admin'
            || str_contains($position, 'manager')
            || str_contains($position, 'supervisor');
    }

    private function isQualityEmployee(): bool
    {
        return str_contains(strtolower((string) session('employee_position')), 'quality')
            || str_contains(strtolower((string) session('employee_department')), 'quality');
    }

    /**
     * Release a passed manufacturing build into the dedicated fulfillment
     * database. Non-ecommerce/internal work orders have no linked order and
     * simply complete in Manufacturing.
     */
    private function releaseToFulfillment(WorkOrder $workOrder): ?string
    {
        $fulfillmentOrderId = $workOrder->fulfillment_order_id;

        if (! $fulfillmentOrderId && preg_match('/^Ecommerce\s+(.+)$/i', (string) $workOrder->source, $matches)) {
            $fulfillmentOrderId = trim($matches[1]);
            $workOrder->update(['fulfillment_order_id' => $fulfillmentOrderId]);
        }

        if (! $fulfillmentOrderId) {
            return null;
        }

        $fulfillment = DB::connection('order_fulfillment')->table('orders')
            ->where('id', $fulfillmentOrderId)
            ->where('client_id', $workOrder->client_id)
            ->first();

        if (! $fulfillment) {
            throw new RuntimeException('The linked Order Fulfillment order could not be found for this work order.');
        }

        if (in_array(strtoupper((string) $fulfillment->status), ['CANCELLED', 'DELIVERED', 'RETURNED'], true)) {
            throw new RuntimeException('The linked Order Fulfillment order can no longer be released for packing.');
        }

        if (strtoupper((string) $fulfillment->status) === 'NEW') {
            DB::connection('order_fulfillment')->table('orders')
                ->where('id', $fulfillmentOrderId)
                ->where('client_id', $workOrder->client_id)
                ->update(['status' => 'PACKING', 'updated_at' => now()]);
        }

        return $fulfillmentOrderId;
    }
}
