<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Invoice Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { navy: {900:'#0b1e3b',800:'#132b52',700:'#17325f',600:'#1c3a6e'}, muted:'#9bb0d1' } } } }
  </script>
  <style>
    .main-wrapper {
      opacity: 0;
      animation: showPage .8s ease forwards;
    }
    @keyframes showPage {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    #printInvoice {
    display: none;
}

@media print {

    body > *:not(#printInvoice) {
        display: none !important;
    }

    #printInvoice {
        display: block !important;
        width: 100%;
        margin: 0;
        padding: 0;
        background: white;
    }

}
  </style>
</head>
<body class="bg-navy-900 text-white font-sans p-5">



<div class="main-wrapper max-w-[1400px] mx-auto space-y-5">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Total Invoices</h3>
        <div class="w-9 h-9 rounded-lg border border-blue-400/40 flex items-center justify-center text-blue-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="statTotal">
    ₱{{ number_format($currentTotal, 2) }}
</div>

      <small class="{{ $color }}">
          {{ $trend }}{{ number_format(abs($percent), 1) }}% vs last month
      </small>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Paid Invoices</h3>
        <div class="w-9 h-9 rounded-lg border border-emerald-400/40 flex items-center justify-center text-emerald-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="statPaid">
    ₱{{ number_format($currentPaid, 2) }}
</div>

<small class="{{ $paidColor }}">
    {{ $paidTrend }}{{ abs($paidPercent) }}% vs last month
</small>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Pending Payment</h3>
        <div class="w-9 h-9 rounded-lg border border-amber-400/40 flex items-center justify-center text-amber-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="statPending">
    ₱{{ number_format($currentPending, 2) }}
</div>

<small class="{{ $pendingColor }}">
    {{ $pendingTrend }}{{ abs($pendingPercent) }}% vs last month
</small>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Overdue Amount</h3>
        <div class="w-9 h-9 rounded-lg border border-red-400/40 flex items-center justify-center text-red-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="statOverdue">
    ₱{{ number_format($currentOverdue, 2) }}
</div>

<small class="{{ $overdueColor }}">
    {{ $overdueTrend }}{{ abs($overduePercent) }}% vs last month
</small>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-start">

    <div class="lg:col-span-3 bg-navy-800 rounded-xl p-5">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-semibold">Invoice List</h3>

        <div class="flex flex-wrap items-center gap-2" >
          <div class="flex items-center gap-2 bg-navy-700 rounded-lg px-3 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input id="searchInput" type="text" placeholder="Search Invoices..."
                   class="bg-transparent outline-none text-sm placeholder-muted w-36 sm:w-48"
                   oninput="currentPage = 1; renderInvoices();">
          </div>

          <select id="statusFilter" onchange="currentPage = 1; renderInvoices();"
                  class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
            <option value="All">All Status</option>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
            <option value="Overdue">Overdue</option>
            <option value="Draft">Draft</option>

          </select>

          <select id="dateRange"
                    onchange="currentPage = 1; renderInvoices();"
                    class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">

                <option value="all">All Time</option>
                <option value="week">Last Week</option>
                <option value="month">Last Month</option>
                <option value="year">Last Year</option>

            </select>
          <button onclick="exportCSV()"
                  class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition text-sm rounded-lg px-3 py-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
            Export
          </button>
          <div class="relative">

          </div>
        </div>

      </div>


      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-muted text-left border-b border-navy-600">
              <th class="py-2 pr-2">INVOICE #</th>
              <th class="py-2 pr-2">CLIENT</th>
              <th class="py-2 pr-2">ISSUE DATE</th>
              <th class="py-2 pr-2">DUE DATE</th>
              <th class="py-2 pr-2">AMOUNT</th>
              <th class="py-2 pr-2">STATUS</th>
              <th class="py-2 pr-2">ACTIONS</th>
            </tr>
          </thead>
          <tbody id="invoiceTableBody"></tbody>
        </table>
      </div>

      <div class="flex flex-wrap items-center justify-between mt-4 text-sm text-muted gap-2">
        <span id="pageInfo">Showing 0 of 0 invoices</span>
        <div class="flex items-center gap-2">
          <button onclick="changePage(-1)" class="px-2 py-1 bg-navy-700 rounded-md">‹</button>
          <span id="pageNumbers" class="flex gap-1 select-none touch-pan-y overflow-x-auto max-w-[220px]"></span>
          <button onclick="changePage(1)" class="px-2 py-1 bg-navy-700 rounded-md">›</button>
          <select id="perPage" onchange="currentPage = 1; renderInvoices();" class="bg-navy-700 rounded-md px-2 py-1 ml-2">
            <option value="10">10/page</option>
            <option value="20">20/page</option>
            <option value="50">50/page</option>
          </select>
        </div>
      </div>
    </div>

    <div class="space-y-5">

      <div class="bg-navy-800 rounded-xl p-5">
        <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>
        <div class="flex items-center gap-4">
          <svg id="donutChart" viewBox="0 0 42 42" class="w-28 h-28 -rotate-90"></svg>
          <div class="space-y-2 text-sm" id="summaryLegend"></div>
        </div>
      </div>

      <div class="bg-navy-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold">Recent Activity</h3>
          <button class="text-xs bg-navy-700 rounded-md px-2 py-1">View All</button>
        </div>
        <ul id="activityList" class="space-y-3 text-sm"></ul>
      </div>

    </div>
  </div>



