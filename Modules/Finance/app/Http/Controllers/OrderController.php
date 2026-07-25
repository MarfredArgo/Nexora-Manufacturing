<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Finance\Models\order;
use Modules\Finance\Models\orderitems;
use Modules\Finance\Models\Invoice;

use Carbon\Carbon;

class OrderController extends Controller
{

    public function store(Request $request)
{
    $request->validate([

    'order_id' => 'required|string',

    'customer_name' => 'required|string',

    'due_date' => 'required|date',

    'items' => 'required|array|min:1',

    'items.*.product_name' => 'required|string',

    'items.*.qty' => 'required|integer|min:1',

    'items.*.product_amount' => 'required|numeric|min:0',

]);


        try {

            /*
            |--------------------------------------------------------------------------
            | Create Order
            |--------------------------------------------------------------------------
            */

            $order = order::create([

                'id' => $request->order_id,

                'customer_name' => $request->customer_name,

                'address' => $request->address,

                'status' => 'NEW',

                'due_date' => $request->due_date,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Order Items
            |--------------------------------------------------------------------------
            */

            $total = 0;


            foreach ($request->items as $item) {


    orderitems::create([

        'order_id' => $order->id,

        'product_name' => $item['product_name'],

        'qty' => $item['qty'],

        'product_amount' => $item['product_amount']

    ]);


    $total += $item['qty'] * $item['product_amount'];

}


            /*
            |--------------------------------------------------------------------------
            | Generate Invoice Automatically
            |--------------------------------------------------------------------------
            */

            $newInvoice = Invoice::create([

    'order_id' => $order->id,

    'issue_date' => Carbon::now(),

    'due_date' => $order->due_date,

    'status' => 'Pending',

    'payment_status' => 'Unpaid',

    'paid_amount' => 0,

    'outstanding_amount' => $total,

]);







            return response()->json([

                'success' => true,

                'order_id' => $order->id,

                'message' => 'Order created and invoice generated.'

            ]);



        }catch (\Exception $e) {



    return response()->json([

        'success' => false,

        'error' => $e->getMessage(),

        'previous' => $e->getPrevious()?->getMessage(),

        'line' => $e->getLine(),

        'file' => $e->getFile()

    ],500);

}

    }

}
