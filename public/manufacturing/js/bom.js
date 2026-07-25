// bom.js
// Bill of Materials builder: component rows + component search modal.
// Inventory data is injected server-side as a JSON <script> tag (#bom-inventory-data).

const bomInventory = JSON.parse(
    (document.getElementById('bom-inventory-data') || {}).textContent || '[]'
);

let bomComponentIndex = 0;
let bomSearchTarget   = null;
let bomCategoriesReady = false;

// The search modal lives in modals.blade (rendered after this script), so fill
// its category filter lazily the first time the modal is opened.
function populateBomCategories() {
    if (bomCategoriesReady) return;
    const sel = document.getElementById('bom-search-category');
    if (!sel) return;
    const cats = [...new Set(bomInventory.map(x => x.category).filter(Boolean))].sort();
    sel.innerHTML = '<option value="">All categories</option>' +
        cats.map(c => `<option value="${c}">${c}</option>`).join('');
    bomCategoriesReady = true;
}

const bomSearchIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>';

function addBomComponent() {
    const i = bomComponentIndex++;
    const options = bomInventory.map(x => `<option value="${x.id}">${x.label}</option>`).join('');
    document.getElementById('components').insertAdjacentHTML('beforeend', `
        <div class="flex items-center gap-2">
            <select id="bom-select-${i}" name="items[${i}][inventory_item_id]" required
                    class="flex-1 min-w-0 border border-nexora-corporate/40 rounded-lg px-2.5 py-2 text-xs
                           text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                <option value="">Select inventory item</option>${options}
            </select>
            <button type="button" onclick="openBomSearch(${i})" title="Search components"
                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg border border-nexora-corporate/40
                           text-nexora-corporate bg-nexora-slate-200 hover:bg-nexora-corporate hover:text-white transition-colors">
                ${bomSearchIcon}
            </button>
            <input type="number" name="items[${i}][quantity_required]" value="1" min="1" required
                   class="w-16 flex-shrink-0 border border-nexora-corporate/40 rounded-lg px-2.5 py-2 text-xs
                          text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            <button type="button" onclick="this.parentElement.remove()"
                    class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid
                           hover:bg-nexora-danger/10 hover:text-nexora-danger transition-colors text-sm leading-none">×</button>
        </div>`);
}

function openBomSearch(i) {
    populateBomCategories();
    bomSearchTarget = i;
    document.getElementById('bom-search-input').value = '';
    document.getElementById('bom-search-category').value = '';
    renderBomResults();
    openModal('bom-search-backdrop');
}

function renderBomResults() {
    const q   = (document.getElementById('bom-search-input').value || '').toLowerCase().trim();
    const cat = document.getElementById('bom-search-category').value;
    const list = bomInventory.filter(x =>
        (!cat || x.category === cat) &&
        (!q || x.name.toLowerCase().includes(q) || (x.sku || '').toLowerCase().includes(q))
    );
    const box = document.getElementById('bom-search-results');
    if (!list.length) {
        box.innerHTML = '<p class="text-xs text-nexora-navy-mid px-2 py-4 text-center">No components match.</p>';
        return;
    }
    box.innerHTML = list.map(x => `
        <button type="button" onclick="selectBomComponent(${x.id})"
                class="text-left px-3 py-2 rounded-lg hover:bg-nexora-steel-blue/20 transition-colors">
            <p class="text-xs font-medium text-nexora-deep-navy">${x.name}</p>
            <p class="text-[10px] text-nexora-navy-mid font-['Courier_New']">${x.sku || ''}${x.category ? ' · ' + x.category : ''}</p>
        </button>`).join('');
}

function selectBomComponent(id) {
    if (bomSearchTarget !== null) {
        const sel = document.getElementById('bom-select-' + bomSearchTarget);
        if (sel) sel.value = String(id);
    }
    closeModal('bom-search-backdrop');
}

addBomComponent();
