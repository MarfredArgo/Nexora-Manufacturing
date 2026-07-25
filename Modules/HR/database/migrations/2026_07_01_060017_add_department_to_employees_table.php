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
    if (! Schema::hasColumn('employees', 'department')) {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable();
        });
    }
}

public function down(): void
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropColumn('department');
    });
}
};
