<!DOCTYPE html>
<html lang="en">
  <script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { navy: {900:'#0b1e3b',800:'#132b52',700:'#17325f',600:'#1c3a6e'}, muted:'#9bb0d1' } } } }
</script>
<head>
  <meta charset="UTF-8">
 <style>
.main-wrapper {
    opacity: 0;
    animation: showPage .8s ease forwards;
}

@keyframes showPage {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

</head>

<body class="bg-navy-900 text-white font-sans p-5">

<div class="main-wrapper space-y-5">

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Cash On Hand</h3>
        <div class="w-9 h-9 rounded-full bg-brand/20 flex items-center justify-center text-brand">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v13H3z"/><path d="M3 7l3-4h12l3 4"/><circle cx="12" cy="14" r="2"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="cfCashOnHand">₱0</div>
      <small class="text-muted text-xs">Available balance</small>
      <small id="cfCashOnHandChange" class="text-emerald-400"></small>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Cash Inflow</h3>
        <div class="w-9 h-9 rounded-full bg-emerald-400/20 flex items-center justify-center text-emerald-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="cfInflow">₱0</div>
      <small class="text-muted text-xs">This month</small>
      <small id="cfInflowChange" class="text-emerald-400"></small>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Cash Outflow</h3>
        <div class="w-9 h-9 rounded-full bg-red-400/20 flex items-center justify-center text-red-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="cfOutflow">₱0</div>
      <small class="text-muted text-xs">This month</small>
      <small id="cfOutflowChange" class="text-red-400"></small>
    </div>

    <div class="bg-navy-800 rounded-xl p-5 flex flex-col gap-2">
      <div class="flex items-center justify-between">
        <h3 class="text-muted text-sm font-medium">Net Cash Flow</h3>
        <div class="w-9 h-9 rounded-full bg-brand/20 flex items-center justify-center text-brand">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></svg>
        </div>
      </div>
      <div class="text-2xl font-semibold" id="cfNet">₱0</div>
      <small class="text-muted text-xs">This month</small>
      <small id="cfNetChange" class="text-emerald-400"></small>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

    <div class="bg-navy-800 rounded-xl p-5">
      <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
        <h3 class="text-lg font-semibold">Cash Flow Trend</h3>
        <div class="flex gap-2">
          <select id="cfRange" onchange="renderTrendChart()" class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
            <option value="6">Last 6 months</option>
            <option value="12">Last 12 months</option>
          </select>
          <select id="cfGranularity" onchange="renderTrendChart()" class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
            <option value="monthly">Monthly</option>
            <option value="weekly">Weekly</option>
          </select>
        </div>
      </div>
      <div class="w-full overflow-x-auto">
        <svg id="cfTrendChart" viewBox="0 0 640 240" class="w-full h-auto"></svg>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">Cash Flow Summary</h3>
        <select class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
          <option>This month</option>
          <option>Last month</option>
        </select>
      </div>
      <div class="space-y-3 text-sm">
        <div class="flex justify-between text-muted">
          <span>Beginning Cash Balance</span>
        </div>
        <div class="font-semibold text-base" id="cfSummaryBeginning">₱0</div>

        <div class="flex justify-between">
          <span class="text-muted">+ Cash Inflow</span>
          <span class="text-emerald-400 font-medium" id="cfSummaryInflow">₱0</span>
        </div>
        <div class="flex justify-between">
          <span class="text-muted">− Cash Outflow</span>
          <span class="text-red-400 font-medium" id="cfSummaryOutflow">₱0</span>
        </div>
        <div class="flex justify-between border-t border-navy-600 pt-2">
          <span class="text-muted">Net Cash Flow</span>
          <span class="text-brand font-medium" id="cfSummaryNet">₱0</span>
        </div>

        <div class="text-muted pt-2">Ending Cash Balance</div>
        <div class="font-semibold text-base" id="cfSummaryEnding">₱0</div>
      </div>
    </div>
    <div class="bg-navy-800 rounded-xl p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Cash Flow by Activity</h3>
        <select class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
          <option>This month</option>
          <option>Last month</option>
        </select>
      </div>
      <div class="flex items-center gap-8 flex-wrap">
        <svg id="cfActivityDonut" viewBox="0 0 42 42" class="w-40 h-40 -rotate-90"></svg>
        <div class="space-y-2 text-sm min-h-[120px]" id="cfActivityLegend"></div>
      </div>
    </div>

  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

    <div class="lg:col-span-1 bg-navy-800 rounded-xl p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">Cash Flow Statement</h3>
        <select class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
          <option>This month</option>
          <option>Last month</option>
        </select>
      </div>
      <div class="overflow-x-auto min-h-[140px]">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-muted text-left border-b border-navy-600">
              <th class="py-2 pr-2">Category</th>
              <th class="py-2 pr-2">Inflow</th>
              <th class="py-2 pr-2">Outflow</th>
              <th class="py-2 pr-2">Net</th>
            </tr>
          </thead>
          <tbody id="cfStatementBody"></tbody>
        </table>
      </div>
    </div>

    <div class="bg-navy-800 rounded-xl p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">Upcoming Cash Outflow</h3>
        <select class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
          <option>Next 30 days</option>
          <option>Next 7 days</option>
        </select>
      </div>
      <ul id="cfUpcomingList" class="space-y-3 text-sm min-h-[140px]"></ul>
    </div>


    <div class="bg-navy-800 rounded-xl p-5 flex flex-col">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">Cash Position</h3>
        <select class="bg-navy-700 text-sm rounded-lg px-3 py-2 outline-none">
          <option>This month</option>
          <option>Last month</option>
        </select>
      </div>
      <div class="space-y-2 text-sm flex-1">
        <div class="flex justify-between">
          <span class="text-muted">Cash on Hand (start)</span>
          <span class="font-medium" id="cfPosStart">₱0</span>
        </div>
        <div class="flex justify-between">
          <span class="text-muted">Net Cash Flow</span>
          <span class="font-medium" id="cfPosNet">₱0</span>
        </div>
        <div class="flex justify-between bg-navy-700 rounded-lg px-3 py-2 mt-2">
          <span class="text-muted">Cash on Hand (end)</span>
          <span class="font-semibold text-brand" id="cfPosEnd">₱0</span>
        </div>
      </div>
      <button onclick="viewCashFlowReport()"
              class="mt-4 flex items-center justify-center gap-2 bg-brand hover:brightness-110 transition rounded-lg px-4 py-2 text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg>
        View Cash Flow Report
      </button>
    </div>

  </div>

</div>

<script>

function fmtPeso(n){
  if (typeof n !== 'number' || isNaN(n) || n < 0) n = 0;
  return "₱" + Math.round(n).toLocaleString();
}

function fmtChange(pct){
  if (typeof pct !== 'number' || isNaN(pct)) return "";
  const up = pct >= 0;
  return `${up ? "▲" : "▼"} ${Math.abs(pct).toFixed(2)}% vs. last month`;
}

let cashFlowData = {
  cashOnHand: { amount: 0, changePct: null },
  topline: { inflow: 0, outflow: 0, net: 0, changes: { inflow: null, outflow: null, net: null } },
  trend: { months: [], inflow: [], outflow: [] },
  summary: { beginningBalance: 0, inflow: 0, outflow: 0 },
  activity: [],
  statement: [],
  upcoming: []
};

function setCashFlowData(data) {
  if (!data || typeof data !== 'object') {
    console.warn('Invalid cash flow data received');
    return;
  }

  if (data.cashOnHand) {
    setCashOnHand(
      data.cashOnHand.amount,
      data.cashOnHand.changePct
    );
  }

  if (data.topline) {
    setCashFlowTopline(
      data.topline.inflow,
      data.topline.outflow,
      data.topline.net,
      data.topline.changes?.inflow,
      data.topline.changes?.outflow,
      data.topline.changes?.net
    );
  }

  if (data.trend) {
    setCashFlowTrendData(
      data.trend.months,
      data.trend.inflow,
      data.trend.outflow
    );
  }

  if (data.summary) {
    setCashFlowSummary(
      data.summary.beginningBalance,
      data.summary.inflow,
      data.summary.outflow
    );
  }

  if (data.activity) {
    setActivityBreakdown(data.activity);
  }

  if (data.statement) {
    setCashFlowStatement(data.statement);
  }

  if (data.upcoming) {
    setUpcomingOutflows(data.upcoming);
  }
}

function setCashOnHand(amount, changePct){
  cashFlowData.cashOnHand = {
    amount: typeof amount === 'number' && !isNaN(amount) && amount >= 0 ? amount : 0,
    changePct: typeof changePct === 'number' && !isNaN(changePct) ? changePct : null
  };
  renderCashOnHand();
}

function renderCashOnHand(){
  document.getElementById("cfCashOnHand").textContent = fmtPeso(cashFlowData.cashOnHand.amount);
  const el = document.getElementById("cfCashOnHandChange");
  if (cashFlowData.cashOnHand.changePct === null) {
    el.textContent = "";
  } else {
    el.textContent = fmtChange(cashFlowData.cashOnHand.changePct);
    el.className = cashFlowData.cashOnHand.changePct >= 0 ? "text-emerald-400" : "text-red-400";
  }
}

function setCashFlowTopline(inflow, outflow, net, changeInflow, changeOutflow, changeNet){
  cashFlowData.topline = {
    inflow: typeof inflow === 'number' && !isNaN(inflow) && inflow >= 0 ? inflow : 0,
    outflow: typeof outflow === 'number' && !isNaN(outflow) && outflow >= 0 ? outflow : 0,
    net: typeof net === 'number' && !isNaN(net) ? net : 0,
    changes: {
      inflow: typeof changeInflow === 'number' && !isNaN(changeInflow) ? changeInflow : null,
      outflow: typeof changeOutflow === 'number' && !isNaN(changeOutflow) ? changeOutflow : null,
      net: typeof changeNet === 'number' && !isNaN(changeNet) ? changeNet : null
    }
  };
  renderTopline();
}

function renderTopline(){
  const d = cashFlowData.topline;
  document.getElementById("cfInflow").textContent = fmtPeso(d.inflow);
  document.getElementById("cfOutflow").textContent = fmtPeso(d.outflow);
  document.getElementById("cfNet").textContent = fmtPeso(d.net);

  const setChange = (id, pct) => {
    const el = document.getElementById(id);
    if (pct === null) {
      el.textContent = "";
      return;
    }
    el.textContent = fmtChange(pct);
    el.className = pct >= 0 ? "text-emerald-400" : "text-red-400";
  };
  setChange("cfInflowChange", d.changes.inflow);
  setChange("cfOutflowChange", d.changes.outflow);
  setChange("cfNetChange", d.changes.net);
}

function setCashFlowTrendData(months, inflowArr, outflowArr){
  cashFlowData.trend = {
    months: Array.isArray(months) ? months : [],
    inflow: Array.isArray(inflowArr) ? inflowArr.map(v => typeof v === 'number' && !isNaN(v) && v >= 0 ? v : 0) : [],
    outflow: Array.isArray(outflowArr) ? outflowArr.map(v => typeof v === 'number' && !isNaN(v) && v >= 0 ? v : 0) : []
  };
  renderTrendChart();
}

function setCashFlowSummary(beginningBalance, inflow, outflow){
  cashFlowData.summary = {
    beginningBalance: typeof beginningBalance === 'number' && !isNaN(beginningBalance) && beginningBalance >= 0 ? beginningBalance : 0,
    inflow: typeof inflow === 'number' && !isNaN(inflow) && inflow >= 0 ? inflow : 0,
    outflow: typeof outflow === 'number' && !isNaN(outflow) && outflow >= 0 ? outflow : 0
  };
  renderSummary();
}

function renderSummary() {
  const { beginningBalance, inflow, outflow } = cashFlowData.summary;
  const net = inflow - outflow;
  const ending = beginningBalance + net;

  document.getElementById("cfSummaryBeginning").textContent = fmtPeso(beginningBalance);
  document.getElementById("cfSummaryInflow").textContent = "+ " + fmtPeso(inflow);
  document.getElementById("cfSummaryOutflow").textContent = "- " + fmtPeso(outflow);
  document.getElementById("cfSummaryNet").textContent = fmtPeso(net);
  document.getElementById("cfSummaryEnding").textContent = fmtPeso(ending);

  document.getElementById("cfPosStart").textContent = fmtPeso(beginningBalance);
  document.getElementById("cfPosNet").textContent = fmtPeso(net);
  document.getElementById("cfPosEnd").textContent = fmtPeso(ending);
}

function setActivityBreakdown(segments){
  cashFlowData.activity = Array.isArray(segments) ? segments.map(s => ({
    label: s.label || 'Unknown',
    value: typeof s.value === 'number' && !isNaN(s.value) && s.value >= 0 ? s.value : 0,
    color: s.color || '#4ca6ff'
  })) : [];
  renderActivityDonut();
}

function renderActivityDonut() {
  const total = cashFlowData.activity.reduce((s, a) => s + a.value, 0) || 1;
  const svg = document.getElementById("cfActivityDonut");
  svg.innerHTML = "";
  const r = 15.9155, cx = 21, cy = 21;
  let offset = 0;

  const bg = document.createElementNS("http://www.w3.org/2000/svg", "circle");
  bg.setAttribute("cx", cx); bg.setAttribute("cy", cy); bg.setAttribute("r", r);
  bg.setAttribute("fill", "transparent"); bg.setAttribute("stroke", "#27477d"); bg.setAttribute("stroke-width", "6");
  svg.appendChild(bg);

  cashFlowData.activity.forEach(seg => {
    const pct = (seg.value / total) * 100;
    const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    circle.setAttribute("cx", cx); circle.setAttribute("cy", cy); circle.setAttribute("r", r);
    circle.setAttribute("fill", "transparent");
    circle.setAttribute("stroke", seg.color);
    circle.setAttribute("stroke-width", "6");
    circle.setAttribute("stroke-dasharray", `${pct} ${100 - pct}`);
    circle.setAttribute("stroke-dashoffset", -offset);
    svg.appendChild(circle);
    offset += pct;
  });

  document.getElementById("cfActivityLegend").innerHTML = cashFlowData.activity.length > 0 ?
    cashFlowData.activity.map(seg => `
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:${seg.color}"></span>
        <span class="text-muted">${seg.label}</span>
        <span class="ml-auto font-medium">${fmtPeso(seg.value)}</span>
      </div>`).join("") :
    '<span class="text-muted text-xs">No activity data</span>';
}

const statementIcons = {
  briefcase: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>',
  trend: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></svg>',
  bank: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M4 10h16"/><path d="M6 10v8"/><path d="M18 10v8"/><path d="M12 3l9 5H3z"/></svg>',
  home: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10l9-7 9 7"/><path d="M5 9v11h14V9"/></svg>',
  cart: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
  bolt: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>',
};

function setCashFlowStatement(rows){
  cashFlowData.statement = Array.isArray(rows) ? rows.map(r => ({
    category: r.category || 'Unknown',
    icon: r.icon || 'trend',
    inflow: typeof r.inflow === 'number' && !isNaN(r.inflow) && r.inflow >= 0 ? r.inflow : 0,
    outflow: typeof r.outflow === 'number' && !isNaN(r.outflow) && r.outflow >= 0 ? r.outflow : 0
  })) : [];
  renderStatement();
}

function renderStatement() {
  const rows = cashFlowData.statement;
  if (rows.length === 0) {
    document.getElementById("cfStatementBody").innerHTML =
      '<tr><td colspan="4" class="text-center text-muted py-4">No statement data</td></tr>';
    return;
  }

  const totalInflow = rows.reduce((s, r) => s + r.inflow, 0);
  const totalOutflow = rows.reduce((s, r) => s + r.outflow, 0);

  const body = document.getElementById("cfStatementBody");
  body.innerHTML = rows.map(r => `
    <tr class="border-b border-navy-600/60">
      <td class="py-2 pr-2 flex items-center gap-2 text-muted">${statementIcons[r.icon] || ""} ${r.category}</td>
      <td class="py-2 pr-2 text-emerald-400">${fmtPeso(r.inflow)}</td>
      <td class="py-2 pr-2 text-red-400">${fmtPeso(r.outflow)}</td>
      <td class="py-2 pr-2 text-brand">${fmtPeso(r.inflow - r.outflow)}</td>
    </tr>`).join("") + `
    <tr>
      <td class="py-2 pr-2 font-semibold">Total</td>
      <td class="py-2 pr-2 font-semibold text-emerald-400">${fmtPeso(totalInflow)}</td>
      <td class="py-2 pr-2 font-semibold text-red-400">${fmtPeso(totalOutflow)}</td>
      <td class="py-2 pr-2 font-semibold text-brand">${fmtPeso(totalInflow - totalOutflow)}</td>
    </tr>`;
}

function setUpcomingOutflows(rows){
  cashFlowData.upcoming = Array.isArray(rows) ? rows.map(r => ({
    label: r.label || 'Unknown',
    icon: r.icon || 'trend',
    amount: typeof r.amount === 'number' && !isNaN(r.amount) && r.amount >= 0 ? r.amount : 0,
    due: r.due || 'N/A'
  })) : [];
  renderUpcoming();
}

function renderUpcoming() {
  const items = cashFlowData.upcoming;
  document.getElementById("cfUpcomingList").innerHTML = items.length > 0 ?
    items.map(o => `
      <li class="flex items-center gap-3">
        <span class="w-8 h-8 rounded-lg bg-navy-700 flex items-center justify-center text-brand shrink-0">${statementIcons[o.icon] || ""}</span>
        <div class="flex-1">
          <p>${o.label}</p>
        </div>
        <div class="text-right">
          <p class="font-medium">${fmtPeso(o.amount)}</p>
          <p class="text-red-400 text-xs">${o.due}</p>
        </div>
      </li>`).join("") :
    '<li class="text-center text-muted text-sm py-4">No upcoming outflows</li>';
}

function renderTrendChart() {
  const svg = document.getElementById("cfTrendChart");
  if (!svg) return;
  const range = parseInt(document.getElementById("cfRange").value) || 6;
  const months = cashFlowData.trend.months.slice(-range);
  const inflow = cashFlowData.trend.inflow.slice(-range);
  const outflow = cashFlowData.trend.outflow.slice(-range);

  if (months.length === 0) {
    svg.innerHTML = '';
    const text = document.createElementNS("http://www.w3.org/2000/svg","text");
    text.setAttribute("x", "320");
    text.setAttribute("y", "120");
    text.setAttribute("fill", "#9bb0d1");
    text.setAttribute("font-size", "14");
    text.setAttribute("text-anchor", "middle");
    text.textContent = "No trend data available";
    svg.appendChild(text);
    return;
  }

  const net = inflow.map((v, i) => v - outflow[i]);

  svg.innerHTML = "";
  const w = 640, h = 240, padX = 30, padY = 20;
  const maxVal = Math.max(...inflow, ...outflow, 1) * 1.15;
  const groupWidth = (w - padX * 2) / Math.max(months.length, 1);
  const barWidth = groupWidth * 0.28;

  const yFor = (v) => h - padY - (v / maxVal) * (h - padY * 2);

  months.forEach((m, i) => {
    const groupX = padX + i * groupWidth + groupWidth / 2;

    const bar1 = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    bar1.setAttribute("x", groupX - barWidth - 2);
    bar1.setAttribute("y", yFor(inflow[i]));
    bar1.setAttribute("width", barWidth);
    bar1.setAttribute("height", h - padY - yFor(inflow[i]));
    bar1.setAttribute("fill", "#4ca6ff");
    bar1.setAttribute("rx", "2");
    svg.appendChild(bar1);

    const bar2 = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    bar2.setAttribute("x", groupX + 2);
    bar2.setAttribute("y", yFor(outflow[i]));
    bar2.setAttribute("width", barWidth);
    bar2.setAttribute("height", h - padY - yFor(outflow[i]));
    bar2.setAttribute("fill", "#e74c3c");
    bar2.setAttribute("rx", "2");
    svg.appendChild(bar2);

    const label = document.createElementNS("http://www.w3.org/2000/svg", "text");
    label.setAttribute("x", groupX); label.setAttribute("y", h - 2);
    label.setAttribute("fill", "#9bb0d1"); label.setAttribute("font-size", "10"); label.setAttribute("text-anchor", "middle");
    label.textContent = m;
    svg.appendChild(label);
  });

  const netMax = Math.max(...net.map(Math.abs), 1);
  const points = net.map((v, i) => {
    const x = padX + i * groupWidth + groupWidth / 2;
    const y = h / 2 - (v / netMax) * (h / 2 - padY);
    return `${x},${y}`;
  }).join(" ");

  const poly = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
  poly.setAttribute("points", points);
  poly.setAttribute("fill", "none");
  poly.setAttribute("stroke", "#ffffff");
  poly.setAttribute("stroke-width", "2");
  svg.appendChild(poly);

  points.split(" ").forEach(pt => {
    const [x, y] = pt.split(",");
    const dot = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    dot.setAttribute("cx", x); dot.setAttribute("cy", y); dot.setAttribute("r", "3");
    dot.setAttribute("fill", "#ffffff");
    svg.appendChild(dot);
  });
}

function viewCashFlowReport() {
  const { beginningBalance, inflow, outflow } = cashFlowData.summary;
  const net = inflow - outflow;
  const ending = beginningBalance + net;

  const lines = [];
  lines.push("CASH FLOW REPORT");
  lines.push(`Generated: ${new Date().toLocaleString()}`);
  lines.push("");
  lines.push("SUMMARY");
  lines.push(`Beginning Cash Balance,${beginningBalance}`);
  lines.push(`Cash Inflow,${inflow}`);
  lines.push(`Cash Outflow,${outflow}`);
  lines.push(`Net Cash Flow,${net}`);
  lines.push(`Ending Cash Balance,${ending}`);
  lines.push("");
  lines.push("CASH FLOW STATEMENT");
  lines.push("Category,Inflow,Outflow,Net");
  cashFlowData.statement.forEach(r => {
    lines.push(`${r.category},${r.inflow},${r.outflow},${r.inflow - r.outflow}`);
  });
  lines.push("");
  lines.push("UPCOMING CASH OUTFLOW");
  lines.push("Item,Amount,Due");
  cashFlowData.upcoming.forEach(o => {
    lines.push(`${o.label},${o.amount},${o.due}`);
  });

  const blob = new Blob([lines.join("\n")], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `cash_flow_report_${new Date().toISOString().slice(0,10)}.csv`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

function renderCashFlowDashboard() {
  renderCashOnHand();
  renderTopline();
  renderSummary();
  renderActivityDonut();
  renderStatement();
  renderUpcoming();
  renderTrendChart();
}

document.addEventListener("DOMContentLoaded", renderCashFlowDashboard);
</script>
</body>
</html>