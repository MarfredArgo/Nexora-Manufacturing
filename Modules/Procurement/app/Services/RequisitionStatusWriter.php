<?php

namespace Modules\Procurement\Services;

use Illuminate\Support\Facades\DB;

/**
 * Writes requisition status back to the external requisition sources
 * (Inventory, Order Fulfillment). Procurement owns these transitions:
 *
 *   Pending    -> Approved | Rejected   (Procurement approves/rejects)
 *   Approved   -> Processing            (a PO was created for it)
 *   Processing -> Completed             (its delivery was marked Completed)
 *   Rejected / Completed are terminal.
 *
 * Casing matches the column default ('Pending'), i.e. title case.
 *
 * A requisition may span several rows sharing one req_id; every transition
 * moves the whole group together inside a single transaction.
 */
class RequisitionStatusWriter
{
    public const PENDING = 'Pending';
    public const APPROVED = 'Approved';
    public const REJECTED = 'Rejected';
    public const PROCESSING = 'Processing';
    public const COMPLETED = 'Completed';

    /** Requisition sources, in lookup order. */
    private const CONNECTIONS = ['order_fulfillment', 'inventory'];

    /** Allowed target statuses, keyed by the lowercased current status. */
    private const TRANSITIONS = [
        'pending' => [self::APPROVED, self::REJECTED],
        'approved' => [self::PROCESSING],
        'processing' => [self::COMPLETED],
        'rejected' => [],
        'completed' => [],
    ];

    /** Every status Procurement is allowed to ask for. */
    public static function isKnownStatus(string $status): bool
    {
        return in_array($status, [
            self::PENDING, self::APPROVED, self::REJECTED, self::PROCESSING, self::COMPLETED,
        ], true);
    }

    /** Normalise arbitrary input ("approved", "APPROVED") to title case. */
    public static function normalise(string $status): string
    {
        return ucfirst(strtolower(trim($status)));
    }

    /**
     * Locate a requisition by primary key or by its requisition number,
     * across the configured sources.
     *
     * @return array{connection:\Illuminate\Database\Connection,source:string,refColumn:?string,row:object,hasStatus:bool}|null
     */
    private function locate($id = null, ?string $reference = null): ?array
    {
        foreach (self::CONNECTIONS as $source) {
            try {
                $connection = DB::connection($source);
                $schema = $connection->getSchemaBuilder();

                if (! $schema->hasTable('requisitions')) {
                    continue;
                }

                // Inventory uses req_id; Order Fulfillment uses req_number.
                $refColumn = $schema->hasColumn('requisitions', 'req_id')
                    ? 'req_id'
                    : ($schema->hasColumn('requisitions', 'req_number') ? 'req_number' : null);

                $query = $connection->table('requisitions');
                if ($id !== null) {
                    $query->where('id', $id);
                } elseif ($refColumn !== null && $reference !== null) {
                    $query->where($refColumn, $reference);
                } else {
                    continue;
                }

                $row = $query->first();
                if (! $row) {
                    continue;
                }

                return [
                    'connection' => $connection,
                    'source' => $source,
                    'refColumn' => $refColumn,
                    'row' => $row,
                    'hasStatus' => $schema->hasColumn('requisitions', 'status'),
                ];
            } catch (\Throwable $e) {
                // Skip broken or unavailable external connections.
            }
        }

        return null;
    }

    /**
     * Current stored status, or null when the source has no status column
     * (Order Fulfillment) or the requisition can't be found.
     */
    public function statusOfReference(string $reference): ?string
    {
        $found = $this->locate(null, $reference);

        if (! $found || ! $found['hasStatus']) {
            return null;
        }

        return trim((string) ($found['row']->status ?? self::PENDING));
    }

    public function transitionById($id, string $target): array
    {
        return $this->apply($this->locate($id, null), $target);
    }

    public function transitionByReference(string $reference, string $target): array
    {
        return $this->apply($this->locate(null, $reference), $target);
    }

    /**
     * @return array{ok:bool,status?:string,updated?:int,code?:int,message?:string}
     */
    private function apply(?array $found, string $target): array
    {
        if (! $found) {
            return ['ok' => false, 'code' => 404, 'message' => 'Requisition not found.'];
        }

        if (! $found['hasStatus']) {
            // Order Fulfillment has no status column — there is nothing to
            // persist and no lifecycle to enforce. Treat it as a benign no-op
            // success (not a 409) so the caller's optimistic UI update stands
            // instead of surfacing an error. `persisted:false` signals that the
            // change won't survive a reload for this source.
            return ['ok' => true, 'status' => $target, 'updated' => 0, 'persisted' => false];
        }

        $current = trim((string) ($found['row']->status ?? self::PENDING));

        // Idempotent: asking for the status it already has is a no-op success.
        if (strcasecmp($current, $target) === 0) {
            return ['ok' => true, 'status' => $target, 'updated' => 0];
        }

        $allowed = self::TRANSITIONS[strtolower($current)] ?? [];
        if (! in_array($target, $allowed, true)) {
            return [
                'ok' => false,
                'code' => 422,
                'message' => sprintf('Cannot change requisition from "%s" to "%s".', $current, $target),
            ];
        }

        $connection = $found['connection'];
        $refColumn = $found['refColumn'];
        $reference = $refColumn !== null ? ($found['row']->{$refColumn} ?? null) : null;

        $query = $connection->table('requisitions');

        // Move every row in the req_id group together; fall back to the single
        // row when there's no reference column.
        if ($refColumn !== null && $reference !== null && $reference !== '') {
            $query->where($refColumn, $reference);
        } else {
            $query->where('id', $found['row']->id);
        }

        // Deliberately a single UPDATE rather than an explicit
        // beginTransaction/commit block. One UPDATE is already atomic in
        // PostgreSQL — the whole req_id group moves all-or-nothing — and these
        // requisition databases sit behind a pooled (PgBouncer/Neon) endpoint
        // where a multi-statement transaction can have its BEGIN and COMMIT
        // land on different backends, silently discarding the write.
        $updated = $query->update([
            'status' => $target,
            'updated_at' => now(),
        ]);

        return ['ok' => true, 'status' => $target, 'updated' => $updated];
    }
}
