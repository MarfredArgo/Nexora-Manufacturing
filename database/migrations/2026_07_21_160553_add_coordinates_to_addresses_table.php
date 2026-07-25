<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection('ecommerce');

        // This database was originally managed by the standalone storefront.
        // Some deployed copies already have one or both coordinate columns even
        // when this unified-app migration has not been recorded there.
        if (! $schema->hasTable('addresses')) {
            return;
        }

        if (! $schema->hasColumn('addresses', 'latitude')) {
            $schema->table('addresses', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable();
            });
        }

        if (! $schema->hasColumn('addresses', 'longitude')) {
            $schema->table('addresses', function (Blueprint $table) {
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection('ecommerce');

        if (! $schema->hasTable('addresses')) {
            return;
        }

        $columns = array_filter(['latitude', 'longitude'], fn (string $column) => $schema->hasColumn('addresses', $column));

        if ($columns) {
            $schema->table('addresses', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
