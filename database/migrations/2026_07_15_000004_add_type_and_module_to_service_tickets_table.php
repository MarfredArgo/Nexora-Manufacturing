<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::table('service_tickets', function (Blueprint $table) {
            $table->string('ticket_type')->default('erp_module')->after('ticket_no');
            $table->string('module')->nullable()->after('client_name');
        });
    }

    public function down(): void
    {
        Schema::table('service_tickets', function (Blueprint $table) {
            $table->dropColumn(['ticket_type', 'module']);
        });
    }
};
