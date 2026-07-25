<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-stock {--threshold=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check external inventory DB for items with stock below threshold and store alerts.';

    public function handle()
    {
        $threshold = (int) $this->option('threshold');
        $this->info("Checking inventory DB for stock < {$threshold}...");

        try {
            $rows = DB::connection('inventory')
                ->table('stock_levels as sl')
                ->join('items as i', 'sl.item_id', '=', 'i.id')
                ->select('sl.id as stock_level_id', 'sl.item_id', 'sl.warehouse_id', 'sl.stock', 'i.name as item_name', 'i.sku')
                ->where('sl.stock', '<', $threshold)
                ->get();
        } catch (\Exception $e) {
            $this->error('Failed to query inventory DB: ' . $e->getMessage());
            return 2;
        }

        $count = 0;
        foreach ($rows as $r) {
            $count++;
            DB::connection('procurement')->table('low_stock_alerts')->updateOrInsert(
                ['external_item_id' => $r->stock_level_id, 'warehouse_id' => $r->warehouse_id],
                ['sku' => $r->sku, 'item_name' => $r->item_name, 'stock' => $r->stock, 'threshold' => $threshold, 'updated_at' => now()]
            );
        }

        $this->info("Found {$count} low-stock items.");
        return 0;
    }
}
