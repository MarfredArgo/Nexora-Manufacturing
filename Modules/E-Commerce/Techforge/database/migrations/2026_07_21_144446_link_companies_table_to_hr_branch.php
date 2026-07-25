<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'ecommerce';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Client ownership is represented by client_id; do not mirror ITSM
        // tables or embed cross-database credentials in a migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
