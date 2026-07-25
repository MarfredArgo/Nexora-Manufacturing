<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'ecommerce';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = DB::connection('ecommerce')->select("SELECT table_name FROM information_schema.columns WHERE column_name = 'client_id' AND table_schema = 'public'");
        
        foreach ($tables as $table) {
            $tableName = $table->table_name;
            // Since there is existing data containing invalid UUID representations, we drop the old values
            // and forcefully convert the column to bigint to match the companies id.
            DB::connection('ecommerce')->statement("ALTER TABLE \"$tableName\" ALTER COLUMN client_id TYPE bigint USING NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = DB::connection('ecommerce')->select("SELECT table_name FROM information_schema.columns WHERE column_name = 'client_id' AND table_schema = 'public'");
        
        foreach ($tables as $table) {
            $tableName = $table->table_name;
            DB::connection('ecommerce')->statement("ALTER TABLE \"$tableName\" ALTER COLUMN client_id TYPE uuid USING NULL");
        }
    }
};
