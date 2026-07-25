<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->string('range', '6months')->toString();
        $query = DB::connection('procurement')->table('purchase_orders');
        $clientId = session('employee_client_id');

        if (Schema::connection('procurement')->hasColumn('purchase_orders', 'client_id')) {
            if ($clientId) {
                $query->where('client_id', $clientId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $expenses = (clone $query)->select('po_number', 'item', 'qty', 'amount', 'status', 'created_at')->get();
        $procurementTotal = (float) (clone $query)->selectRaw('COALESCE(SUM(qty * amount), 0) as total')->value('total');
        $from = match ($range) { 'week' => now()->subDays(6), 'month' => now()->subMonth(), 'year' => now()->subYear(), default => now()->subMonths(6) };
        $dateFormat = $range === 'week' ? "TO_CHAR(order_date, 'Dy')" : ($range === 'month' ? "CONCAT('Week ', EXTRACT(WEEK FROM order_date)::int)" : "TO_CHAR(order_date, 'YYYY-MM')");
        $monthly = (clone $query)->selectRaw("{$dateFormat} as month, SUM(qty * amount) as total")->where('order_date', '>=', $from)->groupByRaw($dateFormat)->orderByRaw('MIN(order_date)')->get();

        return view('finance::expensesdash', ['expenses' => $expenses, 'procurementTotal' => $procurementTotal, 'overallExpenses' => $procurementTotal, 'procurementPercent' => $procurementTotal > 0 ? 100 : 0, 'labels' => $monthly->pluck('month')->all(), 'totals' => $monthly->pluck('total')->map(fn ($total) => (float) $total)->all(), 'range' => $range]);
    }
}
