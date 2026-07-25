<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('hr');

        $this->renameClientColumn($schema, 'employees');
        $this->renameClientColumn($schema, 'attendances');
    }

    public function down(): void
    {
        // Keep the client key when rolling back. HR data must remain intact.
    }

    private function renameClientColumn($schema, string $table): void
    {
        if (! $schema->hasTable($table)) {
            return;
        }

        $hasLegacyColumn = $schema->hasColumn($table, 'itsm_company_id');
        $hasClientColumn = $schema->hasColumn($table, 'client_id');

        if (! $hasClientColumn) {
            $schema->table($table, function (Blueprint $blueprint) use ($hasLegacyColumn): void {
                if ($hasLegacyColumn) {
                    $blueprint->renameColumn('itsm_company_id', 'client_id');
                } else {
                    $blueprint->unsignedBigInteger('client_id')->nullable()->index();
                }
            });

            return;
        }

        if ($hasLegacyColumn) {
            DB::connection('hr')->table($table)
                ->whereNull('client_id')
                ->whereNotNull('itsm_company_id')
                ->update(['client_id' => DB::raw('itsm_company_id')]);
        }
    }
};
