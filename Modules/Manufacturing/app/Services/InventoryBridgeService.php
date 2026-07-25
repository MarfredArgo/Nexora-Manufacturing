<?php

namespace Modules\Manufacturing\Services;

use Illuminate\Support\Facades\DB;

/**
 * Write-bridge from Manufacturing into the Inventory database (read/writes the
 * existing inventory tables only — creates no tables, touches no other module's
 * code). Every operation is defensive: if the referenced row cannot be matched
 * it no-ops rather than guessing, so a wrong linkage assumption cannot corrupt
 * Inventory data.
 *
 * Linkage assumptions (verify against live data before relying on them):
 *   - order_reservations.order_reference == work_orders.id
 *   - work_order_parts.product_id resolves to items.id (numeric) or items.sku,
 *     falling back to items.name.
 */
class InventoryBridgeService
{
    private function inv()
    {
        return DB::connection('inventory');
    }

    private function resolveItemId(?string $productId, ?string $partName, ?int $clientId): ?int
    {
        $base = fn () => tap($this->inv()->table('items'), function ($q) use ($clientId) {
            if ($clientId) $q->where('client_id', $clientId);
        });

        if ($productId !== null && $productId !== '') {
            if (ctype_digit($productId) && ($id = $base()->where('id', (int) $productId)->value('id'))) {
                return (int) $id;
            }
            if ($id = $base()->where('sku', $productId)->value('id')) {
                return (int) $id;
            }
        }
        if ($partName && ($id = $base()->where('name', $partName)->value('id'))) {
            return (int) $id;
        }
        return null;
    }

    /**
     * #1 — A build component was marked Ready in the status modal: confirm its
     * reservation (deduct from reserved) and consume the physical unit.
     */
    public function consumeReservationForPart(string $woId, array $part, ?int $clientId): void
    {
        try {
            $itemId = $this->resolveItemId($part['product_id'] ?? null, $part['name'] ?? null, $clientId);
            if (!$itemId) return;

            $this->inv()->transaction(function () use ($woId, $itemId) {
                $res = $this->inv()->table('order_reservations')
                    ->where('order_reference', $woId)
                    ->where('item_id', $itemId)
                    ->whereNull('confirmed_at')
                    ->whereNull('cancelled_at')
                    ->first();
                if (!$res) return;

                $sl = $this->inv()->table('stock_levels')
                    ->where('item_id', $itemId)
                    ->where('warehouse_id', $res->warehouse_id)
                    ->first();
                if ($sl) {
                    $this->inv()->table('stock_levels')->where('id', $sl->id)->update([
                        'reserved_quantity' => max(0, $sl->reserved_quantity - $res->quantity),
                        'stock'             => max(0, $sl->stock - $res->quantity),
                        'updated_at'        => now(),
                    ]);
                }

                $this->inv()->table('order_reservations')->where('id', $res->id)->update([
                    'status'       => 'confirmed',
                    'confirmed_at' => now(),
                    'updated_at'   => now(),
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * #2 — A QC-failed part is sent back: log it straight into the Inventory
     * defects table. Grabbing a replacement is a separate, explicit action
     * (see grabReplacementFromStock), triggered per part from the rework screen.
     */
    public function logDefect(string $woId, string $partName, int $qty, ?int $clientId, string $createdBy): void
    {
        try {
            $this->inv()->table('defects')->insert([
                'client_id'   => $clientId,
                'part_name'   => $partName,
                'quantity'    => $qty,
                'description' => "Returned from Manufacturing QC for work order {$woId}.",
                'status'      => 'Pending',
                'source'      => 'manufacturing',
                'source_id'   => $woId,
                'created_by'  => $createdBy,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Available (unreserved) on-hand quantity in Inventory for a part, summed
     * across warehouses. Used to enable/disable the "Grab from Stock" button.
     */
    public function availableStockFor(?string $partName, ?int $clientId): int
    {
        try {
            $itemId = $this->resolveItemId(null, $partName, $clientId);
            if (!$itemId) return 0;

            $row = $this->inv()->table('stock_levels')
                ->where('item_id', $itemId)
                ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
                ->selectRaw('COALESCE(SUM(stock), 0) AS s, COALESCE(SUM(reserved_quantity), 0) AS r')
                ->first();

            return max(0, (int) ($row->s ?? 0) - (int) ($row->r ?? 0));
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
    }

    /**
     * Pull one (or $qty) fresh unit(s) of a replacement part from stock.
     * Returns false when no unreserved stock is available (button stays locked).
     */
    public function grabReplacementFromStock(?string $partName, int $qty, ?int $clientId): bool
    {
        try {
            $itemId = $this->resolveItemId(null, $partName, $clientId);
            if (!$itemId) return false;

            $sl = $this->inv()->table('stock_levels')
                ->where('item_id', $itemId)
                ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
                ->whereRaw('stock - reserved_quantity >= ?', [$qty])
                ->orderByRaw('(stock - reserved_quantity) DESC')
                ->first();
            if (!$sl) return false; // no unreserved stock -> caller keeps it locked

            $this->inv()->table('stock_levels')->where('id', $sl->id)->update([
                'stock'      => max(0, $sl->stock - $qty),
                'updated_at' => now(),
            ]);
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
