<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title></title>
  <style>
    .main-wrapper { opacity: 0; animation: showPage .8s ease forwards; }
@keyframes showPage { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.dd-menu { animation: ddIn .12s ease forwards; }
@keyframes ddIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
::-webkit-scrollbar { height: 8px; width: 8px; }
::-webkit-scrollbar-thumb { background: #1c3a6e; border-radius: 4px; }
    .main-wrapper {
    opacity: 0;
    animation: showPage .8s ease forwards;
}
@keyframes showPage {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

    body {
      margin: 0;
      height: 100vh;
      background-color: #0B1E3D;
    }
  </style>
</head>
<script>
window.onload = () => {
  const splash = document.getElementById("splash");
  const SPLASH_DURATION = 500;

  setTimeout(() => {
    splash.style.opacity = "0";
    splash.style.pointerEvents = "none";
  }, SPLASH_DURATION);
};
</script>
  <div id="splash"></div>
    <div class="main-wrapper">
  <div>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { navy: {900:'#0b1e3b',800:'#132b52',700:'#17325f',600:'#1c3a6e'}, muted:'#9bb0d1' } } } }
</script>
<body class="bg-navy-900 text-white font-sans min-h-screen">

<div class="h-[3px] w-full bg-gradient-to-r from-blue-500/60 via-blue-400/20 to-transparent"></div>

<div class="main-wrapper max-w-[1400px] mx-auto p-6 space-y-4">


  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold">Charts of Account</h1>
    </div>
    <div class="flex items-center gap-2">
      <div class="flex rounded-lg overflow-hidden">
        <button onclick="openAccountModal()" class="bg-blue-500 hover:bg-blue-600 transition px-4 py-2 text-sm font-medium">New account</button>
      </div>
    </div>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-2">


      <div class="flex items-center gap-2 bg-navy-800 border border-navy-600 rounded-lg px-3 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input id="searchInput" type="text" placeholder="Filter by name or number" oninput="onSearchInput(this.value)"
               class="bg-transparent outline-none text-sm placeholder-muted w-44 sm:w-56">
      </div>

      <select id="typeFilter" onchange="setFilterType(this.value)" class="bg-navy-800 border border-navy-600 text-sm rounded-lg px-3 py-2 outline-none">
        <option value="All">All</option>
      </select>
    </div>
    <div class="flex items-center gap-3">
      <div class="relative">
        <div id="settingsMenu" class="dd-menu hidden absolute right-0 mt-1 bg-navy-800 border border-navy-600 rounded-lg shadow-lg text-sm w-56 p-3 space-y-3 z-20">

          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="chkIncludeInactive" onchange="state.includeInactive=this.checked; state.page=1; renderTable();" class="w-4 h-4 accent-blue-500">
            Include inactive accounts
          </label>
        </div>
      </div>
    </div>
  </div>


  <div class="flex justify-end items-center gap-3 text-sm text-muted">
    <button id="prevBtn" onclick="changePage(-1)" class="hover:text-white disabled:opacity-40 disabled:cursor-not-allowed" disabled>Previous</button>
    <span id="pageInfo" class="text-white">1 - 10</span>
    <button id="nextBtn" onclick="changePage(1)" class="hover:text-white disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
  </div>

  <div class="bg-navy-800 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-muted text-left border-b border-navy-600 select-none">
            <th class="py-3 pl-4 pr-2 w-10">
              <input type="checkbox" id="selectAllChk" onchange="toggleSelectAllPage(this.checked)" class="w-4 h-4 accent-blue-500 cursor-pointer">
            </th>
            <th class="py-3 pr-2 cursor-pointer" onclick="setSort('name')">
              <span class="inline-flex items-center gap-1">NAME <span id="sort-name"></span></span>
            </th>
            <th class="py-3 pr-2 cursor-pointer" onclick="setSort('number')">
              <span class="inline-flex items-center gap-1">NUMBER <span id="sort-number"></span></span>
            </th>
            <th class="py-3 pr-2 cursor-pointer" onclick="setSort('type')">
              <span class="inline-flex items-center gap-1">ACCOUNT TYPE <span id="sort-type"></span></span>
            </th>
            <th class="py-3 pr-2 cursor-pointer" onclick="setSort('detail')">
              <span class="inline-flex items-center gap-1">DETAIL TYPE <span id="sort-detail"></span></span>
            </th>

            <th class="py-3 pr-2 cursor-pointer" onclick="setSort('balance')">
              <span class="inline-flex items-center gap-1">BALANCE <span id="sort-balance"></span></th>
              <th class="py-3 pr-2 cursor-pointer" onclick="setSort('detail')">
              <span class="inline-flex items-center gap-1">DATE<span id="sort-detail"></span></span>
            </th>
            <th class="py-3 pr-4">ACTION</th>

        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
    <div id="emptyState" class="hidden text-center text-muted py-10 text-sm">No accounts match your filters.</div>
  </div>

