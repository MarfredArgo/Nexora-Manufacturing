<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    protected $connection = 'manufacturing';

    public function up(): void
    {
        if (!Schema::connection('manufacturing')->hasColumn('requisitions', 'destination')) {
            Schema::connection('manufacturing')->table('requisitions', function (Blueprint $table) {
                $table->string('destination', 100)->default('Inventory')->after('department');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('manufacturing')->table('requisitions', function (Blueprint $table) {
            $table->dropColumn('destination');
        });
    }
};
