@php
    // Single source of truth for Bills of Materials (prebuilts). Loaded here so the
    // Work Orders → BoM tab is self-contained within the dashboard shell.
    $boms           = \Modules\Manufacturing\Models\ProductBom::with('items')->latest()->get();
    $inventoryItems = \Modules\Inventory\Models\Item::query()->orderBy('name')->get(['id', 'sku', 'name']);
@endphp

<div class="flex gap-3 h-full text-nexora-deep-navy">

    {{-- LEFT: Create BoM (prebuilt) --}}
    <section class="w-[32%] flex-shrink-0 bg-nexora-off-white border border-nexora-corporate rounded-xl
                    p-5 overflow-y-auto [&::-webkit-scrollbar]:hidden">
        <h2 class="font-heading font-medium text-lg text-nexora-navy-mid">New Prebuilt BoM</h2>
        <p class="text-xs text-nexora-navy-mid mt-1 mb-4 leading-relaxed">
            A product can be listed in E-commerce only after an active BoM exists here.
        </p>

        <form method="post" action="{{ route('manufacturing.boms.store') }}" class="flex flex-col gap-3">
            @csrf
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Product SKU</label>
                <input name="sku" value="{{ old('sku') }}" required
                       class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs
                              text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Product Name</label>
                <input name="name" value="{{ old('name') }}" required
                       class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs
                              text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Description</label>
                <textarea name="description" rows="2"
                          class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs
                                 text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Inventory Components</label>
                <div id="components" class="mt-1.5 flex flex-col gap-2"></div>
                <button type="button" onclick="addBomComponent()"
                        class="mt-2 text-[10px] font-semibold px-2.5 py-1 rounded-full border border-nexora-corporate
                               text-nexora-corporate hover:bg-nexora-corporate hover:text-white transition-colors">
                    + Add component
                </button>
                @error('items')<p class="text-[10px] text-nexora-danger mt-1.5">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="mt-2 w-full py-2 rounded-xl text-xs font-semibold border border-nexora-corporate
                           bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">
                Create Active BoM
            </button>
        </form>
    </section>

    {{-- RIGHT: Current BoM list --}}
    <section class="flex-1 bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl
                    p-5 overflow-y-auto [&::-webkit-scrollbar]:hidden">
        <div class="flex items-center justify-between mb-4">
            <h1 class="font-heading font-medium text-xl text-nexora-navy-mid">Active BoMs</h1>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-corporate/15 text-nexora-corporate">
                {{ count($boms) }} total
            </span>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-nexora-success/40 bg-nexora-success/10 px-3 py-2
                        text-[11px] font-medium text-nexora-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-3">
            @forelse($boms as $bom)
                <article class="bg-nexora-off-white border border-nexora-corporate/30 rounded-xl px-4 py-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-nexora-deep-navy truncate">{{ $bom->name }}</p>
                                <span class="text-[10px] font-['Courier_New'] text-nexora-navy-mid">{{ $bom->sku }}</span>
                            </div>
                            @if($bom->description)
                                <p class="text-[11px] text-nexora-navy-mid mt-0.5 leading-relaxed">{{ $bom->description }}</p>
                            @endif
                        </div>
                        <form method="post" action="{{ route('manufacturing.boms.destroy', $bom) }}" class="flex-shrink-0">
                            @csrf @method('delete')
                            <button class="text-[10px] font-semibold px-2.5 py-1 rounded-full border border-nexora-danger/50
                                           text-nexora-danger hover:bg-nexora-danger hover:text-white transition-colors">
                                Remove
                            </button>
                        </form>
                    </div>
                    <div class="mt-3 pt-2 border-t border-nexora-corporate/15">
                        <p class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider mb-1.5">Components</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($bom->items as $item)
                                <span class="px-2.5 py-1 rounded-full text-[10px] bg-nexora-slate-200 text-nexora-deep-navy">
                                    {{ $item->item_name }} <span class="text-nexora-navy-mid">×{{ $item->quantity_required }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-xs text-nexora-navy-mid">No BoMs yet. Create one from inventory components.</p>
            @endforelse
        </div>
    </section>
</div>

<script>
const bomInventory = @json($inventoryItems->map(fn($item) => ['id' => $item->id, 'label' => trim($item->sku.' · '.$item->name)])->values());
let bomComponentIndex = 0;

function addBomComponent() {
    const i = bomComponentIndex++;
    const options = bomInventory.map(x => `<option value="${x.id}">${x.label}</option>`).join('');
    document.getElementById('components').insertAdjacentHTML('beforeend', `
        <div class="flex items-center gap-2">
            <select name="items[${i}][inventory_item_id]" required
                    class="flex-1 border border-nexora-corporate/40 rounded-lg px-2.5 py-2 text-xs
                           text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                <option value="">Select inventory item</option>${options}
            </select>
            <input type="number" name="items[${i}][quantity_required]" value="1" min="1" required
                   class="w-16 border border-nexora-corporate/40 rounded-lg px-2.5 py-2 text-xs
                          text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            <button type="button" onclick="this.parentElement.remove()"
                    class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid
                           hover:bg-nexora-danger/10 hover:text-nexora-danger transition-colors text-sm leading-none">×</button>
        </div>`);
}
addBomComponent();
</script>
