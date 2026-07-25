@props(['config', 'type' => 'prebuilt'])

@php
    $platform = $config->platform ?? (str_contains(optional($config->cpu)->name, 'Ryzen') ? 'AMD' : 'INTEL');
    $rating = $config->rating ?? 5;
    $reviewCount = $config->review_count ?? rand(10, 150);
    $isOnSale = rand(0, 1) == 1; // 50% chance to be on sale
    $originalPrice = $config->price + (floor(rand(5000, 15000) / 1000) * 1000);
    
    // Determine the route name and parameters based on the type
    $routeName = $type === 'custom' ? 'ecommerce.configurator-overview' : ($type === 'laptop' ? 'ecommerce.laptop-overview' : 'ecommerce.prebuilt-overview');
    $routeParams = ['id' => $config->id];
@endphp

<div class="config-card w-full liquid-glass rounded-2xl p-5 border border-white/10 flex flex-col group hover:border-primary/50 transition-all duration-300" data-platform="{{ $platform }}">
    <div class="mb-4 flex justify-between items-start gap-2">
        <h3 class="text-lg font-bold text-white leading-tight">{{ $config->name }}</h3>
        <div class="flex flex-col items-end gap-0.5 shrink-0 mt-0.5">
            @if($reviewCount > 0)
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        @if($rating >= $i)
                            <i class="ph-fill ph-star text-primary text-[10px]"></i>
                        @elseif($rating >= $i - 0.5)
                            <i class="ph-fill ph-star-half text-primary text-[10px]"></i>
                        @else
                            <i class="ph-fill ph-star text-gray-600 text-[10px]"></i>
                        @endif
                    @endfor
                </div>
                <span class="text-[8px] text-gray-400 font-medium leading-none">{{ $reviewCount }} Reviews</span>
            @else
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="ph-fill ph-star text-gray-600 text-[10px]"></i>
                    @endfor
                </div>
                <span class="text-[8px] text-gray-500 font-medium leading-none">No reviews</span>
            @endif
        </div>
    </div>
    
    <div class="aspect-[16/9] w-full rounded-xl bg-black/40 mb-4 flex items-center justify-center p-2 border border-white/5 overflow-hidden">
        <img src="{{ $config->image_url ?? 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $config->name }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
    </div>

    <div class="space-y-2 mb-4">
        <div class="flex items-center gap-2 text-[11px]">
            <i class="ph ph-windows-logo text-gray-500 text-sm"></i>
            <span class="text-gray-300 truncate">Windows 11 Home</span>
        </div>
        <div class="flex items-center gap-2 text-[11px]">
            <i class="ph ph-cpu text-gray-500 text-sm"></i>
            <span class="text-gray-300 truncate">{{ $type === 'laptop' ? $config->processor : (optional($config->cpu)->name ?? 'N/A') }}</span>
        </div>
        <div class="flex items-center gap-2 text-[11px]">
            <i class="ph ph-graphics-card text-gray-500 text-sm"></i>
            <span class="text-gray-300 truncate">{{ $type === 'laptop' ? $config->gpu : (optional($config->gpu)->name ?? 'N/A') }}</span>
        </div>
        <div class="flex items-center gap-2 text-[11px]">
            <i class="ph ph-memory text-gray-500 text-sm"></i>
            <span class="text-gray-300 truncate">{{ $type === 'laptop' ? $config->ram : (optional($config->ram)->name ?? 'N/A') }}</span>
        </div>
        <div class="flex items-center gap-2 text-[11px]">
            <i class="ph ph-hard-drives text-gray-500 text-sm"></i>
            <span class="text-gray-300 truncate">{{ $type === 'laptop' ? $config->storage : (optional($config->storage)->name ?? 'N/A') }}</span>
        </div>
        @if($type === 'laptop' && $config->display)
        <div class="flex items-center gap-2 text-[11px]">
            <i class="ph ph-monitor text-gray-500 text-sm"></i>
            <span class="text-gray-300 truncate">{{ $config->display }}</span>
        </div>
        @endif
    </div>

    <div class="pt-3 border-t border-white/10 mb-4 space-y-1">
        <div class="flex items-center justify-between text-[10px]">
            <span class="text-gray-400">Forge Points</span>
            <span class="text-primary font-bold">+{{ number_format(floor($config->price / 100)) }} FP</span>
        </div>
        <div class="flex items-center justify-between text-[10px]">
            <span class="text-gray-400">Shipping</span>
            <span class="text-white">{{ rand(0, 1) ? 'Free Shipping' : 'Calculated at checkout' }}</span>
        </div>
    </div>

    <div class="mt-auto pt-4 border-t border-white/10 flex items-center justify-between min-h-[72px]">
        <div class="flex items-center gap-3">
            @if($isOnSale)
                @php
                    $saveAmount = $originalPrice - $config->price;
                    $saveAbbrev = $saveAmount >= 1000 ? floor($saveAmount / 1000) . 'K' : $saveAmount;
                @endphp
                <div class="w-10 h-10 rounded-lg bg-primary/20 border border-primary/30 flex flex-col items-center justify-center text-primary shrink-0">
                    <span class="text-[7px] font-black uppercase leading-none mb-0.5">Save</span>
                    <span class="text-xs font-black leading-none">{{ $saveAbbrev }}</span>
                </div>
            @endif
            <div class="flex flex-col">
                <span class="text-[9px] text-gray-500 uppercase tracking-widest block mb-0.5">Starting at</span>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black text-white leading-none">₱{{ number_format($config->price) }}</span>
                    @if($isOnSale)
                        <span class="text-gray-500 text-xs line-through leading-none">₱{{ number_format($originalPrice) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ $routeName ? route($routeName, $routeParams) : '#' }}" class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center hover:bg-white hover:text-black hover:scale-110 transition-all shadow-[0_0_10px_rgba(255,107,0,0.4)] shrink-0">
            <i class="ph-bold ph-arrow-right text-lg"></i>
        </a>
    </div>
</div>

