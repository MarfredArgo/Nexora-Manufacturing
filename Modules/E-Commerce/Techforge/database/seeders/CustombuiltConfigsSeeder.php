<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ecommerce\Models\CustombuiltConfig;
use Modules\Ecommerce\Models\Cpu;
use Modules\Ecommerce\Models\Gpu;
use Modules\Ecommerce\Models\Motherboard;
use Modules\Ecommerce\Models\Ram;
use Modules\Ecommerce\Models\Storage;
use Modules\Ecommerce\Models\PowerSupply;
use Modules\Ecommerce\Models\PcCase;

class CustombuiltConfigsSeeder extends Seeder
{
    public function run(): void
    {
        CustombuiltConfig::truncate();

        // 1. Techforge Core
        CustombuiltConfig::create([
            'name' => 'Techforge Core',
            'description' => 'A great starting point for 1080p gaming.',
            'price' => 35000,
            'image_url' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=800&q=80',
            'tier' => 'Core',
            'rating' => rand(40, 50) / 10,
            'review_count' => rand(15, 120),
            
            'intel_cpu_id' => Cpu::where('name', 'Intel Core Ultra 5 250K Plus (18-Core)')->first()->id ?? 1,
            'amd_cpu_id' => Cpu::where('name', 'AMD Ryzen 5 9600X')->first()->id ?? 1,
            
            'intel_motherboard_id' => Motherboard::where('name', 'MSI PRO Z890-P WIFI')->first()->id ?? 1,
            'amd_motherboard_id' => Motherboard::where('name', 'Gigabyte B850 AORUS ELITE WIFI7')->first()->id ?? 1,
            
            'intel_ram_id' => Ram::where('name', '32GB (2x16GB) DDR5-6400')->first()->id ?? 1,
            'amd_ram_id' => Ram::where('name', '32GB (2x16GB) DDR5-6000')->first()->id ?? 1,
            
            'gpu_id' => Gpu::where('name', 'NVIDIA GeForce RTX 5060 Ti 16GB')->first()->id ?? 1,
            'storage_id' => Storage::where('name', '1TB NVMe PCIe 4.0 SSD')->first()->id ?? 1,
            'power_supply_id' => PowerSupply::where('name', '650W 80+ Gold')->first()->id ?? 1,
            'pc_case_id' => PcCase::where('name', 'Fractal Design Pop Air')->first()->id ?? 1,
            'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Deepcool AK400 Air Cooler')->first()->id ?? 1,
        ]);