</div>

<!-- Account Modal -->
<div id="accountModalWrap"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">

    <div class="bg-navy-800 w-full max-w-lg rounded-xl shadow-2xl border border-navy-600">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-navy-600">
            <h2 id="accountModalTitle" class="text-xl font-semibold">
                New Account
            </h2>

            <button onclick="closeAccountModal()"
                    class="text-gray-400 hover:text-white text-2xl leading-none">
                &times;
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">

            <div>
                <label class="block text-sm text-muted mb-1">
                    Account Name
                </label>

                <input
                    id="fName"
                    type="text"
                    class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 outline-none"
                    placeholder="Cash">
            </div>

            <div>

            </div>

            <div>
                <label class="block text-sm text-muted mb-1">
                    Account Type
                </label>

                <select
                    id="fType"
                    class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 outline-none">

                </select>
            </div>

            <div>
                <label class="block text-sm text-muted mb-1">
                    Detail Type
                </label>

                <input
                    id="fDetail"
                    type="text"
                    class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 outline-none"
                    placeholder="Cash on Hand">
            </div>

            <div>
                <label class="block text-sm text-muted mb-1">
                    Opening Balance
                </label>

                <input
                    id="fBalance"
                    type="number"
                    step="0.01"
                    value="0"
                    class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 outline-none">
            </div>

        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t border-navy-600">

            <button
                onclick="closeAccountModal()"
                class="px-4 py-2 rounded-lg bg-gray-600 hover:bg-gray-700">
                Cancel
            </button>

            <button
                onclick="saveAccount()"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700">
                Save Account
            </button>

        </div>

    </div>
</div>

<script>


let accounts = @json($accounts);

const ACCOUNT_TYPES = ["Asset","Liability","Equity"];

let state = {
  search: "", filterType: "All", sortField: "name", sortDir: "asc",
  page: 1, pageSize: 10, selected: new Set(),
  includeInactive: false, compact: false,
};
let editingAccountId = null;

if (editingAccountId) {
    const a = accounts.find(x => x.account_id == editingAccountId);
}
function fmtMoney(n){
  const neg = n < 0;
  const v = "₱" + Math.abs(n).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
  return neg ? `-${v}` : v;
}

function getFiltered(){
  return accounts.filter(a => {
    if (!state.includeInactive && a.inactive) return false;
    if (state.filterType !== "All" && a.type !== state.filterType) return false;
    const q = state.search.toLowerCase();
    if (q && !(a.name.toLowerCase().includes(q) || a.number.toLowerCase().includes(q))) return false;
    return true;
  });
}

function getSorted(list){
  const dir = state.sortDir === "asc" ? 1 : -1;
  return [...list].sort((a,b) => {
    let va = a[state.sortField], vb = b[state.sortField];
    if (typeof va === "string") va = va.toLowerCase();
    if (typeof vb === "string") vb = vb.toLowerCase();
    if (va < vb) return -1 * dir;
    if (va > vb) return 1 * dir;
    return 0;
  });
}

function currentPageItems(){
  const filtered = getSorted(getFiltered());
  const start = (state.page - 1) * state.pageSize;
  return filtered.slice(start, start + state.pageSize);
}
function toggleMenu(id) {
    const menu = document.getElementById(id);

    // Check if this menu is already open
    const isOpen = !menu.classList.contains("hidden");

    // Close all menus first
    closeAllMenus();

    // If it wasn't open before, open it
    if (!isOpen) {
        menu.classList.remove("hidden");
    }
}

