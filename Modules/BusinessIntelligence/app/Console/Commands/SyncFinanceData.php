<?php

namespace App\Console\Commands;

use App\Models\FinanceDeptInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncFinanceData extends Command
{
    protected $signature = 'sync:finance';

    protected $description = 'Sync Finance department invoices';

    protected const CHUNK_SIZE = 500;

    public function handle(): int
    {
        $this->info('Syncing finance_dept -> local tables...');

        // Invoices — primary key is invoice_id, not id.
        // Destination columns match FinanceDeptInvoice::$fillable exactly:
        // source_id, client_id, issue_date, due_date, payment_date,
        // invoice_amount, paid_amount, outstanding_amount, status,
        // source_created_at, source_updated_at. There is no invoice_number
        // column on this table.
        $invoicesSynced = 0;
        DB::connection('finance_dept')
            ->table('invoice')
            ->orderBy('invoice_id')
            ->chunk(self::CHUNK_SIZE, function ($rows) use (&$invoicesSynced) {
                $batch = $rows->map(fn($row) => [
                    'source_id' => $row->invoice_id,
                    'client_id' => $row->client_id ?? null,
                    'issue_date' => $row->issue_date ?? null,
                    'due_date' => $row->due_date ?? null,
                    'payment_date' => $row->payment_date ?? null,
                    'invoice_amount' => $row->invoice_amount ?? 0,
                    'paid_amount' => $row->paid_amount ?? 0,
                    'outstanding_amount' => $row->outstanding_amount ?? 0,
                    'status' => $row->status ?? 'Pending',
                    'source_created_at' => $row->created_at ?? now(),
                    'source_updated_at' => $row->updated_at ?? now(),
                ])->all();

                if ($batch !== []) {
                    FinanceDeptInvoice::upsert($batch, ['source_id'], [
                        'client_id',
                        'issue_date',
                        'due_date',
                        'payment_date',
                        'invoice_amount',
                        'paid_amount',
                        'outstanding_amount',
                        'status',
                        'source_created_at',
                        'source_updated_at',
                    ]);
                }

                $invoicesSynced += count($batch);
            });
        $this->info("Synced {$invoicesSynced} invoices.");

        // Expenses — monthly summary with category breakdowns
        try {
            $expensesSynced = 0;
            $now = now();
            DB::connection('finance_dept')
                ->table('expenses')
                ->orderBy('id')
                ->chunk(self::CHUNK_SIZE, function ($rows) use (&$expensesSynced, $now) {
                    $batch = $rows->map(fn($row) => [
                        'source_id' => $row->id,
                        'month' => $row->month ?? now()->format('Y-m'),
                        'total_expenses' => $row->total_expenses ?? 0,
                        'percent_change' => $row->percent_change ?? 0,
                        'budget_used' => $row->budget_used ?? 0,
                        'budget_total' => $row->budget_total ?? 0,
                        'manufacturing' => $row->manufacturing ?? 0,
                        'procurement' => $row->procurement ?? 0,
                        'inventory' => $row->inventory ?? 0,
                        'order_fulfillment' => $row->order_fulfillment ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    if ($batch !== []) {
                        DB::table('finance_dept_expenses')->upsert($batch, ['source_id'], [
                            'month',
                            'total_expenses',
                            'percent_change',
                            'budget_used',
                            'budget_total',
                            'manufacturing',
                            'procurement',
                            'inventory',
                            'order_fulfillment',
                            'updated_at',
                        ]);
                    }

                    $expensesSynced += count($batch);
                });
            $this->info("Synced {$expensesSynced} expense summaries.");
        } catch (\Throwable) {
            $this->warn('Expenses table not available.');
        }

        // Sales — monthly summary
        try {
            $salesSynced = 0;
            $now = now();
            DB::connection('finance_dept')
                ->table('sales')
                ->orderBy('id')
                ->chunk(self::CHUNK_SIZE, function ($rows) use (&$salesSynced, $now) {
                    $batch = $rows->map(fn($row) => [
                        'source_id' => $row->id,
                        'total_sales' => $row->total_sales ?? 0,
                        'percent_change' => $row->percent_change ?? 0,
                        'difference_from_last_month' => $row->difference_from_last_month ?? 0,
                        'period' => $row->period ?? now()->format('Y-m'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    if ($batch !== []) {
                        DB::table('finance_dept_sales')->upsert($batch, ['source_id'], [
                            'total_sales',
                            'percent_change',
                            'difference_from_last_month',
                            'period',
                            'updated_at',
                        ]);
                    }

                    $salesSynced += count($batch);
                });
            $this->info("Synced {$salesSynced} sales summaries.");
        } catch (\Throwable) {
            $this->warn('Sales table not available.');
        }

        $this->info('Finance sync complete.');

        return self::SUCCESS;
    }
}
