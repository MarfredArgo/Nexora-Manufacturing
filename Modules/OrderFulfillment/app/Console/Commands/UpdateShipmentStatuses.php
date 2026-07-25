<?php

namespace Modules\OrderFulfillment\App\Console\Commands;

use App\Models\Shipment;
use Illuminate\Console\Command;

class UpdateShipmentStatuses extends Command
{
    protected $signature = 'shipments:update-status';

    protected $description = 'Auto-promote shipments from SHIPPED to READY_TO_SHIP once 24 hours have passed';

    public function handle(): int
    {
        $count = Shipment::where('status', 'SHIPPED')
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', now()->subDay())
            ->update(['status' => 'READY_TO_SHIP']);

        $this->info("Promoted {$count} shipment(s) to READY_TO_SHIP.");

        return self::SUCCESS;
    }
}
