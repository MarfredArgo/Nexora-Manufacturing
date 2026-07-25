<?php

namespace Modules\BusinessIntelligence\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\BusinessIntelligence\Services\AI\AIRouter;
use Modules\BusinessIntelligence\Services\AI\Contracts\AIProviderInterface;

class BusinessIntelligenceController
{
    /**
     * BI deliberately reads the owning module databases instead of writing
     * employee or business records into ITSM. Every query is scoped to the
     * current client before any aggregate is calculated.
     */
    public function dashboard(Request $request): View
    {
        $clientId = $this->clientId($request);
        if ($request->boolean('fresh')) {
            $this->bustCache($clientId);
        }
        $metrics = $this->metrics($clientId);
        $this->recordSnapshot($clientId, $metrics);

        $kpis = $this->dashboardKpis($metrics);
        $topProducts = $this->topProducts($clientId);
        $operationalEfficiency = $this->operationalEfficiency($clientId, $metrics);

        return view('bi::dashboard', compact('kpis', 'topProducts', 'operationalEfficiency', 'metrics', 'clientId'));
    }

    public function departmentAnalytics(): View
    {
        return view('bi::department-analytics');
    }

    public function liveMonitor(): View
    {
        return view('bi::live-monitor');
    }

    public function aiInsights(Request $request): View
    {
        $clientId = $this->clientId($request);
        $metrics = $this->metrics($clientId);

        // Read the pre-generated AI report from cache (produced in the
        // background by the warm-cache schedule) so the page never blocks on a
        // live model call. Falls back to metric-driven rule-based insights
        // until the first report has been generated.
        $ai = $this->cachedAiInsight($clientId) ?? [];

        return view('bi::ai-insights', [
            'alerts' => $this->insightAlerts($metrics),
            'executiveSummary' => !empty($ai['executiveSummary']) ? $ai['executiveSummary'] : $this->executiveSummary($metrics),
            'recommendations' => !empty($ai['recommendations']) ? $ai['recommendations'] : $this->recommendations($metrics),
            'risks' => !empty($ai['risks']) ? $ai['risks'] : $this->riskDetection($metrics),
        ]);
    }

    /**
     * Recompute and cache every client-scoped result the BI pages read, so
     * an actual page load hits warm cache instead of paying the ~15s live
     * cross-database query cost. Called by the bi:warm-cache command on a
     * schedule, and safe to call ad hoc.
     */
    public function warmCache(?int $clientId): void
    {
        // Recompute fresh values and overwrite the cache in place (no version
        // bump) so the previously cached values keep serving while this runs —
        // avoiding a cold-cache gap during the ~15s recompute.
        $metrics = $this->computeMetrics($clientId);
        $this->cachePut($clientId, 'metrics', $metrics);
        $this->cachePut($clientId, 'top_products', $this->computeTopProducts($clientId));
        $this->cachePut($clientId, 'op_efficiency', $this->computeOperationalEfficiency($clientId, $metrics));

        foreach (['finance', 'inventory', 'procurement', 'manufacturing', 'fulfillment', 'ecommerce'] as $department) {
            $this->cachePut($clientId, 'dept_' . $department, $this->computeDepartmentSummary($department, $clientId));
        }

        // Publish the current alerts to the shared dept_alerts store, plus a
        // per-department AI briefing to dept_alert_briefings, so other
        // departments can read their inbox. No-ops until the BI database is
        // configured (and, for briefings, an AI provider is set).
        $alertDefinitions = $this->departmentAlertDefinitions($metrics);
        $this->publishDepartmentAlerts($clientId, $alertDefinitions);
        $this->publishDepartmentBriefings($clientId, $alertDefinitions);

        // Pre-generate the AI insight report off the request path, but only
        // when the cached one has expired (~every AI_CACHE_TTL), so the metered
        // AI provider isn't called on every warm cycle. On failure it caches
        // nothing and simply retries next cycle — a user never waits on it, and
        // the AI Insights page reads whatever report is already cached.
        try {
            if ($this->aiConfigured() && !Cache::has('bi_ai_report_' . $clientId)) {
                $this->generateAiInsight($clientId, $metrics);
            }
        } catch (\Throwable) {
            // Warming AI insights is best-effort and must never fail the run.
        }
    }

