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
        Schema::connection('manufacturing')->table('work_order_parts', function (Blueprint $table) {
            if (!Schema::connection('manufacturing')->hasColumn('work_order_parts', 'product_id')) {
                $table->string('product_id', 50)->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('manufacturing')->table('work_order_parts', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