</div>
<div id="invoiceModalWrap" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
  <div class="bg-navy-800 rounded-xl p-6 w-full max-w-md space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">New Invoice</h3>
      <button onclick="closeInvoiceModal()" class="text-muted hover:text-white">✕</button>
    </div>
    <div class="space-y-3 text-sm">
      <div>
        <label class="text-muted text-xs">Invoice #</label>
        <input id="invoiceNumber" type="text" placeholder="Invoice #" class="w-full mt-1 bg-navy-700 rounded-lg px-3 py-2 outline-none">
      </div>
      <div>
        <label class="text-muted text-xs">Client</label>
        <input id="clientName" type="text" placeholder="Client" class="w-full mt-1 bg-navy-700 rounded-lg px-3 py-2 outline-none">
      </div>
      <div>
        <label class="text-muted text-xs">Amount</label>
        <input id="amount" type="number" placeholder="Amount" class="w-full mt-1 bg-navy-700 rounded-lg px-3 py-2 outline-none">
      </div>
    </div>
    <div class="flex justify-end gap-2 pt-2">
      <button onclick="closeInvoiceModal()" class="border border-navy-600 hover:bg-navy-700 transition rounded-lg px-4 py-2 text-sm">Cancel</button>
      <button onclick="createInvoice()" class="bg-emerald-500 hover:bg-emerald-600 transition rounded-lg px-4 py-2 text-sm font-medium">+ Create</button>
    </div>
  </div>
</div>
<!-- =========================
     Edit Invoice Modal
========================== -->

