<?php

namespace Modules\Manufacturing\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InstallManufacturingSchema extends Command
{
    protected $signature = 'manufacturing:install-schema';

    protected $description = 'Installs or upgrades the dedicated Manufacturing schema without running standalone migrations against ITSM.';

    public function handle(): int
    {
        $exitCode = $this->call('migrate', [
            '--database' => 'manufacturing',
            '--path' => 'Modules/Manufacturing/database/manual-migrations',
            '--force' => true,
        ]);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        $schema = Schema::connection('manufacturing');
        $tables = [
            'work_orders', 'work_order_parts', 'workers', 'qc_sessions', 'qc_results',
            'rework_orders', 'rework_failed_checks', 'rework_required_parts', 'requisitions',
        ];

        foreach ($tables as $table) {
            if (! $schema->hasTable($table) || $schema->hasColumn($table, 'client_id')) {
                continue;
            }

            $schema->table($table, function (Blueprint $table): void {
                $table->unsignedBigInteger('client_id')->nullable()->index();
            });

            $this->line("Added client_id to {$table}.");
        }

        $this->info('Manufacturing schema is ready and client-scoped.');

        return self::SUCCESS;
    }
}
