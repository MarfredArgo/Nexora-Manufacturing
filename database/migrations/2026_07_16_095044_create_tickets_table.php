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
   public function up()
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->string('requester_name');
        $table->string('module_scope');
        $table->string('subject');
        $table->string('category');
        $table->string('priority');
        $table->string('status');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
