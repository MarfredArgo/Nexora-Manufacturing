<?php

namespace Modules\Ecommerce\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillEcommerceOrderLinks extends Command
{
    protected $signature = 'ecommerce:backfill-order-links {--dry-run : Report the changes without writing them}';

    protected $description = 'Backfills fulfillment line items and manufacturing links for existing ecommerce orders.';

    public function handle(): int
    {
        foreach ([
            ['ecommerce', 'orders'],
            ['ecommerce', 'order_items'],
            ['order_fulfillment', 'orders'],
            ['order_fulfillment', 'order_items'],
            ['manufacturing', 'work_orders'],
        ] as [$connection, $table]) {
            if (! Schema::connection($connection)->hasTable($table)) {
                $this->error("Missing {$connection}.{$table}. Install the module schemas before running this command.");
                return self::FAILURE;
            }
        }

        if (! Schema::connection('manufacturing')->hasColumn('work_orders', 'fulfillment_order_id')) {
            $this->error('Missing manufacturing.work_orders.fulfillment_order_id. Run manufacturing:install-schema first.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $ecommerce = DB::connection('ecommerce');
        $fulfillment = DB::connection('order_fulfillment');
        $manufacturing = DB::connection('manufacturing');
        $counts = [
            'orders_scanned' => 0,
            'line_items_added' => 0,
            'work_orders_linked' => 0,
            'unmatched_fulfillment' => 0,
            'unmatched_work_orders' => 0,
        ];

        $ecommerce->table('orders')
            ->whereNotNull('client_id')
            ->orderBy('id')
            ->chunkById(100, function ($orders) use ($ecommerce, $fulfillment, $manufacturing, $dryRun, &$counts): void {
                foreach ($orders as $order) {
                    $counts['orders_scanned']++;
                    $orderId = (string) $order->id;
                    $clientId = (int) $order->client_id;

                    $fulfillmentOrder = $fulfillment->table('orders')
                        ->where('id', $orderId)
                        ->where('client_id', $clientId)
                        ->first();

                    if (! $fulfillmentOrder) {
                        $counts['unmatched_fulfillment']++;
                    } else {
                        $existingLineItems = $fulfillment->table('order_items')
                            ->where('order_id', $orderId)
                            ->where('client_id', $clientId)
                            ->exists();

                        if (! $existingLineItems) {
                            $lines = $ecommerce->table('order_items')
                                ->where('order_id', $orderId)
                                ->get()
                                ->map(fn ($item): array => [
                                    'client_id' => $clientId,
                                    'order_id' => $orderId,
                                    'product_name' => (string) ($item->name ?: 'Storefront item'),
                                    'qty' => max(1, (int) ($item->quantity ?: 1)),
                                    'product_amount' => (float) ($item->price ?: 0),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ])->all();

                            if ($lines) {
                                $counts['line_items_added'] += count($lines);
                                if (! $dryRun) {
                                    $fulfillment->table('order_items')->insert($lines);
                                }
                            }
                        }
                    }

                    $workOrder = $manufacturing->table('work_orders')
                        ->where('client_id', $clientId)
                        ->where(function ($query) use ($orderId): void {
                            $query->where('fulfillment_order_id', $orderId)
                                ->orWhere('source', 'Ecommerce '.$orderId)
                                ->orWhere('id', 'WO-'.strtoupper(substr(sha1($orderId), 0, 12)));
                        })
                        ->first();

                    if (! $workOrder) {
                        $counts['unmatched_work_orders']++;
                        continue;
                    }

                    if (! $workOrder->fulfillment_order_id) {
                        $counts['work_orders_linked']++;
                        if (! $dryRun) {
                            $manufacturing->table('work_orders')
                                ->where('id', $workOrder->id)
                                ->update([
                                    'fulfillment_order_id' => $orderId,
                                    'updated_at' => now(),
                                ]);
                        }
                    }
                }
            }, 'id');

        $verb = $dryRun ? 'Would update' : 'Updated';
        $this->table(['Metric', 'Count'], [
            ['E-commerce orders scanned', $counts['orders_scanned']],
            ["{$verb} fulfillment line items", $counts['line_items_added']],
            ["{$verb} manufacturing links", $counts['work_orders_linked']],
            ['Orders without a fulfillment record (left unchanged)', $counts['unmatched_fulfillment']],
            ['Orders without a manufacturing record (left unchanged)', $counts['unmatched_work_orders']],
        ]);

        return self::SUCCESS;
    }
}
