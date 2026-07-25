<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { navy: {900:'#0b1e3b',800:'#132b52',700:'#17325f',600:'#1c3a6e'}, muted:'#9bb0d1' } } } }
</script>
<head>
<meta charset="UTF-8">
<title>Dashboard main</title>
<style>
.main-wrapper { opacity: 0; animation: showPage .8s ease forwards; }
@keyframes showPage { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.dd-menu { animation: ddIn .12s ease forwards; }
@keyframes ddIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body class="bg-navy-900 text-white font-sans min-h-screen">

<div class="main-wrapper max-w-[1400px] mx-auto p-6 space-y-5">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold tracking-wide">CASH FLOW</h3>
        <div class="relative">
          <button onclick="event.stopPropagation(); toggleMenu('dd-cashflow')" class="dd-toggle flex items-center gap-1 text-[11px] bg-navy-800 border border-navy-600 rounded-lg px-2 py-1 hover:bg-navy-700 transition">
            <span id="cashFlowPeriodLabel">This month</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div id="dd-cashflow" class="dd-menu hidden absolute right-0 mt-1 bg-navy-700 border border-navy-600 rounded-lg shadow-lg text-xs w-32 overflow-hidden z-20">
            <button onclick="selectCashFlowPeriod('Today')" class="w-full text-left px-3 py-2 hover:bg-navy-600">Today</button>
            <button onclick="selectCashFlowPeriod('This week')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This week</button>
            <button onclick="selectCashFlowPeriod('This month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This month</button>
          </div>
        </div>
      </div>
      <div class="text-2xl font-bold mt-2" id="cashFlowBalance">₱0</div>
      <p class="text-muted text-xs mb-2">Current cash balance</p>
      <div class="flex gap-2 flex-1">
        <div id="cashFlowYAxis" class="flex flex-col justify-between text-[10px] text-muted py-1"></div>
        <div class="flex-1">
          <svg id="cashFlowChart" viewBox="0 0 260 130" class="w-full h-28"></svg>
        </div>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold tracking-wide">EXPENSES</h3>
        <div class="relative">
          <button onclick="event.stopPropagation(); toggleMenu('dd-expenses')" class="dd-toggle flex items-center gap-1 text-[11px] bg-navy-800 border border-navy-600 rounded-lg px-2 py-1 hover:bg-navy-700 transition">
            <span id="expensesPeriodLabel">Last month</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div id="dd-expenses" class="dd-menu hidden absolute right-0 mt-1 bg-navy-700 border border-navy-600 rounded-lg shadow-lg text-xs w-32 overflow-hidden z-20">
            <button onclick="selectExpensesPeriod('This month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This month</button>
            <button onclick="selectExpensesPeriod('Last month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">Last month</button>
            <button onclick="selectExpensesPeriod('This year')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This year</button>
          </div>
        </div>
      </div>
      <div class="text-2xl font-bold mt-2" id="expensesTotal">₱0</div>
      <p class="text-muted text-xs mb-3">Business spending</p>
      <div class="flex items-center gap-4 flex-1">
        <svg id="expensesDonut" viewBox="0 0 42 42" class="w-20 h-20 -rotate-90 shrink-0"></svg>
        <div class="space-y-1.5 text-xs" id="expensesLegend"></div>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <h3 class="text-sm font-semibold tracking-wide">PROFIT AND LOSS</h3>
      <div class="text-2xl font-bold mt-2" id="netIncome">₱0</div>
      <p class="text-muted text-xs mb-4">Net income</p>
      <div class="space-y-4 flex-1">
        <div>
          <div class="flex items-center justify-between text-xs mb-1">
            <span>Income <span class="text-muted" id="incomeValue">₱0</span></span>
            <span class="text-muted" id="incomePct">0%</span>
          </div>
          <div class="h-1.5 bg-navy-700 rounded-full overflow-hidden">
            <div class="h-full bg-blue-400 rounded-full" id="incomeBar" style="width:0%"></div>
          </div>
        </div>
        <div>
          <div class="flex items-center justify-between text-xs mb-1">
            <span>Expenses <span class="text-muted" id="plExpensesValue">₱0</span></span>
            <span class="text-muted" id="plExpensesPct">0%</span>
          </div>
          <div class="h-1.5 bg-navy-700 rounded-full overflow-hidden">
            <div class="h-full bg-blue-400 rounded-full" id="plExpensesBar" style="width:0%"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold tracking-wide">INVOICES</h3>
        <span class="text-muted text-xs" id="invoicesUnpaid">₱0 unpaid</span>
      </div>
      <div class="mt-3">
        <div class="text-2xl font-bold" id="invoicesOverdueValue">₱0</div>
        <p class="text-red-400 text-xs mb-1">Overdue</p>
        <div class="h-1 bg-navy-700 rounded-full overflow-hidden">
          <div class="h-full bg-red-500 rounded-full" id="invoicesOverdueBar" style="width:0%"></div>
        </div>
      </div>
      <div class="mt-5">
        <p class="text-muted text-xs" id="invoicesPaid">₱0 paid</p>
        <div class="text-2xl font-bold" id="invoicesDepositedValue">₱0</div>
        <p class="text-blue-400 text-xs mb-1">Deposited</p>
        <div class="h-1 bg-navy-700 rounded-full overflow-hidden">
          <div class="h-full bg-blue-500 rounded-full" id="invoicesDepositedBar" style="width:0%"></div>
        </div>
      </div>
    </div>

  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <div class="min-h-[76px]">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">Monthly Revenue</h3>
          <div class="relative">
            <button onclick="event.stopPropagation(); toggleMenu('dd-revenue')" class="dd-toggle flex items-center gap-1 text-[11px] bg-navy-800 border border-navy-600 rounded-lg px-2 py-1 hover:bg-navy-700 transition">
              <span id="revenuePeriodLabel">This week</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div id="dd-revenue" class="dd-menu hidden absolute right-0 mt-1 bg-navy-700 border border-navy-600 rounded-lg shadow-lg text-xs w-32 overflow-hidden z-20">
              <button onclick="selectRevenuePeriod('Today')" class="w-full text-left px-3 py-2 hover:bg-navy-600">Today</button>
              <button onclick="selectRevenuePeriod('This week')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This week</button>
              <button onclick="selectRevenuePeriod('This month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This month</button>
              <button onclick="selectRevenuePeriod('This year')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This year</button>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2 mt-2">
          <span class="text-3xl font-bold" id="revenueTotal">₱0</span>
          <span class="text-xs font-semibold rounded-full px-2 py-0.5 bg-emerald-500/20 text-emerald-400" id="revenueChangeBadge">₱‘ 0%</span>
        </div>
        <p class="text-muted text-xs mb-3" id="revenueChangeSub"></p>
      </div>

      <div class="flex gap-2">
        <div id="revenueYAxis" class="flex flex-col justify-between text-[10px] text-muted py-1"></div>
        <div class="flex-1">
          <svg id="revenueChart" viewBox="0 0 500 180" class="w-full h-28"></svg>
        </div>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <div class="min-h-[76px]">
        <div class="flex items-center justify-between mb-1">
          <h3 class="text-lg font-semibold">Monthly Invoice Trend</h3>
          <div class="relative">
            <button onclick="event.stopPropagation(); toggleMenu('dd-invoiceTrend')" class="dd-toggle flex items-center gap-1 text-[11px] bg-navy-800 border border-navy-600 rounded-lg px-2 py-1 hover:bg-navy-700 transition">
              <span id="invoiceTrendPeriodLabel">This month</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div id="dd-invoiceTrend" class="dd-menu hidden absolute right-0 mt-1 bg-navy-700 border border-navy-600 rounded-lg shadow-lg text-xs w-28 overflow-hidden z-20">
              <button onclick="selectInvoiceTrendPeriod('This month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This month</button>
              <button onclick="selectInvoiceTrendPeriod('Last month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">Last month</button>
              <button onclick="selectInvoiceTrendPeriod('This week')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This week</button>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3 text-[10px] text-muted mb-2">
          <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span> Invoice</span>
          <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-200 inline-block"></span> Paid</span>
        </div>
      </div>

      <div class="flex gap-2">
        <div id="invoiceTrendYAxis" class="flex flex-col justify-between text-[9px] text-muted py-1"></div>
        <div class="flex-1">
          <svg id="invoiceTrendChart" viewBox="0 0 300 180" class="w-full h-28"></svg>
        </div>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5">
      <h3 class="text-lg font-semibold mb-4">Activities</h3>
      <div class="space-y-3 text-sm" id="activitiesCounters"></div>
    </div>

  </div>

  <div class="bg-navy-800 rounded-xl p-5">
    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
    <div class="overflow-x-auto min-h-[220px]">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-muted text-left border-b border-navy-600">
            <th class="py-2 pr-4">Date</th>
            <th class="py-2 pr-4">Description</th>
            <th class="py-2 pr-4">Category</th>
            <th class="py-2 pr-4">Amount</th>
            <th class="py-2 pr-4">Status</th>
          </tr>
        </thead>
        <tbody id="activityBody"></tbody>
      </table>
    </div>
  </div>

</div>

<script>

function toggleMenu(id){
  const el = document.getElementById(id);
  const wasHidden = el.classList.contains("hidden");
  closeAllMenus();
  if (wasHidden) el.classList.remove("hidden");
}
function closeAllMenus(){
  document.querySelectorAll(".dd-menu").forEach(m => m.classList.add("hidden"));
}
document.addEventListener("click", (e) => {
  if (!e.target.closest(".dd-menu") && !e.target.closest(".dd-toggle")) closeAllMenus();
});

function fmtPeso(n){ return "₱" + Number(n || 0).toLocaleString(); }

function getPeriodLabels(period){
  switch(period){
    case "Today":      return ["12am","4am","8am","12pm","4pm","8pm"];
    case "This week":  return ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
    case "This month": return ["Week 1","Week 2","Week 3","Week 4"];
    case "This year":  return ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    default:           return ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
  }
}

let cashFlowData = { balance: 0, dates: [], seriesA: [], seriesB: [] };

function setCashFlowData(balance, dates, seriesA, seriesB){
  cashFlowData = { balance, dates, seriesA, seriesB };
  renderCashFlow();
}

function selectCashFlowPeriod(period){
  document.getElementById("cashFlowPeriodLabel").textContent = period;
  const dates = getPeriodLabels(period);
  cashFlowData = { balance: cashFlowData.balance, dates, seriesA: Array(dates.length).fill(0), seriesB: Array(dates.length).fill(0) };
  closeAllMenus();
  renderCashFlowChart();
}

function renderCashFlow(){
  document.getElementById("cashFlowBalance").textContent = fmtPeso(cashFlowData.balance);
  renderCashFlowChart();
}

function renderCashFlowChart(){
  const svg = document.getElementById("cashFlowChart");
  const yAxisEl = document.getElementById("cashFlowYAxis");
  const { dates, seriesA, seriesB } = cashFlowData;
  const hasData = dates.length > 0;

  const rect = svg.getBoundingClientRect();
  const w = Math.round(Math.max(rect.width, 200));
  const h = Math.round(Math.max(rect.height, 90));
  svg.setAttribute("viewBox", `0 0 ${w} ${h}`);
  svg.innerHTML = "";

  const padX = 6, padTop = 6, padBottom = 20;
  const plotH = h - padTop - padBottom;
  const maxVal = hasData ? Math.max(...seriesA, ...seriesB, 1) * 1.15 : 4;

  const steps = 4;
  yAxisEl.innerHTML = Array.from({ length: steps + 1 }).map((_, i) => {
    const val = hasData ? Math.round(maxVal * (1 - i / steps)) : 0;
    return `<span>₱${(val / 1000).toFixed(0)}k</span>`;
  }).join("");

  const base = document.createElementNS("http://www.w3.org/2000/svg","line");
  base.setAttribute("x1", padX); base.setAttribute("x2", w - padX);
  base.setAttribute("y1", h - padBottom); base.setAttribute("y2", h - padBottom);
  base.setAttribute("stroke", "#1c3a6e"); base.setAttribute("stroke-width", "1");
  svg.appendChild(base);

  if (!hasData) return;

  const yFor = (v) => padTop + (1 - v / maxVal) * plotH;
  const groupW = (w - padX * 2) / dates.length;
  const barW = groupW * 0.28;

  dates.forEach((d, i) => {
    const groupX = padX + i * groupW + groupW / 2;

    const barA = document.createElementNS("http://www.w3.org/2000/svg","rect");
    barA.setAttribute("x", groupX - barW - 2);
    barA.setAttribute("y", yFor(seriesA[i] || 0));
    barA.setAttribute("width", barW);
    barA.setAttribute("height", h - padBottom - yFor(seriesA[i] || 0));
    barA.setAttribute("rx", "2");
    barA.setAttribute("fill", "#4ca6ff");
    svg.appendChild(barA);

    const barB = document.createElementNS("http://www.w3.org/2000/svg","rect");
    barB.setAttribute("x", groupX + 2);
    barB.setAttribute("y", yFor(seriesB[i] || 0));
    barB.setAttribute("width", barW);
    barB.setAttribute("height", h - padBottom - yFor(seriesB[i] || 0));
    barB.setAttribute("rx", "2");
    barB.setAttribute("fill", "#7ec8ff");
    svg.appendChild(barB);

    const label = document.createElementNS("http://www.w3.org/2000/svg","text");
    label.setAttribute("x", groupX); label.setAttribute("y", h - 4);
    label.setAttribute("fill", "#9bb0d1"); label.setAttribute("font-size", "10");
    label.setAttribute("text-anchor", "middle");
    label.textContent = d;
    svg.appendChild(label);
  });
}

let expensesData = { total: 0, breakdown: [] };

function setExpensesData(total, breakdown){
  expensesData = { total, breakdown };
  renderExpenses();
}

function selectExpensesPeriod(period){
  document.getElementById("expensesPeriodLabel").textContent = period;
  expensesData = { total: 0, breakdown: [] };
  closeAllMenus();
  renderExpenses();
}

function renderExpenses(){
  document.getElementById("expensesTotal").textContent = fmtPeso(expensesData.total);

  const svg = document.getElementById("expensesDonut");
  svg.innerHTML = "";
  const r = 15.9155, cx = 21, cy = 21;
  const bg = document.createElementNS("http://www.w3.org/2000/svg","circle");
  bg.setAttribute("cx", cx); bg.setAttribute("cy", cy); bg.setAttribute("r", r);
  bg.setAttribute("fill", "transparent"); bg.setAttribute("stroke", "#1c3a6e"); bg.setAttribute("stroke-width", "6");
  svg.appendChild(bg);

  const breakdown = expensesData.breakdown;
  const total = breakdown.reduce((s, seg) => s + seg.value, 0) || 1;
  let offset = 0;
  breakdown.forEach(seg => {
    const pct = (seg.value / total) * 100;
    const circle = document.createElementNS("http://www.w3.org/2000/svg","circle");
    circle.setAttribute("cx", cx); circle.setAttribute("cy", cy); circle.setAttribute("r", r);
    circle.setAttribute("fill", "transparent");
    circle.setAttribute("stroke", seg.color);
    circle.setAttribute("stroke-width", "6");
    circle.setAttribute("stroke-dasharray", `${pct} ${100 - pct}`);
    circle.setAttribute("stroke-dashoffset", -offset);
    svg.appendChild(circle);
    offset += pct;
  });

  document.getElementById("expensesLegend").innerHTML = breakdown.map(seg => `
    <div class="flex items-center gap-2">
      <span class="w-2 h-2 rounded-full inline-block" style="background:${seg.color}"></span>
      <span class="font-medium">${fmtPeso(seg.value)}</span>
      <span class="text-muted">${seg.label}</span>
    </div>`).join("");
}

let profitLossData = { netIncome: 0, income: { value: 0, pct: 0 }, expenses: { value: 0, pct: 0 } };

function setProfitLossData(netIncome, incomeValue, incomePct, expensesValue, expensesPct){
  profitLossData = { netIncome, income: { value: incomeValue, pct: incomePct }, expenses: { value: expensesValue, pct: expensesPct } };
  renderProfitLoss();
}

function renderProfitLoss(){
  const { netIncome, income, expenses } = profitLossData;
  document.getElementById("netIncome").textContent = fmtPeso(netIncome);
  document.getElementById("incomeValue").textContent = fmtPeso(income.value);
  document.getElementById("incomePct").textContent = income.pct + "%";
  document.getElementById("incomeBar").style.width = income.pct + "%";
  document.getElementById("plExpensesValue").textContent = fmtPeso(expenses.value);
  document.getElementById("plExpensesPct").textContent = expenses.pct + "%";
  document.getElementById("plExpensesBar").style.width = expenses.pct + "%";
}

let invoicesData = { unpaid: 0, overdue: 0, overduePct: 0, paid: 0, deposited: 0, depositedPct: 0 };

function setInvoicesData(unpaid, overdue, overduePct, paid, deposited, depositedPct){
  invoicesData = { unpaid, overdue, overduePct, paid, deposited, depositedPct };
  renderInvoicesCard();
}

function renderInvoicesCard(){
  const d = invoicesData;
  document.getElementById("invoicesUnpaid").textContent = fmtPeso(d.unpaid) + " unpaid";
  document.getElementById("invoicesOverdueValue").textContent = fmtPeso(d.overdue);
  document.getElementById("invoicesOverdueBar").style.width = d.overduePct + "%";
  document.getElementById("invoicesPaid").textContent = fmtPeso(d.paid) + " paid";
  document.getElementById("invoicesDepositedValue").textContent = fmtPeso(d.deposited);
  document.getElementById("invoicesDepositedBar").style.width = d.depositedPct + "%";
}

let revenueData = {
  total: 0,
  changePct: 0,
  changeSub: "",
  months: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
  values: Array(7).fill(0)
};

function setRevenueData(total, changePct, changeSub, months, values){
  revenueData = { total, changePct, changeSub, months, values };
  renderMonthlyRevenue();
}

function selectRevenuePeriod(period){
  document.getElementById("revenuePeriodLabel").textContent = period;
  const months = getPeriodLabels(period);
  revenueData = { total: 0, changePct: 0, changeSub: "", months, values: Array(months.length).fill(0) };
  closeAllMenus();
  renderMonthlyRevenue();
}

function renderMonthlyRevenue(){
  const d = revenueData;
  document.getElementById("revenueTotal").textContent = fmtPeso(d.total);
  const badge = document.getElementById("revenueChangeBadge");
  const up = d.changePct >= 0;
  badge.textContent = `${up ? "₱" : "₱"} ${Math.abs(d.changePct)}%`;
  badge.className = `text-xs font-semibold rounded-full px-2 py-0.5 ${up ? "bg-emerald-500/20 text-emerald-400" : "bg-red-500/20 text-red-400"}`;
  document.getElementById("revenueChangeSub").textContent = d.changeSub;
  renderRevenueChart();
}

function renderRevenueChart(){
  const svg = document.getElementById("revenueChart");
  const yAxisEl = document.getElementById("revenueYAxis");
  const { months, values } = revenueData;
  const hasData = values.some(v => v > 0);

  const rect = svg.getBoundingClientRect();
  const w = Math.round(Math.max(rect.width, 200));
  const h = Math.round(Math.max(rect.height, 120));
  svg.setAttribute("viewBox", `0 0 ${w} ${h}`);
  svg.innerHTML = "";

  const padX = 4, padTop = 10, padBottom = 22;
  const maxVal = hasData ? Math.max(...values) * 1.1 : 4;

  const steps = 3;
  yAxisEl.innerHTML = Array.from({ length: steps + 1 }).map((_, i) => {
    const val = hasData ? Math.round(maxVal * (1 - i / steps)) : 0;
    return `<span>₱${(val / 1000).toFixed(0)}k</span>`;
  }).join("");

  const xFor = (i) => padX + (i * (w - padX * 2) / (months.length - 1));
  const yFor = (v) => padTop + (1 - v / maxVal) * (h - padTop - padBottom);

  const base = document.createElementNS("http://www.w3.org/2000/svg","line");
  base.setAttribute("x1", padX); base.setAttribute("x2", w - padX);
  base.setAttribute("y1", h - padBottom); base.setAttribute("y2", h - padBottom);
  base.setAttribute("stroke", "#1c3a6e"); base.setAttribute("stroke-width", "1");
  svg.appendChild(base);

  const color = hasData ? "#4ca6ff" : "#ffffff80";
  const points = values.map((v,i) => `${xFor(i)},${yFor(v)}`).join(" ");
  const poly = document.createElementNS("http://www.w3.org/2000/svg","polyline");
  poly.setAttribute("points", points);
  poly.setAttribute("fill", "none");
  poly.setAttribute("stroke", color);
  poly.setAttribute("stroke-width", "2.5");
  poly.setAttribute("stroke-linecap", "round");
  poly.setAttribute("stroke-linejoin", "round");
  svg.appendChild(poly);

  values.forEach((v,i) => {
    const dot = document.createElementNS("http://www.w3.org/2000/svg","circle");
    dot.setAttribute("cx", xFor(i)); dot.setAttribute("cy", yFor(v)); dot.setAttribute("r", "3.5");
    dot.setAttribute("fill", color);
    svg.appendChild(dot);
  });

  months.forEach((m,i) => {
    const anchor = i === 0 ? "start" : (i === months.length - 1 ? "end" : "middle");
    const label = document.createElementNS("http://www.w3.org/2000/svg","text");
    label.setAttribute("x", xFor(i)); label.setAttribute("y", h - 6);
    label.setAttribute("fill", "#9bb0d1"); label.setAttribute("font-size", "10");
    label.setAttribute("text-anchor", anchor);
    label.textContent = m;
    svg.appendChild(label);
  });
}

let invoiceTrendPeriod = "This month";

function getInvoiceTrendLabels(period){
  if (period === "This week") return ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
  return ["Week 1","Week 2","Week 3","Week 4"];
}

let invoiceTrendData = (() => {
  const months = getInvoiceTrendLabels(invoiceTrendPeriod);
  return { months, invoiceAmt: Array(months.length).fill(0), paidAmt: Array(months.length).fill(0) };
})();

function selectInvoiceTrendPeriod(period){
  invoiceTrendPeriod = period;
  document.getElementById("invoiceTrendPeriodLabel").textContent = period;
  const months = getInvoiceTrendLabels(period);
  invoiceTrendData = { months, invoiceAmt: Array(months.length).fill(0), paidAmt: Array(months.length).fill(0) };
  closeAllMenus();
  renderInvoiceTrendChart();
}

function setInvoiceTrendData(months, invoiceAmt, paidAmt){
  invoiceTrendData = { months, invoiceAmt, paidAmt };
  renderInvoiceTrendChart();
}

function renderInvoiceTrendChart(){
  const svg = document.getElementById("invoiceTrendChart");
  const yAxisEl = document.getElementById("invoiceTrendYAxis");
  const { months, invoiceAmt, paidAmt } = invoiceTrendData;
  const hasData = invoiceAmt.some(v => v > 0) || paidAmt.some(v => v > 0);

  const rect = svg.getBoundingClientRect();
  const w = Math.round(Math.max(rect.width, 200));
  const h = Math.round(Math.max(rect.height, 120));
  svg.setAttribute("viewBox", `0 0 ${w} ${h}`);
  svg.innerHTML = "";

  const padX = 4, padTop = 8, padBottom = 18;
  const maxVal = hasData ? Math.max(...invoiceAmt, ...paidAmt) * 1.1 : 4;

  const xFor = (i) => padX + (i * (w - padX * 2) / (months.length - 1));
  const yFor = (v) => padTop + (1 - v / maxVal) * (h - padTop - padBottom);

  const steps = 3;
  yAxisEl.innerHTML = Array.from({ length: steps + 1 }).map((_, i) => {
    const val = hasData ? Math.round(maxVal * (1 - i / steps)) : 0;
    return `<span>₱${(val / 1000).toFixed(0)}k</span>`;
  }).join("");

  const base = document.createElementNS("http://www.w3.org/2000/svg","line");
  base.setAttribute("x1", padX); base.setAttribute("x2", w - padX);
  base.setAttribute("y1", h - padBottom); base.setAttribute("y2", h - padBottom);
  base.setAttribute("stroke", "#1c3a6e"); base.setAttribute("stroke-width", "1");
  svg.appendChild(base);

  const makeLine = (values, color) => {
    const points = values.map((v,i) => `${xFor(i)},${yFor(v)}`).join(" ");
    const poly = document.createElementNS("http://www.w3.org/2000/svg","polyline");
    poly.setAttribute("points", points);
    poly.setAttribute("fill", "none");
    poly.setAttribute("stroke", color);
    poly.setAttribute("stroke-width", "2");
    poly.setAttribute("stroke-linecap", "round");
    poly.setAttribute("stroke-linejoin", "round");
    svg.appendChild(poly);

    values.forEach((v,i) => {
      const dot = document.createElementNS("http://www.w3.org/2000/svg","circle");
      dot.setAttribute("cx", xFor(i)); dot.setAttribute("cy", yFor(v)); dot.setAttribute("r", "2.5");
      dot.setAttribute("fill", color);
      svg.appendChild(dot);
    });
  };

  if (hasData) {
    makeLine(invoiceAmt, "#4ca6ff");
    makeLine(paidAmt, "#a5d8ff");
  } else {
    makeLine(invoiceAmt, "#ffffff80");
    makeLine(paidAmt, "#ffffff40");
  }

  months.forEach((m,i) => {
    const anchor = i === 0 ? "start" : (i === months.length - 1 ? "end" : "middle");
    const label = document.createElementNS("http://www.w3.org/2000/svg","text");
    label.setAttribute("x", xFor(i)); label.setAttribute("y", h - 4);
    label.setAttribute("fill", "#9bb0d1"); label.setAttribute("font-size", "9");
    label.setAttribute("text-anchor", anchor);
    label.textContent = m;
    svg.appendChild(label);
  });
}

let activitiesCountData = [
  { label: "Assets", count: 0 },
  { label: "Liabilities", count: 0 },
  { label: "Equity", count: 0 },
];

function setActivitiesCountData(data){
  activitiesCountData = data;
  renderActivitiesCounters();
}

function renderActivitiesCounters(){
  document.getElementById("activitiesCounters").innerHTML = activitiesCountData.map(item => `
    <div class="flex items-center justify-between bg-navy-700/40 rounded-lg px-4 py-3">
      <span class="text-muted">${item.label}</span>
      <span class="text-xl font-bold">${item.count}</span>
    </div>`).join("");
}

const statusStyles = {
  Success: "bg-emerald-500/90 text-white",
  Pending: "bg-amber-500/90 text-white",
};

let activityData = [];

function setActivityData(data){
  activityData = data;
  renderActivity();
}


function renderActivity(){
  const tbody = document.getElementById("activityBody");

  if (!activityData || activityData.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="py-16 text-center text-muted">No recent activity yet</td>
      </tr>`;
    return;
  }

  tbody.innerHTML = activityData.map(a => `
    <tr class="border-b border-navy-700/60">
      <td class="py-2 pr-4 text-muted">${a.date}</td>
      <td class="py-2 pr-4">${a.desc}</td>
      <td class="py-2 pr-4 text-muted">${a.category}</td>
      <td class="py-2 pr-4">${fmtPeso(a.amount)}</td>
      <td class="py-2 pr-4"><span class="inline-flex items-center justify-center text-xs font-semibold rounded-md px-2 py-1 ${statusStyles[a.status] || ''}">${a.status}</span></td>
    </tr>`).join("");
}

function renderAllCharts(){
  renderCashFlowChart();
  renderRevenueChart();
  renderInvoiceTrendChart();
}

const financeDashboard = @json($financeDashboard ?? []);

requestAnimationFrame(() => {
  const paid = Number(financeDashboard.paid || 0);
  const unpaid = Number(financeDashboard.unpaid || 0);
  const overdue = Number(financeDashboard.overdue || 0);
  const invoiceTotal = Number(financeDashboard.invoice_total || 0);
  const overduePct = invoiceTotal ? Math.round((overdue / invoiceTotal) * 100) : 0;
  const paidPct = invoiceTotal ? Math.round((paid / invoiceTotal) * 100) : 0;

  setCashFlowData(
    paid,
    financeDashboard.week_labels || [],
    financeDashboard.invoice_values || [],
    financeDashboard.paid_values || []
  );
  setProfitLossData(paid - unpaid, paid, 100, 0, 0);
  setInvoicesData(unpaid, overdue, overduePct, paid, paid, paidPct);
  setRevenueData(paid, 0, 'Paid invoice revenue', financeDashboard.week_labels || [], financeDashboard.paid_values || []);
  setInvoiceTrendData(financeDashboard.week_labels || [], financeDashboard.invoice_values || [], financeDashboard.paid_values || []);
  setActivityData(financeDashboard.recent_activity || []);
  renderCashFlow();
  renderExpenses();
  renderProfitLoss();
  renderInvoicesCard();
  renderMonthlyRevenue();
  renderInvoiceTrendChart();
  renderActivitiesCounters();
  renderActivity();
});
window.addEventListener("resize", renderAllCharts);
</script>

</body>
</html>
