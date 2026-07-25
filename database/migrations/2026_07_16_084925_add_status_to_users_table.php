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
    Schema::table('users', function (Blueprint $table) {
        $table->string('status')->default('active'); 
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
