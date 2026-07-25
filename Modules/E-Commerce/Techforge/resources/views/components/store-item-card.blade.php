@props([
    'id' => '0',
    'category' => 'Accessory',
    'name' => 'Store Item',
    'price' => '1000',
    'image' => 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=800&q=80',
    'rating' => 5,
    'reviews' => rand(10, 150),
    'sale' => false,
    'originalPrice' => null
])

<div {{ $attributes->merge(['class' => 'store-item-card liquid-glass rounded-2xl p-5 border border-white/10 flex flex-col group hover:border-primary/50 transition-all duration-300']) }}>

    <div class="aspect-square w-full rounded-xl bg-black/40 mb-4 flex items-center justify-center p-4 border border-white/5 overflow-hidden">
        <img src="{{ $image }}" alt="{{ $name }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500 drop-shadow-[0_10px_15px_rgba(0,0,0,0.5)]">
    </div>

    <div class="flex justify-between items-start gap-3 mb-4">
        <h3 class="text-lg font-bold text-white leading-tight truncate flex-1" title="{{ $name }}">{{ $name }}</h3>
        
        <div class="flex flex-col items-end gap-0.5 shrink-0 mt-1">
            <div class="flex gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    @if($rating >= $i)
                        <i class="ph-fill ph-star text-primary text-[10px]"></i>
                    @else
                        <i class="ph-fill ph-star text-gray-600 text-[10px]"></i>
                    @endif
                @endfor
            </div>
            <span class="text-[8px] text-gray-400 font-medium leading-none">{{ $reviews }} Reviews</span>
        </div>
    </div>

    <div class="mt-auto pt-4 border-t border-white/10 flex items-center justify-between min-h-[72px]">
        <div class="flex items-center gap-3">
            @if($sale)
                <div class="w-10 h-10 rounded-lg bg-primary/20 border border-primary/30 flex flex-col items-center justify-center text-primary shrink-0">
                    <span class="text-[7px] font-black uppercase leading-none mb-0.5">Sale</span>
                </div>
            @endif
            <div class="flex flex-col">
                <span class="text-[9px] text-gray-500 uppercase tracking-widest block mb-0.5">Price</span>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black text-white leading-none">₱{{ number_format($price) }}</span>
                    @if($sale && $originalPrice)
                        <span class="text-gray-500 text-xs line-through leading-none">₱{{ number_format($originalPrice) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <button onclick="addToCart('{{ $id }}', '{{ addslashes($name) }}', {{ $price }}, '{{ $image }}', 1, 'accessory', null, event.currentTarget)" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-primary hover:border-primary hover:text-white hover:scale-110 transition-all shrink-0 z-10 relative">
            <i class="ph-bold ph-shopping-cart-simple text-lg"></i>
        </button>
    </div>
</div>
