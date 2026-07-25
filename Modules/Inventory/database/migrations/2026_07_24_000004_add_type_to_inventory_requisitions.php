<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('inventory');
        if (! $schema->hasColumn('requisitions', 'type')) {
            $schema->table('requisitions', function ($table) {
                $table->string('type', 20)->nullable()->after('requested_by');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('inventory')->table('requisitions', function ($table) {
            $table->dropColumn('type');
        });
    }
};
