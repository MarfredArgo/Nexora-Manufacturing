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
use App\Models\AIReport;
use Illuminate\Support\Collection;


/**
 * Read-side counterpart to AIManager. AIManager writes new report
 * generations; ReportGenerator is how the dashboard, Intelligence
 * Center, and chatbot read the current (or historical) one back out —
 * always from the database, never from Gemini directly.
 */
class ReportGenerator
{
    public function __construct(protected CacheService $cacheService)
    {
    }

    /**
     * The current report, shaped for easy consumption:
     * ['executive_summary' => ..., 'top_recommendations' => [...],
     *  'risk_analysis' => [...], 'business_health' => [...],
     *  'department_insights' => ['finance' => ..., 'inventory' => ..., ...]]
     *
     * @return array<string, mixed>|null Null if no report has been generated yet.
     */
    public function getCurrentReport(): ?array
    {
        return $this->cacheService->rememberCurrentReport(function () {
            $generation = AIGeneration::with('reports')
                ->where('is_current', true)
                ->first();

            if (!$generation) {
                return null;
            }

            return [
                'generation_id' => $generation->id,
                'generation_number' => $generation->generation_number,
                'generated_at' => $generation->completed_at,
                'executive_summary' => $this->sectionContent($generation->reports, 'executive_summary'),
                'top_recommendations' => $this->sectionContent($generation->reports, 'top_recommendations', []),
                'risk_analysis' => $this->sectionContent($generation->reports, 'risk_analysis', []),
                'business_health' => $this->sectionContent($generation->reports, 'business_health', []),
                'department_insights' => $generation->reports
                    ->where('type', 'department_insights')
                    ->whereNotNull('department')
                    ->pluck('content', 'department')
                    ->toArray(),
            ];
        });
    }

    /**
     * All past generations, most recent first, for the AI History browser
     * (Package 8).
     *
     * @return Collection<int, AIGeneration>
     */
    public function getHistory(int $limit = 20): Collection
    {
        return AIGeneration::query()
            ->where('status', 'completed')
            ->orderByDesc('generation_number')
            ->limit($limit)
            ->get();
    }

    /**
     * Fetch one historical generation with its report sections, for
     * viewing or comparing (Package 8's AI History / Compare screens).
     */
    public function getGeneration(int $generationId): ?AIGeneration
    {
        return AIGeneration::with('reports', 'snapshots')->find($generationId);
    }

    /**
     * Simple side-by-side diff of two generations' Executive Summary and
     * Business Health score, for the "Compare reports" feature.
     *
     * @return array<string, mixed>
     */
    public function compare(int $generationIdA, int $generationIdB): array
    {
        $a = $this->getGeneration($generationIdA);
        $b = $this->getGeneration($generationIdB);

        return [
            'generation_a' => [
                'id' => $a?->id,
                'executive_summary' => $a ? $this->sectionContent($a->reports, 'executive_summary') : null,
                'business_health' => $a ? $this->sectionContent($a->reports, 'business_health', []) : null,
            ],
            'generation_b' => [
                'id' => $b?->id,
                'executive_summary' => $b ? $this->sectionContent($b->reports, 'executive_summary') : null,
                'business_health' => $b ? $this->sectionContent($b->reports, 'business_health', []) : null,
            ],
        ];
    }

    /**
     * @param  Collection<int, AIReport>  $reports
     */
    protected function sectionContent(Collection $reports, string $type, mixed $default = null): mixed
    {
        return $reports->firstWhere('type', $type)?->content ?? $default;
    }
}
