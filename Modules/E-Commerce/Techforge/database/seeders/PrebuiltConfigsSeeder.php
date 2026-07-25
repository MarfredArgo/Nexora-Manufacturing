<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\Cpu;
use Modules\Ecommerce\Models\Gpu;
use Modules\Ecommerce\Models\Motherboard;
use Modules\Ecommerce\Models\Ram;
use Modules\Ecommerce\Models\Storage;
use Modules\Ecommerce\Models\PowerSupply;
use Modules\Ecommerce\Models\PcCase;
use Modules\Ecommerce\Models\PrebuiltConfig;

class PrebuiltConfigsSeeder extends Seeder
{
    public function run(): void
    {
        PrebuiltConfig::truncate();

        $builds = [
            [
                'name' => 'TechForge Cobalt',
                'description' => 'Perfect balance for high-refresh 1080p gaming.',
                'cpu_id' => Cpu::where('name', 'like', '%Ryzen 5%')->first()->id ?? 1,
                'gpu_id' => Gpu::where('name', 'like', '%RTX 4060%')->orWhere('name', 'like', '%RX 7600%')->first()->id ?? 1,
                'motherboard_id' => Motherboard::where('name', 'like', '%B650%')->orWhere('socket', 'AM5')->first()->id ?? 1,
                'ram_id' => Ram::where('name', 'like', '%16GB%')->first()->id ?? 1,
                'storage_id' => Storage::where('name', 'like', '%1TB%')->first()->id ?? 1,
                'power_supply_id' => PowerSupply::where('name', 'like', '%650%')->first()->id ?? 1,
                'pc_case_id' => PcCase::first()->id ?? 1,
                'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Deepcool AK400 Air Cooler')->first()->id ?? 1,
                'price' => 55000,
                'image_url' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'TechForge Titanium',
                'description' => 'Uncompromised 4K performance for demanding titles.',
                'cpu_id' => Cpu::where('name', 'like', '%Ryzen 7%')->first()->id ?? 2,
                'gpu_id' => Gpu::where('name', 'like', '%RTX 4070%')->orWhere('name', 'like', '%RX 7800%')->first()->id ?? 2,
                'motherboard_id' => Motherboard::where('name', 'like', '%X670%')->orWhere('socket', 'AM5')->first()->id ?? 2,
                'ram_id' => Ram::where('name', 'like', '%32GB%')->first()->id ?? 2,
                'storage_id' => Storage::where('name', 'like', '%2TB%')->first()->id ?? 2,
                'power_supply_id' => PowerSupply::where('name', 'like', '%750%')->first()->id ?? 2,
                'pc_case_id' => PcCase::skip(1)->first()->id ?? 2,
                'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Thermalright Peerless Assassin 120')->first()->id ?? 2,
                'price' => 110000,
                'image_url' => 'https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'TechForge Tungsten',
                'description' => 'The absolute pinnacle of AMD computing power.',
                'cpu_id' => Cpu::where('name', 'like', '%Ryzen 9%')->first()->id ?? 3,
                'gpu_id' => Gpu::where('name', 'like', '%RTX 4090%')->orWhere('name', 'like', '%RX 7900%')->first()->id ?? 3,
                'motherboard_id' => Motherboard::where('name', 'like', '%X670E%')->orWhere('socket', 'AM5')->first()->id ?? 3,
                'ram_id' => Ram::where('name', 'like', '%64GB%')->first()->id ?? 3,
                'storage_id' => Storage::where('name', 'like', '%4TB%')->first()->id ?? 3,
                'power_supply_id' => PowerSupply::where('name', 'like', '%1000%')->first()->id ?? 3,
                'pc_case_id' => PcCase::skip(2)->first()->id ?? 3,
                'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'NZXT Kraken 240 RGB Liquid Cooler')->first()->id ?? 3,
                'price' => 250000,
                'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'TechForge Chromium',
                'description' => 'Smooth 1080p gaming with Intel reliability.',
                'cpu_id' => Cpu::where('name', 'like', '%i5%')->first()->id ?? 1,
                'gpu_id' => Gpu::where('name', 'like', '%RTX 4060 Ti%')->orWhere('name', 'like', '%RTX 3060%')->first()->id ?? 1,
                'motherboard_id' => Motherboard::where('name', 'like', '%Z790%')->orWhere('name', 'like', '%B760%')->first()->id ?? 1,
                'ram_id' => Ram::where('name', 'like', '%16GB%')->first()->id ?? 1,
                'storage_id' => Storage::where('name', 'like', '%1TB%')->first()->id ?? 1,
                'power_supply_id' => PowerSupply::where('name', 'like', '%750%')->first()->id ?? 1,
                'pc_case_id' => PcCase::first()->id ?? 1,
                'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Deepcool AK400 Air Cooler')->first()->id ?? 1,
                'price' => 60000,
                'image_url' => 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'TechForge Palladium',
                'description' => 'Premium 4K gaming and productivity.',
                'cpu_id' => Cpu::where('name', 'like', '%i7%')->first()->id ?? 2,
                'gpu_id' => Gpu::where('name', 'like', '%RTX 4080%')->first()->id ?? 2,
                'motherboard_id' => Motherboard::where('name', 'like', '%Z790%')->first()->id ?? 2,
                'ram_id' => Ram::where('name', 'like', '%32GB%')->first()->id ?? 2,
                'storage_id' => Storage::where('name', 'like', '%2TB%')->first()->id ?? 2,
                'power_supply_id' => PowerSupply::where('name', 'like', '%850%')->first()->id ?? 2,
                'pc_case_id' => PcCase::skip(1)->first()->id ?? 2,
                'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Thermalright Peerless Assassin 120')->first()->id ?? 2,
                'price' => 135000,
                'image_url' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'TechForge Iridium',
                'description' => 'Ultimate Intel performance without compromise.',
                'cpu_id' => Cpu::where('name', 'like', '%i9%')->first()->id ?? 3,
                'gpu_id' => Gpu::where('name', 'like', '%RTX 4090%')->first()->id ?? 3,
                'motherboard_id' => Motherboard::where('name', 'like', '%Z790%')->first()->id ?? 3,
                'ram_id' => Ram::where('name', 'like', '%64GB%')->first()->id ?? 3,
                'storage_id' => Storage::where('name', 'like', '%4TB%')->first()->id ?? 3,
                'power_supply_id' => PowerSupply::where('name', 'like', '%1000%')->first()->id ?? 3,
                'pc_case_id' => PcCase::skip(2)->first()->id ?? 3,
                'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Corsair iCUE Link H150i RGB')->first()->id ?? 3,
                'price' => 260000,
                'image_url' => 'https://images.unsplash.com/photo-1600861194942-f883de0dfe96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($builds as $build) {
            PrebuiltConfig::create($build);
        }
    }
}
