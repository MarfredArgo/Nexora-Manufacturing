<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'manufacturing';

    private function fkExists(string $connection, string $constraintName): bool
    {
        $result = DB::connection($connection)->select(
            "SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = ?",
            [$constraintName]
        );
        return count($result) > 0;
    }

    public function up(): void
    {
        Schema::connection('manufacturing')->table('qc_results', function (Blueprint $table) {
            if (!$this->fkExists('manufacturing', 'qc_results_session_id_fkey')) {
                $table->foreign(['session_id'], 'qc_results_session_id_fkey')->references(['id'])->on('qc_sessions')->onUpdate('no action')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('manufacturing')->table('qc_results', function (Blueprint $table) {
            if ($this->fkExists('manufacturing', 'qc_results_session_id_fkey')) {
                $table->dropForeign('qc_results_session_id_fkey');
            }
        });
    }
};
