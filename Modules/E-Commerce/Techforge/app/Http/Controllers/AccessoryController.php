<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;

class AccessoryController extends Controller
{
    public function index(Request $request)
    {
        $initialCategory = $request->query('category', 'all');

        $collections = collect();

        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessoryKeyboard::all()->map(function($i) { $i->category = 'Keyboard'; $i->filter_key = 'keyboards'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessoryKeyboardAccessory::all()->map(function($i) { $i->category = 'Keyboard Accessory'; $i->filter_key = 'keyboard-accessories'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessoryHeadset::all()->map(function($i) { $i->category = 'Headset'; $i->filter_key = 'headsets'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessoryMouse::all()->map(function($i) { $i->category = 'Mouse'; $i->filter_key = 'mice'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessoryMousePad::all()->map(function($i) { $i->category = 'Mouse Pad'; $i->filter_key = 'mouse-pads'; return $i; }));
        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessorySpeakerSystem::all()->map(function($i) { $i->category = 'Speaker System'; $i->filter_key = 'speaker-systems'; return $i; }));

        // Add dummy reviews and sale properties so store-item-card works correctly
        $collections = $collections->map(function ($item) {
            $item->rating = 5;
            $item->reviews = rand(50, 300);
            $item->sale = true;
            $item->originalPrice = $item->price * 1.25;

            // Extract dynamic filters
            $filters = [];
            if (!empty($item->brand)) $filters['Brand'] = $item->brand;
            if (isset($item->is_wireless)) $filters['Wireless'] = $item->is_wireless ? 'Yes' : 'No';
            if (!empty($item->switch_type)) $filters['Switch'] = $item->switch_type;
            if (!empty($item->size_layout)) $filters['Size'] = $item->size_layout;
            if (!empty($item->size)) $filters['Size'] = $item->size;
            if (!empty($item->sensor_type)) $filters['Sensor'] = $item->sensor_type;
            if (!empty($item->channels)) $filters['Channels'] = $item->channels;
            if (!empty($item->type)) $filters['Type'] = $item->type;
            
            $item->filter_data = json_encode($filters);

            return $item;
        });

        // Sort latest
        $items = $collections->sortByDesc('created_at')->values();

        return view('ecommerce::store.accessories', compact('items', 'initialCategory'));
    }

    public function monitors(Request $request)
    {
        $collections = collect();

        $collections = $collections->concat(\Modules\Ecommerce\Models\AccessoryMonitor::all()->map(function($i) { $i->category = 'Monitor'; $i->filter_key = 'all'; return $i; }));

        $collections = $collections->map(function ($item) {
            $item->rating = 5;
            $item->reviews = rand(50, 300);
            $item->sale = true;
            $item->originalPrice = $item->price * 1.25;

            // Extract dynamic filters
            $filters = [];
            if (!empty($item->brand)) $filters['Brand'] = $item->brand;
            if (!empty($item->resolution)) $filters['Resolution'] = $item->resolution;
            if (!empty($item->refresh_rate)) $filters['Refresh Rate'] = $item->refresh_rate;
            if (!empty($item->panel_type)) $filters['Panel Type'] = $item->panel_type;
            if (!empty($item->size)) $filters['Size'] = $item->size;
            
            $item->filter_data = json_encode($filters);

            return $item;
        });

        // Sort latest
        $items = $collections->sortByDesc('created_at')->values();

        return view('ecommerce::store.monitors', compact('items'));
    }
}

