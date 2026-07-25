<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'manufacturing';

    public function up(): void
    {
        $schema = Schema::connection('manufacturing');

        if (! $schema->hasTable('qc_results')) {
            return;
        }

        // The original schema limited this to varchar(10), while actual
        // benchmark IDs such as CPU_cinebench and Storage_read are longer.
        // PostgreSQL can widen varchar without data loss.
        DB::connection('manufacturing')->statement(
            'ALTER TABLE qc_results ALTER COLUMN check_id TYPE varchar(100)'
        );
    }

    public function down(): void
    {
        // Do not shrink this column: existing benchmark identifiers may no
        // longer fit and a rollback would discard valid QC history.
    }
};
