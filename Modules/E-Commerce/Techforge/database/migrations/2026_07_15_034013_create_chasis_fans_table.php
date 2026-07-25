<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('components_chasisfan', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('size')->nullable(); // e.g. 120mm, 140mm
            $table->string('rpm')->nullable(); // e.g. 1500 RPM
            $table->string('airflow')->nullable(); // e.g. 50 CFM
            $table->string('noise_level')->nullable(); // e.g. 20 dB(A)
            $table->string('color')->nullable();
            $table->boolean('rgb')->default(false);
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('components_chasisfan');
    }
};
