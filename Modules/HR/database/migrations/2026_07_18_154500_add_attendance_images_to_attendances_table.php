<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'time_in_image')) {
                $table->string('time_in_image')->nullable()->after('time_in');
            }

            if (! Schema::hasColumn('attendances', 'time_out_image')) {
                $table->string('time_out_image')->nullable()->after('time_out');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'time_out_image')) {
                $table->dropColumn('time_out_image');
            }

            if (Schema::hasColumn('attendances', 'time_in_image')) {
                $table->dropColumn('time_in_image');
            }
        });
    }
};