<div id="editInvoiceModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">

    <div class="bg-navy-800 w-full max-w-xl rounded-xl shadow-2xl border border-navy-600">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-navy-600">

            <h2 class="text-xl font-semibold text-white">
                Edit Invoice
            </h2>

            <button
                type="button"
                onclick="closeEditModal()"
                class="text-gray-400 hover:text-white text-2xl leading-none">
                &times;
            </button>

        </div>

        <!-- Form -->
        <form id="editInvoiceForm" method="POST">

            @csrf
            @method('PUT')

            <input
                type="hidden"
                id="editInvoiceId"
                name="invoice_id">

            <!-- Body -->
            <div class="p-6 space-y-5">

                <!-- Client -->
                <div>

                    <label class="block text-sm text-muted mb-1">
                        Client
                    </label>

                    <input
                        id="editClient"
                        name="client"
                        type="text"
                        readonly
                        class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white cursor-not-allowed">

                </div>

                <!-- Status -->
                <div>

                    <label class="block text-sm text-muted mb-1">
                        Invoice Status
                    </label>

                    <select
                        id="editStatus"
                        name="status"
                        class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                        <option value="Draft">Draft</option>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Overdue">Overdue</option>

                    </select>

                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">

                    <div>

                        <label class="block text-sm text-muted mb-1">
                            Issue Date
                        </label>

                        <input
                            id="editIssue"
                            name="issue_date"
                            type="date"
                            class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                    </div>

                    <div>

                        <label class="block text-sm text-muted mb-1">
                            Due Date
                        </label>

                        <input
                            id="editDue"
                            name="due_date"
                            type="date"
                            class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                    </div>

                </div>

                <!-- Amount -->
                <div>

                    <label class="block text-sm text-muted mb-1">
                        Invoice Amount
                    </label>

                    <div class="relative">

                        <span class="absolute left-3 top-2.5 text-muted">
                            ₱
                        </span>

                        <input
                            id="editAmount"

                            name="invoice_amount"
                            readonly

                            type="number"
                            step="0.01"
                            class="w-full bg-navy-700 border border-navy-600 rounded-lg pl-8 pr-3 py-2 text-white">

                    </div>

                </div>
                <!-- Paid Amount -->
                <div>

                <label class="block text-sm text-muted mb-1">
                    Paid Amount
                </label>

                <div class="relative">

                <span class="absolute left-3 top-2.5 text-muted">
                    ₱
                </span>

                <input
                    id="editPaidAmount"
                    name="paid_amount"
                    type="number"
                    step="0.01"
                    class="w-full bg-navy-700 border border-navy-600 rounded-lg pl-8 pr-3 py-2 text-white">

                </div>

                </div>


                <!-- Payment Method -->
                <div>

                    <label class="block text-sm text-muted mb-1">
                        Payment Method
                    </label>

                    <input
                        id="editPaymentMethod"
                        name="payment_method"
                        type="text"
                        placeholder="Cash / GCash / Maya / Bank"
                        class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                </div>
                    <!-- Payment Details -->
                    <div>

                        <label class="block text-sm text-muted mb-1">
                            Payment Details
                        </label>

                        <input
                            id="editPaymentDetails"
                            name="payment_details"
                            type="text"
                            placeholder="Bank / GCash / Maya Account"
                            class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                    </div>


                    <!-- Reference Number -->
                    <div>

                        <label class="block text-sm text-muted mb-1">
                            Reference Number
                        </label>

                        <input
                            id="editReferenceNumber"
                            name="reference_number"
                            type="text"
                            placeholder="Transaction Reference"
                            class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                    </div>
                <!-- Payment Status -->
                <div>

                    <label class="block text-sm text-muted mb-1">
                        Payment Status
                    </label>

                    <select
                        id="editPaymentStatus"
                        name="payment_status"
                        class="w-full bg-navy-700 border border-navy-600 rounded-lg px-3 py-2 text-white">

                        <option value="Unpaid">Unpaid</option>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>

                    </select>

                </div>

            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-navy-600">

                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="px-4 py-2 rounded-lg bg-gray-600 hover:bg-gray-700 text-white">

                    Cancel

                </button>

                <button
                    type="submit"
                    class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">

                    Save Changes

                </button>

            </div>

        </form>

    </div>

</div>


<script>
  const rawInvoices = @json($invoices);

let invoices = rawInvoices.map(inv => ({
    id: inv.invoice_id,
    number: `INV-${inv.invoice_id}`,

    status: getInvoiceStatus(inv),

    issue_date: inv.issue_date,
    due_date: inv.due_date,

    invoice_amount: Number(
    inv.invoice_amount ??
    getInvoiceAmount(inv) ??
    0
),
outstanding_amount: Number(inv.outstanding_amount ?? 0),

    paid_amount: Number(inv.paid_amount),
    discount: Number(inv.discount ?? 0),
    shipping_fee: Number(inv.shipping_fee ?? 0),

    order_id: inv.order_id,

    client: inv.order?.customer_name ?? "",
    client_name: inv.order?.customer_name ?? "",
    client_address: inv.order?.address ?? "",

    product_name: inv.order?.items?.[0]?.product_name ?? inv.order?.product_name ?? "",

qty: Number(inv.order?.items?.[0]?.qty ?? inv.order?.qty ?? 0),

unit_price: Number(inv.order?.items?.[0]?.product_amount ?? inv.order?.product_amount ?? 0),

    payment_method: inv.payment_method ?? "",
    payment_status: inv.payment_status ?? "",
    reference_number: inv.reference_number ?? "",
    payment_details: inv.payment_details ?? "",

    // Order Fulfillment deliberately keeps a summary row rather than an
    // order_items table. Preserve the detail-printing UI with a single
    // summary line when that is the only data available.
    items: inv.order?.items ?? (inv.order ? [{
        product_name: inv.order.product_name,
        qty: inv.order.qty,
        product_amount: inv.order.product_amount,
    }] : []),

status: getInvoiceStatus(inv)
}));

