<?php

namespace Modules\Finance\Http\Controllers;

class FinanceController extends Controller
{
    public function index()
    {
        return view('finance::maindash');
    }

    public function dashboard()
    {
        return view('finance::dashboard');
    }

    public function sales()
    {
        return view('finance::salesdash');
    }

    public function cashflow()
    {
        return view('finance::cashflowdash');
    }
}
