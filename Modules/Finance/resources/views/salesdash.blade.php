<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { navy: {900:'#0b1e3b',800:'#132b52',700:'#17325f',600:'#1c3a6e'}, muted:'#9bb0d1' } } } }
</script>
<head>
<meta charset="UTF-8">
<title>Sales</title>
<style>
.main-wrapper { opacity: 0; animation: showPage .8s ease forwards; }
@keyframes showPage { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.dd-menu { animation: ddIn .12s ease forwards; }
@keyframes ddIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body class="bg-navy-900 text-white font-sans min-h-screen">

<div class="main-wrapper max-w-[1400px] mx-auto p-6 space-y-5">

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5">

    <div class="bg-navy-800 rounded-xl p-6">
      <div class="flex items-start justify-between mb-1">
        <div>
          <p class="text-sm text-muted mb-1">Sales</p>
          <div class="flex items-center gap-2">
            <span class="text-3xl font-bold" id="salesTotal">₱0</span>
            <span class="text-xs font-semibold rounded-full px-2 py-0.5 bg-emerald-500/20 text-emerald-400" id="salesChangeBadge">↑0%</span>
          </div>
          <p class="text-muted text-xs mt-1" id="salesChangeSub"></p>
        </div>
        <div class="relative">
          <button onclick="event.stopPropagation(); toggleMenu('dd-salesRange')" class="dd-toggle flex items-center gap-2 text-xs bg-navy-800 border border-navy-600 rounded-full px-4 py-1.5 hover:bg-navy-700 transition">
            <span id="salesRangeLabel">This week</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div id="dd-salesRange" class="dd-menu hidden absolute right-0 mt-1 bg-navy-700 border border-navy-600 rounded-lg shadow-lg text-xs w-32 overflow-hidden z-20">
            <button onclick="selectSalesRange('This week')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This week</button>
            <button onclick="selectSalesRange('Last week')" class="w-full text-left px-3 py-2 hover:bg-navy-600">Last week</button>
            <button onclick="selectSalesRange('This month')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This month</button>
            <button onclick="selectSalesRange('This year')" class="w-full text-left px-3 py-2 hover:bg-navy-600">This year</button>
          </div>
        </div>
      </div>

      <div id="salesChartLegend" class="flex flex-wrap items-center gap-4 text-xs text-muted mt-2"></div>
      <svg id="salesChart" viewBox="0 0 640 260" class="w-full h-64 mt-3"></svg>
    </div>

    <div class="space-y-3">
      <div class="bg-navy-700 rounded-lg p-4 flex items-center justify-between">
        <span class="text-sm text-muted">Total Sales</span>
        <span class="text-lg font-bold" id="totalSalesSidebar">₱0</span>
      </div>
      <div id="topProductsList" class="space-y-3 min-h-[220px]"></div>
    </div>

  </div>

  <div class="bg-navy-800 rounded-xl p-6">
    <h3 class="text-lg font-semibold">Sales</h3>
    <p class="text-muted text-xs mb-4">This month, grouped by revenue stream</p>

    <div class="space-y-3 min-h-[180px]" id="revenueStreamList"></div>
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

function fmtPeso(n){
  if (typeof n !== 'number' || isNaN(n) || n < 0) n = 0;
  return "₱" + Math.round(n).toLocaleString();
}

let salesData = {
  summary: {
    total: Number(@json($totalSales ?? 0)),
    changePct: 0,
    changeSub: ""
  },
  range: "This week",
  trend: { months: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"], series: [] },
  totalSalesSidebar: Number(@json($totalSales ?? 0)),
  topProducts: [],
  revenueStreams: []
};

function setSalesData(data) {
  if (!data || typeof data !== 'object') {
    console.warn('Invalid sales data received');
    return;
  }

  if (data.summary) {
    setSalesSummary(
      data.summary.total,
      data.summary.changePct,
      data.summary.changeSub
    );
  }

  if (data.trend) {
    setSalesTrendData(
      data.trend.months || getSalesLabelsForRange(salesData.range),
      data.trend.series || []
    );
  }

  if (data.totalSalesSidebar !== undefined) {
    setTotalSalesSidebar(data.totalSalesSidebar);
  }

  if (data.topProducts) {
    setTopProducts(data.topProducts);
  }

  if (data.revenueStreams) {
    setRevenueStreamData(data.revenueStreams);
  }
  renderSalesSummary();
}

function setSalesSummary(total, changePct, changeSub){
  salesData.summary = {
    total: typeof total === 'number' && !isNaN(total) && total >= 0 ? total : 0,
    changePct: typeof changePct === 'number' && !isNaN(changePct) ? changePct : 0,
    changeSub: changeSub || ''
  };
  renderSalesSummary();
}

function renderSalesSummary(){
  const d = salesData.summary;
  document.getElementById("salesTotal").textContent = fmtPeso(d.total);
  const badge = document.getElementById("salesChangeBadge");
  const up = d.changePct >= 0;
  badge.textContent = `${up ? "↑" : "↓"}${Math.abs(d.changePct).toFixed(1)}%`;
  badge.className = `text-xs font-semibold rounded-full px-2 py-0.5 ${up ? "bg-emerald-500/20 text-emerald-400" : "bg-red-500/20 text-red-400"}`;
  document.getElementById("salesChangeSub").textContent = d.changeSub || 'No change from previous period';
}

function getSalesLabelsForRange(range){
  if (range === "This week" || range === "Last week") return ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
  if (range === "This month") return ["Week 1","Week 2","Week 3","Week 4"];
  if (range === "This year") return ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  return ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
}

function selectSalesRange(range){
  salesData.range = range;
  document.getElementById("salesRangeLabel").textContent = range;

  const newMonths = getSalesLabelsForRange(range);
  const currentSeries = salesData.trend.series;

  salesData.trend.months = newMonths;

  if (currentSeries.length > 0) {
    salesData.trend.series = currentSeries.map(s => ({
      ...s,
      values: Array(newMonths.length).fill(0)
    }));
  } else {
    salesData.trend.series = [{
      label: 'Sales',
      color: '#4ca6ff',
      values: Array(newMonths.length).fill(0)
    }];
  }

  closeAllMenus();
  renderSalesChart();
}

function setSalesTrendData(months, series){
  const newMonths = Array.isArray(months) && months.length > 0 ? months : getSalesLabelsForRange(salesData.range);

  salesData.trend = {
    months: newMonths,
    series: Array.isArray(series) && series.length > 0 ? series.map(s => ({
      label: s.label || 'Sales',
      color: s.color || '#4ca6ff',
      values: Array.isArray(s.values) ? s.values.map(v => typeof v === 'number' && !isNaN(v) && v >= 0 ? v : 0) : Array(newMonths.length).fill(0)
    })) : [
      { label: 'Sales', color: '#4ca6ff', values: Array(newMonths.length).fill(0) }
    ]
  };
  renderSalesChart();
}

function renderSalesChart(){
  const svg = document.getElementById("salesChart");
  const rect = svg.getBoundingClientRect();
  const w = Math.round(Math.max(rect.width, 300));
  const h = Math.round(Math.max(rect.height, 160));
  svg.setAttribute("viewBox", `0 0 ${w} ${h}`);
  svg.innerHTML = "";

  const { months, series } = salesData.trend;
  const padX = 4, padTop = 10, padBottom = 26;
  const plotH = h - padTop - padBottom;

  const allValues = series.flatMap(s => s.values).filter(v => v > 0);
  const maxVal = allValues.length > 0 ? Math.max(...allValues) * 1.15 : 1000;

  const xFor = (i) => padX + (i * (w - padX * 2) / Math.max(months.length - 1, 1));
  const yFor = (v) => padTop + (1 - v / maxVal) * plotH;

  [0, 0.33, 0.66, 1].forEach(f => {
    const y = padTop + f * plotH;
    const line = document.createElementNS("http://www.w3.org/2000/svg","line");
    line.setAttribute("x1", padX); line.setAttribute("x2", w - padX);
    line.setAttribute("y1", y); line.setAttribute("y2", y);
    line.setAttribute("stroke", "#1c3a6e"); line.setAttribute("stroke-width", "1");
    svg.appendChild(line);
  });

  if (series.length > 0 && months.length > 0) {
    series.forEach(s => {
      const points = s.values.map((v,i) => `${xFor(i)},${yFor(v)}`).join(" ");
      const poly = document.createElementNS("http://www.w3.org/2000/svg","polyline");
      poly.setAttribute("points", points);
      poly.setAttribute("fill", "none");
      poly.setAttribute("stroke", s.color);
      poly.setAttribute("stroke-width", "2.5");
      poly.setAttribute("stroke-linecap", "round");
      poly.setAttribute("stroke-linejoin", "round");
      svg.appendChild(poly);

      s.values.forEach((v,i) => {
        const dot = document.createElementNS("http://www.w3.org/2000/svg","circle");
        dot.setAttribute("cx", xFor(i)); dot.setAttribute("cy", yFor(v)); dot.setAttribute("r", "3.5");
        dot.setAttribute("fill", s.color);
        svg.appendChild(dot);
      });
    });
  }

  months.forEach((m,i) => {
    const x = xFor(i);
    const anchor = i === 0 ? "start" : (i === months.length - 1 ? "end" : "middle");
    const label = document.createElementNS("http://www.w3.org/2000/svg","text");
    label.setAttribute("x", x); label.setAttribute("y", h - 6);
    label.setAttribute("fill", "#9bb0d1"); label.setAttribute("font-size", "11");
    label.setAttribute("text-anchor", anchor);
    label.textContent = m;
    svg.appendChild(label);
  });

  const legend = document.getElementById("salesChartLegend");
  legend.innerHTML = series.map(s => `
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:${s.color}"></span> ${s.label}</span>
  `).join("");
}

function setTotalSalesSidebar(total){
  salesData.totalSalesSidebar = typeof total === 'number' && !isNaN(total) && total >= 0 ? total : 0;
  document.getElementById("totalSalesSidebar").textContent = fmtPeso(salesData.totalSalesSidebar);
}

function setTopProducts(products){
  salesData.topProducts = Array.isArray(products) ? products.map(p => ({
    name: p.name || 'Unknown Product',
    value: typeof p.value === 'number' && !isNaN(p.value) && p.value >= 0 ? p.value : 0
  })) : [];
  renderTopProducts();
}

function renderTopProducts(){
  const list = document.getElementById("topProductsList");
  if (salesData.topProducts.length === 0) {
    list.innerHTML = `
      <div class="bg-navy-700 rounded-lg p-4 flex items-center justify-between">
        <span class="text-sm text-muted">No products yet</span>
        <span class="text-sm font-medium text-muted">₱0</span>
      </div>`;
    return;
  }
  list.innerHTML = salesData.topProducts.map(p => `
    <div class="bg-navy-700 rounded-lg p-4 flex items-center justify-between">
      <span class="text-sm">${p.name}</span>
      <span class="text-sm font-medium">${fmtPeso(p.value)}</span>
    </div>`).join("");
}

function setRevenueStreamData(streams){
  salesData.revenueStreams = Array.isArray(streams) ? streams.map(r => ({
    label: r.label || 'Unknown',
    value: typeof r.value === 'number' && !isNaN(r.value) && r.value >= 0 ? r.value : 0
  })) : [];
  renderRevenueStreams();
}

function renderRevenueStreams(){
  const streams = salesData.revenueStreams;
  const list = document.getElementById("revenueStreamList");

  const total = streams.reduce((s, r) => s + r.value, 0);
  const rows = streams.length > 0 ? streams.map(r => `
    <div class="flex items-center justify-between bg-navy-700/40 border border-blue-400/30 rounded-lg px-4 py-3">
      <span class="text-blue-400 font-medium text-sm">${r.label}</span>
      <span class="text-sm font-semibold">${fmtPeso(r.value)}</span>
    </div>`).join("") : `
    <div class="flex items-center justify-between bg-navy-700/40 border border-blue-400/30 rounded-lg px-4 py-3">
      <span class="text-blue-400 font-medium text-sm">No revenue streams</span>
      <span class="text-sm font-semibold">₱0</span>
    </div>`;

  list.innerHTML = rows + `
    <div class="flex items-center justify-between px-4 pt-2">
      <span class="text-sm text-muted">Total Sales:</span>
      <span class="text-sm font-semibold">${fmtPeso(total)}</span>
    </div>`;
}

salesData.trend.months = getSalesLabelsForRange("This week");
salesData.trend.series = [{ label: 'Sales', color: '#4ca6ff', values: Array(7).fill(0) }];

requestAnimationFrame(() => {
  renderSalesSummary();
  renderSalesChart();
  renderTopProducts();
  renderRevenueStreams();
});
window.addEventListener("resize", renderSalesChart);
</script>

</body>
</html>