let currentPage = 1;

const statusStyles = {
    Paid: "bg-emerald-500/90 text-white",
    Pending: "bg-amber-500/90 text-white",
    Overdue: "bg-red-500/90 text-white",
    Draft: "bg-[#4A9EE8]/90 text-white",
    Rejected: "bg-gray-500 text-white"
};

function getInvoiceStatus(inv) {

    const outstanding = Number(inv.outstanding_amount ?? 0);
    const paid = Number(inv.paid_amount ?? 0);

    const dueDate = new Date(inv.due_date);
    const today = new Date();


    // Fully paid
    if (
        inv.payment_status === "Paid" ||
        (outstanding <= 0 && paid > 0)
    ) {
        return "Paid";
    }


    // Overdue
    if (
        outstanding > 0 &&
        dueDate < today
    ) {
        return "Overdue";
    }


    // Rejected
    if (
        inv.status === "Rejected"
    ) {
        return "Rejected";
    }


    // Default
    return inv.status;
}

function getInvoiceAmount(inv) {

    if (inv.order?.items?.length) {

        return inv.order.items.reduce((total, item) => {

            return total +
                (Number(item.qty || 0) *
                 Number(item.product_amount || 0));

        },0);

    }

    return Number(inv.invoice_amount || 0);
}

function fmtPeso(n){ return "₱" + Number(n).toLocaleString(); }
function fmtDate(iso){
  const d = new Date(iso);
  return d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
}

