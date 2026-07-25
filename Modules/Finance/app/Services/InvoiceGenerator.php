<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\order;
use Modules\Finance\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceGenerator
{
    public function generate($orderId)
    {
        // 1. Find order from order fulfillment database
        $order = order::find($orderId);

        if (!$order) {
            throw new \Exception("Order not found.");
        }


        // 2. Only NEW orders can generate invoices
        if ($order->status !== 'NEW') {
            throw new \Exception(
                "Order status is not NEW."
            );
        }


        // 3. Prevent duplicate invoice creation
        $existingInvoice = Invoice::where(
            'order_id',
            $order->id
        )->first();


        if ($existingInvoice) {
            return $existingInvoice;
        }


        // 4. Calculate order total
        $total = $order->items->sum(function ($item) {

            return $item->qty * $item->product_amount;

        });


        // 5. Create invoice in finance database
        $invoice = Invoice::create([

            'order_id' => $order->id,

            'issue_date' => now(),

            'due_date' => $order->due_date,

            'status' => 'Pending',

            'payment_status' => 'Unpaid',

            'paid_amount' => 0,

            'outstanding_amount' => $total,

        ]);


        return $invoice;
    }
}
