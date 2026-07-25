<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkDelayedDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deliveries:mark-delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark intransit/scheduled/pending deliveries as delayed once their expected arrival date has passed.';

    public function handle()
    {
        // Business rule: "delayed" = expected date has already passed and
        // the delivery hasn't been received/completed/cancelled yet.
        // Previously this was only ever set once, at the moment a delivery
        // was logged — if the expected date passed while it was still
        // "intransit", nothing ever flipped it to "Delayed" automatically.
        $updated = DB::connection('procurement')->table('deliveries')
            ->whereIn('status', ['pending', 'scheduled', 'intransit'])
            ->whereNotNull('estimated_arrival')
            ->whereDate('estimated_arrival', '<', now()->toDateString())
            ->update([
                'status' => 'delayed',
                'updated_at' => now(),
            ]);

        $this->info("Marked {$updated} delivery(ies) as delayed.");

        return 0;
    }
}