function getFilteredInvoices() {

    const query = document.getElementById("searchInput")
        .value
        .toLowerCase();

    const status = document.getElementById("statusFilter").value;

    const range = document.getElementById("dateRange").value;

    const today = new Date();

    return invoices.filter(inv => {

        // Search Filter
        const matchesSearch =
            inv.number.toLowerCase().includes(query) ||
            inv.client.toLowerCase().includes(query);

        // Status Filter
        const matchesStatus =
            status === "All" || inv.status === status;

        // Date Filter
        let matchesDate = true;

        if (range !== "all") {

            const issueDate = new Date(inv.issue_date);

            switch (range) {

                case "week":

                    const lastWeek = new Date();
                    lastWeek.setDate(today.getDate() - 7);

                    matchesDate = issueDate >= lastWeek;
                    break;

                case "month":

                    const lastMonth = new Date();
                    lastMonth.setMonth(today.getMonth() - 1);

                    matchesDate = issueDate >= lastMonth;
                    break;

                case "year":

                    const lastYear = new Date();
                    lastYear.setFullYear(today.getFullYear() - 1);

                    matchesDate = issueDate >= lastYear;
                    break;
            }
        }

        return matchesSearch &&
               matchesStatus &&
               matchesDate;

    });

}
function renderInvoices() {
    const filtered = getFilteredInvoices();
    const perPage = parseInt(document.getElementById("perPage").value);
    const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));

    if (currentPage > totalPages) currentPage = totalPages;

    const start = (currentPage - 1) * perPage;
    const pageItems = filtered.slice(start, start + perPage);

    const tbody = document.getElementById("invoiceTableBody");
    tbody.innerHTML = "";

    pageItems.forEach(inv => {

        const row = document.createElement("tr");
        row.className =
            "border-b border-navy-700/60 hover:bg-navy-700/40 transition";

        row.innerHTML = `
            <td class="py-2 pr-2 text-blue-400 font-medium">${inv.number}</td>

            <td class="py-2 pr-2">
                ${inv.client}
            </td>

            <td class="py-2 pr-2 text-muted">
                ${fmtDate(inv.issue_date)}
            </td>

            <td class="py-2 pr-2 text-muted">
                ${fmtDate(inv.due_date)}
            </td>

            <td class="py-2 pr-2">
                ${fmtPeso(getInvoiceAmount(inv))}
            </td>

            <td class="py-2 pr-2">
                <span class="inline-flex items-center justify-center text-xs font-semibold rounded-md px-2 py-1 w-[70px] ${statusStyles[inv.status]}">
                    ${inv.status}
                </span>
            </td>

            <td class="py-2 pr-2">
                <div class="flex gap-2 text-muted">

                    <button
                        type="button"
                        onclick="printInvoice(${inv.id})"
                        title="Print"
                        class="text-blue-400 hover:text-blue-300">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2">

                            <path d="M6 9V2h12v7"/>
                            <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                            <path d="M6 14h12v8H6z"/>

                        </svg>

                    </button>

                   <button
                        title="Edit"
                        onclick="openEditModal(${inv.id})"
                        class="hover:text-white">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2">

                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                            <path d="M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/>

                        </svg>

                    </button>

                    <button
                      title="Reject Invoice"
                      onclick="rejectInvoice(${inv.id})"
                      class="hover:text-red-400">

                      <svg xmlns="http://www.w3.org/2000/svg"
                          class="w-4 h-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2">

                          <!-- Circle -->
                          <circle cx="12" cy="12" r="9"></circle>

                          <!-- Slash -->
                          <path d="M8 16L16 8"></path>

                      </svg>

                  </button>

                </div>
            </td>
        `;

        tbody.appendChild(row);
    });

    document.getElementById("pageInfo").textContent =
        `Showing ${filtered.length === 0 ? 0 : start + 1} to ${Math.min(start + perPage, filtered.length)} of ${filtered.length} invoices`;

    renderPageNumbers(totalPages);

    renderSummary();
    renderActivity();
}

function renderPageNumbers(totalPages) {
  const wrap = document.getElementById("pageNumbers");
  wrap.innerHTML = "";
  for (let p = 1; p <= totalPages; p++) {
    const btn = document.createElement("button");
    btn.textContent = p;
    btn.className = "px-2 py-1 rounded-md " + (p === currentPage ? "bg-blue-500 text-white" : "bg-navy-700");
    btn.onclick = () => { currentPage = p; renderInvoices(); };
    wrap.appendChild(btn);
  }
}

function changePage(delta) {
  const perPage = parseInt(document.getElementById("perPage").value);
  const totalPages = Math.max(1, Math.ceil(getFilteredInvoices().length / perPage));
  currentPage = Math.min(totalPages, Math.max(1, currentPage + delta));
  renderInvoices();
}
(function enableSwipePagination() {
  let touchStartX = 0;
  const el = document.getElementById("pageNumbers");
  el.addEventListener("touchstart", (e) => {
    touchStartX = e.changedTouches[0].clientX;
  }, { passive: true });
  el.addEventListener("touchend", (e) => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    const SWIPE_THRESHOLD = 40;
    if (dx <= -SWIPE_THRESHOLD) changePage(1);
    else if (dx >= SWIPE_THRESHOLD) changePage(-1);
  }, { passive: true });
})();

