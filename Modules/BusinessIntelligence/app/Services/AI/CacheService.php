<?php

namespace Modules\BusinessIntelligence\Services\AI;

use Illuminate\Support\Facades\Cache;

/**
 * Thin wrapper around Laravel's cache for AI report reads. The real
 * token-optimization already happens by storing reports in the database
 * instead of calling Gemini per page load; this adds a fast layer on top
 * so a busy dashboard doesn't re-run the same DB query on every request.
 *
 * Cache is invalidated by AIManager the moment a new generation becomes
 * current, so it can never serve a stale report past that point.
 */
class CacheService
{
    protected const CURRENT_REPORT_KEY = 'nexora_ai.current_report';

    protected const DEPARTMENT_INSIGHT_KEY_PREFIX = 'nexora_ai.department_insight.';

    public function rememberCurrentReport(\Closure $callback): mixed
    {
        return Cache::remember(
            self::CURRENT_REPORT_KEY,
            now()->addHours((int) config('ai.cache_ttl_hours', 12)),
            $callback,
        );
    }

    public function forgetCurrentReport(): void
    {
        Cache::forget(self::CURRENT_REPORT_KEY);
    }

    public function rememberDepartmentInsight(string $department, \Closure $callback): mixed
    {
        return Cache::remember(
            self::DEPARTMENT_INSIGHT_KEY_PREFIX . $department,
            now()->addHours((int) config('ai.cache_ttl_hours', 12)),
            $callback,
        );
    }

    public function forgetDepartmentInsight(string $department): void
    {
        Cache::forget(self::DEPARTMENT_INSIGHT_KEY_PREFIX . $department);
    }
}
