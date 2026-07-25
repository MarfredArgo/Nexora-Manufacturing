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
        // Queue tables are owned by ITSM's primary connection.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