/* update */
function openEditModal(id) {

    const invoice = invoices.find(i => i.id == id);

    if (!invoice) return;

    document.getElementById("editInvoiceId").value = invoice.id;
    document.getElementById("editClient").value = invoice.client;
    document.getElementById("editAmount").value =getInvoiceAmount(invoice);
    document.getElementById("editPaidAmount").value = invoice.paid_amount;
    document.getElementById("editStatus").value = invoice.status;
    document.getElementById("editIssue").value =
    invoice.issue_date;

document.getElementById("editDue").value =
    invoice.due_date;

document.getElementById("editPaymentMethod").value =
    invoice.payment_method ?? "";

document.getElementById("editPaymentDetails").value =
    invoice.payment_details ?? "";

document.getElementById("editReferenceNumber").value =
    invoice.reference_number ?? "";

document.getElementById("editPaymentStatus").value =
    invoice.payment_status ?? "Unpaid";

    document.getElementById("editInvoiceForm").action = `/invoice/${invoice.id}`;
    const modal = document.getElementById("editInvoiceModal");

    modal.classList.remove("hidden");
    modal.classList.add("flex");
}
function closeEditModal() {

    const modal = document.getElementById("editInvoiceModal");

    modal.classList.remove("flex");
    modal.classList.add("hidden");
}

/* delete */
function rejectInvoice(id) {

    if (!confirm("Are you sure you want to reject this invoice?")) {
        return;
    }

    fetch(`/invoice/${id}/reject`, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    })
    .then(async response => {

        const text = await response.text();

        console.log(text);

        if (!response.ok) {
            throw new Error(text);
        }

        location.reload();

    })
    .catch(error => {
        console.error(error);
        alert(error);
    });

}

