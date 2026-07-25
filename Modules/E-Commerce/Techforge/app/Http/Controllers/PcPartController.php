<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;

class PcPartController extends Controller
{
    public function index(Request $request)
    {
        $initialCategory = $request->query('category', 'all');

        $collections = collect();

        $collections = $collections->concat(\Modules\Ecommerce\Models\PcCase::all()->map(function($i) { $i->category = 'Case'; $i->filter_key = 'cases'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\ChasisFan::all()->map(function($i) { $i->category = 'Chasis Fan'; $i->filter_key = 'chasis-fans'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\Storage::all()->map(function($i) { $i->category = 'Storage'; $i->filter_key = 'storages'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\Gpu::all()->map(function($i) { $i->category = 'Video Card'; $i->filter_key = 'video-cards'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\Cpu::all()->map(function($i) { $i->category = 'Processor'; $i->filter_key = 'processors'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\Cooler::all()->map(function($i) { $i->category = 'Cooler'; $i->filter_key = 'coolers'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\Motherboard::all()->map(function($i) { $i->category = 'Motherboard'; $i->filter_key = 'motherboards'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\Ram::all()->map(function($i) { $i->category = 'RAM'; $i->filter_key = 'rams'; return $i; }));

        $collections = $collections->map(function ($item) {
            $item->rating = 5;
            $item->reviews = rand(50, 300);
            $item->sale = true;
            $item->originalPrice = $item->price * 1.25;

            // Extract dynamic filters
            $filters = [];
            
            // Common Brand filter
            if (!empty($item->brand) && $item->brand !== 'Generic') {
                $filters['Brand'] = $item->brand;
            }

            switch ($item->category) {
                case 'Case':
                    if (!empty($item->type)) $filters['Form Factor'] = $item->type;
                    if (!empty($item->max_mobo_size)) {
                        $sizes = [1 => 'Mini-ITX', 2 => 'Micro-ATX', 3 => 'ATX', 4 => 'E-ATX'];
                        $filters['Mobo Size'] = $sizes[$item->max_mobo_size] ?? 'ATX';
                    }
                    if (!empty($item->color)) $filters['Color'] = $item->color;
                    if (!empty($item->fans_included)) $filters['Fans Included'] = $item->fans_included;
                    break;
                case 'Chasis Fan':
                    if (!empty($item->rpm)) $filters['RPM'] = $item->rpm;
                    if (!empty($item->noise_level)) $filters['Noise Level'] = $item->noise_level;
                    if (!empty($item->color)) $filters['Color'] = $item->color;
                    if (isset($item->rgb)) $filters['RGB'] = $item->rgb ? 'Yes' : 'No';
                    break;
                case 'Storage':
                    if (!empty($item->type)) $filters['Type'] = $item->type;
                    if (!empty($item->form_factor)) $filters['Form Factor'] = $item->form_factor;
                    if (!empty($item->interface)) $filters['Interface'] = $item->interface;
                    break;
                case 'Video Card':
                    if (!empty($item->chipset)) $filters['Chipset'] = $item->chipset;
                    break;
                case 'Processor':
                    if (!empty($item->core_count)) $filters['Cores'] = $item->core_count;
                    if (!empty($item->integrated_graphics)) $filters['Integrated Graphics'] = $item->integrated_graphics;
                    break;
                case 'Cooler':
                    if (!empty($item->fan_rpm)) $filters['RPM'] = $item->fan_rpm;
                    if (!empty($item->noise_level)) $filters['Noise Level'] = $item->noise_level;
                    if (!empty($item->radiator_size)) $filters['Radiator Size'] = $item->radiator_size;
                    break;
                case 'Motherboard':
                    if (!empty($item->socket)) $filters['Socket'] = $item->socket;
                    if (!empty($item->memory_slots)) $filters['Memory Slots'] = $item->memory_slots;
                    if (!empty($item->color)) $filters['Color'] = $item->color;
                    if (isset($item->wifi)) $filters['WiFi'] = $item->wifi ? 'Yes' : 'No';
                    break;
                case 'RAM':
                    if (!empty($item->speed)) {
                        $gen = $item->generation ? $item->generation . '-' : '';
                        $filters['Speed'] = $gen . $item->speed;
                    }
                    if (!empty($item->modules)) $filters['Modules'] = $item->modules;
                    break;
            }
            
            $item->filter_data = json_encode($filters);

            return $item;
        });

        // Sort latest
        $items = $collections->sortByDesc('created_at')->values();

        return view('ecommerce::store.pc-parts', compact('items', 'initialCategory'));
    }
}

