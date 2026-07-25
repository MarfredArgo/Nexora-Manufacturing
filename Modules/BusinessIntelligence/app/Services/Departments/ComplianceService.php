<?php

namespace App\Services\Departments;

use App\Models\ComplianceRisk;

/**
 * Computes Compliance & Risk Management KPIs from compliance_risks.
 */
class ComplianceService
{
    /**
     * @return array<string, mixed>
     */
    public function getSnapshot(): array
    {
        return [
            'open_risks' => $this->openRisksCount(),
            'high_severity_risks' => $this->highSeverityRisksCount(),
            'compliance_score_percent' => $this->complianceScorePercent(),
            'risks_by_severity' => $this->risksBySeverity(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getKpiSummaryForAi(): array
    {
        return [
            'open_risks' => $this->openRisksCount(),
            'high_severity_risks' => $this->highSeverityRisksCount(),
            'compliance_score_percent' => $this->complianceScorePercent(),
        ];
    }

    public function openRisksCount(): int
    {
        return ComplianceRisk::open()->count();
    }

    public function highSeverityRisksCount(): int
    {
        return ComplianceRisk::open()->highSeverity()->count();
    }

    /**
     * A simple compliance score: percentage of all identified risks that
     * are not currently open (i.e. mitigated or closed).
     */
    public function complianceScorePercent(): float
    {
        $total = ComplianceRisk::count();

        if ($total === 0) {
            return 100.0;
        }

        $resolved = ComplianceRisk::whereIn('status', ['mitigated', 'closed'])->count();

        return round(($resolved / $total) * 100, 2);
    }

    /**
     * @return array<string, int>
     */
    public function risksBySeverity(): array
    {
        return ComplianceRisk::open()
            ->selectRaw('severity, count(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity')
            ->toArray();
    }

    /**
     * The oldest open risks, for the "Top Issues by Age" table on the
     * dashboard's Operational Efficiency card.
     *
     * @return array<int, array{issue: string, category: string, days_open: int}>
     */
    public function topIssuesByAge(int $limit = 5): array
    {
        return ComplianceRisk::open()
            ->orderBy('identified_date')
            ->limit($limit)
            ->get()
            ->map(fn(ComplianceRisk $risk) => [
                'issue' => $risk->title,
                'category' => $risk->standard ?? 'General',
                'days_open' => (int) $risk->identified_date->diffInDays(now()),
            ])
            ->toArray();
    }
}