function exportCSV() {
  const rows = getFilteredInvoices();
  if (rows.length === 0) { alert("Nothing to export."); return; }

  const header = ["Invoice #","Client","Issue Date","Due Date","Amount","Status"];
  const csvRows = [header.join(",")];

  rows.forEach(inv => {
    csvRows.push([
      inv.number,
      `"${inv.client}"`,
      fmtDate(inv.issue_date),
      fmtDate(inv.due_date),
      getInvoiceAmount(inv),
      inv.status
    ].join(","));
  });

  const blob = new Blob([csvRows.join("\n")], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `invoices_${new Date().toISOString().slice(0,10)}.csv`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}



function renderSummary() {
console.log(invoices.map(i => ({
    id:i.id,
    status:i.status,
    amount:getInvoiceAmount(i)
})));


  const activeInvoices = invoices.filter(i =>
    i.status?.toLowerCase() !== "rejected"
);


const total = activeInvoices.reduce((sum, invoice) => {

    return sum +
        Number(invoice.paid_amount || 0) +
        Number(invoice.outstanding_amount || 0);

}, 0);


const paid = activeInvoices
.filter(i => i.payment_status === "Paid")
.reduce((s, i) =>
    s + Number(i.paid_amount || 0), 0
);


const pending = activeInvoices
.filter(i => i.status === "Pending")
.reduce((s, i) =>
    s + Number(i.outstanding_amount || 0), 0
);


const overdue = activeInvoices
.filter(i => i.status === "Overdue")
.reduce((s, i) =>
    s + Number(i.outstanding_amount || 0), 0
);

console.log("SUMMARY DEBUG", {
    activeInvoices,
    total,
    paid,
    pending,
    overdue
});

function percentage(value) {

    if (total <= 0) {
        return 0;
    }

    return Math.round((value / total) * 100);

}

const segments = [
    {
        label: "Paid",
        value: paid,
        color: "#2ecc71"
    },

    {
        label: "Pending",
        value: pending,
        color: "#f39c12"
    },

    {
        label: "Overdue",
        value: overdue,
        color: "#e74c3c"
    },
];

  const svg = document.getElementById("donutChart");
  svg.innerHTML = "";
  const r = 15.9155, cx = 21, cy = 21;
  let offset = 0;

  const bg = document.createElementNS("http://www.w3.org/2000/svg","circle");
  bg.setAttribute("cx", cx); bg.setAttribute("cy", cy); bg.setAttribute("r", r);
  bg.setAttribute("fill", "transparent"); bg.setAttribute("stroke", "#1c3a6e"); bg.setAttribute("stroke-width", "5");
  svg.appendChild(bg);

  segments.forEach(seg => {
    const pct = total > 0
    ? (seg.value / total) * 100
    : 0;
    const circle = document.createElementNS("http://www.w3.org/2000/svg","circle");
    circle.setAttribute("cx", cx); circle.setAttribute("cy", cy); circle.setAttribute("r", r);
    circle.setAttribute("fill", "transparent");
    circle.setAttribute("stroke", seg.color);
    circle.setAttribute("stroke-width", "5");
    circle.setAttribute("stroke-dasharray", `${pct} ${100-pct}`);
    circle.setAttribute("stroke-dashoffset", -offset);
    svg.appendChild(circle);
    offset += pct;
  });

  const legend = document.getElementById("summaryLegend");
  legend.innerHTML = segments.map(seg => `
    <div class="flex items-center gap-2">
      <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:${seg.color}"></span>
      <span class="text-muted">${seg.label}</span>
      <span class="ml-auto font-medium">${fmtPeso(seg.value)} (${percentage(seg.value)}%)</span>
    </div>`).join("");
}




document.addEventListener("DOMContentLoaded", renderInvoices);


function printInvoice(id) {

    const invoice = invoices.find(i => i.id == id);

    if (!invoice) {
        alert("Invoice not found.");
        return;
    }

    // --------------------------
    // Invoice Information
    // --------------------------

    document.getElementById("printInvoiceNo").textContent =
        invoice.number;

    document.getElementById("printIssueDate").textContent =
        fmtDate(invoice.issue_date);

    document.getElementById("printDueDate").textContent =
        fmtDate(invoice.due_date);

    // --------------------------
    // Client Information
    // --------------------------

    document.getElementById("printBillTo").innerHTML = `
${invoice.client_name}<br>
${invoice.client_address}
`;

document.getElementById("printShipTo").innerHTML = `
${invoice.client_name}<br>
${invoice.client_address}
`;

    // --------------------------
    // Invoice Items
    // --------------------------

 let html = "";

invoice.items.forEach((item,index)=>{

    html += `
    <tr>
        <td>${index + 1}</td>

        <td>${item.product_name}</td>

        <td>${item.qty}</td>

        <td>
            ${fmtPeso(item.product_amount)}
        </td>

        <td>
            ${fmtPeso(
                item.qty * item.product_amount
            )}
        </td>

    </tr>
    `;

});


document.getElementById("printItems").innerHTML = html;


    // --------------------------
// Totals
// ---------------------------

const subtotal = invoice.items.reduce((sum, item) => {
    return sum + (item.qty * item.product_amount);
}, 0);


const discount = Number(invoice.discount || 0);

const shipping = Number(invoice.shipping_fee || 0);


// VAT 12%
const vat = subtotal * 0.12;


const grandTotal = subtotal + vat - discount + shipping;


const paid = Number(invoice.paid_amount || 0);

const balance = grandTotal - paid;



document.getElementById("printSubtotal").textContent =
    fmtPeso(subtotal);


document.getElementById("printVAT").textContent =
    fmtPeso(vat);


document.getElementById("printGrandTotal").textContent =
    fmtPeso(grandTotal);


document.getElementById("printBalance").textContent =
    fmtPeso(balance);


document.getElementById("printDiscount").textContent =
    fmtPeso(discount);


document.getElementById("printShipping").textContent =
    fmtPeso(shipping);


document.getElementById("printPaid").textContent =
    fmtPeso(paid);

    // --------------------------
    // Payment Information
    // --------------------------

    document.getElementById("printPaymentMethod").textContent =
        invoice.payment_method || "-";

    document.getElementById("printReference").textContent =
        invoice.reference_number || "-";

    document.getElementById("printPaymentDetails").textContent =
        invoice.payment_details || "-";

    document.getElementById("printPaymentStatus").textContent =
        invoice.payment_status || "-";

    // --------------------------
    // Print
    // --------------------------

    window.print();
}
</script>
<div id="printInvoice" style="display:none;">
    <!-- Invoice HTML will go here -->

   <style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}
@page{
    size:A4;
    margin:12mm;
}

body{
    background:#f5f5f5;
    padding:30px;
}

.invoice{
    width:100%;
    max-width:900px;
    margin:auto;
    background:#fff;
    padding:20px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
}

