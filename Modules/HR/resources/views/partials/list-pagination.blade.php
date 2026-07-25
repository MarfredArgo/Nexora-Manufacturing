@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\Paginator $paginator */
    $label = $label ?? 'records';
    $query = collect(request()->query())->except(['page', 'ajax'])->all();
@endphp

@if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator)
<div class="pagination-wrap w-full flex flex-col items-center gap-3 mt-5 mb-8">
    <nav class="flex items-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="w-9 h-9 grid place-items-center rounded-lg bg-[#0B1E3D] text-[#4c6291] cursor-not-allowed">
                <i class="fa-solid fa-chevron-left text-[11px]"></i>
            </span>
        @else
            <a href="{{ $paginator->appends($query)->previousPageUrl() }}"
               class="w-9 h-9 grid place-items-center rounded-lg bg-[#0B1E3D] text-white transition-colors duration-200 hover:bg-[#2e5ca3]">
                <i class="fa-solid fa-chevron-left text-[11px]"></i>
            </a>
        @endif

        @foreach ($paginator->appends($query)->getUrlRange(1, max($paginator->lastPage(), 1)) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="w-9 h-9 grid place-items-center rounded-lg bg-[#2D7EFF] text-white text-[12px] font-semibold">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}"
                   class="w-9 h-9 grid place-items-center rounded-lg bg-[#0B1E3D] text-[#C9DAF8] text-[12px] transition-colors duration-200 hover:bg-[#2e5ca3] hover:text-white">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->appends($query)->nextPageUrl() }}"
               class="w-9 h-9 grid place-items-center rounded-lg bg-[#0B1E3D] text-white transition-colors duration-200 hover:bg-[#2e5ca3]">
                <i class="fa-solid fa-chevron-right text-[11px]"></i>
            </a>
        @else
            <span class="w-9 h-9 grid place-items-center rounded-lg bg-[#0B1E3D] text-[#4c6291] cursor-not-allowed">
                <i class="fa-solid fa-chevron-right text-[11px]"></i>
            </span>
        @endif
    </nav>

    <div class="text-[11.5px] text-[#93abd3]">
        @if (method_exists($paginator, 'total') && $paginator->total() > 0)
            Showing {{ $paginator->firstItem() }}â€“{{ $paginator->lastItem() }} of {{ $paginator->total() }} {{ $label }}
        @else
            No {{ $label }} to show
        @endif
    </div>
</div>
@endif
