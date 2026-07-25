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
        Schema::table('custombuilt_configs', function (Blueprint $table) {
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('review_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custombuilt_configs', function (Blueprint $table) {
            $table->dropColumn(['rating', 'review_count']);
        });
    }
};
