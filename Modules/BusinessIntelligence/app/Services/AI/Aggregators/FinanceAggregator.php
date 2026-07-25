<?php

namespace Modules\BusinessIntelligence\Services\AI\Aggregators;

/**
 * NOTE (migration): This class depends on a per-department data adapter
 * (old project's App\Services\Departments\*) that was intentionally NOT
 * ported 1:1 into Modules\<Department> during this migration, because
 * those modules already have their own mature Eloquent models and schema
 * that do not match the old department-service assumptions. This class is
 * carried over as reference scaffolding and is not wired into any route
 * or service provider. BusinessIntelligenceController already implements
 * an equivalent, schema-correct metrics/chat flow directly against this
 * project's per-connection tables. See MIGRATION_NOTES.md.
 */
use App\Services\Departments\FinanceService;

/**
 * Shapes Finance KPIs for AI consumption. Delegates all computation to
 * FinanceService — the same service the Finance dashboard tab uses —
 * so the AI never sees numbers that disagree with the UI.
 */
class FinanceAggregator
{
    public function __construct(protected FinanceService $financeService) {}

    /**
     * @return array<string, mixed>
     */
    public function summarize(): array
    {
        return $this->financeService->getKpiSummaryForAi();
    }
}
