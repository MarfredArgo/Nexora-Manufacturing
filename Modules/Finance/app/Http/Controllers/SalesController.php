<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Finance\Models\Invoice;

class SalesController extends Controller
{

    public function index()
    {

        $totalSales = Invoice::where('payment_status', 'Paid')
            ->sum('paid_amount');


        return view('finance::salesdash', [

            'totalSales' => $totalSales

        ]);

    }

}
