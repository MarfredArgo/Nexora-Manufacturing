<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        $schema = Schema::connection('hr');

        if (! $schema->hasTable('delivery_drivers')) {
            $schema->create('delivery_drivers', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('courier_provider');
                $table->string('vehicle_type')->nullable();
                $table->string('plate_number')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('availability')->default('AVAILABLE');
                $table->timestamps();

                $table->unique(['client_id', 'employee_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::connection('hr')->dropIfExists('delivery_drivers');
    }
};
