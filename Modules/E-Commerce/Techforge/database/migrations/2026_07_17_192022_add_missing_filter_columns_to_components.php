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
        Schema::table('components_pc_cases', function (Blueprint $table) {
            $table->string('fans_included')->nullable()->after('color');
        });

        Schema::table('components_motherboards', function (Blueprint $table) {
            $table->boolean('wifi')->default(false)->after('color');
        });

        // Fix broken Storage brands (where brand contains "TB" or numeric)
        $storages = \Illuminate\Support\Facades\DB::table('components_storages')->get();
        foreach ($storages as $storage) {
            if (preg_match('/[0-9]+TB/i', $storage->brand) || is_numeric($storage->brand)) {
                \Illuminate\Support\Facades\DB::table('components_storages')
                    ->where('id', $storage->id)
                    ->update(['brand' => 'Generic']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('components_pc_cases', function (Blueprint $table) {
            $table->dropColumn('fans_included');
        });

        Schema::table('components_motherboards', function (Blueprint $table) {
            $table->dropColumn('wifi');
        });
    }
};
