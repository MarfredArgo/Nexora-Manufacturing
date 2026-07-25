<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InstallFinanceSchema extends Command
{
    protected $signature = 'finance:install-schema';
    protected $description = 'Adds Nexora client ownership to the dedicated Finance tables without running standalone Finance migrations.';

    public function handle(): int
    {
        $exitCode = $this->call('migrate', [
            '--database' => 'finance',
            '--path' => 'Modules/Finance/database/migrations',
            '--force' => true,
        ]);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        $schema = Schema::connection('finance');
        if (! $schema->hasTable('invoice')) {
            $baseMigration = require __DIR__.'/../../database/migrations/2026_07_24_000001_create_finance_invoice_table.php';
            $baseMigration->up();
        }

        foreach (['accounts', 'invoice', 'expenses', 'sales'] as $table) {
            if (! $schema->hasTable($table) || $schema->hasColumn($table, 'nexora_client_id')) {
                continue;
            }

            $schema->table($table, function (Blueprint $table): void {
                // invoice.client_id is a legacy invoice/customer reference,
                // so use a distinct column for ERP company ownership.
                $table->unsignedBigInteger('nexora_client_id')->nullable()->index();
            });
            $this->line("Added nexora_client_id to {$table}.");
        }

        $this->info('Finance schema is ready and client-scoped.');
        return self::SUCCESS;
    }
}
