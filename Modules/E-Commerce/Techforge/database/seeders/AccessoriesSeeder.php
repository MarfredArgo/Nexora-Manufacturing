<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\AccessoryMonitor;
use Modules\Ecommerce\Models\AccessoryKeyboard;
use Modules\Ecommerce\Models\AccessoryKeyboardAccessory;
use Modules\Ecommerce\Models\AccessoryHeadset;
use Modules\Ecommerce\Models\AccessoryMouse;
use Modules\Ecommerce\Models\AccessoryMousePad;
use Modules\Ecommerce\Models\AccessorySpeakerSystem;

class AccessoriesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Monitors
        $monitorBrands = ['ASUS', 'Samsung', 'LG', 'AOC', 'BenQ', 'Gigabyte', 'MSI', 'Acer', 'Dell', 'Alienware', 'ViewSonic', 'Razer'];
        foreach ($monitorBrands as $index => $brand) {
            $is4k = $index % 3 == 0;
            AccessoryMonitor::create([
                'name' => $brand . ($is4k ? ' 32" 4K UHD OLED' : ' 27" 165Hz IPS Gaming Monitor'),
                'price' => $is4k ? 45000 + ($index * 1000) : 15000 + ($index * 500),
                'brand' => $brand,
                'description' => 'Experience ultra-smooth visuals and immersive gameplay with this premium gaming monitor.',
                'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&q=80',
                'is_sold_out' => $index == 5,
                'resolution' => $is4k ? '3840x2160' : '2560x1440',
                'refresh_rate' => $is4k ? '120Hz' : '165Hz',
                'panel_type' => $is4k ? 'OLED' : 'IPS',
                'size' => $is4k ? '32"' : '27"'
            ]);
        }

        // 2. Keyboards
        $kbBrands = ['Razer', 'Corsair', 'Logitech G', 'SteelSeries', 'HyperX', 'Keychron', 'Ducky', 'RK Royal Kludge', 'Akko', 'Epomaker', 'Glorious', 'Wooting'];
        foreach ($kbBrands as $index => $brand) {
            $isWireless = $index % 2 == 0;
            $layout = ['Full-size', 'TKL', '60%', '75%'][$index % 4];
            AccessoryKeyboard::create([
                'name' => $brand . ' Pro ' . $layout . ' Mechanical Keyboard',
                'price' => 4500 + ($index * 300),
                'brand' => $brand,
                'description' => 'Precision engineered mechanical keyboard for maximum gaming performance.',
                'image_url' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&q=80',
                'is_sold_out' => false,
                'type' => 'Mechanical',
                'switch_type' => ['Linear Red', 'Tactile Brown', 'Clicky Blue', 'Optical', 'Magnetic'][$index % 5],
                'size_layout' => $layout,
                'is_wireless' => $isWireless
            ]);
        }

        // 3. Keyboard Accessories
        $kbAccNames = ['PBT Double-shot Keycap Set', 'Linear Switches (36-pack)', 'Premium Wooden Wrist Rest', 'Coiled Aviator Cable', 'Switch Puller & Tweezers', 'Krytox Switch Lube Kit', 'Silicone O-Rings (100-pack)', 'Extended Desk Mat', 'Hard Shell Carrying Case', 'Resin Artisan Keycap', 'Durock V2 Stabilizer Kit', 'Switch Films (120-pack)'];
        foreach ($kbAccNames as $index => $name) {
            AccessoryKeyboardAccessory::create([
                'name' => $name,
                'price' => 500 + ($index * 150),
                'brand' => 'Forge Custom',
                'description' => 'High quality accessory to customize and improve your mechanical keyboard.',
                'image_url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&q=80',
                'is_sold_out' => false,
                'type' => str_contains($name, 'Keycap') ? 'Keycaps' : (str_contains($name, 'Switch') ? 'Switches' : 'Accessory'),
                'compatibility' => 'Universal MX'
            ]);
        }

        // 4. Headsets
        $hsBrands = ['HyperX', 'SteelSeries', 'Razer', 'Logitech G', 'Corsair', 'Sennheiser', 'Audio-Technica', 'Astro', 'Turtle Beach', 'JBL Quantum', 'Sony INZONE', 'EPOS'];
        foreach ($hsBrands as $index => $brand) {
            $isWireless = $index % 2 != 0;
            AccessoryHeadset::create([
                'name' => $brand . ' Cloud ' . ($isWireless ? 'Wireless' : 'Pro') . ' Gaming Headset',
                'price' => 3500 + ($index * 400),
                'brand' => $brand,
                'description' => 'Immersive spatial audio and crystal clear comms for competitive gaming.',
                'image_url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&q=80', // Replace later if needed
                'is_sold_out' => false,
                'is_wireless' => $isWireless,
                'has_microphone' => true,
                'surround_sound' => $index % 3 == 0
            ]);
        }

        // 5. Mice
        $mouseBrands = ['Logitech G', 'Razer', 'Finalmouse', 'Glorious', 'Pulsar', 'Lamzu', 'Zowie', 'Vaxee', 'SteelSeries', 'Endgame Gear', 'Roccat', 'HyperX'];
        foreach ($mouseBrands as $index => $brand) {
            $isWireless = $index % 3 != 0;
            AccessoryMouse::create([
                'name' => $brand . ' Superlight ' . ($isWireless ? 'Wireless' : 'Wired') . ' Mouse',
                'price' => 2500 + ($index * 350),
                'brand' => $brand,
                'description' => 'Ultra-lightweight gaming mouse with pixel-perfect tracking.',
                'image_url' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=500&q=80',
                'is_sold_out' => $index == 2,
                'dpi' => [16000, 25600, 30000][$index % 3],
                'is_wireless' => $isWireless,
                'sensor_type' => 'Optical',
                'buttons' => [5, 6, 8][$index % 3]
            ]);
        }

        // 6. Mouse Pads
        $padNames = ['Artisan Zero', 'SteelSeries QcK Heavy', 'Logitech G640', 'Razer Gigantus V2', 'Glorious Element Ice', 'Zowie G-SR', 'Aqua Control Plus', 'Lethal Gaming Gear Saturn', 'Corsair MM300', 'HyperX Fury S', 'Endgame Gear MPC450', 'Fnatic Dash'];
        foreach ($padNames as $index => $name) {
            AccessoryMousePad::create([
                'name' => $name . ' Gaming Mousepad',
                'price' => 1200 + ($index * 100),
                'brand' => explode(' ', $name)[0],
                'description' => 'Premium surface for perfect mouse control and glide.',
                'image_url' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&q=80',
                'is_sold_out' => false,
                'size' => ['Medium', 'Large', 'XL', 'Extended'][$index % 4],
                'surface_type' => $index % 2 == 0 ? 'Control' : 'Speed'
            ]);
        }

        // 7. Speaker Systems
        $speakerBrands = ['Logitech', 'Creative', 'Edifier', 'Bose', 'Klipsch', 'Razer', 'Harman Kardon', 'JBL', 'Sony', 'Audioengine', 'PreSonus', 'Mackie'];
        foreach ($speakerBrands as $index => $brand) {
            $channels = ['2.0', '2.1', '5.1'][$index % 3];
            AccessorySpeakerSystem::create([
                'name' => $brand . ' Soundstage ' . $channels . ' System',
                'price' => 5000 + ($index * 800),
                'brand' => $brand,
                'description' => 'Room-filling sound for the ultimate multimedia and gaming experience.',
                'image_url' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=500&q=80',
                'is_sold_out' => false,
                'channels' => $channels,
                'is_wireless' => $index % 2 == 0,
                'total_wattage' => [60, 120, 240, 400][$index % 4]
            ]);
        }
    }
}
