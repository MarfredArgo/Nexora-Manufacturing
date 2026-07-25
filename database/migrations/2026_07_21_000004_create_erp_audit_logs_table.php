<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        if (Schema::hasTable('erp_audit_logs')) return;
        Schema::create('erp_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('event');
            $table->string('module');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'event', 'created_at']);
        });
    }
    public function down(): void {}
};
