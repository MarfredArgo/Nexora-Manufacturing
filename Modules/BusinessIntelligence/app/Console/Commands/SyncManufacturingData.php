<?php

namespace App\Console\Commands;

use App\Models\ManufacturingQcResult;
use App\Models\ManufacturingWorkOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncManufacturingData extends Command
{
    protected $signature = 'sync:manufacturing';

    protected $description = 'Pull work orders and QC results from the Manufacturing department database into our local tables';

    protected const CHUNK_SIZE = 500;

    public function handle(): int
    {
        $this->info('Syncing manufacturing_dept -> local tables...');

        $workOrdersSynced = $this->syncTable(
            'work_orders',
            ManufacturingWorkOrder::class,
            fn($row) => [
                'source_id' => $row->id,
                'name' => $row->name,
                'specs' => $row->specs,
                'status' => $row->status,
                'due' => $row->due,
                'source' => $row->source,
                'assigned' => $row->assigned,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            ['name', 'specs', 'status', 'due', 'source', 'assigned', 'source_created_at', 'source_updated_at']
        );
        $this->info("Synced {$workOrdersSynced} work orders.");

        $qcResultsSynced = $this->syncTable(
            'qc_results',
            ManufacturingQcResult::class,
            fn($row) => [
                'source_id' => $row->id,
                'source_session_id' => $row->session_id,
                'check_id' => $row->check_id,
                'value' => $row->value,
                'verdict' => $row->verdict,
                'note' => $row->note,
                'source_created_at' => $row->created_at,
                'source_updated_at' => $row->updated_at,
            ],
            ['source_session_id', 'check_id', 'value', 'verdict', 'note', 'source_created_at', 'source_updated_at']
        );
        $this->info("Synced {$qcResultsSynced} QC results.");

        $this->info('Manufacturing sync complete.');

        return self::SUCCESS;
    }

    /**
     * @param  \Closure(object): array<string, mixed>  $mapRow
     * @param  array<int, string>  $updateColumns
     */
    protected function syncTable(string $sourceTable, string $modelClass, \Closure $mapRow, array $updateColumns): int
    {
        $synced = 0;

        DB::connection('manufacturing_dept')
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
