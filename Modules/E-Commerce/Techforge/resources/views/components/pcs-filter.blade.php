<!-- Mobile Filter Toggle (Visible on smaller screens) -->
        <button id="mobile-filter-btn" class="lg:hidden w-full liquid-glass rounded-xl p-4 flex items-center justify-between text-white border border-white/10">
            <span class="font-bold">Filters</span>
            <i class="ph ph-sliders text-xl"></i>
        </button>

        <!-- Sidebar / Filters -->
        <aside id="filter-sidebar" class="w-full lg:w-[280px] xl:w-[320px] flex-shrink-0 fixed lg:static top-0 left-0 h-full lg:h-auto z-[100] lg:z-auto bg-gradient-to-b from-[#1a0a05] to-[#0a0402] border border-[#2a110a] rounded-2xl p-6 overflow-y-auto lg:overflow-visible transition-transform duration-300 transform -translate-x-full lg:translate-x-0 shadow-2xl lg:shadow-none">
            
            <!-- Mobile Close Button -->
            <div class="flex justify-between items-center mb-8 lg:hidden">
                <h2 class="text-xl font-bold text-white">Filters</h2>
                <button id="close-filter-btn" class="text-gray-400 hover:text-white">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-white">Filter</h2>
                @php
                    $resetParams = [];
                    if(request()->has('q')) $resetParams['q'] = request('q');
                    if(request()->has('tab')) $resetParams['tab'] = request('tab');
                @endphp
                <a href="{{ route(str_starts_with($route, 'ecommerce.') ? $route : 'ecommerce.' . $route, $resetParams) }}" class="text-xs text-primary hover:text-orange-400 font-bold uppercase tracking-widest transition-colors">Reset All</a>
            </div>

            <!-- Price Accordion -->
            <div class="mb-6 border-t border-[#3a1810] pt-6">
                <input type="checkbox" id="price-accordion" class="peer hidden" checked>
                <label for="price-accordion" class="flex items-center justify-between cursor-pointer group">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="ph ph-currency-circle text-primary"></i> Price
                    </h3>
                    <i class="ph ph-caret-down text-gray-500 transition-transform peer-checked:rotate-180"></i>
                </label>
                
                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                    <div class="min-h-0 pt-4 pb-2">
                        <div class="flex items-center justify-center gap-4 mb-4">
                            <div class="flex-1">
                                <span class="text-xs text-gray-500 block text-center mb-1">Min</span>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">P</span>
                                    <input type="number" name="price_min" value="{{ request('price_min') }}" id="price-min" placeholder="0" class="w-full bg-black/40 border border-[#3a1810] rounded-lg py-1.5 pl-6 pr-2 text-sm text-center font-bold text-white focus:border-primary outline-none transition-all [&::-webkit-inner-spin-button]:appearance-none">
                                </div>
                            </div>
                            <span class="text-gray-500 font-bold mt-5">—</span>
                            <div class="flex-1">
                                <span class="text-xs text-gray-500 block text-center mb-1">Max</span>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">P</span>
                                    <input type="number" name="price_max" value="{{ request('price_max') }}" id="price-max" placeholder="250000" class="w-full bg-black/40 border border-[#3a1810] rounded-lg py-1.5 pl-6 pr-2 text-sm text-center font-bold text-white focus:border-primary outline-none transition-all [&::-webkit-inner-spin-button]:appearance-none">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dual Range Slider -->
                        <div class="relative h-2 bg-[#3a1810] rounded-full mt-6 mb-2">
                            @php
                                $reqMin = max(request('price_min', $globalMinPrice), $globalMinPrice);
                                $reqMax = min(request('price_max', $globalMaxPrice), $globalMaxPrice);
                                $range = max($globalMaxPrice - $globalMinPrice, 1); // prevent division by zero
                                $leftPercent = (($reqMin - $globalMinPrice) / $range) * 100;
                                $rightPercent = 100 - ((($reqMax - $globalMinPrice) / $range) * 100);
                            @endphp
                            <div id="slider-track" class="absolute top-0 bottom-0 bg-primary rounded-full" style="left: {{ $leftPercent }}%; right: {{ $rightPercent }}%;"></div>
                            <input type="range" id="range-min" min="{{ $globalMinPrice }}" max="{{ $globalMaxPrice }}" step="1000" value="{{ $reqMin }}" class="absolute w-full -top-1 h-4 appearance-none bg-transparent pointer-events-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-primary [&::-webkit-slider-thumb]:rounded-full cursor-pointer">
                            <input type="range" id="range-max" min="{{ $globalMinPrice }}" max="{{ $globalMaxPrice }}" step="1000" value="{{ $reqMax }}" class="absolute w-full -top-1 h-4 appearance-none bg-transparent pointer-events-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-primary [&::-webkit-slider-thumb]:rounded-full cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processor Accordion -->
            <div class="mb-6 border-t border-[#3a1810] pt-6">
                <input type="checkbox" id="proc-accordion" class="peer hidden" checked>
                <label for="proc-accordion" class="flex items-center justify-between cursor-pointer group mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="ph ph-cpu text-primary"></i> Processor
                    </h3>
                    <i class="ph ph-caret-down text-gray-500 transition-transform peer-checked:rotate-180"></i>
                </label>
                
                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                    <div class="min-h-0">
                        @php
                            $reqProcs = request('processor', []);
                            
                            $amdOpts = collect($counts['processors'] ?? [])->filter(fn($c, $p) => str_starts_with($p, 'AMD'));
                            $amdTotal = $amdOpts->count();
                            $amdChecked = $amdOpts->keys()->intersect($reqProcs)->count();
                            $isAmdChecked = $amdTotal > 0 && $amdChecked === $amdTotal;
                            $isAmdIndeterminate = $amdChecked > 0 && $amdChecked < $amdTotal;
                            $amdOpen = $amdChecked > 0;
                            
                            $intelOpts = collect($counts['processors'] ?? [])->filter(fn($c, $p) => str_starts_with($p, 'Intel'));
                            $intelTotal = $intelOpts->count();
                            $intelChecked = $intelOpts->keys()->intersect($reqProcs)->count();
                            $isIntelChecked = $intelTotal > 0 && $intelChecked === $intelTotal;
                            $isIntelIndeterminate = $intelChecked > 0 && $intelChecked < $intelTotal;
                            $intelOpen = $intelChecked > 0;
                        @endphp

                        <div class="space-y-4">
                            <!-- AMD Dropdown -->
                            <div>
                                <input type="checkbox" id="amd-accordion" class="peer hidden" {{ $amdOpen ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 peer-checked:[&_.ph-caret-down]:rotate-180">
                                    <input type="checkbox" data-parent="proc-amd" id="brand-filter-amd" class="brand-checkbox appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full [&.is-indeterminate]:bg-primary [&.is-indeterminate]:border-primary [&.is-indeterminate]:after:block [&.is-indeterminate]:after:h-0.5 [&.is-indeterminate]:after:rounded-sm transition-all cursor-pointer {{ $isAmdIndeterminate ? 'is-indeterminate' : '' }}" {{ $isAmdChecked ? 'checked' : '' }}>
                                    <label for="amd-accordion" class="flex-1 flex items-center cursor-pointer text-sm font-bold text-gray-300 hover:text-white transition-colors select-none">
                                        AMD
                                        <i class="ph ph-caret-down ml-auto text-gray-500 transition-transform"></i>
                                    </label>
                                </div>
                                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                                    <div class="min-h-0 pl-5 mt-3 space-y-3 border-l border-[#3a1810] ml-2">
                                        @foreach($amdOpts as $proc => $count)
                                        <label class="flex items-center gap-3 group cursor-pointer">
                                            <input type="checkbox" name="processor[]" value="{{ $proc }}" data-child-of="proc-amd" class="appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full transition-all cursor-pointer" {{ in_array($proc, $reqProcs) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-400 group-hover:text-white transition-colors">{{ str_replace('AMD ', '', $proc) }}</span>
                                            <span class="text-[10px] text-gray-500 ml-auto">{{ $count }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Intel Dropdown -->
                            <div>
                                <input type="checkbox" id="intel-accordion" class="peer hidden" {{ $intelOpen ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 peer-checked:[&_.ph-caret-down]:rotate-180">
                                    <input type="checkbox" data-parent="proc-intel" id="brand-filter-intel" class="brand-checkbox appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full [&.is-indeterminate]:bg-primary [&.is-indeterminate]:border-primary [&.is-indeterminate]:after:block [&.is-indeterminate]:after:h-0.5 [&.is-indeterminate]:after:rounded-sm transition-all cursor-pointer {{ $isIntelIndeterminate ? 'is-indeterminate' : '' }}" {{ $isIntelChecked ? 'checked' : '' }}>
                                    <label for="intel-accordion" class="flex-1 flex items-center cursor-pointer text-sm font-bold text-gray-300 hover:text-white transition-colors select-none">
                                        Intel
                                        <i class="ph ph-caret-down ml-auto text-gray-500 transition-transform"></i>
                                    </label>
                                </div>
                                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                                    <div class="min-h-0 pl-5 mt-3 space-y-3 border-l border-[#3a1810] ml-2">
                                        @foreach($intelOpts as $proc => $count)
                                        <label class="flex items-center gap-3 group cursor-pointer">
                                            <input type="checkbox" name="processor[]" value="{{ $proc }}" data-child-of="proc-intel" class="appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full transition-all cursor-pointer" {{ in_array($proc, $reqProcs) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-400 group-hover:text-white transition-colors">{{ str_replace('Intel ', '', $proc) }}</span>
                                            <span class="text-[10px] text-gray-500 ml-auto">{{ $count }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Card Accordion -->
            <div class="mb-6 border-t border-[#3a1810] pt-6">
                <input type="checkbox" id="gpu-accordion" class="peer hidden" {{ request()->filled('gpu') || request()->filled('gpu_brand') ? 'checked' : '' }}>
                <label for="gpu-accordion" class="flex items-center justify-between cursor-pointer group mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="ph ph-graphics-card text-primary"></i> Video Card
                    </h3>
                    <i class="ph ph-caret-down text-gray-500 transition-transform peer-checked:rotate-180"></i>
                </label>
                
                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                    <div class="min-h-0">
                        @php
                            $reqGpus = request('gpu', []);
                            
                            $nvidiaOpts = collect($counts['gpus'] ?? [])->filter(fn($c, $g) => str_starts_with($g, 'NVIDIA'));
                            $nvidiaTotal = $nvidiaOpts->count();
                            $nvidiaChecked = $nvidiaOpts->keys()->intersect($reqGpus)->count();
                            $isNvidiaChecked = $nvidiaTotal > 0 && $nvidiaChecked === $nvidiaTotal;
                            $isNvidiaIndeterminate = $nvidiaChecked > 0 && $nvidiaChecked < $nvidiaTotal;
                            $nvidiaOpen = $nvidiaChecked > 0;
                            
                            $amdGpuOpts = collect($counts['gpus'] ?? [])->filter(fn($c, $g) => str_starts_with($g, 'AMD'));
                            $amdGpuTotal = $amdGpuOpts->count();
                            $amdGpuChecked = $amdGpuOpts->keys()->intersect($reqGpus)->count();
                            $isAmdGpuChecked = $amdGpuTotal > 0 && $amdGpuChecked === $amdGpuTotal;
                            $isAmdGpuIndeterminate = $amdGpuChecked > 0 && $amdGpuChecked < $amdGpuTotal;
                            $amdGpuOpen = $amdGpuChecked > 0;
                        @endphp
                        
                        <div class="space-y-4">
                            <!-- NVIDIA Dropdown -->
                            <div>
                                <input type="checkbox" id="nvidia-accordion" class="peer hidden" {{ $nvidiaOpen ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 peer-checked:[&_.ph-caret-down]:rotate-180">
                                    <input type="checkbox" data-parent="gpu-nvidia" id="brand-filter-nvidia" class="brand-checkbox appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full [&.is-indeterminate]:bg-primary [&.is-indeterminate]:border-primary [&.is-indeterminate]:after:block [&.is-indeterminate]:after:h-0.5 [&.is-indeterminate]:after:rounded-sm transition-all cursor-pointer {{ $isNvidiaIndeterminate ? 'is-indeterminate' : '' }}" {{ $isNvidiaChecked ? 'checked' : '' }}>
                                    <label for="nvidia-accordion" class="flex-1 flex items-center cursor-pointer text-sm font-bold text-gray-300 hover:text-white transition-colors select-none">
                                        NVIDIA
                                        <i class="ph ph-caret-down ml-auto text-gray-500 transition-transform"></i>
                                    </label>
                                </div>
                                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                                    <div class="min-h-0 pl-5 mt-3 space-y-3 border-l border-[#3a1810] ml-2">
                                        @foreach($nvidiaOpts as $gpu => $count)
                                        <label class="flex items-center gap-3 group cursor-pointer">
                                            <input type="checkbox" name="gpu[]" value="{{ $gpu }}" data-child-of="gpu-nvidia" class="appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full transition-all cursor-pointer" {{ in_array($gpu, $reqGpus) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-400 group-hover:text-white transition-colors truncate" title="{{ $gpu }}">{{ str_replace('NVIDIA ', '', $gpu) }}</span>
                                            <span class="text-[10px] text-gray-500 ml-auto shrink-0">{{ $count }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="amd-gpu-accordion" class="peer hidden" {{ $amdGpuOpen ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 peer-checked:[&_.ph-caret-down]:rotate-180">
                                    <input type="checkbox" data-parent="gpu-amd" id="brand-filter-amd-gpu" class="brand-checkbox appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full [&.is-indeterminate]:bg-primary [&.is-indeterminate]:border-primary [&.is-indeterminate]:after:block [&.is-indeterminate]:after:h-0.5 [&.is-indeterminate]:after:rounded-sm transition-all cursor-pointer {{ $isAmdGpuIndeterminate ? 'is-indeterminate' : '' }}" {{ $isAmdGpuChecked ? 'checked' : '' }}>
                                    <label for="amd-gpu-accordion" class="flex-1 flex items-center cursor-pointer text-sm font-bold text-gray-300 hover:text-white transition-colors select-none">
                                        AMD
                                        <i class="ph ph-caret-down ml-auto text-gray-500 transition-transform"></i>
                                    </label>
                                </div>
                                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                                    <div class="min-h-0 pl-5 mt-3 space-y-3 border-l border-[#3a1810] ml-2">
                                        @foreach($amdGpuOpts as $gpu => $count)
                                        <label class="flex items-center gap-3 group cursor-pointer">
                                            <input type="checkbox" name="gpu[]" value="{{ $gpu }}" data-child-of="gpu-amd" class="appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full transition-all cursor-pointer" {{ in_array($gpu, $reqGpus) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-400 group-hover:text-white transition-colors truncate" title="{{ $gpu }}">{{ str_replace('AMD ', '', $gpu) }}</span>
                                            <span class="text-[10px] text-gray-500 ml-auto shrink-0">{{ $count }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memory Accordion -->
            <div class="mb-6 border-t border-[#3a1810] pt-6">
                <input type="checkbox" id="ram-accordion" class="peer hidden" {{ request()->filled('ram') || request()->filled('ram_capacity') ? 'checked' : '' }}>
                <label for="ram-accordion" class="flex items-center justify-between cursor-pointer group mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="ph ph-memory text-primary"></i> Memory
                    </h3>
                    <i class="ph ph-caret-down text-gray-500 transition-transform peer-checked:rotate-180"></i>
                </label>
                
                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                    <div class="min-h-0">
                        @php
                            $reqRams = request('ram', []);
                            $reqRamCaps = request('ram_capacity', []);
                        @endphp
                        
                        <div class="space-y-4">
                            @foreach(['16GB', '32GB', '64GB'] as $cap)
                                @php
                                    $ramOpts = collect($counts['rams'] ?? [])->filter(fn($c, $r) => str_contains($r, $cap));
                                    $ramTotal = $ramOpts->count();
                                    $ramChecked = $ramOpts->keys()->intersect($reqRams)->count();
                                    $isCapChecked = $ramTotal > 0 && $ramChecked === $ramTotal;
                                    $isCapIndeterminate = $ramChecked > 0 && $ramChecked < $ramTotal;
                                    $capOpen = $ramChecked > 0;
                                @endphp
                                
                                @if($ramOpts->count() > 0)
                                <div>
                                    <input type="checkbox" id="ram-{{ $cap }}-accordion" class="peer hidden" {{ $capOpen ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3 peer-checked:[&_.ph-caret-down]:rotate-180">
                                        <input type="checkbox" data-parent="ram-{{ str_replace(' ', '', $cap) }}" class="brand-checkbox appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full [&.is-indeterminate]:bg-primary [&.is-indeterminate]:border-primary [&.is-indeterminate]:after:block [&.is-indeterminate]:after:h-0.5 [&.is-indeterminate]:after:rounded-sm transition-all cursor-pointer {{ $isCapIndeterminate ? 'is-indeterminate' : '' }}" {{ $isCapChecked ? 'checked' : '' }}>
                                        <label for="ram-{{ $cap }}-accordion" class="flex-1 flex items-center cursor-pointer text-sm font-bold text-gray-300 hover:text-white transition-colors select-none">
                                            {{ $cap }}
                                            <i class="ph ph-caret-down ml-auto text-gray-500 transition-transform"></i>
                                        </label>
                                    </div>
                                    <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                                        <div class="min-h-0 pl-5 mt-3 space-y-3 border-l border-[#3a1810] ml-2">
                                            @foreach($ramOpts as $ram => $count)
                                            <label class="flex items-center gap-3 group cursor-pointer">
                                                <input type="checkbox" name="ram[]" value="{{ $ram }}" data-child-of="ram-{{ str_replace(' ', '', $cap) }}" class="appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full transition-all cursor-pointer" {{ in_array($ram, $reqRams) ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-400 group-hover:text-white transition-colors truncate" title="{{ $ram }}">{{ trim(str_replace($cap, '', $ram)) }}</span>
                                                <span class="text-[10px] text-gray-500 ml-auto shrink-0">{{ $count }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Accordion -->
            <div class="mb-4 border-t border-[#3a1810] pt-6">
                <input type="checkbox" id="storage-accordion" class="peer hidden" {{ request()->filled('storage') ? 'checked' : '' }}>
                <label for="storage-accordion" class="flex items-center justify-between cursor-pointer group mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="ph ph-hard-drives text-primary"></i> Storage
                    </h3>
                    <i class="ph ph-caret-down text-gray-500 transition-transform peer-checked:rotate-180"></i>
                </label>
                
                <div class="grid grid-rows-[0fr] peer-checked:grid-rows-[1fr] transition-all duration-300 opacity-0 peer-checked:opacity-100 overflow-hidden">
                    <div class="min-h-0 space-y-3 pt-4 pb-2">
                        @php
                            $storageGroups = [];
                            foreach($counts['storages'] as $storage => $count) {
                                $size = explode(' ', trim($storage))[0];
                                if (!isset($storageGroups[$size])) {
                                    $storageGroups[$size] = 0;
                                }
                                $storageGroups[$size] += $count;
                            }
                            
                            // Sort by size (e.g. 1TB, 2TB, 512GB)
                            uksort($storageGroups, function($a, $b) {
                                $aNum = (int)$a;
                                $bNum = (int)$b;
                                if (str_contains($a, 'TB')) $aNum *= 1000;
                                if (str_contains($b, 'TB')) $bNum *= 1000;
                                return $aNum <=> $bNum;
                            });
                        @endphp
                        @foreach($storageGroups as $size => $count)
                        <label class="flex items-center gap-3 group cursor-pointer">
                            <input type="checkbox" name="storage[]" value="{{ $size }}" class="appearance-none w-4 h-4 shrink-0 border border-[#5a2810] rounded-sm bg-black/40 checked:bg-primary checked:border-primary relative after:content-[''] after:absolute after:hidden checked:after:block after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-2 after:h-2 after:bg-white after:rounded-full transition-all cursor-pointer" {{ in_array($size, request('storage', [])) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-300 group-hover:text-white transition-colors truncate">{{ $size }}</span>
                            <span class="text-[10px] text-gray-500 ml-auto shrink-0">{{ $count }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

        </aside>