    public function aiChat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1500'],
        ]);

        $clientId = $this->clientId($request);
        if (!$clientId) {
            return response()->json(['message' => 'Select a client before requesting BI insights.'], 422);
        }

        if (!$this->aiConfigured()) {
            return response()->json(['message' => 'AI Insights is not configured yet. Contact your system administrator.'], 503);
        }

        $metrics = $this->metrics($clientId);

        try {
            $answer = trim($this->aiProvider()->generate(
                $this->aiSystemPrompt(),
                'Client-scoped BI metrics (JSON): ' . json_encode($metrics, JSON_THROW_ON_ERROR)
                    . "\n\nUser question: " . $validated['message'],
            )['content']);

            if ($answer === '') {
                return response()->json(['message' => 'AI Insights returned no answer. Please try again.'], 502);
            }

            $this->recordConversation($clientId, 'user', $validated['message'], true);
            $this->recordConversation($clientId, 'assistant', $answer, true);

            return response()->json(['message' => $answer]);
        } catch (\Throwable $exception) {
            Log::warning('BI AI chat request failed.', [
                'client_id' => $clientId,
                'exception' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'AI Insights is temporarily unavailable. Please try again shortly.'], 502);
        }
    }

    public function salesForecast(Request $request): JsonResponse
    {
        $days = match ($request->string('range')->toString()) {
            '1m' => 30,
            '1y' => 365,
            default => 7,
        };
        $clientId = $this->clientId($request);
        $rows = collect($this->remember($clientId, "forecast_{$days}", fn (): array => $this->financeInvoiceQuery($clientId)
                ?->whereDate('issue_date', '>=', now()->subDays($days - 1))
            ->selectRaw('DATE(issue_date) as day, COALESCE(SUM(paid_amount), 0) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->all() ?? []))->keyBy('day');

        $labels = [];
        $sales = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $labels[] = $days === 365 ? now()->subDays($i)->format('M') : now()->subDays($i)->format('M d');
            $sales[] = (float) ($rows->get($day)->total ?? 0);
        }

        return response()->json(compact('labels', 'sales'));
    }

    public function departmentData(Request $request, string $department): JsonResponse
    {
        $clientId = $this->clientId($request);

        return response()->json($this->departmentSummary($department, $clientId));
    }

    public function liveFeed(Request $request): JsonResponse
    {
        $clientId = $this->clientId($request);
        $metrics = $this->metrics($clientId);

        $alerts = array_map(
            fn (array $a): array => $this->alert($a['severity'], $a['icon'], $a['department_label'], $a['title'], $a['message'], $a['metrics']),
            $this->departmentAlertDefinitions($metrics)
        );

        return response()->json([
            'alerts' => $alerts,
            'summary' => [
                'critical' => collect($alerts)->where('severity', 'critical')->count(),
                'warning' => collect($alerts)->where('severity', 'warning')->count(),
                'info' => collect($alerts)->where('severity', 'info')->count(),
            ],
        ]);
    }

    /**
     * Single source of truth for the department-facing alerts. Both the
     * live-feed JSON and the dept_alerts publisher build from this so the
     * bell/live-monitor and the departments' inboxes never disagree. Each
     * entry carries everything both consumers need: the feed fields
     * (severity/icon/department_label/title/message/metrics) plus the
     * publish fields (target_department/category/action).
     *
     * @return array<int, array<string, mixed>>
     */
    private function departmentAlertDefinitions(array $metrics): array
    {
        $definitions = [];

        if ($metrics['inventory_low_stock'] > 0) {
            $definitions[] = [
                'target_department' => 'inventory', 'department_label' => 'Inventory',
                'category' => 'low_stock', 'severity' => 'critical', 'icon' => 'alert-triangle',
                'title' => $metrics['inventory_low_stock'] . ' Items Low Stock',
                'message' => 'Stock levels have fallen below their reorder thresholds.',
                'action' => 'Review and reorder affected items.',
                'metrics' => [
                    ['label' => 'Low Stock', 'value' => $metrics['inventory_low_stock']],
                    ['label' => 'Total Items', 'value' => $metrics['inventory_items']],
                ],
            ];
        }
        if ($metrics['finance_overdue'] > 0) {
            $definitions[] = [
                'target_department' => 'finance', 'department_label' => 'Finance',
                'category' => 'overdue_invoices', 'severity' => 'warning', 'icon' => 'dollar-sign',
                'title' => $metrics['finance_overdue'] . ' Overdue Invoices',
                'message' => 'Invoices past their due date await collection.',
                'action' => 'Follow up on overdue collections.',
                'metrics' => [['label' => 'Overdue', 'value' => $metrics['finance_overdue']]],
            ];
        }
        if ($metrics['procurement_open'] > 0) {
            $definitions[] = [
                'target_department' => 'procurement', 'department_label' => 'Procurement',
                'category' => 'open_purchase_orders', 'severity' => 'info', 'icon' => 'file-text',
                'title' => $metrics['procurement_open'] . ' Open Purchase Orders',
                'message' => 'Purchase orders awaiting delivery or approval.',
                'action' => 'Review open purchase orders.',
                'metrics' => [['label' => 'Open POs', 'value' => $metrics['procurement_open']]],
            ];
        }
        if ($metrics['manufacturing_active'] > 0) {
            $definitions[] = [
                'target_department' => 'manufacturing', 'department_label' => 'Manufacturing',
                'category' => 'active_work_orders', 'severity' => 'info', 'icon' => 'cpu',
                'title' => $metrics['manufacturing_active'] . ' Active Work Orders',
                'message' => 'Work orders currently in progress.',
                'action' => 'Monitor active work orders.',
                'metrics' => [['label' => 'Active', 'value' => $metrics['manufacturing_active']]],
            ];
        }
        if ($metrics['fulfillment_delayed'] > 0) {
            $definitions[] = [
                'target_department' => 'fulfillment', 'department_label' => 'Fulfillment',
                'category' => 'delayed_shipments', 'severity' => 'warning', 'icon' => 'truck',
                'title' => $metrics['fulfillment_delayed'] . ' Delayed Shipments',
                'message' => 'Shipments are past their due date.',
                'action' => 'Expedite delayed shipments.',
                'metrics' => [
                    ['label' => 'Delayed', 'value' => $metrics['fulfillment_delayed']],
                    ['label' => 'Total Orders', 'value' => $metrics['fulfillment_orders']],
                ],
            ];
        }

        return $definitions;
    }

    /**
     * Whether the shared dept_alerts store is reachable. Skips fast when the
     * business_intelligence connection isn't configured (so publishing is a
     * no-op until BUSINESS_INTELLIGENCE_DB_URL is set and the table exists).
     */
    private function deptAlertsAvailable(): bool
    {
        $config = config('database.connections.business_intelligence', []);
        if (empty($config['url']) && empty($config['host']) && empty($config['database'])) {
            return false;
        }

        try {
            return Schema::connection('business_intelligence')->hasTable('dept_alerts');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Publish the client's currently-firing alerts to the shared dept_alerts
     * table so each department can read its own inbox. Upserts one OPEN row
     * per condition (keyed by fingerprint) and auto-resolves open alerts whose
     * condition no longer fires. Entirely best-effort: any failure is logged
     * and swallowed so it can never break the warm cycle or a page load.
     *
     * @param  array<int, array<string, mixed>>  $definitions
     */
    private function publishDepartmentAlerts(?int $clientId, array $definitions): void
    {
        if (!$clientId || !$this->deptAlertsAvailable()) {
            return;
        }

        try {
            $connection = DB::connection('business_intelligence');
            $now = now();
            $firingFingerprints = [];

            foreach ($definitions as $definition) {
                $fingerprint = md5($clientId . '|' . $definition['target_department'] . '|' . $definition['category']);
                $firingFingerprints[] = $fingerprint;

                $connection->table('dept_alerts')->updateOrInsert(
                    [
                        'client_id' => $clientId,
                        'target_department' => $definition['target_department'],
                        'fingerprint' => $fingerprint,
                        'status' => 'open',
                    ],
                    [
                        'source_department' => 'business_intelligence',
                        'category' => $definition['category'],
                        'severity' => $definition['severity'],
                        'title' => $definition['title'],
                        'message' => $definition['message'],
                        'action' => $definition['action'] ?? null,
                        'metadata' => json_encode($definition['metrics'] ?? [], JSON_THROW_ON_ERROR),
                        'updated_at' => $now,
                    ]
                );
            }

            // Auto-resolve open alerts for this client that stopped firing.
            $stale = $connection->table('dept_alerts')
                ->where('client_id', $clientId)
                ->where('status', 'open');

            if ($firingFingerprints !== []) {
                $stale->whereNotIn('fingerprint', $firingFingerprints);
            }

            $stale->update(['status' => 'resolved', 'resolved_at' => $now, 'updated_at' => $now]);
        } catch (\Throwable $exception) {
            Log::warning('BI dept_alerts publish failed.', [
                'client_id' => $clientId,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Generate and cache a short AI briefing per department into
     * dept_alert_briefings. To keep the metered AI provider cheap, a
     * department's briefing is only regenerated when its alert set has
     * actually changed (tracked via alerts_hash) — most warm cycles make zero
     * AI calls. Departments whose alerts have all cleared have their briefing
     * removed. Fully best-effort: failures are logged and never break the run,
     * and a failed generation keeps the previous briefing and retries next
     * cycle.
     *
     * @param  array<int, array<string, mixed>>  $definitions
     */
    private function publishDepartmentBriefings(?int $clientId, array $definitions): void
    {
        if (!$clientId || !$this->aiConfigured() || !$this->deptAlertsAvailable()) {
            return;
        }

        try {
            if (!Schema::connection('business_intelligence')->hasTable('dept_alert_briefings')) {
                return;
            }

            $connection = DB::connection('business_intelligence');

            // Group the firing alerts by department.
            $byDepartment = [];
            foreach ($definitions as $definition) {
                $byDepartment[$definition['target_department']][] = $definition;
            }

            // Drop briefings for departments that no longer have any alerts.
            $activeDepartments = array_keys($byDepartment);
            $stale = $connection->table('dept_alert_briefings')->where('client_id', $clientId);
            if ($activeDepartments !== []) {
                $stale->whereNotIn('target_department', $activeDepartments);
            }
            $stale->delete();

            foreach ($byDepartment as $department => $alerts) {
                $hash = $this->alertsHash($alerts);

                $existing = $connection->table('dept_alert_briefings')
                    ->where('client_id', $clientId)
                    ->where('target_department', $department)
                    ->first();

                // No change since last time → skip the AI call entirely.
                if ($existing && $existing->alerts_hash === $hash) {
                    continue;
                }

                $text = $this->generateDepartmentBriefing($department, $alerts);
                if ($text === null) {
                    continue; // provider failed — keep the old briefing, retry next cycle
                }

                $connection->table('dept_alert_briefings')->updateOrInsert(
                    ['client_id' => $clientId, 'target_department' => $department],
                    ['ai_text' => $text, 'alerts_hash' => $hash, 'generated_at' => now(), 'updated_at' => now()]
                );
            }
        } catch (\Throwable $exception) {
            Log::warning('BI dept_alert_briefings publish failed.', [
                'client_id' => $clientId,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Stable fingerprint of a department's current alert set, so a briefing is
     * only regenerated when the alerts actually change.
     *
     * @param  array<int, array<string, mixed>>  $alerts
     */
    private function alertsHash(array $alerts): string
    {
        $parts = array_map(
            fn (array $a): string => $a['category'] . '|' . $a['severity'] . '|' . $a['title'] . '|' . $a['message'],
            $alerts
        );
        sort($parts);

        return md5(implode('||', $parts));
    }

    /**
     * Ask the configured AI provider for a short, department-scoped briefing
     * grounded strictly in that department's alerts. Returns null on any
     * failure so the caller keeps the previous briefing.
     *
     * @param  array<int, array<string, mixed>>  $alerts
     */
    private function generateDepartmentBriefing(string $department, array $alerts): ?string
    {
        try {
            $payload = array_map(fn (array $a): array => [
                'severity' => $a['severity'],
                'title' => $a['title'],
                'description' => $a['message'],
                'metrics' => $a['metrics'] ?? [],
            ], $alerts);

            $system = 'You are Nexora BI, writing a short internal alert briefing for ONE department of an ERP suite. '
                . 'Use ONLY the alert data provided. Never invent numbers, records, or other clients. '
                . 'Monetary values are Philippine pesos. Write 2-4 sentences: state the top priority first, note any '
                . 'data inconsistency you can infer from the alerts, and end with a concrete recommended action. '
                . 'Plain professional tone, no markdown, no preamble.';

            $user = "Department: {$department}\nActive alerts (JSON): " . json_encode($payload, JSON_THROW_ON_ERROR);

            $text = trim($this->aiProvider()->generate($system, $user)['content']);

            return $text === '' ? null : $text;
        } catch (\Throwable $exception) {
            Log::warning('BI department briefing generation failed.', [
                'department' => $department,
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function metrics(?int $clientId): array
    {
        return $this->remember($clientId, 'metrics', fn (): array => $this->computeMetrics($clientId));
    }

    private function computeMetrics(?int $clientId): array
    {
        $revenue = $this->financeInvoiceSum($clientId, 'paid_amount');
        $invoiced = $this->financeInvoicedTotal($clientId);
        $expenses = $this->sum('finance', 'expenses', 'total_expenses', $clientId, 'nexora_client_id');

        return [
            'revenue' => $revenue,
            'invoiced' => $invoiced,
            'expenses' => $expenses,
            'profit' => $revenue - $expenses,
            'finance_overdue' => $this->financeOverdueCount($clientId),
            'inventory_items' => $this->count('inventory', 'items', $clientId),
            'inventory_low_stock' => $this->lowStockCount($clientId),
            'procurement_open' => $this->openPurchaseOrders($clientId),
            'manufacturing_active' => $this->activeWorkOrders($clientId),
            'fulfillment_orders' => $this->count('order_fulfillment', 'orders', $clientId),
            'fulfillment_delayed' => $this->delayedShipments($clientId),
            'ecommerce_products' => $this->firstCount('ecommerce', ['storefront_listings', 'products', 'prebuilt_configs', 'configurator_configs'], $clientId),
        ];
    }

    private function departmentSummary(string $department, ?int $clientId): array
    {
        return $this->remember($clientId, 'dept_' . $department, fn (): array => $this->computeDepartmentSummary($department, $clientId));
    }

    private function computeDepartmentSummary(string $department, ?int $clientId): array
    {
        $metrics = $this->metrics($clientId);

        return match ($department) {
            'finance' => [
                'title' => 'Finance & Accounting',
                'stats' => [['label' => 'Revenue', 'value' => $metrics['revenue']], ['label' => 'Invoiced', 'value' => $metrics['invoiced']], ['label' => 'Expenses', 'value' => $metrics['expenses']], ['label' => 'Overdue', 'value' => $metrics['finance_overdue']]],
                'chart1' => ['type' => 'line', 'label' => 'Invoice revenue', 'data' => $this->financeTrend($clientId)],
                'chart2' => ['type' => 'bar', 'label' => 'Invoice status', 'data' => $this->financeStatusBreakdown($clientId)],
            ],
            'inventory' => [
                'title' => 'Inventory & Warehouse',
                'stats' => [['label' => 'Items', 'value' => $metrics['inventory_items']], ['label' => 'Low stock', 'value' => $metrics['inventory_low_stock']]],
                'chart1' => ['type' => 'bar', 'label' => 'Items by category', 'data' => $this->inventoryCategoryBreakdown($clientId)],
                'chart2' => ['type' => 'bar', 'label' => 'Stock alerts', 'data' => [['label' => 'Low stock', 'value' => $metrics['inventory_low_stock']]]],
            ],
            'procurement' => [
                'title' => 'Procurement',
                'stats' => [['label' => 'Open purchase orders', 'value' => $metrics['procurement_open']]],
                'chart1' => ['type' => 'doughnut', 'label' => 'Purchase order status', 'data' => $this->statusBreakdown('procurement', 'purchase_orders', $clientId)],
                'chart2' => ['type' => 'bar', 'label' => 'Open orders', 'data' => [['label' => 'Open', 'value' => $metrics['procurement_open']]]],
            ],
            'manufacturing' => [
                'title' => 'Manufacturing',
                'stats' => [['label' => 'Active work orders', 'value' => $metrics['manufacturing_active']]],
                'chart1' => ['type' => 'doughnut', 'label' => 'Work order status', 'data' => $this->statusBreakdown('manufacturing', 'work_orders', $clientId)],
                'chart2' => ['type' => 'bar', 'label' => 'Work orders in progress', 'data' => [['label' => 'Active', 'value' => $metrics['manufacturing_active']]]],
            ],
            'fulfillment' => [
                'title' => 'Order Fulfillment',
                'stats' => [['label' => 'Orders', 'value' => $metrics['fulfillment_orders']], ['label' => 'Delayed shipments', 'value' => $metrics['fulfillment_delayed']]],
                'chart1' => ['type' => 'doughnut', 'label' => 'Order status', 'data' => $this->statusBreakdown('order_fulfillment', 'orders', $clientId)],
                'chart2' => ['type' => 'bar', 'label' => 'Shipment risk', 'data' => [['label' => 'Delayed', 'value' => $metrics['fulfillment_delayed']]]],
            ],
            default => [
                'title' => 'E-commerce & CRM',
                'stats' => [['label' => 'Catalog records', 'value' => $metrics['ecommerce_products']]],
                'chart1' => ['type' => 'bar', 'label' => 'Catalog records', 'data' => [['label' => 'Products', 'value' => $metrics['ecommerce_products']]]],
                'chart2' => ['type' => 'bar', 'label' => 'Status', 'data' => []],
            ],
        };
    }

    /**
     * The five headline KPIs shown at the top of the executive dashboard,
     * derived from the client-scoped aggregate metrics.
     */
    private function dashboardKpis(array $metrics): array
    {
        $orders = $metrics['fulfillment_orders'];
        $onTime = $orders > 0
            ? round(max(0, $orders - $metrics['fulfillment_delayed']) / $orders * 100, 1)
            : null;

        return [
            [
                'icon' => 'dollar-sign',
                'label' => 'Total Revenue',
                'value' => '₱' . number_format($metrics['revenue'], 2),
                'change' => '',
                'change_class' => 'change-up',
            ],
            [
                'icon' => 'pie-chart',
                'label' => 'Gross Profit',
                'value' => '₱' . number_format($metrics['profit'], 2),
                'change' => $metrics['profit'] >= 0 ? '' : 'Operating at a loss',
                'change_class' => $metrics['profit'] >= 0 ? 'change-up' : 'change-down',
            ],
            [
                'icon' => 'shopping-cart',
                'label' => 'Fulfillment Orders',
                'value' => number_format($metrics['fulfillment_orders']),
                'change' => '',
                'change_class' => 'change-up',
            ],
            [
                'icon' => 'package',
                'label' => 'Inventory Items',
                'value' => number_format($metrics['inventory_items']),
                'change' => $metrics['inventory_low_stock'] > 0 ? $metrics['inventory_low_stock'] . ' low stock' : '',
                'change_class' => $metrics['inventory_low_stock'] > 0 ? 'change-down' : 'change-up',
            ],
            [
                'icon' => 'truck',
                'label' => 'On-Time Delivery',
                'value' => $onTime !== null ? $onTime . '%' : 'N/A',
                'change' => $onTime !== null ? '' : 'No orders recorded yet',
                'change_class' => ($onTime !== null && $onTime < 80) ? 'change-down' : 'change-up',
            ],
        ];
    }

    /**
     * Top products by units sold, from the Order Fulfillment line items
     * (order_items), which carry product_name, qty and product_amount scoped
     * by client_id. "prev_units" compares the prior 30-day window so the
     * dashboard can show a trend. Coverage/stock are omitted because these
     * ordered products don't map to the inventory catalog.
     */
    private function topProducts(?int $clientId): array
    {
        return $this->remember($clientId, 'top_products', fn (): array => $this->computeTopProducts($clientId));
    }

    private function computeTopProducts(?int $clientId): array
    {
        try {
            if (!$clientId) {
                return [];
            }

            $schema = Schema::connection('order_fulfillment');
            if (!$schema->hasTable('order_items') || !$schema->hasColumn('order_items', 'client_id')) {
                return [];
            }

            $connection = DB::connection('order_fulfillment');
            $windowStart = now()->subDays(30);
            $priorStart = now()->subDays(60);

            $base = fn () => $connection->table('order_items')->where('client_id', $clientId);

            $current = (clone $base())
                ->where('created_at', '>=', $windowStart)
                ->selectRaw('product_name, SUM(qty) as units, SUM(qty * product_amount) as revenue')
                ->groupBy('product_name')->orderByDesc('units')->limit(10)->get();

            // If nothing landed in the last 30 days, fall back to all-time so
            // the card still shows the client's products.
            if ($current->isEmpty()) {
                $current = (clone $base())
                    ->selectRaw('product_name, SUM(qty) as units, SUM(qty * product_amount) as revenue')
                    ->groupBy('product_name')->orderByDesc('units')->limit(10)->get();
            }

            $prev = (clone $base())
                ->whereBetween('created_at', [$priorStart, $windowStart])
                ->selectRaw('product_name, SUM(qty) as units')
                ->groupBy('product_name')->pluck('units', 'product_name');

            return $current->map(fn ($row) => [
                'name' => (string) $row->product_name,
                'units_sold' => (int) $row->units,
                'prev_units' => (int) ($prev[$row->product_name] ?? 0),
                'revenue' => (float) $row->revenue,
            ])->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Operational efficiency for the dashboard, computed from the
     * Manufacturing and Order Fulfillment databases. Always returns the
     * full nested structure the view expects, even when a source is empty.
     */
    private function operationalEfficiency(?int $clientId, array $metrics): array
    {
        return $this->remember($clientId, 'op_efficiency', fn (): array => $this->computeOperationalEfficiency($clientId, $metrics));
    }

    private function computeOperationalEfficiency(?int $clientId, array $metrics): array
    {
        $mfgTotal = $this->count('manufacturing', 'work_orders', $clientId);
        $mfgActive = $metrics['manufacturing_active'];
        $completion = $mfgTotal > 0 ? round(max(0, $mfgTotal - $mfgActive) / $mfgTotal * 100, 1) : null;

        $orders = $metrics['fulfillment_orders'];
        $delayed = $metrics['fulfillment_delayed'];
        $fulfillmentRate = $orders > 0 ? round(max(0, $orders - $delayed) / $orders * 100, 1) : null;

        [$mfgStatus, $mfgClass] = $completion !== null ? $this->healthStatus($completion) : ['No Data', 'health-yellow'];
        [$flfStatus, $flfClass] = $fulfillmentRate !== null ? $this->healthStatus($fulfillmentRate) : ['No Data', 'health-yellow'];

        $healths = array_values(array_filter([$completion, $fulfillmentRate], fn($v) => $v !== null));
        $overall = count($healths) ? round(array_sum($healths) / count($healths), 1) : 0;
        [$overallStatus, $overallClass] = count($healths) ? $this->healthStatus($overall) : ['No Data', 'health-yellow'];

        return [
            'overall' => ['percent' => $overall, 'status' => $overallStatus, 'class' => $overallClass],
            'summary_text' => $this->efficiencySummary($overall, $completion, $fulfillmentRate),
            'manufacturing' => [
                'percent' => $completion ?? 0,
                'health' => $mfgStatus,
                'class' => $mfgClass,
                'detail' => 'Live data from the Manufacturing database.',
                'metrics' => [
                    ['icon' => 'check-circle', 'label' => 'Completion Rate', 'value' => $completion !== null ? $completion . '%' : 'No data'],
                    ['icon' => 'hammer', 'label' => 'Active Work Orders', 'value' => number_format($mfgActive)],
                    ['icon' => 'list-checks', 'label' => 'Total Work Orders', 'value' => number_format($mfgTotal)],
                ],
            ],
            'fulfillment' => [
                'percent' => $fulfillmentRate ?? 0,
                'health' => $flfStatus,
                'class' => $flfClass,
                'detail' => 'Live data from the Order Fulfillment database.',
                'metrics' => [
                    ['icon' => 'package-check', 'label' => 'On-Time Rate', 'value' => $fulfillmentRate !== null ? $fulfillmentRate . '%' : 'No data'],
                    ['icon' => 'clock-alert', 'label' => 'Delayed Shipments', 'value' => number_format($delayed)],
                    ['icon' => 'boxes', 'label' => 'Total Orders', 'value' => number_format($orders)],
                ],
            ],
        ];
    }

    private function healthStatus(float $value): array
    {
        return match (true) {
            $value >= 80 => ['Healthy', 'health-green'],
            $value >= 60 => ['Stable', 'health-yellow'],
            $value >= 40 => ['Warning', 'health-orange'],
            default => ['Critical', 'health-red'],
        };
    }

    private function efficiencySummary(float $overall, ?float $completion, ?float $fulfillmentRate): string
    {
        $mfg = $completion !== null ? "Manufacturing is at {$completion}% completion" : 'Manufacturing has no work-order data yet';
        $flf = $fulfillmentRate !== null ? "fulfillment is at {$fulfillmentRate}% on-time" : 'fulfillment has no shipment data yet';

        $verdict = match (true) {
            $overall >= 80 => 'Overall operations are healthy.',
            $overall >= 60 => 'Some metrics need attention.',
            $overall >= 40 => 'Several metrics are below target.',
            default => 'Urgent action is required across operations.',
        };

        return ucfirst("{$mfg} and {$flf}. {$verdict}");
    }

    /**
     * How long a generated AI insight bundle stays cached (seconds). AI calls
     * are slow and metered, so this is deliberately longer than the metric
     * cache.
     */
    private const AI_CACHE_TTL = 1800;

    private function aiProvider(): AIProviderInterface
    {
        return app(AIRouter::class)->provider();
    }

    /**
     * Whether an AI provider is usable — the default provider has an API key.
     */
    private function aiConfigured(): bool
    {
        try {
            $provider = config('ai.default');

            return $provider !== null && (bool) config("ai.providers.{$provider}.api_key");
        } catch (\Throwable) {
            return false;
        }
    }

    private function aiSystemPrompt(): string
    {
        return 'You are Nexora BI, a business analyst for an enterprise ERP suite. Answer only from the client-scoped aggregate metrics supplied to you. Never claim access to raw records, other clients, credentials, personal information, or system internals. All monetary values are in Philippine pesos (PHP). If the metrics cannot answer a question, say so plainly and suggest a safe next metric to add. Keep answers practical and concise.';
    }

    /**
     * Read the pre-generated AI insight bundle from cache. This never makes a
     * live AI call — generation happens off the request path in warmCache() on
     * the schedule — so the AI Insights page load never blocks on the model.
     * Returns null when no report has been generated yet, letting the caller
     * fall back to rule-based insights.
     *
     * @return array{executiveSummary: array, recommendations: array, risks: array}|null
     */
    private function cachedAiInsight(?int $clientId): ?array
    {
        if (!$clientId) {
            return null;
        }

        try {
            return Cache::get('bi_ai_report_' . $clientId);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Make the live AI call, map the result to the view's shape, and cache it
     * for AI_CACHE_TTL seconds. Intended to run off the request path (the
     * background warm-cache schedule), so its latency and any transient
     * provider errors never reach a user. Returns the mapped bundle on
     * success; on failure it returns null WITHOUT caching, so the next warm
     * cycle simply retries.
     *
     * @return array{executiveSummary: array, recommendations: array, risks: array}|null
     */
    private function generateAiInsight(?int $clientId, array $metrics): ?array
    {
        if (!$clientId || !$this->aiConfigured()) {
            return null;
        }

        try {
            $prompt = 'Analyse these client-scoped BI metrics and produce concise insights. '
                . 'Metrics (JSON): ' . json_encode($metrics, JSON_THROW_ON_ERROR) . "\n\n"
                . 'Return ONLY a JSON object with exactly these keys: '
                . '"executive_summary": a 1-2 sentence string; '
                . '"recommendations": an array of up to 4 objects each with "title", "detail", and "impact" (one of High, Med, Low); '
                . '"risks": an array of up to 4 objects each with "title", "detail", and "severity" (one of High, Medium, Low). '
                . 'Base everything strictly on the metrics. Monetary values are Philippine pesos.';

            $raw = $this->aiProvider()->generate($this->aiSystemPrompt(), $prompt, jsonMode: true, thinkingLevel: 'medium')['content'];
            $parsed = json_decode(trim(preg_replace('/^```(?:json)?|```$/m', '', trim((string) $raw))), true);

            if (!is_array($parsed)) {
                return null;
            }

            $mapped = $this->mapAiReport($parsed);
            Cache::put('bi_ai_report_' . $clientId, $mapped, self::AI_CACHE_TTL);

            return $mapped;
        } catch (\Throwable $exception) {
            Log::warning('BI AI insight generation failed.', [
                'client_id' => $clientId,
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Map the AI's raw JSON report onto the structures the ai-insights view
     * iterates over.
     */
    private function mapAiReport(array $parsed): array
    {
        $executiveSummary = [];
        if (!empty($parsed['executive_summary'])) {
            $executiveSummary[] = [
                'color' => 'blue',
                'icon' => 'brain',
                'text' => (string) $parsed['executive_summary'],
                'sub_text' => 'Generated by Nexora AI',
            ];
        }

        $recommendations = collect($parsed['recommendations'] ?? [])->map(fn ($item): array => [
            'title' => (string) ($item['title'] ?? 'Recommendation'),
            'description' => (string) ($item['detail'] ?? $item['description'] ?? ''),
            'impact' => $this->normalizeImpact((string) ($item['impact'] ?? 'Med')),
        ])->all();

        $risks = collect($parsed['risks'] ?? [])->map(function ($item): array {
            $level = ucfirst(strtolower((string) ($item['severity'] ?? $item['level'] ?? 'Medium')));

            return [
                'color' => match ($level) {
                    'Critical', 'High' => 'red',
                    'Medium', 'Med' => 'orange',
                    'Low' => 'green',
                    default => 'blue',
                },
                'icon' => 'alert-triangle',
                'title' => (string) ($item['title'] ?? 'Risk'),
                'description' => (string) ($item['detail'] ?? $item['description'] ?? ''),
                'level' => $level === 'Med' ? 'Medium' : $level,
            ];
        })->all();

        return [
            'executiveSummary' => $executiveSummary,
            'recommendations' => $recommendations,
            'risks' => $risks,
        ];
    }

    /**
     * Recommendation impact normalised to High/Med/Low so it matches the
     * .mb-{impact}-impact badge classes in the stylesheet.
     */
    private function normalizeImpact(string $impact): string
    {
        return match (strtolower($impact)) {
            'high' => 'High',
            'low' => 'Low',
            default => 'Med',
        };
    }

    /**
     * Rule-based "Recent System Alerts" for the AI Insights page, built from
     * the client-scoped metrics (no external AI provider required).
     */
    private function insightAlerts(array $metrics): array
    {
        $alerts = [];

        if ($metrics['inventory_low_stock'] > 0) {
            $alerts[] = [
                'type' => 'critical',
                'icon' => 'alert-triangle',
                'title' => 'Low Stock Detected',
                'time' => 'Live',
                'priority' => 'High priority',
                'description' => "{$metrics['inventory_low_stock']} inventory records are at or below their reorder threshold.",
                'action' => 'Review and reorder affected items.',
            ];
        }
        if ($metrics['finance_overdue'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'dollar-sign',
                'title' => 'Overdue Invoices',
                'time' => 'Live',
                'priority' => 'Medium priority',
                'description' => "{$metrics['finance_overdue']} invoices are past their due date.",
                'action' => 'Follow up on collections.',
            ];
        }
        if ($metrics['fulfillment_delayed'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'truck',
                'title' => 'Delayed Shipments',
                'time' => 'Live',
                'priority' => 'Medium priority',
                'description' => "{$metrics['fulfillment_delayed']} shipments are past their due date.",
                'action' => 'Expedite delayed deliveries.',
            ];
        }
        if ($metrics['procurement_open'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'file-text',
                'title' => 'Open Purchase Orders',
                'time' => 'Live',
                'priority' => 'Informational',
                'description' => "{$metrics['procurement_open']} purchase orders remain active.",
                'action' => 'Monitor delivery timelines.',
            ];
        }

        return $alerts;
    }

    private function executiveSummary(array $metrics): array
    {
        $summary = [
            [
                'color' => $metrics['profit'] >= 0 ? 'green' : 'red',
                'icon' => $metrics['profit'] >= 0 ? 'trending-up' : 'trending-down',
                'text' => $metrics['profit'] >= 0
                    ? 'The business is profitable, with revenue exceeding recorded expenses.'
                    : 'Expenses currently exceed revenue — margins need attention.',
                'sub_text' => 'Revenue ₱' . number_format($metrics['revenue'], 2) . ' · Expenses ₱' . number_format($metrics['expenses'], 2),
            ],
            [
                'color' => 'blue',
                'icon' => 'file-text',
                'text' => 'Total invoiced value across all clients this period.',
                'sub_text' => '₱' . number_format($metrics['invoiced'], 2) . ' invoiced',
            ],
        ];

        if ($metrics['inventory_low_stock'] > 0) {
            $summary[] = [
                'color' => 'orange',
                'icon' => 'package',
                'text' => "{$metrics['inventory_low_stock']} inventory items are low on stock and may need replenishment.",
                'sub_text' => number_format($metrics['inventory_items']) . ' items tracked',
            ];
        }

        return $summary;
    }

    private function recommendations(array $metrics): array
    {
        $recommendations = [];

        if ($metrics['finance_overdue'] > 0) {
            $recommendations[] = [
                'title' => 'Accelerate collections',
                'description' => "Chase the {$metrics['finance_overdue']} overdue invoices to improve cash flow.",
                'impact' => 'High',
            ];
        }
        if ($metrics['inventory_low_stock'] > 0) {
            $recommendations[] = [
                'title' => 'Replenish low stock',
                'description' => "Reorder the {$metrics['inventory_low_stock']} items sitting below their reorder threshold.",
                'impact' => 'High',
            ];
        }
        if ($metrics['fulfillment_delayed'] > 0) {
            $recommendations[] = [
                'title' => 'Clear delayed shipments',
                'description' => "Prioritise the {$metrics['fulfillment_delayed']} shipments running past their due date.",
                'impact' => 'Med',
            ];
        }
        if ($metrics['procurement_open'] > 0) {
            $recommendations[] = [
                'title' => 'Review open purchase orders',
                'description' => "Confirm delivery timelines for {$metrics['procurement_open']} active purchase orders.",
                'impact' => 'Low',
            ];
        }

        return $recommendations;
    }

    private function riskDetection(array $metrics): array
    {
        $risks = [];

        if ($metrics['inventory_low_stock'] > 0) {
            $risks[] = [
                'color' => 'red',
                'icon' => 'package-x',
                'title' => 'Stockout risk',
                'description' => "{$metrics['inventory_low_stock']} items are at or below reorder level.",
                'level' => 'High',
            ];
        }
        if ($metrics['finance_overdue'] > 0) {
            $risks[] = [
                'color' => 'orange',
                'icon' => 'dollar-sign',
                'title' => 'Cash-flow risk',
                'description' => "{$metrics['finance_overdue']} overdue invoices are outstanding.",
                'level' => 'Medium',
            ];
        }
        if ($metrics['fulfillment_delayed'] > 0) {
            $risks[] = [
                'color' => 'orange',
                'icon' => 'truck',
                'title' => 'Delivery risk',
                'description' => "{$metrics['fulfillment_delayed']} shipments are past due.",
                'level' => 'Medium',
            ];
        }
        if ($metrics['profit'] < 0) {
            $risks[] = [
                'color' => 'red',
                'icon' => 'trending-down',
                'title' => 'Margin risk',
                'description' => 'Expenses currently exceed revenue for this period.',
                'level' => 'High',
            ];
        }

        return $risks;
    }

    /**
     * How long a computed client-scoped result stays cached before the next
     * page load or poll is allowed to re-query the remote department
     * databases. This is what stops every navigation from firing a fresh
     * batch of cross-database queries.
     */
    private const CACHE_TTL = 60;

    /**
     * Cache the result of an expensive client-scoped computation for
     * CACHE_TTL seconds. The key carries the client id and a per-client
     * version so a "sync" can invalidate everything at once. Falls back to
     * computing directly if the cache store is unavailable.
     */
    private function remember(?int $clientId, string $key, callable $callback): mixed
    {
        try {
            $cacheKey = 'bi_' . $key . '_' . ($clientId ?? 0) . '_v' . $this->cacheVersion($clientId);

            return Cache::remember($cacheKey, self::CACHE_TTL, $callback);
        } catch (\Throwable) {
            return $callback();
        }
    }

    private function cacheVersion(?int $clientId): int
    {
        try {
            return (int) Cache::get('bi_cache_version_' . ($clientId ?? 0), 1);
        } catch (\Throwable) {
            return 1;
        }
    }

    /**
     * Overwrite a cached result in place (same key as remember()), used by
     * warmCache() to refresh values without a version bump so readers keep
     * hitting the previous value until the fresh one lands.
     */
    private function cachePut(?int $clientId, string $key, mixed $value): void
    {
        try {
            Cache::put('bi_' . $key . '_' . ($clientId ?? 0) . '_v' . $this->cacheVersion($clientId), $value, self::CACHE_TTL);
        } catch (\Throwable) {
            // Cache unavailable — warming is best-effort.
        }
    }

    /**
     * Invalidate every cached result for a client so the next request
     * re-queries live data — used by the dashboard "sync" action.
     */
    private function bustCache(?int $clientId): void
    {
        try {
            Cache::forever('bi_cache_version_' . ($clientId ?? 0), $this->cacheVersion($clientId) + 1);
        } catch (\Throwable) {
            // Cache unavailable — nothing to invalidate.
        }
    }

    private function clientId(Request $request): ?int
    {
        if (session('employee_client_id')) {
            return (int) session('employee_client_id');
        }

        if (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin') {
            return $request->integer('client_id') ?: null;
        }

        return null;
    }

    private function query(string $connection, string $table, ?int $clientId, string $tenantColumn = 'client_id'): ?Builder
    {
        try {
            $schema = Schema::connection($connection);
            if (!$clientId || !$schema->hasTable($table) || !$schema->hasColumn($table, $tenantColumn)) {
                return null;
            }

            return DB::connection($connection)->table($table)->where($tenantColumn, $clientId);
        } catch (\Throwable) {
            return null;
        }
    }

    private function count(string $connection, string $table, ?int $clientId, string $tenantColumn = 'client_id'): int
    {
        try {
            return (int) ($this->query($connection, $table, $clientId, $tenantColumn)?->count() ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function firstCount(string $connection, array $tables, ?int $clientId): int
    {
        foreach ($tables as $table) {
            $count = $this->count($connection, $table, $clientId);
            if ($count > 0 || $this->query($connection, $table, $clientId)) {
                return $count;
            }
        }

        return 0;
    }

    private function sum(string $connection, string $table, string $column, ?int $clientId, string $tenantColumn = 'client_id'): float
    {
        try {
            if (!Schema::connection($connection)->hasColumn($table, $column)) {
                return 0.0;
            }
            return (float) ($this->query($connection, $table, $clientId, $tenantColumn)?->sum($column) ?? 0);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    private function countWhere(string $connection, string $table, ?int $clientId, string $tenantColumn, string $column, mixed $value): int
    {
        try {
            if (!Schema::connection($connection)->hasColumn($table, $column)) {
                return 0;
            }
            return (int) ($this->query($connection, $table, $clientId, $tenantColumn)?->where($column, $value)->count() ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function lowStockCount(?int $clientId): int
    {
        try {
            $query = $this->query('inventory', 'stock_levels', $clientId);
            return (int) ($query?->where(function (Builder $query): void{
                $query->whereColumn('stock', '<=', 'reserved_quantity')
                    ->orWhere(function (Builder $query): void{
                        $query->where('reorder_threshold', '>', 0)
                            ->whereRaw('(stock - reserved_quantity) <= reorder_threshold');
                    });
            })->count() ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function openPurchaseOrders(?int $clientId): int
    {
        try {
            return (int) ($this->query('procurement', 'purchase_orders', $clientId)
                    ?->whereNotIn('status', ['received', 'cancelled', 'closed'])->count() ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function activeWorkOrders(?int $clientId): int
    {
        try {
            return (int) ($this->query('manufacturing', 'work_orders', $clientId)
                    ?->whereNotIn('status', ['Finished', 'Cancelled', 'completed', 'cancelled'])->count() ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function delayedShipments(?int $clientId): int
    {
        try {
            return (int) ($this->query('order_fulfillment', 'shipments', $clientId)
                    ?->whereDate('due_date', '<', today())
                ->whereNotIn('status', ['Delivered', 'Completed', 'delivered', 'completed'])->count() ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function groupCount(string $connection, string $table, string $column, ?int $clientId): array
    {
        try {
            if (!Schema::connection($connection)->hasColumn($table, $column)) {
                return [];
            }
            return $this->query($connection, $table, $clientId)?->selectRaw("{$column} as label, COUNT(*) as value")
                ->groupBy($column)->orderByDesc('value')->limit(8)->get()->map(fn($row) => ['label' => (string) ($row->label ?? 'Unassigned'), 'value' => (int) $row->value])->all() ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function statusBreakdown(string $connection, string $table, ?int $clientId, string $tenantColumn = 'client_id'): array
    {
        try {
            if (!Schema::connection($connection)->hasColumn($table, 'status')) {
                return [];
            }
            return $this->query($connection, $table, $clientId, $tenantColumn)?->selectRaw('status as label, COUNT(*) as value')
                ->groupBy('status')->orderByDesc('value')->get()->map(fn($row) => ['label' => (string) ($row->label ?? 'Unspecified'), 'value' => (int) $row->value])->all() ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function financeTrend(?int $clientId): array
    {
        try {
            return $this->financeInvoiceQuery($clientId)?->whereDate('issue_date', '>=', now()->subDays(6))
                ->selectRaw('DATE(issue_date) as label, COALESCE(SUM(paid_amount), 0) as value')
                ->groupBy('label')->orderBy('label')->get()->map(fn($row) => ['label' => $row->label, 'value' => (float) $row->value])->all() ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Finance invoices have no tenant column of their own — they relate to a
     * client only through order_id -> order_fulfillment.orders.client_id.
     * Returns an invoice query already restricted to the client's orders, a
     * query that yields nothing when the client has no orders, or null when
     * finance is unavailable.
     */
    private function financeInvoiceQuery(?int $clientId): ?Builder
    {
        try {
            if (!$clientId) {
                return null;
            }

            $schema = Schema::connection('finance');
            if (!$schema->hasTable('invoice') || !$schema->hasColumn('invoice', 'order_id')) {
                return null;
            }

            $orderIds = $this->clientOrderIds($clientId);
            $query = DB::connection('finance')->table('invoice');

            return empty($orderIds) ? $query->whereRaw('1 = 0') : $query->whereIn('order_id', $orderIds);
        } catch (\Throwable) {
            return null;
        }
    }

    private function clientOrderIds(?int $clientId): array
    {
        try {
            return $this->query('order_fulfillment', 'orders', $clientId)?->pluck('id')->all() ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function financeInvoiceSum(?int $clientId, string $column): float
    {
        try {
            return (float) ($this->financeInvoiceQuery($clientId)?->sum($column) ?? 0);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    private function financeInvoicedTotal(?int $clientId): float
    {
        try {
            $query = $this->financeInvoiceQuery($clientId);
            if (!$query) {
                return 0.0;
            }

            return (float) ($query->selectRaw('COALESCE(SUM(paid_amount + outstanding_amount), 0) as total')->value('total') ?? 0);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    private function financeOverdueCount(?int $clientId): int
    {
        try {
            $query = $this->financeInvoiceQuery($clientId);
            if (!$query) {
                return 0;
            }

            return (int) $query->where('payment_status', 'Unpaid')->whereDate('due_date', '<', today())->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    private function financeStatusBreakdown(?int $clientId): array
    {
        try {
            return $this->financeInvoiceQuery($clientId)?->selectRaw('status as label, COUNT(*) as value')
                ->groupBy('status')->orderByDesc('value')->get()
                ->map(fn($row) => ['label' => (string) ($row->label ?? 'Unspecified'), 'value' => (int) $row->value])->all() ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Item counts grouped by category name. items.category_id has no name of
     * its own, so this joins the inventory categories table. Columns are
     * table-qualified because both items and categories carry a client_id,
     * which would otherwise make the tenant filter ambiguous.
     */
    private function inventoryCategoryBreakdown(?int $clientId): array
    {
        try {
            $schema = Schema::connection('inventory');
            if (!$clientId || !$schema->hasTable('items')
                || !$schema->hasColumn('items', 'category_id') || !$schema->hasColumn('items', 'client_id')) {
                return [];
            }

            $query = DB::connection('inventory')->table('items')->where('items.client_id', $clientId);

            if ($schema->hasTable('categories') && $schema->hasColumn('categories', 'name')) {
                return $query->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                    ->selectRaw("COALESCE(categories.name, 'Uncategorized') as label, COUNT(*) as value")
                    ->groupBy('label')->orderByDesc('value')->limit(8)->get()
                    ->map(fn($row) => ['label' => (string) $row->label, 'value' => (int) $row->value])->all();
            }

            return $query->selectRaw('category_id as label, COUNT(*) as value')
                ->groupBy('category_id')->orderByDesc('value')->limit(8)->get()
                ->map(fn($row) => ['label' => (string) ($row->label ?? 'Uncategorized'), 'value' => (int) $row->value])->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function alert(string $severity, string $icon, string $department, string $title, string $description, array $metrics = []): array
    {
        return [
            'severity' => $severity,
            'icon' => $icon,
            'department' => $department,
            'title' => $title,
            'description' => $description,
            'metrics' => $metrics,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function recordSnapshot(?int $clientId, array $metrics): void
    {
        if (!$clientId || !config('database.connections.business_intelligence.url')) {
            return;
        }

        try {
            if (!Schema::connection('business_intelligence')->hasTable('bi_snapshots')) {
                return;
            }

            DB::connection('business_intelligence')->table('bi_snapshots')->updateOrInsert(
                ['client_id' => $clientId, 'source' => 'live-dashboard'],
                ['payload' => json_encode($metrics, JSON_THROW_ON_ERROR), 'captured_at' => now(), 'updated_at' => now()]
            );
        } catch (\Throwable) {
            // BI must remain read-only and available when its optional
            // snapshot store is temporarily unavailable.
        }
    }

    private function recordConversation(int $clientId, string $role, string $message, bool $usedAi): void
    {
        if (!config('database.connections.business_intelligence.url')) {
            return;
        }

        try {
            if (!Schema::connection('business_intelligence')->hasTable('bi_ai_conversations')) {
                return;
            }

            DB::connection('business_intelligence')->table('bi_ai_conversations')->insert([
                'client_id' => $clientId,
                'employee_id' => session('employee_id'),
                'role' => $role,
                'message' => $message,
                'used_ai' => $usedAi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable) {
            // Conversation auditing must not prevent the client from using BI.
        }
    }
}
