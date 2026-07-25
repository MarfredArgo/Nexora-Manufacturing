<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ecommerce\Models\Cpu;
use Modules\Ecommerce\Models\Motherboard;
use Modules\Ecommerce\Models\Ram;
use Modules\Ecommerce\Models\Gpu;
use Modules\Ecommerce\Models\PowerSupply;
use Modules\Ecommerce\Models\PcCase;

class ConfiguratorSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Cpu::truncate();
        Motherboard::truncate();
        Ram::truncate();
        Gpu::truncate();
        PowerSupply::truncate();
        PcCase::truncate();
        \Modules\Ecommerce\Models\Storage::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // CPUs
        // AMD
        Cpu::create(['name' => 'AMD Ryzen 5 9600X', 'price' => 14000, 'socket' => 'AM5', 'tdp' => 105, 'core_count' => 6, 'core_clock' => '3.9 GHz', 'boost_clock' => '5.4 GHz', 'microarchitecture' => 'Zen 5', 'integrated_graphics' => 'Radeon Graphics']);
        Cpu::create(['name' => 'AMD Ryzen 7 9700X', 'price' => 22000, 'socket' => 'AM5', 'tdp' => 120, 'core_count' => 8, 'core_clock' => '3.8 GHz', 'boost_clock' => '5.5 GHz', 'microarchitecture' => 'Zen 5', 'integrated_graphics' => 'Radeon Graphics']);
        Cpu::create(['name' => 'AMD Ryzen 7 9800X3D', 'price' => 38000, 'socket' => 'AM5', 'tdp' => 120, 'core_count' => 8, 'core_clock' => '4.7 GHz', 'boost_clock' => '5.2 GHz', 'microarchitecture' => 'Zen 5', 'integrated_graphics' => 'Radeon Graphics']);
        Cpu::create(['name' => 'AMD Ryzen 9 9950X3D2 (Dual Edition)', 'price' => 55000, 'socket' => 'AM5', 'tdp' => 170, 'core_count' => 16, 'core_clock' => '4.3 GHz', 'boost_clock' => '5.7 GHz', 'microarchitecture' => 'Zen 5', 'integrated_graphics' => 'Radeon Graphics']);
        
        // Intel
        Cpu::create(['name' => 'Intel Core Ultra 5 250K Plus (18-Core)', 'price' => 14000, 'socket' => 'LGA 1851', 'tdp' => 125, 'core_count' => 18, 'core_clock' => '3.5 GHz', 'boost_clock' => '5.2 GHz', 'microarchitecture' => 'Arrow Lake', 'integrated_graphics' => 'Intel Graphics']);
        Cpu::create(['name' => 'Intel Core Ultra 7 265K (20-Core)', 'price' => 22000, 'socket' => 'LGA 1851', 'tdp' => 125, 'core_count' => 20, 'core_clock' => '3.9 GHz', 'boost_clock' => '5.4 GHz', 'microarchitecture' => 'Arrow Lake', 'integrated_graphics' => 'Intel Graphics']);
        Cpu::create(['name' => 'Intel Core Ultra 7 270K Plus (24-Core)', 'price' => 38000, 'socket' => 'LGA 1851', 'tdp' => 125, 'core_count' => 24, 'core_clock' => '3.8 GHz', 'boost_clock' => '5.6 GHz', 'microarchitecture' => 'Arrow Lake', 'integrated_graphics' => 'Intel Graphics']);
        Cpu::create(['name' => 'Intel Core Ultra 9 285K (24-Core)', 'price' => 55000, 'socket' => 'LGA 1851', 'tdp' => 250, 'core_count' => 24, 'core_clock' => '3.7 GHz', 'boost_clock' => '5.7 GHz', 'microarchitecture' => 'Arrow Lake', 'integrated_graphics' => 'Intel Graphics']);
        // Motherboards
        // AMD Motherboards
        Motherboard::create(['name' => 'Gigabyte B850 AORUS ELITE WIFI7', 'price' => 13500, 'socket' => 'AM5', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '192 GB', 'memory_slots' => 4, 'color' => 'Black']);
        Motherboard::create(['name' => 'MSI MAG B850 TOMAHAWK WIFI', 'price' => 12500, 'socket' => 'AM5', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '192 GB', 'memory_slots' => 4, 'color' => 'Black']);
        Motherboard::create(['name' => 'ASUS ROG Strix X870-A Gaming WiFi', 'price' => 38000, 'socket' => 'AM5', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '192 GB', 'memory_slots' => 4, 'color' => 'White']);
        Motherboard::create(['name' => 'Gigabyte X870E AORUS MASTER X3D ICE', 'price' => 45000, 'socket' => 'AM5', 'form_factor' => 4, 'supported_ram_gen' => 'DDR5', 'memory_max' => '256 GB', 'memory_slots' => 4, 'color' => 'White']);
        
        // Intel Motherboards
        Motherboard::create(['name' => 'MSI PRO Z890-P WIFI', 'price' => 13000, 'socket' => 'LGA 1851', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '192 GB', 'memory_slots' => 4, 'color' => 'Black']);
        Motherboard::create(['name' => 'ASUS TUF Gaming Z890-PLUS WIFI', 'price' => 16500, 'socket' => 'LGA 1851', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '192 GB', 'memory_slots' => 4, 'color' => 'Black']);
        Motherboard::create(['name' => 'Gigabyte Z890 AORUS ELITE AX', 'price' => 22000, 'socket' => 'LGA 1851', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '192 GB', 'memory_slots' => 4, 'color' => 'Black']);
        Motherboard::create(['name' => 'ASUS ROG Maximus Z890 Hero', 'price' => 42000, 'socket' => 'LGA 1851', 'form_factor' => 3, 'supported_ram_gen' => 'DDR5', 'memory_max' => '256 GB', 'memory_slots' => 4, 'color' => 'Black']);

        // RAM
        Ram::create(['name' => '32GB (2x16GB) DDR5-6000', 'price' => 6500, 'generation' => 'DDR5', 'capacity' => 32, 'speed' => 6000, 'modules' => '2x16GB']);
        Ram::create(['name' => '32GB (2x16GB) DDR5-6000 CL30', 'price' => 7500, 'generation' => 'DDR5', 'capacity' => 32, 'speed' => 6000, 'modules' => '2x16GB']);
        Ram::create(['name' => '32GB (2x16GB) DDR5-6400 CL32', 'price' => 8500, 'generation' => 'DDR5', 'capacity' => 32, 'speed' => 6400, 'modules' => '2x16GB']);
        Ram::create(['name' => '64GB (2x32GB) DDR5-8200 (AMD EXPO)', 'price' => 16500, 'generation' => 'DDR5', 'capacity' => 64, 'speed' => 8200, 'modules' => '2x32GB']);
        Ram::create(['name' => '32GB (2x16GB) DDR5-6400', 'price' => 8000, 'generation' => 'DDR5', 'capacity' => 32, 'speed' => 6400, 'modules' => '2x16GB']);
        Ram::create(['name' => '32GB (2x16GB) DDR5-7200', 'price' => 10500, 'generation' => 'DDR5', 'capacity' => 32, 'speed' => 7200, 'modules' => '2x16GB']);
        Ram::create(['name' => '32GB (2x16GB) DDR5-7600', 'price' => 12500, 'generation' => 'DDR5', 'capacity' => 32, 'speed' => 7600, 'modules' => '2x16GB']);
        Ram::create(['name' => '64GB (2x32GB) DDR5-8000 CUDIMM', 'price' => 18500, 'generation' => 'DDR5', 'capacity' => 64, 'speed' => 8000, 'modules' => '2x32GB']);

        // GPUs
        Gpu::create(['name' => 'NVIDIA GeForce RTX 5060 Ti 16GB', 'price' => 35000, 'tdp' => 160, 'length_mm' => 260, 'chipset' => 'GeForce RTX 5060 Ti', 'memory' => 16, 'boost_clock' => '2535 MHz', 'color' => 'Black']);
        Gpu::create(['name' => 'AMD Radeon RX 9070 XT 16GB', 'price' => 45000, 'tdp' => 250, 'length_mm' => 280, 'chipset' => 'Radeon RX 9070 XT', 'memory' => 16, 'boost_clock' => '2450 MHz', 'color' => 'Black']);
        Gpu::create(['name' => 'NVIDIA GeForce RTX 5070 12GB', 'price' => 50000, 'tdp' => 220, 'length_mm' => 280, 'chipset' => 'GeForce RTX 5070', 'memory' => 12, 'boost_clock' => '2475 MHz', 'color' => 'Black']);
        Gpu::create(['name' => 'NVIDIA GeForce RTX 5080 16GB', 'price' => 85000, 'tdp' => 320, 'length_mm' => 310, 'chipset' => 'GeForce RTX 5080', 'memory' => 16, 'boost_clock' => '2505 MHz', 'color' => 'Black']);
        Gpu::create(['name' => 'NVIDIA GeForce RTX 5090 32GB', 'price' => 135000, 'tdp' => 450, 'length_mm' => 340, 'chipset' => 'GeForce RTX 5090', 'memory' => 32, 'boost_clock' => '2600 MHz', 'color' => 'Silver']);

        // Power Supplies
        PowerSupply::create(['name' => '650W 80+ Gold', 'price' => 6500, 'wattage' => 650, 'form_factor' => 'ATX', 'type' => 'ATX', 'modular' => 'Full', 'color' => 'Black', 'efficiency' => '80+ Gold']);
        PowerSupply::create(['name' => '750W 80+ Gold', 'price' => 8500, 'wattage' => 750, 'form_factor' => 'ATX', 'type' => 'ATX', 'modular' => 'Full', 'color' => 'Black', 'efficiency' => '80+ Gold']);
        PowerSupply::create(['name' => '850W 80+ Gold (ATX 3.1)', 'price' => 11500, 'wattage' => 850, 'form_factor' => 'ATX', 'type' => 'ATX 3.1', 'modular' => 'Full', 'color' => 'Black', 'efficiency' => '80+ Gold']);
        PowerSupply::create(['name' => '1200W 80+ Platinum (ATX 3.1)', 'price' => 21500, 'wattage' => 1200, 'form_factor' => 'ATX', 'type' => 'ATX 3.1', 'modular' => 'Full', 'color' => 'Black', 'efficiency' => '80+ Platinum']);
        PowerSupply::create(['name' => '1000W 80+ Gold', 'price' => 14000, 'wattage' => 1000, 'form_factor' => 'ATX', 'type' => 'ATX 3.0', 'modular' => 'Full', 'color' => 'Black', 'efficiency' => '80+ Gold']);
        PowerSupply::create(['name' => '850W 80+ Gold', 'price' => 9500, 'wattage' => 850, 'form_factor' => 'ATX', 'type' => 'ATX', 'modular' => 'Full', 'color' => 'White', 'efficiency' => '80+ Gold']);

        // PC Cases
        PcCase::create(['name' => 'Corsair 4000D Airflow', 'price' => 6500, 'max_mobo_size' => 3, 'max_gpu_length' => 360, 'type' => 'Mid Tower', 'color' => 'Black', 'side_panel' => 'Tempered Glass']);
        PcCase::create(['name' => 'NZXT H5 Flow', 'price' => 6200, 'max_mobo_size' => 3, 'max_gpu_length' => 365, 'type' => 'Mid Tower', 'color' => 'White', 'side_panel' => 'Tempered Glass']);
        PcCase::create(['name' => 'Lian Li Lancool 216', 'price' => 8500, 'max_mobo_size' => 4, 'max_gpu_length' => 392, 'type' => 'Mid Tower', 'color' => 'Black', 'side_panel' => 'Tempered Glass']);
        PcCase::create(['name' => 'Lian Li O11 Dynamic EVO', 'price' => 9800, 'max_mobo_size' => 4, 'max_gpu_length' => 426, 'type' => 'Mid Tower', 'color' => 'White', 'side_panel' => 'Tempered Glass']);
        PcCase::create(['name' => 'Fractal Design Pop Air', 'price' => 5200, 'max_mobo_size' => 3, 'max_gpu_length' => 405, 'type' => 'Mid Tower', 'color' => 'Black', 'side_panel' => 'Tempered Glass']);
        PcCase::create(['name' => 'NZXT H7 Flow', 'price' => 7800, 'max_mobo_size' => 4, 'max_gpu_length' => 400, 'type' => 'Mid Tower', 'color' => 'Black', 'side_panel' => 'Tempered Glass']);
        
        // Storage
        \Modules\Ecommerce\Models\Storage::create(['name' => '1TB NVMe PCIe 4.0 SSD', 'type' => 'NVMe Gen4', 'capacity' => 1000, 'price' => 3800, 'cache' => '1GB DRAM', 'form_factor' => 'M.2 2280', 'interface' => 'PCIe 4.0 x4']);
        \Modules\Ecommerce\Models\Storage::create(['name' => '2TB NVMe PCIe 4.0 SSD', 'type' => 'NVMe Gen4', 'capacity' => 2000, 'price' => 6500, 'cache' => '2GB DRAM', 'form_factor' => 'M.2 2280', 'interface' => 'PCIe 4.0 x4']);
        \Modules\Ecommerce\Models\Storage::create(['name' => '2TB NVMe PCIe 5.0 SSD', 'type' => 'NVMe Gen5', 'capacity' => 2000, 'price' => 12500, 'cache' => '4GB DRAM', 'form_factor' => 'M.2 2280', 'interface' => 'PCIe 5.0 x4']);
        \Modules\Ecommerce\Models\Storage::create(['name' => '4TB (2x 2TB) NVMe PCIe 5.0 SSD', 'type' => 'NVMe Gen5', 'capacity' => 4000, 'price' => 24000, 'cache' => '4GB DRAM per drive', 'form_factor' => 'M.2 2280', 'interface' => 'PCIe 5.0 x4']);
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Kingston NV2 1TB', 'type' => 'NVMe M.2', 'capacity' => 1000, 'price' => 3200, 'cache' => 'DRAMless', 'form_factor' => 'M.2 2280', 'interface' => 'PCIe 4.0 x4']);

        // SATA SSDs
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Samsung 870 EVO 1TB', 'type' => 'SATA SSD', 'capacity' => 1000, 'price' => 4500, 'cache' => '1GB LPDDR4', 'form_factor' => '2.5"', 'interface' => 'SATA 6Gb/s']);
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Crucial MX500 2TB', 'type' => 'SATA SSD', 'capacity' => 2000, 'price' => 7500, 'cache' => 'Crucial Custom', 'form_factor' => '2.5"', 'interface' => 'SATA 6Gb/s']);
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Western Digital Blue 4TB', 'type' => 'SATA SSD', 'capacity' => 4000, 'price' => 15000, 'cache' => 'N/A', 'form_factor' => '2.5"', 'interface' => 'SATA 6Gb/s']);

        // HDDs
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Seagate Barracuda 2TB', 'type' => 'HDD', 'capacity' => 2000, 'price' => 2800, 'cache' => '256MB', 'form_factor' => '3.5"', 'interface' => 'SATA 6Gb/s']);
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Western Digital Black 4TB', 'type' => 'HDD', 'capacity' => 4000, 'price' => 7500, 'cache' => '256MB', 'form_factor' => '3.5"', 'interface' => 'SATA 6Gb/s']);
        \Modules\Ecommerce\Models\Storage::create(['name' => 'Toshiba X300 8TB', 'type' => 'HDD', 'capacity' => 8000, 'price' => 11000, 'cache' => '256MB', 'form_factor' => '3.5"', 'interface' => 'SATA 6Gb/s']);

        // Coolers
        \Modules\Ecommerce\Models\Cooler::truncate();
        \Modules\Ecommerce\Models\Cooler::create(['name' => 'Deepcool AK400 Air Cooler', 'price' => 1500, 'fan_rpm' => '500-1850 RPM', 'noise_level' => '29 dB(A)', 'color' => 'Black', 'radiator_size' => 'N/A']);
        \Modules\Ecommerce\Models\Cooler::create(['name' => 'Thermalright Peerless Assassin 120', 'price' => 2500, 'fan_rpm' => '1550 RPM', 'noise_level' => '25.6 dB(A)', 'color' => 'White', 'radiator_size' => 'N/A']);
        \Modules\Ecommerce\Models\Cooler::create(['name' => 'NZXT Kraken 240 RGB Liquid Cooler', 'price' => 8500, 'fan_rpm' => '500-1800 RPM', 'noise_level' => '30.6 dB(A)', 'color' => 'Black', 'radiator_size' => '240mm']);
        \Modules\Ecommerce\Models\Cooler::create(['name' => 'Corsair iCUE Link H150i RGB', 'price' => 13500, 'fan_rpm' => '400-2400 RPM', 'noise_level' => '34.1 dB(A)', 'color' => 'Black', 'radiator_size' => '360mm']);
        \Modules\Ecommerce\Models\Cooler::create(['name' => 'Arctic Liquid Freezer III 360', 'price' => 7500, 'fan_rpm' => '200-2000 RPM', 'noise_level' => '22.5 dB(A)', 'color' => 'Black', 'radiator_size' => '360mm']);

        // Case Fans
        \Modules\Ecommerce\Models\ChasisFan::truncate();
        \Modules\Ecommerce\Models\ChasisFan::create(['name' => 'Corsair AF120 RGB Elite', 'price' => 1200, 'size' => '120mm', 'rpm' => '1900 RPM', 'airflow' => '59 CFM', 'noise_level' => '28.9 dB(A)', 'color' => 'Black', 'rgb' => true]);
        \Modules\Ecommerce\Models\ChasisFan::create(['name' => 'Noctua NF-A12x25 PWM', 'price' => 1800, 'size' => '120mm', 'rpm' => '2000 RPM', 'airflow' => '60 CFM', 'noise_level' => '22.6 dB(A)', 'color' => 'Brown', 'rgb' => false]);
        \Modules\Ecommerce\Models\ChasisFan::create(['name' => 'Lian Li UNI FAN SL-INF 120', 'price' => 1600, 'size' => '120mm', 'rpm' => '2100 RPM', 'airflow' => '61 CFM', 'noise_level' => '29 dB(A)', 'color' => 'White', 'rgb' => true]);
        \Modules\Ecommerce\Models\ChasisFan::create(['name' => 'Arctic P14 PWM PST', 'price' => 600, 'size' => '140mm', 'rpm' => '1700 RPM', 'airflow' => '72 CFM', 'noise_level' => '24 dB(A)', 'color' => 'Black', 'rgb' => false]);
        \Modules\Ecommerce\Models\ChasisFan::create(['name' => 'Be Quiet! Silent Wings 4', 'price' => 1300, 'size' => '140mm', 'rpm' => '1900 RPM', 'airflow' => '78 CFM', 'noise_level' => '29.3 dB(A)', 'color' => 'Black', 'rgb' => false]);
        \Modules\Ecommerce\Models\ChasisFan::create(['name' => 'NZXT F140 RGB', 'price' => 1400, 'size' => '140mm', 'rpm' => '1800 RPM', 'airflow' => '89 CFM', 'noise_level' => '32.5 dB(A)', 'color' => 'White', 'rgb' => true]);
    }
}
