<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::connection('procurement')->dropIfExists('sessions');
    }

    public function down(): void
    {
        // Sessions belong to ITSM's primary connection, not Procurement.
    }
};
