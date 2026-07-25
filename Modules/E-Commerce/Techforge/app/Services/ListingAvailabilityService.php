<?php

namespace Modules\Ecommerce\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingAvailabilityService
{
    public function availableUnits(int $clientId, int $bomId): int
    {
        try {
            $components = DB::connection('manufacturing')->table('product_bom_items')
                ->where('client_id', $clientId)
                ->where('bom_id', $bomId)
                ->get(['inventory_item_id', 'quantity_required']);

            if ($components->isEmpty()) {
                return 0;
            }

            $availableUnits = PHP_INT_MAX;

            foreach ($components as $component) {
                $available = (int) DB::connection('inventory')->table('stock_levels')
                    ->where('client_id', $clientId)
                    ->where('item_id', $component->inventory_item_id)
                    ->sum(DB::raw('GREATEST(stock - reserved_quantity, 0)'));

                $availableUnits = min($availableUnits, intdiv($available, max(1, (int) $component->quantity_required)));
            }

            return $availableUnits === PHP_INT_MAX ? 0 : max(0, $availableUnits);
        } catch (\Throwable $exception) {
            // Storefront availability must never make a public listing page
            // fail. With no trustworthy inventory response, do not permit a
            // sale; log the cross-module problem for remediation instead.
            Log::warning('Unable to calculate storefront listing availability.', [
                'client_id' => $clientId,
                'bom_id' => $bomId,
                'exception' => $exception,
            ]);

            return 0;
        }
    }
}
