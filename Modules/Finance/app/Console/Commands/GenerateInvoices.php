<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Models\order;
use Modules\Finance\Services\InvoiceGenerator;

class GenerateInvoices extends Command
{
    protected $signature = 'invoices:generate';

    protected $description = 'Generate invoices for NEW orders';


    public function handle(InvoiceGenerator $generator)
    {
        // Get all orders that are NEW
        $orders = order::where('status', 'NEW')->get();


        foreach ($orders as $order) {

            try {

                // Generate invoice or return existing invoice
                $invoice = $generator->generate($order->id);


                $this->info(
                    "Invoice processed for order: {$order->id}"
                );

            } catch (\Exception $e) {

                $this->error(
                    "Failed {$order->id}: {$e->getMessage()}"
                );

            }
        }


        return Command::SUCCESS;
    }
}