.company h1{
    font-size:48px;
    font-weight:bold;
    color:#000;
}

.company h2{
    font-size:22px;
    margin-top:5px;
    color:#000;
}

.company p{
    color:#000;
    margin-top:4px;
}

.logo img{
    width:90px;
}

.info{
    margin-top:40px;
    display:flex;
    justify-content:space-between;
    color:#000;
}

.info .box{
    width:30%;
}

.info h4{
    margin-bottom:8px;
}

.invoice-info table{
    width:100%;
}

.invoice-info td{
    padding:5px 0;
    color:#000;
}

.items{
    width:100%;
    border-collapse:collapse;
    margin-top:40px;
}

.items thead{
    background:#4c9df0;
    color:white;
}

.items th{
    padding:12px;
    text-align:center;
}

.items td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
    color:#000;
}

.summary{
    width:250px;
    margin-top:40px;
    margin-left:auto;
    color:#000;
}

.summary table{
    width:100%;
    border-collapse:collapse;
}

.summary td{
    border:1px solid #999;
    padding:10px;
}

.footer{
    margin-top:80px;
    color:#000;
}

.footer h4{
    margin-bottom:10px;
}

.line{
    margin-top:60px;
    border-top:2px solid #999;
}
</style>
<div class="invoice">

    <div class="header">

        <div class="company">

            <h1>INVOICE</h1>

            <h2>Nexora Company</h2>

            <p>
                Cavite State University<br>
                Don Severino delas Alas Campus
            </p>

        </div>

        <div class="logo">
            <img src="{{asset('images/Nexora_Logo_Transparent.png')}}" alt="logo">
        </div>

    </div>


    <div class="info">

        <div class="box">

            <h4>BILL TO</h4>

            <p id="printBillTo">

</p>

        </div>

        <div class="box">

            <h4>SHIP TO</h4>

            <p id="printShipTo">

</p>

        </div>

        <div class="box invoice-info">

            <table>

                <tr>
                    <td><strong>Invoice No</strong></td>
                    <td id="printInvoiceNo"></td>
                </tr>

                <tr>
                    <td><strong>Invoice Date</strong></td>
                   <td id="printIssueDate"></td>
                </tr>

                <tr>
                    <td><strong>Due Date</strong></td>
                    <td id="printDueDate"></td>
                </tr>

            </table>

        </div>

    </div>


    <table class="items">

        <thead>

        <tr>

            <th>#</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>

        </tr>

        </thead>

        <tbody id="printItems">

</tbody>

    </table>


    <div class="summary">

        <table>

<tr>
<td>Subtotal</td>
<td id="printSubtotal"></td>
</tr>

<tr>
<td>Discount</td>
<td id="printDiscount"></td>
</tr>

<tr>
<td>Shipping Fee</td>
<td id="printShipping"></td>
</tr>

<tr>
<td>VAT (12%)</td>
<td id="printVAT"></td>
</tr>

<tr>
<td><strong>Grand Total</strong></td>
<td id="printGrandTotal"></td>
</tr>

<tr>
<td>Amount Paid</td>
<td id="printPaid"></td>
</tr>

<tr>
<td><strong>Balance Due</strong></td>
<td id="printBalance"></td>
</tr>

</table>

    </div>

    <div class="footer">

        <h4>PAYMENT INFORMATION</h4>

<p><strong>Payment Method:</strong> <span id="printPaymentMethod"></span></p>

<p><strong>Reference No:</strong><span id="printReference"></span></p>

<p><strong>Bank / GCash / Maya:</strong> <span id="printPaymentDetails"></span></p>

<p><strong>Payment Status:</strong><span id="printPaymentStatus"></span></p>

    </div>

    <div style="display:flex;justify-content:space-between;margin-top:80px;">

<div style="width:40%;text-align:center;">

<div style="border-top:1px solid #000;padding-top:8px;">
Authorized Signature
</div>

</div>

<div style="width:40%;text-align:center;">

<div style="border-top:1px solid #000;padding-top:8px;">
Client Signature
</div>

</div>

</div>

</div>

</div>



</div>

</body>
</html>
