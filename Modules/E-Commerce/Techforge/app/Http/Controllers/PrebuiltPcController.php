<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\PrebuiltConfig;

class PrebuiltPcController extends Controller
{
    public function index(Request $request)
    {
        // Get ALL configs and eager load all relationships
        $allConfigs = PrebuiltConfig::with(['cpu', 'gpu', 'motherboard', 'ram', 'storage', 'powerSupply'])->get();
        
        $counts = [
            'processors' => [],
            'gpus' => [],
            'rams' => [],
            'storages' => [],
        ];

        foreach ($allConfigs as $config) {
            $procName = $config->cpu->name ?? '';
            if ($procName) $counts['processors'][$procName] = ($counts['processors'][$procName] ?? 0) + 1;

            $gpuName = $config->gpu->name ?? '';
            if ($gpuName) $counts['gpus'][$gpuName] = ($counts['gpus'][$gpuName] ?? 0) + 1;

            $ramName = $config->ram->name ?? '';
            if ($ramName) $counts['rams'][$ramName] = ($counts['rams'][$ramName] ?? 0) + 1;

            $storageName = $config->storage->name ?? '';
            if ($storageName) $counts['storages'][$storageName] = ($counts['storages'][$storageName] ?? 0) + 1;
        }

        $configs = $allConfigs->map(function($config) {
            $config->html_card = view('ecommerce::components.product-card2', ['config' => $config, 'type' => 'prebuilt'])->render();
            return $config;
        });

        $minPrices = array_filter([
            PrebuiltConfig::min('price'),
        ]);
        $globalMinPrice = !empty($minPrices) ? floor(min($minPrices)) : 0;

        $maxPrices = array_filter([
            PrebuiltConfig::max('price'),
        ]);
        $globalMaxPrice = !empty($maxPrices) ? ceil(max($maxPrices)) : 250000;

        return view('ecommerce::prebuilt-pcs', compact('configs', 'counts', 'globalMinPrice', 'globalMaxPrice'));
    }
}
