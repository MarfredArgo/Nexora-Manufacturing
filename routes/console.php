<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('inventory:backfill-packing-materials {clientId}', function (int $clientId) {
    $inventory = DB::connection('inventory');
    $supplies = $inventory->table('items as items')
        ->leftJoin('stock_levels as levels', 'levels.item_id', '=', 'items.id')
        ->where('items.client_id', $clientId)
        ->select('items.name', DB::raw('COALESCE(SUM(levels.stock), 0) as stock_qty'))
        ->groupBy('items.id', 'items.name')
        ->get();

    $updated = 0;
    foreach ($supplies as $supply) {
        $name = (string) $supply->name;
        $normalized = strtolower($name);
        $isBox = str_contains($normalized, 'box');
        if (! $isBox && ! preg_match('/bubble\s*wrap|packing\s*tape|foam\s*insert|silica\s*gel|fragile\s*tape/', $normalized)) {
            continue;
        }

        $boxSize = $isBox
            ? (str_contains($normalized, 'small') ? 'Small' : (str_contains($normalized, 'medium') ? 'Medium' : (str_contains($normalized, 'large') ? 'Large' : 'Standard')))
            : null;
        $packingName = match (true) {
            $isBox => $name,
            (bool) preg_match('/bubble\s*wrap/', $normalized) => 'Bubble Wrap',
            (bool) preg_match('/packing\s*tape/', $normalized) => 'Packing Tape',
            (bool) preg_match('/foam\s*insert/', $normalized) => 'Foam Inserts',
            (bool) preg_match('/silica\s*gel/', $normalized) => 'Silica Gel Packs',
            default => 'Fragile Tape',
        };
        $row = $inventory->table('packing_materials')
            ->where('client_id', $clientId)
            ->whereRaw('LOWER(name) = LOWER(?)', [$packingName])
            ->first();
        $values = ['stock_qty' => (int) $supply->stock_qty, 'is_box' => $isBox, 'box_size' => $boxSize, 'updated_at' => now()];

        if ($row) {
            $inventory->table('packing_materials')->where('id', $row->id)->update($values);
        } else {
            $inventory->table('packing_materials')->insert($values + [
                'client_id' => $clientId, 'name' => $packingName, 'low_stock_threshold' => 5, 'created_at' => now(),
            ]);
        }
        $updated++;
    }

    $this->info("Synchronized {$updated} packing materials for client {$clientId}.");
})->purpose('Copy received packaging supplies into Order Fulfillment');
