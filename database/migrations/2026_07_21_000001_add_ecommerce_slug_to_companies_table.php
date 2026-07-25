<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            if (! Schema::hasColumn('companies', 'ecommerce_slug')) {
                $table->string('ecommerce_slug')->nullable()->unique()->after('company_name');
            }
        });

        DB::table('companies')->whereNull('ecommerce_slug')->orderBy('id')->each(function (object $company): void {
            $base = Str::slug($company->company_name) ?: 'store';
            $slug = $base;

            if (DB::table('companies')->where('ecommerce_slug', $slug)->where('id', '!=', $company->id)->exists()) {
                $slug = "{$base}-{$company->id}";
            }

            DB::table('companies')->where('id', $company->id)->update(['ecommerce_slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            if (Schema::hasColumn('companies', 'ecommerce_slug')) {
                $table->dropUnique(['ecommerce_slug']);
                $table->dropColumn('ecommerce_slug');
            }
        });
    }
};
