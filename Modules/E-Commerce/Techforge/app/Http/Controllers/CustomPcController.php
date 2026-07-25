<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\CustombuiltConfig;

class CustomPcController extends Controller
{
    public function index()
    {
        $configs = CustombuiltConfig::with(['intelCpu', 'amdCpu', 'gpu', 'intelMotherboard', 'amdMotherboard', 'intelRam', 'amdRam', 'storage', 'powerSupply', 'pcCase'])->get();
        $tiers = ['Core', 'Advanced', 'Extreme', 'Apex'];
        return view('ecommerce::pc-configurator', compact('configs', 'tiers'));
    }
}
