<?php

namespace App\Console\Commands;

use App\Models\ProcurementDeptCompany;
use App\Models\ProcurementDeptRequisition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncProcurementData extends Command
{
    protected $signature = 'sync:procurement';

    protected $description = 'Pull procurement data into local mirror tables';

    protected const CHUNK_SIZE = 500;

    public function handle(): int
    {
        $this->info('Syncing procurement_dept -> local tables...');

        // procurement_dept_companies columns match
        // ProcurementDeptCompany::$fillable: source_id, company_name,
        // industry, company_email, phone_no, admin_name, admin_user_id,
        // employee_table_name, status, source_created_at, source_updated_at.
        try {
            $companiesSynced = $this->syncTable(
                'companies',
                ProcurementDeptCompany::class,
                fn($row) => [
                    'source_id' => $row->id,
                    'company_name' => $row->company_name ?? $row->name ?? '',
                    'industry' => $row->industry ?? null,
                    'company_email' => $row->company_email ?? $row->email ?? null,
                    'phone_no' => $row->phone_no ?? $row->phone ?? null,
                    'admin_name' => $row->admin_name ?? null,
                    'admin_user_id' => $row->admin_user_id ?? null,
                    'employee_table_name' => $row->employee_table_name ?? null,
                    'status' => $row->status ?? null,
                    'source_created_at' => $row->created_at ?? now(),
                    'source_updated_at' => $row->updated_at ?? now(),
                ],
                [
                    'company_name',
                    'industry',
                    'company_email',
                    'phone_no',
                    'admin_name',
                    'admin_user_id',
                    'employee_table_name',
                    'status',
                    'source_created_at',
                    'source_updated_at',
                ]
            );
            $this->info("Synced {$companiesSynced} companies.");
        } catch (\Throwable $e) {
            $this->warn('Companies sync failed: ' . $e->getMessage());
        }

        // procurement_dept_requisitions columns match
        // ProcurementDeptRequisition::$fillable: source_id, req_number, item,
        // qty, uom, delivery_status, department, requested_by, status,
        // date_requested, notes, source_created_at, source_updated_at.
        try {
            $requisitionsSynced = $this->syncTable(
                'requisitions',
                ProcurementDeptRequisition::class,
                fn($row) => [
                    'source_id' => $row->id,
                    'req_number' => $row->req_number ?? null,
                    'item' => $row->item ?? $row->item_name ?? '',
                    'qty' => $row->qty ?? $row->quantity ?? 0,
                    'uom' => $row->uom ?? null,
                    'delivery_status' => $row->delivery_status ?? null,
                    'department' => $row->department ?? null,
                    'requested_by' => $row->requested_by ?? null,
                    'status' => $row->status ?? 'pending',
                    'date_requested' => $row->date_requested ?? null,
                    'notes' => $row->notes ?? null,
                    'source_created_at' => $row->created_at ?? now(),
                    'source_updated_at' => $row->updated_at ?? now(),
                ],
                [
                    'req_number',
                    'item',
                    'qty',
                    'uom',
                    'delivery_status',
                    'department',
                    'requested_by',
                    'status',
                    'date_requested',
                    'notes',
                    'source_created_at',
                    'source_updated_at',
                ]
            );
            $this->info("Synced {$requisitionsSynced} requisitions.");
        } catch (\Throwable $e) {
            $this->warn('Requisitions sync failed: ' . $e->getMessage());
        }

        $this->info('Procurement sync complete.');

        return self::SUCCESS;
    }

    /**
     * @param  \Closure(object): array<string, mixed>  $mapRow
     * @param  array<int, string>  $updateColumns
     */
    protected function syncTable(string $sourceTable, string $modelClass, \Closure $mapRow, array $updateColumns): int
    {
        $synced = 0;

        DB::connection('procurement_dept')
            ->table($sourceTable)
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($rows) use ($mapRow, $modelClass, $updateColumns, &$synced) {
                $batch = $rows->map($mapRow)->all();

                if ($batch !== []) {
                    $modelClass::upsert($batch, ['source_id'], $updateColumns);
                }

                $synced += count($batch);
            });

        return $synced;
    }
}
