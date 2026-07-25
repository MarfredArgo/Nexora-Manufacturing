<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Cpu;
use Modules\Ecommerce\Models\Motherboard;
use Modules\Ecommerce\Models\Ram;
use Modules\Ecommerce\Models\Gpu;
use Modules\Ecommerce\Models\PowerSupply;
use Modules\Ecommerce\Models\PcCase;

class ConfiguratorController extends Controller
{
    public function index()
    {
        $cpus = Cpu::all();
        $motherboards = Motherboard::all();
        $rams = Ram::all();
        $gpus = Gpu::all();
        $powerSupplies = PowerSupply::all();
        $cases = PcCase::all();

        return view('ecommerce::configurator', compact('cpus', 'motherboards', 'rams', 'gpus', 'powerSupplies', 'cases'));
    }
}