function closeAllMenus(){
  document.querySelectorAll(".dd-menu").forEach(m => m.classList.add("hidden"));
}
document.addEventListener("click", (e) => {
  if (!e.target.closest(".dd-menu") && !e.target.closest(".dd-toggle")) closeAllMenus();
});
function populateTypeFilter(){
  const sel = document.getElementById("typeFilter");
  const current = sel.value || "All";
  sel.innerHTML = `<option value="All">All</option>` + ACCOUNT_TYPES.map(t => `<option value="${t}">${t}</option>`).join("");
  sel.value = current;
}

function populateTypeSelect(){
  document.getElementById("fType").innerHTML = ACCOUNT_TYPES.map(t => `<option value="${t}">${t}</option>`).join("");
}

function sortIndicator(field){
  if (state.sortField !== field) return `<span class="text-muted">⇅</span>`;
  return state.sortDir === "asc" ? `<span class="text-blue-400">↑</span>` : `<span class="text-blue-400">↓</span>`;
}

function renderTable(){
  const filtered = getSorted(getFiltered());
  const total = filtered.length;
  const totalPages = Math.max(1, Math.ceil(total / state.pageSize));
  if (state.page > totalPages) state.page = totalPages;
  const start = (state.page - 1) * state.pageSize;
  const pageItems = filtered.slice(start, start + state.pageSize);

  const tbody = document.getElementById("tableBody");
  tbody.innerHTML = pageItems.map(a => `
    <tr class="border-b border-navy-700/60 hover:bg-navy-700/30 transition ${a.inactive ? 'opacity-50' : ''}">
      <td class="${state.compact ? 'py-1.5' : 'py-3'} pl-4 pr-2">
        <input type="checkbox" onchange="toggleSelect('${a.number}')" ${state.selected.has(a.number) ? 'checked' : ''} class="w-4 h-4 accent-blue-500 cursor-pointer">
      </td>
      <td class="${state.compact ? 'py-1.5' : 'py-3'} pr-2 font-medium">${a.name}${a.inactive ? ' <span class=\"text-muted text-xs\">(inactive)</span>' : ''}</td>
      <td class="${state.compact ? 'py-1.5' : 'py-3'} pr-2 text-muted">${a.number || '—'}</td>
      <td class="${state.compact ? 'py-1.5' : 'py-3'} pr-2">${a.type}</td>
      <td class="${state.compact ? 'py-1.5' : 'py-3'} pr-2 text-muted">${a.detail || '—'}</td>
      <td class="${state.compact ? 'py-1.5' : 'py-3'} pr-2">${fmtMoney(a.balance)}</td>

<td class="${state.compact ? 'py-1.5' : 'py-3'} pr-2">
    ${a.date}
</td>

<td class="${state.compact ? 'py-1.5' : 'py-3'} pr-4">
        <div class="relative inline-block">
          <button onclick="event.stopPropagation(); toggleMenu('rowMenu-${a.number}')" class="dd-toggle flex items-center gap-1 text-blue-400 hover:text-blue-300 text-sm">
            Actions
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div id="rowMenu-${a.number}" class="dd-menu hidden absolute right-0 mt-1 bg-navy-800 border border-navy-600 rounded-lg shadow-lg text-sm w-40 overflow-hidden z-20">
            <button onclick="openAccountModal(${a.id})"
                    class="w-full text-left px-3 py-2 hover:bg-navy-700">
                Update Account
            </button>

            <button onclick="confirmDelete(${a.id}, '${a.name}')"
                class="w-full text-left px-3 py-2 hover:bg-navy-700 text-red-400">
                Delete Account
            </button>
          </div>
        </div>
      </td>
    </tr>`).join("");

  document.getElementById("emptyState").classList.toggle("hidden", total !== 0);


  document.getElementById("pageInfo").textContent = total === 0 ? "0 - 0" : `${start + 1} - ${Math.min(start + state.pageSize, total)} of ${total}`;
  document.getElementById("prevBtn").disabled = state.page <= 1;
  document.getElementById("nextBtn").disabled = state.page >= totalPages;

  const pageIds = pageItems.map(a => a.number);
  const allChecked = pageIds.length > 0 && pageIds.every(id => state.selected.has(id));
  document.getElementById("selectAllChk").checked = allChecked;

  ["name","number","type","detail","balance"].forEach(f => {
    const el = document.getElementById("sort-" + f);
    if (el) el.innerHTML = sortIndicator(f);
  });
}

