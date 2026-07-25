<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { navy: {900:'#0b1e3b',800:'#132b52',700:'#17325f',600:'#1c3a6e'}, muted:'#9bb0d1' } } } }
</script>
<head>
<meta charset="UTF-8">
<title>Expenses</title>
<style>
.main-wrapper { opacity: 0; animation: showPage .8s ease forwards; }
@keyframes showPage { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.spin-once { animation: spin .6s ease; }
.dd-menu { animation: ddIn .12s ease forwards; }
@keyframes ddIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
@keyframes spin { from { transform: rotate(0deg);} to { transform: rotate(360deg);} }
</style>
</head>
<body class="bg-navy-900 text-white font-sans min-h-screen">

<div class="main-wrapper max-w-[1400px] mx-auto p-6 space-y-5">

  <div class="bg-navy-800 rounded-xl p-6">
    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">

      <div class="space-y-5">
        <div class="flex items-center justify-between">
          <h1 class="text-xl font-semibold">Overview</h1>
        </div>

        <div>
          <p class="text-muted text-xs tracking-wide">TOTAL EXPENSES THIS MONTH</p>
          <div class="flex items-center gap-2 mt-1">
            <span id="totalExpenses" class="text-3xl font-bold">₱0</span>
            <span id="totalChangeBadge" class="text-xs font-semibold rounded-full px-2 py-0.5 bg-emerald-500/20 text-emerald-400">↑0%</span>
          </div>
          <p id="totalChangeSub" class="text-muted text-xs mt-1"></p>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <p class="text-muted text-xs tracking-wide">BUDGET USED</p>
          </div>
          <div class="w-full h-2 bg-navy-700 rounded-full overflow-hidden">
            <div id="budgetBar" class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full transition-all duration-500" style="width:0%"></div>
          </div>
          <div class="flex items-center justify-between mt-1 text-xs text-muted">
            <span id="budgetLabel">₱0 of ₱0</span>
            <span id="budgetPct">0%</span>
          </div>
        </div>
      </div>


      <div>
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
          <div class="relative">
            <button onclick="event.stopPropagation(); toggleRangeMenu()" class="dd-toggle flex items-center gap-2 text-xs text-muted hover:text-white bg-navy-800 border border-navy-600 rounded-lg px-3 py-1.5 transition tracking-wide">
              <span id="rangeBtnLabel">LAST 6 MONTHS</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div id="rangeMenu" class="dd-menu hidden absolute left-0 mt-1 bg-navy-800 border border-navy-600 rounded-lg shadow-lg text-xs w-40 overflow-hidden z-20"></div>
          </div>
          <div id="chartLegend" class="flex flex-wrap items-center gap-4 text-sm"></div>
        </div>
        <svg id="trendChart" viewBox="0 0 640 210" class="w-full h-64"></svg>
      </div>

    </div>
  </div>
  <div id="categoryCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"></div>

</div>

<script>

let expenseData = {
  categories: [],
  budgetCap: 0,
  months: ["Jan","Feb","Mar","Apr","May","Jun"],
  rangeOptions: ["LAST 6 MONTHS", "LAST WEEK", "LAST MONTH", "LAST YEAR"],
  selectedRange: "LAST 6 MONTHS"
};

function setExpenseData(data) {
  if (!data || typeof data !== 'object') {
    console.warn('Invalid expense data received');
    return;
  }

  if (data.categories && Array.isArray(data.categories)) {
    expenseData.categories = data.categories.map(c => ({
      key: c.key || 'unknown',
      label: c.label || 'Unknown Category',
      color: c.color || '#4ca6ff',
      capacity: typeof c.capacity === 'number' && !isNaN(c.capacity) && c.capacity > 0 ? c.capacity : 0,
      value: typeof c.value === 'number' && !isNaN(c.value) && c.value >= 0 ? c.value : 0,
      prevValue: typeof c.prevValue === 'number' && !isNaN(c.prevValue) && c.prevValue >= 0 ? c.prevValue : 0,
      trend: Array.isArray(c.trend) && c.trend.length > 0 ?
        c.trend.map(v => typeof v === 'number' && !isNaN(v) && v >= 0 ? v : 0) : []
    }));
  } else {
    expenseData.categories = [];
  }

  if (typeof data.budgetCap === 'number' && !isNaN(data.budgetCap) && data.budgetCap > 0) {
    expenseData.budgetCap = data.budgetCap;
  } else {
    expenseData.budgetCap = 0;
  }

  if (data.months && Array.isArray(data.months) && data.months.length > 0) {
    expenseData.months = data.months.map(m => String(m).trim());
  } else {
    expenseData.months = ["Jan","Feb","Mar","Apr","May","Jun"];
  }

  if (data.selectedRange && typeof data.selectedRange === 'string') {
    expenseData.selectedRange = data.selectedRange;
  }

  renderAll();
}

function fmtPeso(n){
  if (typeof n !== 'number' || isNaN(n) || n < 0) n = 0;
  return "₱" + Math.round(n).toLocaleString();
}

function renderLegend(){
  const cats = expenseData.categories || [];
  if (cats.length === 0) {
    document.getElementById("chartLegend").innerHTML = '<span class="text-muted text-xs">No expense categories</span>';
    return;
  }
  document.getElementById("chartLegend").innerHTML = cats.map(c => `
    <span class="flex items-center gap-1.5">
      <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:${c.color}"></span>
      <span class="text-muted">${c.label}</span>
    </span>`).join("");
}

function renderTotals(){
  const cats = expenseData.categories || [];
  if (cats.length === 0) {
    document.getElementById("totalExpenses").textContent = fmtPeso(0);
    document.getElementById("totalChangeBadge").textContent = "0%";
    document.getElementById("totalChangeBadge").className = "text-xs font-semibold rounded-full px-2 py-0.5 bg-emerald-500/20 text-emerald-400";
    document.getElementById("totalChangeSub").textContent = "";
    document.getElementById("budgetBar").style.width = "0%";
    document.getElementById("budgetLabel").textContent = "₱0 of ₱0";
    document.getElementById("budgetPct").textContent = "0%";
    return;
  }

  const total = cats.reduce((s,c) => s + c.value, 0);
  const prevTotal = cats.reduce((s,c) => s + c.prevValue, 0);
  const diff = total - prevTotal;
  const pct = prevTotal > 0 ? (diff / prevTotal) * 100 : 0;
  const up = diff >= 0;

  document.getElementById("totalExpenses").textContent = fmtPeso(total);

  const badge = document.getElementById("totalChangeBadge");
  badge.textContent = `${up ? "↑" : "↓"}${Math.abs(pct).toFixed(1)}%`;
  badge.className = `text-xs font-semibold rounded-full px-2 py-0.5 ${up ? "bg-emerald-500/20 text-emerald-400" : "bg-red-500/20 text-red-400"}`;

  document.getElementById("totalChangeSub").textContent =
    diff !== 0 ? `${fmtPeso(Math.abs(diff))} ${up ? "higher" : "lower"} than last month` : "No change from last month";

  const usedPct = expenseData.budgetCap > 0 ? Math.min(100, (total / expenseData.budgetCap) * 100) : 0;
  document.getElementById("budgetBar").style.width = usedPct + "%";
  document.getElementById("budgetLabel").textContent = `${fmtPeso(total)} of ${fmtPeso(expenseData.budgetCap)}`;
  document.getElementById("budgetPct").textContent = Math.round(usedPct) + "%";
}

function renderCategoryCards(){
  const cats = expenseData.categories || [];
  const wrap = document.getElementById("categoryCards");

  if (cats.length === 0) {
    wrap.innerHTML = `
      <div class="bg-navy-800 rounded-xl p-5 text-center">
        <h3 class="text-base font-semibold text-muted">No Categories</h3>
        <p class="text-muted text-xs">₱0 total</p>
        <div class="relative w-28 h-28 mx-auto mt-4">
          <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#4ca6ff33" stroke-width="9"/>
            <circle cx="50" cy="50" r="45" fill="none" stroke="#4ca6ff" stroke-width="9" stroke-linecap="round" stroke-dasharray="0 282.7"/>
          </svg>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-[70px] h-[70px] rounded-full flex items-center justify-center font-bold text-sm bg-navy-700 text-muted">0%</div>
          </div>
        </div>
        <div class="flex items-center justify-center gap-1.5 mt-4 text-sm text-muted">
          <span>₱0</span>
          <span class="text-xs">0%</span>
        </div>
      </div>
      <div class="bg-navy-800 rounded-xl p-5 text-center">
        <h3 class="text-base font-semibold text-muted">No Categories</h3>
        <p class="text-muted text-xs">₱0 total</p>
        <div class="relative w-28 h-28 mx-auto mt-4">
          <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#2ecc7133" stroke-width="9"/>
            <circle cx="50" cy="50" r="45" fill="none" stroke="#2ecc71" stroke-width="9" stroke-linecap="round" stroke-dasharray="0 282.7"/>
          </svg>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-[70px] h-[70px] rounded-full flex items-center justify-center font-bold text-sm bg-navy-700 text-muted">0%</div>
          </div>
        </div>
        <div class="flex items-center justify-center gap-1.5 mt-4 text-sm text-muted">
          <span>₱0</span>
          <span class="text-xs">0%</span>
        </div>
      </div>
      <div class="bg-navy-800 rounded-xl p-5 text-center">
        <h3 class="text-base font-semibold text-muted">No Categories</h3>
        <p class="text-muted text-xs">₱0 total</p>
        <div class="relative w-28 h-28 mx-auto mt-4">
          <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#ef476f33" stroke-width="9"/>
            <circle cx="50" cy="50" r="45" fill="none" stroke="#ef476f" stroke-width="9" stroke-linecap="round" stroke-dasharray="0 282.7"/>
          </svg>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-[70px] h-[70px] rounded-full flex items-center justify-center font-bold text-sm bg-navy-700 text-muted">0%</div>
          </div>
        </div>
        <div class="flex items-center justify-center gap-1.5 mt-4 text-sm text-muted">
          <span>₱0</span>
          <span class="text-xs">0%</span>
        </div>
      </div>
      <div class="bg-navy-800 rounded-xl p-5 text-center">
        <h3 class="text-base font-semibold text-muted">No Categories</h3>
        <p class="text-muted text-xs">₱0 total</p>
        <div class="relative w-28 h-28 mx-auto mt-4">
          <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#f5a62333" stroke-width="9"/>
            <circle cx="50" cy="50" r="45" fill="none" stroke="#f5a623" stroke-width="9" stroke-linecap="round" stroke-dasharray="0 282.7"/>
          </svg>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-[70px] h-[70px] rounded-full flex items-center justify-center font-bold text-sm bg-navy-700 text-muted">0%</div>
          </div>
        </div>
        <div class="flex items-center justify-center gap-1.5 mt-4 text-sm text-muted">
          <span>₱0</span>
          <span class="text-xs">0%</span>
        </div>
      </div>
    `;
    return;
  }

  wrap.innerHTML = cats.map(c => {
    const pct = c.capacity > 0 ? Math.min(100, Math.round((c.value / c.capacity) * 100)) : 0;
    const dash = (pct / 100) * 282.7;
    const diff = c.value - c.prevValue;
    const diffK = diff / 1000;
    const pctChange = c.prevValue > 0 ? (diff / c.prevValue) * 100 : 0;
    const up = diff >= 0;

    return `
    <div class="bg-navy-800 rounded-xl p-5">
      <h3 class="text-base font-semibold">${c.label}</h3>
      <p class="text-muted text-xs mb-4">${fmtPeso(c.value)} total</p>
      <div class="relative w-28 h-28 mx-auto">
        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
          <circle cx="50" cy="50" r="45" fill="none" stroke="${c.color}33" stroke-width="9"/>
          <circle cx="50" cy="50" r="45" fill="none" stroke="${c.color}" stroke-width="9"
                  stroke-linecap="round" stroke-dasharray="${dash} 282.7"/>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-[70px] h-[70px] rounded-full flex items-center justify-center font-bold text-sm"
               style="background:${c.color}">${pct}%</div>
        </div>
      </div>
      <div class="flex items-center justify-center gap-1.5 mt-4 text-sm ${up ? "text-emerald-400" : "text-red-400"}">
        <span>${up ? "▲" : "▼"}</span>
        <span>${Math.abs(diffK).toFixed(2)}k</span>
        <span class="text-xs">${up ? "+" : "-"}${Math.abs(pctChange).toFixed(1)}%</span>
      </div>
    </div>`;
  }).join("");
}

function renderTrendChart(){
  const svg = document.getElementById("trendChart");
  if (!svg) return;

  const w = 640, h = 210;
  svg.setAttribute("viewBox", `0 0 ${w} ${h}`);
  svg.innerHTML = "";

  const cats = expenseData.categories || [];
  const months = expenseData.months || ["Jan","Feb","Mar","Apr","May","Jun"];

  const padX = 30, padTop = 15, padBottom = 24;
  const plotW = w - padX * 2;
  const plotH = h - padTop - padBottom;

  const allValues = cats.flatMap(c => c.trend).filter(v => v > 0);
  const maxVal = allValues.length > 0 ? Math.max(...allValues) * 1.1 : 1000;
  const minVal = allValues.length > 0 ? Math.min(...allValues) * 0.9 : 0;

  const yFor = (v) => padTop + (1 - (v - minVal) / (maxVal - minVal)) * plotH;
  const xFor = (i) => padX + (i * plotW / Math.max(months.length - 1, 1));


  [0, 0.25, 0.5, 0.75, 1].forEach(f => {
    const y = padTop + (1 - f) * plotH;
    const line = document.createElementNS("http://www.w3.org/2000/svg","line");
    line.setAttribute("x1", padX); line.setAttribute("x2", w - padX);
    line.setAttribute("y1", y); line.setAttribute("y2", y);
    line.setAttribute("stroke", "#1c3a6e"); line.setAttribute("stroke-width", "1");
    svg.appendChild(line);
  });

  const base = document.createElementNS("http://www.w3.org/2000/svg","line");
  base.setAttribute("x1", padX); base.setAttribute("x2", w - padX);
  base.setAttribute("y1", h - padBottom); base.setAttribute("y2", h - padBottom);
  base.setAttribute("stroke", "#1c3a6e"); base.setAttribute("stroke-width", "1");
  svg.appendChild(base);

  if (cats.length > 0) {
    cats.forEach(c => {
      if (c.trend.length === 0) {
        const points = months.map((_, i) => `${xFor(i)},${yFor(0)}`).join(" ");
        const poly = document.createElementNS("http://www.w3.org/2000/svg","polyline");
        poly.setAttribute("points", points);
        poly.setAttribute("fill", "none");
        poly.setAttribute("stroke", c.color);
        poly.setAttribute("stroke-width", "2");
        poly.setAttribute("stroke-linecap", "round");
        poly.setAttribute("stroke-linejoin", "round");
        poly.setAttribute("opacity", "0.5");
        svg.appendChild(poly);
        return;
      }
      const points = c.trend.map((v,i) => `${xFor(i)},${yFor(v)}`).join(" ");
      const poly = document.createElementNS("http://www.w3.org/2000/svg","polyline");
      poly.setAttribute("points", points);
      poly.setAttribute("fill", "none");
      poly.setAttribute("stroke", c.color);
      poly.setAttribute("stroke-width", "2.5");
      poly.setAttribute("stroke-linecap", "round");
      poly.setAttribute("stroke-linejoin", "round");
      svg.appendChild(poly);
    });
  } else {
    const points = months.map((_, i) => `${xFor(i)},${yFor(0)}`).join(" ");
    const poly = document.createElementNS("http://www.w3.org/2000/svg","polyline");
    poly.setAttribute("points", points);
    poly.setAttribute("fill", "none");
    poly.setAttribute("stroke", "#4ca6ff");
    poly.setAttribute("stroke-width", "2");
    poly.setAttribute("stroke-linecap", "round");
    poly.setAttribute("stroke-linejoin", "round");
    poly.setAttribute("opacity", "0.5");
    svg.appendChild(poly);
  }

  months.forEach((m, i) => {
    const x = xFor(i);
    const anchor = i === 0 ? "start" : (i === months.length - 1 ? "end" : "middle");
    const label = document.createElementNS("http://www.w3.org/2000/svg","text");
    label.setAttribute("x", x);
    label.setAttribute("y", h - 6);
    label.setAttribute("fill", "#9bb0d1");
    label.setAttribute("font-size", "11");
    label.setAttribute("text-anchor", anchor);
    label.textContent = m;
    svg.appendChild(label);
  });
}

function renderRangeMenu(){
  document.getElementById("rangeMenu").innerHTML = expenseData.rangeOptions.map(opt => `
    <button onclick="selectRange('${opt}')" class="w-full text-left px-3 py-2 hover:bg-navy-700 ${opt === expenseData.selectedRange ? 'text-blue-400' : ''}">${opt}</button>
  `).join("");
}

function toggleRangeMenu(){
  const el = document.getElementById("rangeMenu");
  const trigger = el.previousElementSibling;
  const arrow = trigger ? trigger.querySelector("svg") : null;
  const wasHidden = el.classList.contains("hidden");

  closeAllMenus();

  if (wasHidden) {
    el.classList.remove("hidden");
    if (arrow) arrow.classList.add("rotate-180");
  }
}

function closeAllMenus(){
  document.querySelectorAll(".dd-menu").forEach(m => m.classList.add("hidden"));
  document.querySelectorAll(".dd-toggle svg").forEach(svg => svg.classList.remove("rotate-180"));
}

document.addEventListener("click", (e) => {
  if (!e.target.closest(".dd-menu") && !e.target.closest(".dd-toggle")) closeAllMenus();
});

function selectRange(label){
  closeAllMenus();
  expenseData.selectedRange = label;
  document.getElementById("rangeBtnLabel").textContent = label;

  if (label === "LAST WEEK") {
    expenseData.months = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
  } else if (label === "LAST MONTH") {
    expenseData.months = ["Week 1","Week 2","Week 3","Week 4"];
  } else if (label === "LAST YEAR") {
    expenseData.months = ["Aug","Sep","Oct","Nov","Dec","Jan","Feb","Mar","Apr","May","Jun","Jul"];
  } else {
    expenseData.months = ["Jan","Feb","Mar","Apr","May","Jun"];
  }

  renderAll();
  renderRangeMenu();
}

function renderAll(){
  renderLegend();
  renderTotals();
  renderCategoryCards();
  renderTrendChart();
}

renderRangeMenu();
window.addEventListener("resize", () => renderTrendChart());

if (expenseData.categories.length === 0) {
  renderAll();
}
</script>

</body>
</html>