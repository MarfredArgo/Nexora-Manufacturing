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

        if (! $schema->hasTable('employees')) {
            return;
        }

        $hasEmailUnique = $schema->hasIndex('employees', 'employees_email_unique');
        $hasCompanyEmailUnique = $schema->hasIndex('employees', 'employees_company_email_unique');
        $hasClientEmailUnique = $schema->hasIndex('employees', 'employees_client_id_email_unique');
        $hasClientCompanyEmailUnique = $schema->hasIndex('employees', 'employees_client_id_company_email_unique');

        $schema->table('employees', function (Blueprint $table) use (
            $hasEmailUnique,
            $hasCompanyEmailUnique,
            $hasClientEmailUnique,
            $hasClientCompanyEmailUnique
        ): void {
            if ($hasEmailUnique) {
                $table->dropUnique('employees_email_unique');
            }

            if ($hasCompanyEmailUnique) {
                $table->dropUnique('employees_company_email_unique');
            }

            if (! $hasClientEmailUnique) {
                $table->unique(['client_id', 'email'], 'employees_client_id_email_unique');
            }

            if (! $hasClientCompanyEmailUnique) {
                $table->unique(['client_id', 'company_email'], 'employees_client_id_company_email_unique');
            }
        });
    }

    public function down(): void
    {
        // Keep the client-scoped identifiers in place to avoid reintroducing cross-client conflicts.
    }
};
