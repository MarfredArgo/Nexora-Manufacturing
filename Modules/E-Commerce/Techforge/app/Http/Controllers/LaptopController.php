<?php

namespace Modules\Ecommerce\Http\Controllers;

use Modules\Ecommerce\Models\Laptop;
use Illuminate\Http\Request;

class LaptopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $laptops = Laptop::all();
        
        $laptops = $laptops->map(function($laptop) {
            $laptop->html_card = view('ecommerce::components.product-card2', ['config' => $laptop, 'type' => 'laptop'])->render();
            return $laptop;
        });

        $counts = [
            'processors' => [],
            'gpus' => [],
            'rams' => [],
            'storages' => [],
        ];

        foreach ($laptops as $laptop) {
            $counts['processors'][$laptop->processor] = ($counts['processors'][$laptop->processor] ?? 0) + 1;
            $counts['gpus'][$laptop->gpu] = ($counts['gpus'][$laptop->gpu] ?? 0) + 1;
            $counts['rams'][$laptop->ram] = ($counts['rams'][$laptop->ram] ?? 0) + 1;
            $counts['storages'][$laptop->storage] = ($counts['storages'][$laptop->storage] ?? 0) + 1;
        }

        $globalMinPrice = floor(Laptop::min('price') ?? 0);
        $globalMaxPrice = ceil(Laptop::max('price') ?? 5000);

        return view('ecommerce::gaming-laptops', compact('laptops', 'counts', 'globalMinPrice', 'globalMaxPrice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Laptop $laptop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laptop $laptop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laptop $laptop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laptop $laptop)
    {
        //
    }
}