        // 2. Techforge Advanced
        CustombuiltConfig::create([
            'name' => 'Techforge Advanced',
            'description' => 'Perfect for 1440p high-refresh gaming.',
            'price' => 75000,
            'image_url' => 'https://images.unsplash.com/photo-1547082299-de196ea013d6?w=800&q=80',
            'tier' => 'Advanced',
            'rating' => rand(40, 50) / 10,
            'review_count' => rand(15, 120),
            
            'intel_cpu_id' => Cpu::where('name', 'Intel Core Ultra 7 265K (20-Core)')->first()->id ?? 1,
            'amd_cpu_id' => Cpu::where('name', 'AMD Ryzen 7 9700X')->first()->id ?? 1,
            
            'intel_motherboard_id' => Motherboard::where('name', 'ASUS TUF Gaming Z890-PLUS WIFI')->first()->id ?? 1,
            'amd_motherboard_id' => Motherboard::where('name', 'MSI MAG B850 TOMAHAWK WIFI')->first()->id ?? 1,
            
            'intel_ram_id' => Ram::where('name', '32GB (2x16GB) DDR5-7200')->first()->id ?? 1,
            'amd_ram_id' => Ram::where('name', '32GB (2x16GB) DDR5-6000 CL30')->first()->id ?? 1,
            
            'gpu_id' => Gpu::where('name', 'NVIDIA GeForce RTX 5070 12GB')->first()->id ?? 1,
            'storage_id' => Storage::where('name', '2TB NVMe PCIe 4.0 SSD')->first()->id ?? 1,
            'power_supply_id' => PowerSupply::where('name', '750W 80+ Gold')->first()->id ?? 1,
            'pc_case_id' => PcCase::where('name', 'Corsair 4000D Airflow')->first()->id ?? 1,
            'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Thermalright Peerless Assassin 120')->first()->id ?? 1,
        ]);

        // 3. Techforge Extreme
        CustombuiltConfig::create([
            'name' => 'Techforge Extreme',
            'description' => 'Uncompromised 4K performance for power users.',
            'price' => 110000,
            'image_url' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=800&q=80',
            'tier' => 'Extreme',
            'rating' => rand(40, 50) / 10,
            'review_count' => rand(15, 120),
            
            'intel_cpu_id' => Cpu::where('name', 'Intel Core Ultra 7 270K Plus (24-Core)')->first()->id ?? 1,
            'amd_cpu_id' => Cpu::where('name', 'AMD Ryzen 9 9900X')->first()->id ?? 1,
            
            'intel_motherboard_id' => Motherboard::where('name', 'Gigabyte Z890 AORUS ELITE AX')->first()->id ?? 1,
            'amd_motherboard_id' => Motherboard::where('name', 'ASUS ROG STRIX X870E-F GAMING WIFI')->first()->id ?? 1,
            
            'intel_ram_id' => Ram::where('name', '32GB (2x16GB) DDR5-7600')->first()->id ?? 1,
            'amd_ram_id' => Ram::where('name', '32GB (2x16GB) DDR5-6400 CL30')->first()->id ?? 1,
            
            'gpu_id' => Gpu::where('name', 'NVIDIA GeForce RTX 5080 16GB')->first()->id ?? 1,
            'storage_id' => Storage::where('name', '2TB NVMe PCIe 5.0 SSD')->first()->id ?? 1,
            'power_supply_id' => PowerSupply::where('name', '850W 80+ Gold (ATX 3.1)')->first()->id ?? 1,
            'pc_case_id' => PcCase::where('name', 'NZXT H7 Flow')->first()->id ?? 1,
            'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'NZXT Kraken 240 RGB Liquid Cooler')->first()->id ?? 1,
        ]);

        // 4. Techforge Apex
        CustombuiltConfig::create([
            'name' => 'Techforge Apex',
            'description' => 'The absolute pinnacle of gaming and productivity.',
            'price' => 150000,
            'image_url' => 'https://images.unsplash.com/photo-1512756290469-ec264b7fbf87?w=800&q=80',
            'tier' => 'Apex',
            'rating' => rand(40, 50) / 10,
            'review_count' => rand(15, 120),
            
            'intel_cpu_id' => Cpu::where('name', 'Intel Core Ultra 9 285K (24-Core)')->first()->id ?? 1,
            'amd_cpu_id' => Cpu::where('name', 'AMD Ryzen 9 9950X3D')->first()->id ?? 1,
            
            'intel_motherboard_id' => Motherboard::where('name', 'ASUS ROG Maximus Z890 Hero')->first()->id ?? 1,
            'amd_motherboard_id' => Motherboard::where('name', 'ASUS ROG CROSSHAIR X870E HERO')->first()->id ?? 1,
            
            'intel_ram_id' => Ram::where('name', '64GB (2x32GB) DDR5-8000 CUDIMM')->first()->id ?? 1,
            'amd_ram_id' => Ram::where('name', '64GB (2x32GB) DDR5-8000 CUDIMM')->first()->id ?? 1,
            
            'gpu_id' => Gpu::where('name', 'NVIDIA GeForce RTX 5090 32GB')->first()->id ?? 1,
            'storage_id' => Storage::where('name', '4TB (2x 2TB) NVMe PCIe 5.0 SSD')->first()->id ?? 1,
            'power_supply_id' => PowerSupply::where('name', '1200W 80+ Platinum (ATX 3.1)')->first()->id ?? 1,
            'pc_case_id' => PcCase::where('name', 'Lian Li O11 Dynamic EVO')->first()->id ?? 1,
            'cooler_id' => \Modules\Ecommerce\Models\Cooler::where('name', 'Corsair iCUE Link H150i RGB')->first()->id ?? 1,
        ]);
    }
}
