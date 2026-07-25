<?php

namespace Modules\Manufacturing\Services;

class BenchmarkTargetService
{
    protected const RANGE_MAP = [
        'high-end'  => 'HE',
        'mid-range' => 'MR',
        'budget'    => 'BU',
        'office'    => 'OF',
    ];

    // ── Targets for a build range ────────────────────────────────────────────
    public function targetsFor(?string $range): array
    {
        $key = self::RANGE_MAP[$range] ?? 'MR';
        return config("nexora.benchmarkTargets.$key", []);
    }

    // ── Pass / Warn / Fail verdict ───────────────────────────────────────────
    public function verdictFor(string $checkId, ?string $range, ?float $value): string
    {
        if ($value === null) return '';

        $targets = $this->targetsFor($range);
        $check   = $targets[$checkId] ?? null;
        if (!$check) return '';

        $target   = (float) $check['target'];
        $operator = $check['operator'];

        if ($operator === '>=') {
            if ($value >= $target)         return 'Pass';
            if ($value >= $target * 0.9)   return 'Warn';
            return 'Fail';
        }

        if ($operator === '<=') {
            if ($value <= $target)         return 'Pass';
            if ($value <= $target * 1.1)   return 'Warn';
            return 'Fail';
        }

        return $value == $target ? 'Pass' : 'Fail';
    }
}
