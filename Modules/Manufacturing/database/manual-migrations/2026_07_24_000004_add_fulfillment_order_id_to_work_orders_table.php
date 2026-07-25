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
        $schema = Schema::connection('manufacturing');

        if (! $schema->hasColumn('work_orders', 'fulfillment_order_id')) {
            $schema->table('work_orders', function (Blueprint $table): void {
                $table->string('fulfillment_order_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('manufacturing');

        if ($schema->hasColumn('work_orders', 'fulfillment_order_id')) {
            $schema->table('work_orders', function (Blueprint $table): void {
                $table->dropColumn('fulfillment_order_id');
            });
        }
    }
};
