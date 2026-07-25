<?php

namespace Modules\BusinessIntelligence\Services\AI;
/**
 * NOTE (migration): AILog, AIReport, and AISnapshot are old-project
 * models that were intentionally excluded from this migration (only
 * AIConversation and AIGeneration were carried over, per the migration
 * instructions). Likewise, App\Services\Departments\* was not ported
 * 1:1 into Modules\<Department> (see MIGRATION_NOTES.md). This class is
 * carried over as reference scaffolding and is not wired into any route
 * or service provider; BusinessIntelligenceController already implements
 * an equivalent, schema-correct metrics/chat flow for this project.
 */

use Modules\BusinessIntelligence\Models\AIGeneration;
use App\Models\AILog;
use App\Models\AIReport;
use App\Models\AISnapshot;
use Modules\BusinessIntelligence\Services\AI\Aggregators\DashboardAggregator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * The single entry point for producing a full AI report bundle
 * (Executive Summary, Top Recommendations, Risk Analysis, Business
 * Health, Department Insights) in ONE AI request.
 *
 * Pipeline: DashboardAggregator (KPIs) → PromptBuilder (prompt) →
 * AIRouter (provider) → parse JSON → versioned storage (AIGeneration +
 * AIReport rows, only one generation ever is_current).
 *
 * No controller, job, or Blade view should call AIRouter or a provider
 * directly for report generation — they all go through here.
 */
class AIManager
{
    public function __construct(
        protected DashboardAggregator $dashboardAggregator,
        protected AIRouter $router,
        protected PromptBuilder $promptBuilder,
        protected CacheService $cacheService,
    ) {
    }

    /**
     * Generate a brand-new full report bundle and make it the current one.
     *
     * @param  string  $triggeredBy  "scheduler" | "manual" | "event"
     * @param  string|null  $reason  Human-readable trigger reason, e.g. "manual_refresh"
     */
    public function generateFullReport(string $triggeredBy = 'manual', ?string $reason = null): AIGeneration
    {
        $kpis = $this->dashboardAggregator->collectAll();

        $generation = AIGeneration::create([
            'generation_number' => $this->nextGenerationNumber(),
            'status' => 'pending',
            'triggered_by' => $triggeredBy,
            'trigger_reason' => $reason,
            'provider' => $this->router->providerName(),
            'model' => $this->router->modelName(),
            'started_at' => now(),
        ]);

        AISnapshot::create([
            'ai_generation_id' => $generation->id,
            'payload' => $kpis,
        ]);

        try {
            $response = $this->router->provider()->generate(
                $this->promptBuilder->systemPrompt(),
                $this->promptBuilder->fullReportPrompt($kpis),
                jsonMode: true,
                thinkingLevel: 'medium',
            );

            $parsed = $this->parseJsonResponse($response['content']);

            DB::transaction(function () use ($generation, $parsed) {
                $this->storeReportSections($generation, $parsed);
                $this->markAsCurrent($generation);
            });

            $generation->update([
                'status' => 'completed',
                'input_tokens' => $response['input_tokens'] ?? 0,
                'output_tokens' => $response['output_tokens'] ?? 0,
                'completed_at' => now(),
            ]);

            $this->log($generation, 'info', 'Full report generated successfully.');
        } catch (Throwable $e) {
            $generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->log($generation, 'error', 'Full report generation failed: ' . $e->getMessage());

            Log::error('[NexoraAI] AIManager::generateFullReport failed', [
                'generation_id' => $generation->id,
                'exception' => $e,
            ]);

            throw $e;
        }

        return $generation->fresh();
    }

    /**
     * Generate a single department's insight only (event-driven path,
     * Package 7) — does NOT touch the full report bundle or is_current.
     *
     * @return array<string, mixed>
     */
    public function generateDepartmentInsight(string $department, string $reason): AIReport
    {
        $kpis = $this->dashboardAggregator->collectDepartment($department);

        $generation = AIGeneration::create([
            'generation_number' => $this->nextGenerationNumber(),
            'status' => 'pending',
            'triggered_by' => 'event',
            'trigger_reason' => $reason,
            'provider' => $this->router->providerName(),
            'model' => $this->router->modelName(),
            'started_at' => now(),
        ]);

        AISnapshot::create([
            'ai_generation_id' => $generation->id,
            'payload' => [$department => $kpis],
        ]);

        try {
            $response = $this->router->provider()->generate(
                $this->promptBuilder->systemPrompt(),
                $this->promptBuilder->singleDepartmentPrompt($department, $kpis),
                jsonMode: true,
                thinkingLevel: 'medium',
            );

            $parsed = $this->parseJsonResponse($response['content']);

            $report = AIReport::create([
                'ai_generation_id' => $generation->id,
                'type' => 'department_insights',
                'department' => $department,
                'content' => $parsed,
            ]);

            $generation->update([
                'status' => 'completed',
                'input_tokens' => $response['input_tokens'] ?? 0,
                'output_tokens' => $response['output_tokens'] ?? 0,
                'completed_at' => now(),
            ]);

            $this->cacheService->forgetDepartmentInsight($department);

            $this->log($generation, 'info', "Single-department insight generated for [{$department}].");

            return $report;
        } catch (Throwable $e) {
            $generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->log($generation, 'error', "Department insight generation failed for [{$department}]: " . $e->getMessage());

            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    protected function storeReportSections(AIGeneration $generation, array $parsed): void
    {
        AIReport::create([
            'ai_generation_id' => $generation->id,
            'type' => 'executive_summary',
            'content' => $parsed['executive_summary'] ?? '',
        ]);

        AIReport::create([
            'ai_generation_id' => $generation->id,
            'type' => 'top_recommendations',
            'content' => $parsed['top_recommendations'] ?? [],
        ]);

        AIReport::create([
            'ai_generation_id' => $generation->id,
            'type' => 'risk_analysis',
            'content' => $parsed['risk_analysis'] ?? [],
        ]);

        AIReport::create([
            'ai_generation_id' => $generation->id,
            'type' => 'business_health',
            'content' => $parsed['business_health'] ?? [],
        ]);

        foreach ((array) ($parsed['department_insights'] ?? []) as $department => $insight) {
            AIReport::create([
                'ai_generation_id' => $generation->id,
                'type' => 'department_insights',
                'department' => $department,
                'content' => is_array($insight) ? $insight : ['insight' => $insight],
            ]);
        }
    }

    /**
     * Flip is_current off every other generation and on for this one,
     * inside the same transaction as storing the report sections.
     */
    protected function markAsCurrent(AIGeneration $generation): void
    {
        AIGeneration::where('is_current', true)->update(['is_current' => false]);
        $generation->update(['is_current' => true]);
        $this->cacheService->forgetCurrentReport();
    }

    protected function nextGenerationNumber(): int
    {
        return (int) AIGeneration::max('generation_number') + 1;
    }

    /**
     * Parses the provider's response as strict JSON. Strips markdown code
     * fences defensively in case the model ignores the "no fences"
     * instruction in the prompt.
     *
     * @return array<string, mixed>
     */
    protected function parseJsonResponse(string $content): array
    {
        $clean = trim(preg_replace('/^```(?:json)?|```$/m', '', trim($content)));

        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new \RuntimeException('AI provider returned invalid JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }

    protected function log(AIGeneration $generation, string $level, string $message): void
    {
        AILog::create([
            'ai_generation_id' => $generation->id,
            'level' => $level,
            'message' => $message,
        ]);
    }
}
