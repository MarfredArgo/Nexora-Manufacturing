<?php

namespace App\Services\Departments;

use App\Models\FinanceDeptInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    protected ?float $revenueCache = null;

    public function getSnapshot(): array
    {
        return [
            'revenue' => $this->totalRevenue(),
            'expenses' => $this->totalExpenses(),
            'profit_margin' => $this->profitMarginPercent(),
            'overdue_payments' => $this->overduePaymentsTotal(),
            'overdue_count' => $this->overduePaymentsCount(),
            'revenue_by_category' => $this->revenueByStatus(),
        ];
    }

    public function getKpiSummaryForAi(): array
    {
        return [
            'revenue' => $this->totalRevenue(),
            'profit_margin_percent' => $this->profitMarginPercent(),
            'expenses' => 0.0,
            'overdue_payments_total' => $this->overduePaymentsTotal(),
            'overdue_payments_count' => $this->overduePaymentsCount(),
        ];
    }

    public function totalRevenue(): float
    {
        return $this->revenueCache ??= (float) FinanceDeptInvoice::sum('paid_amount');
    }

    public function totalExpenses(): float
    {
        $expenses = DB::table('finance_dept_expenses')->latest('id')->first();
        return (float) ($expenses->total_expenses ?? 0.0);
    }

    public function profitMarginPercent(): float
    {
        $invoiced = (float) FinanceDeptInvoice::sum('invoice_amount');
        if ($invoiced <= 0) {
            return 0.0;
        }
        $revenue = $this->totalRevenue();
        return round(($revenue / $invoiced) * 100, 2);
    }

    public function overduePaymentsTotal(): float
    {
        return (float) FinanceDeptInvoice::where('status', 'Overdue')
            ->sum('outstanding_amount');
    }

    public function overduePaymentsCount(): int
    {
        return FinanceDeptInvoice::where('status', 'Overdue')->count();
    }

    public function revenueByStatus(): array
    {
        return FinanceDeptInvoice::select(
            'status',
            DB::raw('SUM(invoice_amount) as total')
        )
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'category' => $row->status,
                'total' => (float) $row->total,
            ])
            ->toArray();
    }

    public function revenueTrend(int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $rows = FinanceDeptInvoice::whereDate('issue_date', '>=', $start)
            ->select(
                'issue_date',
                DB::raw('SUM(invoice_amount) as total')
            )
            ->groupBy('issue_date')
            ->orderBy('issue_date')
            ->get()
            ->keyBy(fn($row) => Carbon::parse($row->issue_date)->toDateString());

        $trend = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();

            $trend[] = [
                'date' => $date,
                'total' => isset($rows[$date])
                    ? (float) $rows[$date]->total
                    : 0.0,
            ];
        }

        return $trend;
    }
}