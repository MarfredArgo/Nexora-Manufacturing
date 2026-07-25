<?php

namespace Modules\Finance\Http\Controllers;

use Carbon\Carbon;
use Modules\Finance\Models\Invoice;

class DashboardController extends Controller
{
    /**
     * The Finance application shell. Keep this separate from the iframe
     * overview so that /finance/dashboard can be the entry point without
     * loading the shell inside itself.
     */
    public function shell()
    {
        return view('finance::maindash');
    }

    /**
     * Client-scoped Finance overview displayed inside the application shell.
     */
    public function overview()
    {
        $invoices = Invoice::query()
            ->orderByDesc('issue_date')
            ->orderByDesc('invoice_id')
            ->get();

        $today = Carbon::today();
        $paid = (float) $invoices
            ->filter(fn (Invoice $invoice): bool => strtolower((string) $invoice->payment_status) === 'paid')
            ->sum('paid_amount');
        $unpaid = (float) $invoices->sum('outstanding_amount');
        $overdue = (float) $invoices
            ->filter(fn (Invoice $invoice): bool => $invoice->due_date !== null
                && Carbon::parse($invoice->due_date)->lt($today)
                && (float) $invoice->outstanding_amount > 0)
            ->sum('outstanding_amount');
        $invoiceTotal = (float) $invoices->sum(fn (Invoice $invoice): float => (float) $invoice->paid_amount + (float) $invoice->outstanding_amount);

        $startOfWeek = $today->copy()->startOfWeek();
        $weeklyInvoices = $invoices
            ->filter(fn (Invoice $invoice): bool => $invoice->issue_date !== null && Carbon::parse($invoice->issue_date)->gte($startOfWeek));
        $weekLabels = collect(range(0, 6))->map(fn (int $day): string => $startOfWeek->copy()->addDays($day)->format('D'));
        $invoiceValues = $weekLabels->map(function (string $label, int $day) use ($weeklyInvoices, $startOfWeek): float {
            $date = $startOfWeek->copy()->addDays($day)->toDateString();

            return (float) $weeklyInvoices
                ->filter(fn (Invoice $invoice): bool => Carbon::parse($invoice->issue_date)->toDateString() === $date)
                ->sum(fn (Invoice $invoice): float => (float) $invoice->paid_amount + (float) $invoice->outstanding_amount);
        });
        $paidValues = $weekLabels->map(function (string $label, int $day) use ($weeklyInvoices, $startOfWeek): float {
            $date = $startOfWeek->copy()->addDays($day)->toDateString();

            return (float) $weeklyInvoices
                ->filter(fn (Invoice $invoice): bool => Carbon::parse($invoice->issue_date)->toDateString() === $date)
                ->sum('paid_amount');
        });

        $recentActivity = $invoices->take(8)->map(function (Invoice $invoice): array {
            $isPaid = strtolower((string) $invoice->payment_status) === 'paid';

            return [
                'date' => $invoice->issue_date?->format('M d, Y') ?? '—',
                'desc' => 'Invoice '.($invoice->reference_number ?: '#'.$invoice->invoice_id),
                'category' => 'E-Commerce order',
                'amount' => (float) $invoice->paid_amount + (float) $invoice->outstanding_amount,
                'status' => $isPaid ? 'Success' : 'Pending',
            ];
        })->values();

        return view('finance::dashboard', [
            'financeDashboard' => [
                'paid' => $paid,
                'unpaid' => $unpaid,
                'overdue' => $overdue,
                'invoice_total' => $invoiceTotal,
                'week_labels' => $weekLabels->values(),
                'invoice_values' => $invoiceValues->values(),
                'paid_values' => $paidValues->values(),
                'recent_activity' => $recentActivity,
            ],
        ]);
    }
}
