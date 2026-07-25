@php
    $perPageOptions = [20, 50, 70, 100];
    $perPage = (int) request('per_page', $perPage ?? 20);
    if (! in_array($perPage, $perPageOptions, true)) {
        $perPage = 20;
    }
@endphp

<div class="flex items-center gap-2 text-[11.5px] text-[#93abd3]">
    <label for="per-page-select" class="whitespace-nowrap">Show</label>
    <select
        id="per-page-select"
        class="js-per-page h-[45px] rounded-lg bg-[#0B1E3D] border border-white/10 text-white text-[12px] px-3 pr-8 cursor-pointer outline-none"
    >
        @foreach ($perPageOptions as $option)
            <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
        @endforeach
    </select>
    <span class="whitespace-nowrap">per page</span>
</div>