function onSearchInput(v){ state.search = v; state.page = 1; renderTable(); }
function setFilterType(v){ state.filterType = v; state.page = 1; renderTable(); }
function setSort(field){
  if (state.sortField === field) state.sortDir = state.sortDir === "asc" ? "desc" : "asc";
  else { state.sortField = field; state.sortDir = "asc"; }
  renderTable();
}
function changePage(delta){
  const total = getFiltered().length;
  const totalPages = Math.max(1, Math.ceil(total / state.pageSize));
  state.page = Math.min(totalPages, Math.max(1, state.page + delta));
  renderTable();
}
function toggleSelect(id){
  if (state.selected.has(id)) state.selected.delete(id); else state.selected.add(id);
  renderTable();
}
function toggleSelectAllPage(checked){
  currentPageItems().forEach(a => { if (checked) state.selected.add(a.number); else state.selected.delete(a.number); });
  renderTable();
}
function toggleCompact(){ state.compact = !state.compact; renderTable(); }



function openAccountModal(id = null) {

    populateTypeSelect();

    editingAccountId = id;

    const modal = document.getElementById("accountModalWrap");
    const modalTitle = document.getElementById("accountModalTitle");

    if (id !== null) {

        const a = accounts.find(x => x.id == id);

        if (!a) {
            alert("Account not found.");
            return;
        }

        modalTitle.textContent = "Edit Account";

        document.getElementById("fName").value = a.name;
        document.getElementById("fType").value = a.type;
        document.getElementById("fDetail").value = a.detail;
        document.getElementById("fBalance").value = a.balance;

    } else {

        editingAccountId = null;

        modalTitle.textContent = "New Account";

        document.getElementById("fName").value = "";
        document.getElementById("fType").value = ACCOUNT_TYPES[0];
        document.getElementById("fDetail").value = "";
        document.getElementById("fBalance").value = 0;
    }

    closeAllMenus();

    modal.classList.remove("hidden");
    modal.classList.add("flex");
}
function closeAccountModal() {
    const modal = document.getElementById("accountModalWrap");

    modal.classList.remove("flex");
    modal.classList.add("hidden");
}
async function saveAccount() {

    const name = document.getElementById("fName").value.trim();
    const type = document.getElementById("fType").value;
    const detail = document.getElementById("fDetail").value.trim();
    const balance = parseFloat(document.getElementById("fBalance").value) || 0;

    if (!name) {
        alert("Account name is required.");
        return;
    }

    try {

        // Decide whether we're creating or updating
        let url = "{{ route('finance.accounts.store') }}";
        let method = "POST";

        if (editingAccountId !== null) {
            url = `/accounts/${editingAccountId}`;
            method = "PUT";
        }

        const response = await fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                name,
                type,
                detail,
                balance
            })
        });

        const data = await response.json();
        console.log(data);

        if (data.success) {

            if (editingAccountId === null) {

                // New account
                accounts.push({
                    id: data.account.id,
                    name: data.account.name,
                    number: data.account.number,
                    type: data.account.type,
                    detail: data.account.detail,
                    balance: parseFloat(data.account.balance),
                    date: data.account.date,
                    inactive: data.account.inactive
                });

                alert("Account created successfully!");

            } else {

                // Updated account
                alert("Account updated successfully!");

            }

            closeAccountModal();
            state.page = 1;
            renderTable();

            // Refresh to sync with database
            location.reload();

        } else {
            alert("Operation failed.");
        }

    } catch (error) {
        console.error(error);
        alert("An error occurred while saving the account.");
    }
}
function confirmDelete(id, name) {

    if (!confirm(`Are you sure you want to delete "${name}"?`)) {
        return;
    }

    deleteAccount(id);

}
async function deleteAccount(id) {

    try {

        const response = await fetch(`/accounts/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        });

        const data = await response.json();

        if (data.success) {

            accounts = accounts.filter(a => a.id !== id);

            renderTable();

            alert("Account deleted successfully!");

        } else {

            alert("Delete failed.");

        }

    } catch (error) {

        console.error(error);
        alert("An error occurred while deleting the account.");

    }

}
populateTypeFilter();
renderTable();
</script>

</body>
</html>
