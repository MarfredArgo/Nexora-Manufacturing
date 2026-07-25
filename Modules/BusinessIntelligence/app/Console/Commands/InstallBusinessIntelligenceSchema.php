<?php

namespace Modules\BusinessIntelligence\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InstallBusinessIntelligenceSchema extends Command
{
    protected $signature = 'bi:install-schema';
    protected $description = 'Creates the dedicated, client-scoped BI tables without touching ITSM or module databases.';

    public function handle(): int
    {
        if (!config('database.connections.business_intelligence.url')) {
            $this->warn('BUSINESS_INTELLIGENCE_DB_URL is not configured; BI snapshot tables were skipped.');
            return self::SUCCESS;
        }

        $schema = Schema::connection('business_intelligence');

        if (!$schema->hasTable('bi_snapshots')) {
            $schema->create('bi_snapshots', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('source')->default('live-dashboard');
                $table->json('payload');
                $table->timestamp('captured_at');
                $table->timestamps();
                $table->unique(['client_id', 'source']);
            });
        }

        if (!$schema->hasTable('bi_ai_conversations')) {
            $schema->create('bi_ai_conversations', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->unsignedBigInteger('employee_id')->nullable()->index();
                $table->string('role', 16);
                $table->text('message');
                $table->boolean('used_ai')->default(false);
                $table->timestamps();
            });
        }

        if (!$schema->hasTable('bi_ai_reports')) {
            $schema->create('bi_ai_reports', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('report_type');
                $table->json('payload');
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();
            });
        }

        // Shared client-scoped alert inbox for the latest BI feature. Other
        // modules may read these records, but BI remains the only writer.
        if (!$schema->hasTable('dept_alerts')) {
            $schema->create('dept_alerts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('source_department');
                $table->string('target_department')->index();
                $table->string('category');
                $table->string('severity');
                $table->string('fingerprint');
                $table->string('status')->default('open')->index();
                $table->string('title');
                $table->text('message');
                $table->text('action')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                $table->unique(['client_id', 'target_department', 'fingerprint', 'status'], 'bi_dept_alerts_active_unique');
            });
        }

        if (!$schema->hasTable('dept_alert_briefings')) {
            $schema->create('dept_alert_briefings', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->string('target_department');
                $table->text('ai_text');
                $table->string('alerts_hash');
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();
                $table->unique(['client_id', 'target_department']);
            });
        }

        // NOTE (migration): `bi_ai_generations` (backing the migrated
        // Modules\BusinessIntelligence\Models\AIGeneration) is intentionally
        // not created yet. AIManager/ReportGenerator were carried over as
        // reference scaffolding only and are not wired into any route —
        // BusinessIntelligenceController::aiChat() already implements an
        // equivalent, schema-correct flow for this project. Uncomment this
        // block once that scaffolding is actually wired up.
        //
        // if (! $schema->hasTable('bi_ai_generations')) {
        //     $schema->create('bi_ai_generations', function (Blueprint $table): void {
        //         $table->id();
        //         $table->unsignedBigInteger('client_id')->index();
        //         $table->unsignedInteger('generation_number');
        //         $table->string('status');
        //         $table->string('triggered_by');
        //         $table->string('trigger_reason')->nullable();
        //         $table->string('provider')->nullable();
        //         $table->string('model')->nullable();
        //         $table->unsignedInteger('input_tokens')->default(0);
        //         $table->unsignedInteger('output_tokens')->default(0);
        //         $table->boolean('is_current')->default(false);
        //         $table->timestamp('started_at')->nullable();
        //         $table->timestamp('completed_at')->nullable();
        //         $table->text('error_message')->nullable();
        //         $table->timestamps();
        //     });
        // }

        $this->info('Business Intelligence schema is ready and client-scoped.');
        return self::SUCCESS;
    }
}
