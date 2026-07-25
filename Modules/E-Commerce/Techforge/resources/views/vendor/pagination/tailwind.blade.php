@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex justify-center mt-12 gap-2">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 rounded-xl liquid-glass border border-white/10 flex items-center justify-center text-gray-600 cursor-not-allowed">
                <i class="ph-bold ph-caret-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 rounded-xl liquid-glass border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:border-primary/50 transition-colors">
                <i class="ph-bold ph-caret-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="w-10 h-10 rounded-xl liquid-glass border border-white/10 flex items-center justify-center text-gray-400">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 rounded-xl bg-primary shadow-[0_0_15px_rgba(255,107,0,0.4)] border border-primary flex items-center justify-center text-white font-bold transition-colors">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 rounded-xl liquid-glass border border-white/10 flex items-center justify-center text-gray-300 hover:text-white hover:border-primary/50 font-bold transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 rounded-xl liquid-glass border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:border-primary/50 transition-colors">
                <i class="ph-bold ph-caret-right"></i>
            </a>
        @else
            <span class="w-10 h-10 rounded-xl liquid-glass border border-white/10 flex items-center justify-center text-gray-600 cursor-not-allowed">
                <i class="ph-bold ph-caret-right"></i>
            </span>
        @endif
    </nav>
@endif
