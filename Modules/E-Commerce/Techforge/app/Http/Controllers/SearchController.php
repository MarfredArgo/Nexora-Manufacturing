<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\PrebuiltConfig;
use Modules\Ecommerce\Models\CustombuiltConfig;
use Modules\Ecommerce\Models\Cpu;
use Modules\Ecommerce\Models\Gpu;
use Modules\Ecommerce\Models\Motherboard;
use Modules\Ecommerce\Models\Ram;
use Modules\Ecommerce\Models\Storage;
use Modules\Ecommerce\Models\PowerSupply;
use Modules\Ecommerce\Models\PcCase;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q', '');
        $tab = $request->query('tab', 'prebuilt');

        // 1. PREBUILT
        $prebuiltBaseQuery = PrebuiltConfig::with(['cpu', 'gpu', 'motherboard', 'ram', 'storage', 'powerSupply']);
        if ($query) {
            $prebuiltBaseQuery->where(function($q) use ($query) {
                $q->where('name', 'ILIKE', '%' . $query . '%')
                  ->orWhereHas('cpu', function($sub) use ($query) {
                      $sub->where('name', 'ILIKE', '%' . $query . '%');
                  })
                  ->orWhereHas('gpu', function($sub) use ($query) {
                      $sub->where('name', 'ILIKE', '%' . $query . '%');
                  });
            });
        }
        $prebuiltCount = (clone $prebuiltBaseQuery)->count();

        // 2. CUSTOM
        $customBaseQuery = CustombuiltConfig::with(['intelCpu', 'amdCpu', 'gpu', 'intelMotherboard', 'amdMotherboard', 'intelRam', 'amdRam', 'storage', 'powerSupply']);
        if ($query) {
            $customBaseQuery->where(function($q) use ($query) {
                $q->where('name', 'ILIKE', '%' . $query . '%')
                  ->orWhereHas('intelCpu', function($sub) use ($query) { $sub->where('name', 'ILIKE', '%' . $query . '%'); })
                  ->orWhereHas('amdCpu', function($sub) use ($query) { $sub->where('name', 'ILIKE', '%' . $query . '%'); })
                  ->orWhereHas('gpu', function($sub) use ($query) { $sub->where('name', 'ILIKE', '%' . $query . '%'); });
            });
        }
        $customCount = (clone $customBaseQuery)->count();

        // 3. PARTS
        $parts = collect();
        $partsCount = 0;
        $partModels = [
            'Processor' => Cpu::class, 
            'Video Card' => Gpu::class, 
            'Motherboard' => Motherboard::class, 
            'Memory' => Ram::class, 
            'Storage' => Storage::class, 
            'Power Supply' => PowerSupply::class, 
            'Case' => PcCase::class,
            'Keyboard' => \Modules\Ecommerce\Models\AccessoryKeyboard::class,
            'Mouse' => \Modules\Ecommerce\Models\AccessoryMouse::class,
            'Headset' => \Modules\Ecommerce\Models\AccessoryHeadset::class,
            'Monitor' => \Modules\Ecommerce\Models\AccessoryMonitor::class,
        ];
        
        foreach ($partModels as $type => $modelClass) {
            $modelQuery = $modelClass::query();
            if ($query) {
                $modelQuery->where('name', 'ILIKE', '%' . $query . '%');
            }
            
            $res = $modelQuery->get();
            $res->transform(function ($item) use ($type) {
                $item->is_part = true;
                $item->type = $type;
                $item->search_category = 'parts';
                return $item;
            });
            $parts = $parts->concat($res);
            $partsCount += $res->count();
        }

        // 4. LAPTOPS (Hardcoded to 0)
        $laptopCount = 0;

        $counts = [
            'processors' => [],
            'gpus' => [],
            'rams' => [],
            'storages' => [],
        ];

        // 5. Compile All Results for Client-Side Tab Switching
        $prebuilts = (clone $prebuiltBaseQuery)->get();
        $prebuilts->transform(function ($item) {
            $item->search_category = 'prebuilt';
            return $item;
        });

        $customs = (clone $customBaseQuery)->get();
        $customs->transform(function ($item) {
            $item->search_category = 'custom';
            return $item;
        });

        $configs = $prebuilts->concat($customs)->concat($parts);

        // Sidebar counts (populate based on Prebuilt and Custom)
        $this->populateFilterCounts($prebuilts, $counts);
        $this->populateFilterCounts($customs, $counts);

        $totalResults = $prebuiltCount + $customCount + $partsCount + $laptopCount;

        $partModels = [
            \Modules\Ecommerce\Models\Cpu::class, \Modules\Ecommerce\Models\Gpu::class, \Modules\Ecommerce\Models\Motherboard::class, 
            \Modules\Ecommerce\Models\Ram::class, \Modules\Ecommerce\Models\Storage::class, \Modules\Ecommerce\Models\PowerSupply::class, \Modules\Ecommerce\Models\PcCase::class,
            \Modules\Ecommerce\Models\AccessoryKeyboard::class, \Modules\Ecommerce\Models\AccessoryMouse::class, 
            \Modules\Ecommerce\Models\AccessoryHeadset::class, \Modules\Ecommerce\Models\AccessoryMonitor::class
        ];
        
        $globalMinPrice = \Illuminate\Support\Facades\Cache::remember('global_min_price', 3600, function() use ($partModels) {
            $minPricesArr = [
                \Modules\Ecommerce\Models\PrebuiltConfig::min('price'),
                \Modules\Ecommerce\Models\CustombuiltConfig::min('price'),
            ];
            foreach ($partModels as $modelClass) {
                $minPricesArr[] = $modelClass::min('price');
            }
            $minPrices = array_filter($minPricesArr);
            return !empty($minPrices) ? floor(min($minPrices)) : 0;
        });

        $globalMaxPrice = \Illuminate\Support\Facades\Cache::remember('global_max_price', 3600, function() use ($partModels) {
            $maxPricesArr = [
                \Modules\Ecommerce\Models\PrebuiltConfig::max('price'),
                \Modules\Ecommerce\Models\CustombuiltConfig::max('price'),
            ];
            foreach ($partModels as $modelClass) {
                $maxPricesArr[] = $modelClass::max('price');
            }
            $maxPrices = array_filter($maxPricesArr);
            return !empty($maxPrices) ? ceil(max($maxPrices)) : 250000;
        });

        return view('ecommerce::search', compact(
            'query', 'tab', 'prebuiltCount', 'customCount', 
            'partsCount', 'laptopCount', 'counts', 'configs', 'totalResults',
            'globalMinPrice', 'globalMaxPrice'
        ));
    }

    private function populateFilterCounts($allConfigs, &$counts)
    {
        foreach ($allConfigs as $config) {
            $cpu = $config->cpu ?? $config->intelCpu ?? $config->amdCpu;
            $ram = $config->ram ?? $config->intelRam ?? $config->amdRam;
            if (!$cpu || !$config->gpu || !$ram || !$config->storage) continue;
            
            $procName = $cpu->name;
            if (!str_starts_with($procName, 'AMD') && str_contains($procName, 'Ryzen')) $procName = 'AMD ' . $procName;
            elseif (!str_starts_with($procName, 'Intel') && str_contains($procName, 'Core')) $procName = 'Intel ' . $procName;
            $counts['processors'][$procName] = ($counts['processors'][$procName] ?? 0) + 1;

            $gpuName = $config->gpu->name;
            if (!str_starts_with($gpuName, 'NVIDIA') && (str_contains($gpuName, 'RTX') || str_contains($gpuName, 'GTX'))) $gpuName = 'NVIDIA ' . $gpuName;
            elseif (!str_starts_with($gpuName, 'AMD') && str_contains($gpuName, 'RX')) $gpuName = 'AMD ' . $gpuName;
            $counts['gpus'][$gpuName] = ($counts['gpus'][$gpuName] ?? 0) + 1;

            $ramName = $ram->name;
            $counts['rams'][$ramName] = ($counts['rams'][$ramName] ?? 0) + 1;

            $storageName = $config->storage->name;
            $counts['storages'][$storageName] = ($counts['storages'][$storageName] ?? 0) + 1;
        }
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $model = $query->getModel();
        if ($request->filled('processor') || $request->filled('processor_brand')) {
            $filterLogic = function($q) use ($request) {
                $procs = $request->processor ?? [];
                $brands = $request->processor_brand ?? [];

                $q->where(function($subQ) use ($procs, $brands) {
                    if (!empty($procs)) {
                        $cleanedProcs = array_map(function($p) {
                            return str_replace(['AMD ', 'Intel '], '', $p);
                        }, $procs);
                        $subQ->where(function($q) use ($procs, $cleanedProcs) {
                            $q->whereIn('name', $procs)
                              ->orWhereIn('name', $cleanedProcs);
                        });
                    }
                    if (!empty($brands)) {
                        foreach ($brands as $brand) {
                            if ($brand === 'AMD') $subQ->orWhere('name', 'ILIKE', '%Ryzen%');
                            if ($brand === 'Intel') $subQ->orWhere('name', 'ILIKE', '%Core%');
                        }
                    }
                });
            };
            if (method_exists($model, 'cpu')) {
                $query->whereHas('cpu', $filterLogic);
            } else {
                $query->where(function($q) use ($filterLogic) {
                    $q->whereHas('intelCpu', $filterLogic)->orWhereHas('amdCpu', $filterLogic);
                });
            }
        }

        if ($request->filled('gpu') || $request->filled('gpu_brand')) {
            $query->whereHas('gpu', function($q) use ($request) {
                $gpus = $request->gpu ?? [];
                $brands = $request->gpu_brand ?? [];

                $q->where(function($subQ) use ($gpus, $brands) {
                    if (!empty($gpus)) {
                        $cleanedGpus = array_map(function($g) {
                            return str_replace(['NVIDIA ', 'AMD '], '', $g);
                        }, $gpus);
                        $subQ->where(function($q) use ($gpus, $cleanedGpus) {
                            $q->whereIn('name', $gpus)
                              ->orWhereIn('name', $cleanedGpus);
                        });
                    }
                    if (!empty($brands)) {
                        foreach ($brands as $brand) {
                            if ($brand === 'NVIDIA') $subQ->orWhere('name', 'ILIKE', '%RTX%')->orWhere('name', 'ILIKE', '%GTX%');
                            if ($brand === 'AMD') $subQ->orWhere('name', 'ILIKE', '%RX%');
                        }
                    }
                });
            });
        }

        if ($request->filled('ram') || $request->filled('ram_capacity')) {
            $filterLogic = function($q) use ($request) {
                $rams = $request->ram ?? [];
                $capacities = $request->ram_capacity ?? [];

                $q->where(function($subQ) use ($rams, $capacities) {
                    if (!empty($rams)) {
                        $subQ->whereIn('name', $rams);
                    }
                    if (!empty($capacities)) {
                        foreach ($capacities as $cap) {
                            $subQ->orWhere('name', 'ILIKE', $cap . '%');
                        }
                    }
                });
            };
            if (method_exists($model, 'ram')) {
                $query->whereHas('ram', $filterLogic);
            } else {
                $query->where(function($q) use ($filterLogic) {
                    $q->whereHas('intelRam', $filterLogic)->orWhereHas('amdRam', $filterLogic);
                });
            }
        }

        $sort = $request->sort ?? 'Recommended';
        if ($sort === 'Price: Low to High') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'Price: High to Low') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('price', 'desc'); 
        }

        return $query;
    }

    public function suggestions(Request $request)
    {
        $query = $request->query('q', '');
        if (empty($query)) {
            return response()->json([]);
        }

        $searchTerm = '%' . $query . '%';

        $prebuilt = \Illuminate\Support\Facades\DB::table('prebuilt_configs')
            ->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Prebuilt PC' as type"))
            ->where('name', 'ILIKE', $searchTerm)
            ->limit(2);

        $custom = \Illuminate\Support\Facades\DB::table('configurator_configs')
            ->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Custom PC' as type"))
            ->where('name', 'ILIKE', $searchTerm)
            ->limit(2);

        $cpu = \Illuminate\Support\Facades\DB::table('components_cpus')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Processor' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $gpu = \Illuminate\Support\Facades\DB::table('components_gpus')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Video Card' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $mobo = \Illuminate\Support\Facades\DB::table('components_motherboards')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Motherboard' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $ram = \Illuminate\Support\Facades\DB::table('components_rams')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Memory' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $storage = \Illuminate\Support\Facades\DB::table('components_storages')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Storage' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $psu = \Illuminate\Support\Facades\DB::table('components_power_supplies')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Power Supply' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $case = \Illuminate\Support\Facades\DB::table('components_pc_cases')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Case' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        
        $keyboard = \Illuminate\Support\Facades\DB::table('components_accessories_keyboards')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Keyboard' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $mouse = \Illuminate\Support\Facades\DB::table('components_accessories_mice')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Mouse' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $headset = \Illuminate\Support\Facades\DB::table('components_accessories_headsets')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Headset' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);
        $monitor = \Illuminate\Support\Facades\DB::table('components_accessories_monitors')->select('name', 'price', \Illuminate\Support\Facades\DB::raw("'Monitor' as type"))->where('name', 'ILIKE', $searchTerm)->limit(2);

        $results = collect($prebuilt
            ->unionAll($custom)
            ->unionAll($cpu)
            ->unionAll($gpu)
            ->unionAll($mobo)
            ->unionAll($ram)
            ->unionAll($storage)
            ->unionAll($psu)
            ->unionAll($case)
            ->unionAll($keyboard)
            ->unionAll($mouse)
            ->unionAll($headset)
            ->unionAll($monitor)
            ->limit(6)
            ->get());

        // Format and limit to top 6 total
        $formatted = $results->take(6)->map(function ($item) {
            return [
                'name' => $item->name,
                'type' => $item->type,
                'price' => 'â‚±' . number_format((float) $item->price, 2)
            ];
        });

        return response()->json($formatted);
    }
}

