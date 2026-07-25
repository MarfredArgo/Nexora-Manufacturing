<?php

namespace App\Services\Departments;

use App\Models\ManufacturingMachine;
use App\Models\ManufacturingProductionLog;
use App\Models\ManufacturingQcResult;
use App\Models\ManufacturingWorkOrder;
use Illuminate\Support\Carbon;

/**
 * Computes Manufacturing & Production KPIs from manufacturing_machines
 * and manufacturing_production_logs.
 */
class ManufacturingService
{
    /**
     * @return array<string, mixed>
     */
    public function getSnapshot(): array
    {
        return [
            'total_downtime_minutes' => $this->totalDowntimeMinutesToday(),
            'production_rate_percent' => $this->productionRatePercent(),
            'machines_down' => $this->machinesDownCount(),
            'machine_status' => $this->machineStatusBreakdown(),
            'defect_rate_percent' => $this->defectRatePercent(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getKpiSummaryForAi(): array
    {
        return [
            'total_downtime_minutes' => $this->totalDowntimeMinutesToday(),
            'production_rate_percent' => $this->productionRatePercent(),
            'machines_down' => $this->machinesDownCount(),
            'defect_rate_percent' => $this->defectRatePercent(),
        ];
    }

    public function totalDowntimeMinutesToday(): int
    {
        return (int) ManufacturingMachine::sum('downtime_minutes_today');
    }

    public function machinesDownCount(): int
    {
        return ManufacturingMachine::down()->count();
    }

    /**
     * @return array<string, int>
     */
    public function machineStatusBreakdown(): array
    {
        return ManufacturingMachine::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public function productionRatePercent(): float
    {
        $today = Carbon::today();

        $totals = ManufacturingProductionLog::query()
            ->where('log_date', $today)
            ->selectRaw('SUM(units_produced) as produced, SUM(units_target) as target')
            ->first();

        if (!$totals || (int) $totals->target === 0) {
            return 0.0;
        }

        return round(($totals->produced / $totals->target) * 100, 2);
    }

    public function defectRatePercent(): float
    {
        $today = Carbon::today();

        $totals = ManufacturingProductionLog::query()
            ->where('log_date', $today)
            ->selectRaw('SUM(defect_count) as defects, SUM(units_produced) as produced')
            ->first();

        if (!$totals || (int) $totals->produced === 0) {
            return 0.0;
        }

        return round(($totals->defects / $totals->produced) * 100, 2);
    }

    /**
     * Used by the event-driven insight trigger in Package 7.
     */
    public function isMachineDown(ManufacturingMachine $machine): bool
    {
        return $machine->status === 'down';
    }

    /**
     * Breakdown of work orders by status, sourced from the Manufacturing
     * department's own system (synced via `sync:manufacturing`).
     *
     * @return array<string, int>
     */
    public function workOrderStatusBreakdown(): array
    {
        return ManufacturingWorkOrder::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * QC pass rate as a percentage, based on graded results only
     * (empty/ungraded verdicts are excluded from the denominator).
     */
    public function qcPassRatePercent(): float
    {
        $graded = ManufacturingQcResult::query()
            ->whereIn('verdict', ['Pass', 'Warn'])
            ->count();

        if ($graded === 0) {
            return 0.0;
        }

        $passed = ManufacturingQcResult::query()
            ->where('verdict', 'Pass')
            ->count();

        return round(($passed / $graded) * 100, 2);
    }

    /**
     * Breakdown of QC verdicts (Pass / Warn / Ungraded) for charting.
     *
     * @return array<string, int>
     */
    public function qcVerdictBreakdown(): array
    {
        $rows = ManufacturingQcResult::query()
            ->selectRaw("CASE WHEN verdict = '' OR verdict IS NULL THEN 'Ungraded' ELSE verdict END as verdict_label, count(*) as total")
            ->groupBy('verdict_label')
            ->pluck('total', 'verdict_label')
            ->toArray();

        return $rows;
    }

    /**
     * Percentage of work orders that are Finished, out of all synced
     * work orders. Used as the "Completion Rate" metric on the
     * Manufacturing Health card.
     */
    public function completionRatePercent(): float
    {
        $total = ManufacturingWorkOrder::count();

        if ($total === 0) {
            return 0.0;
        }

        $finished = ManufacturingWorkOrder::where('status', 'Finished')->count();

        return round(($finished / $total) * 100, 2);
    }

    /**
     * Count of work orders that are past their due date and not yet
     * Finished or Cancelled. Their `due` field is stored as free text
     * by the source system, so unparsable values are safely skipped.
     */
    public function overdueBuildsCount(): int
    {
        return ManufacturingWorkOrder::query()
            ->whereNotIn('status', ['Finished', 'Cancelled'])
            ->get()
            ->filter(function (ManufacturingWorkOrder $workOrder) {
                $due = $this->safeParseDate($workOrder->due);

                return $due !== null && $due->isPast();
            })
            ->count();
    }

    private function safeParseDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
