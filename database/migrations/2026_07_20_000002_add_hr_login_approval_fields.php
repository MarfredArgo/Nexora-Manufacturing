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

        if ($schema->hasTable('employees') && ! $schema->hasColumn('employees', 'must_change_password')) {
            $schema->table('employees', function (Blueprint $table): void {
                $table->boolean('must_change_password')->default(false);
            });
        }
    }

    public function down(): void
    {
        // Do not remove login-state data from HR during rollback.
    }
};
