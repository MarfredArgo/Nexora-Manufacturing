  /* ---------- Tabs (filter queue) ---------- */
  function setActiveFilter(filter){
    document.querySelectorAll('#approval-tabs .tab').forEach(t=>{
      t.classList.toggle('active', t.dataset.filter === filter);
    });
    document.querySelectorAll('.queue-row').forEach(row=>{
      const match = filter === 'all' || row.dataset.type === filter;
      row.classList.toggle('filtered-out', !match);
    });
    checkEmpty();
  }
  document.querySelectorAll('#approval-tabs .tab').forEach(tab=>{
    tab.addEventListener('click', ()=> setActiveFilter(tab.dataset.filter));
  });

  function filterQueueByType(type){
    setActiveFilter(type);
    document.getElementById('approval-tabs').scrollIntoView({behavior:'smooth', block:'nearest'});
    const labelMap = { po:'Purchase Orders', req:'Requisitions', inv:'Invoices', all:'all requests' };
    showToast(`Filtered queue to ${labelMap[type]}`, 'info');
  }

  /* ---------- Donut chart: draw-in + hover sync + click filter ---------- */
  function initDonut(){
    const segs = document.querySelectorAll('.donut-seg');
    // draw-in animation: start collapsed, then apply real dasharray/offset
    requestAnimationFrame(()=>{
      setTimeout(()=>{
        segs.forEach(seg=>{
          seg.setAttribute('stroke-dasharray', seg.dataset.dasharray);
          seg.setAttribute('stroke-dashoffset', seg.dataset.dashoffset);
        });
      }, 120);
    });

    const centerVal = document.getElementById('donut-center-val');
    const centerLabel = document.getElementById('donut-center-label');
    const hole = document.getElementById('donut-center');
    const defaultVal = centerVal.textContent;

    function light(type){
      segs.forEach(seg=>{
        const match = seg.dataset.type === type;
        seg.classList.toggle('dim', type && !match);
        seg.classList.toggle('raise', type && match);
      });
      document.querySelectorAll('.legend-row').forEach(row=>{
        row.style.background = (type && row.dataset.type === type) ? 'var(--bg)' : '';
      });
      if(type){
        const seg = document.querySelector(`.donut-seg[data-type="${type}"]`);
        centerVal.textContent = seg.dataset.pct + '%';
        centerLabel.textContent = seg.dataset.label;
        hole.classList.add('active');
      } else {
        centerVal.textContent = defaultVal;
        centerLabel.textContent = 'total';
        hole.classList.remove('active');
      }
    }

    segs.forEach(seg=>{
      seg.addEventListener('mouseenter', ()=> light(seg.dataset.type));
      seg.addEventListener('mouseleave', ()=> light(null));
      seg.addEventListener('click', ()=> filterQueueByType(seg.dataset.type === 'other' ? 'all' : seg.dataset.type));
    });
    document.querySelectorAll('.legend-row').forEach(row=>{
      row.addEventListener('mouseenter', ()=> light(row.dataset.type));
      row.addEventListener('mouseleave', ()=> light(null));
    });
  }

  /* ---------- Report date-range chips ---------- */
  const rangeData = {
    mtd:    { label: 'Jul 1 – Jul 7, 2026',   values: [142,158,149,171,164,189], months:['Feb','Mar','Apr','May','Jun','Jul'] },
    quarter:{ label: 'Apr 1 – Jul 7, 2026',   values: [149,171,164,189,201,214], months:['Apr','May','Jun','Jul','Aug*','Sep*'] },
    ytd:    { label: 'Jan 1 – Jul 7, 2026',   values: [131,138,149,171,164,189,201], months:['Jan','Feb','Mar','Apr','May','Jun','Jul'] },
    custom: { label: 'Choose a custom range', values: [142,158,149,171,164,189], months:['Feb','Mar','Apr','May','Jun','Jul'] }
  };
  document.querySelectorAll('#report-chips .chip').forEach(chip=>{
    chip.addEventListener('click', ()=>{
      document.querySelectorAll('#report-chips .chip').forEach(c=>c.classList.remove('active'));
      chip.classList.add('active');
      const range = rangeData[chip.dataset.range];
      document.getElementById('date-range-label').lastChild.textContent = ' ' + range.label;
      const maxVal = Math.max(...range.values);
      const bars = document.getElementById('spend-bars');
      bars.innerHTML = range.values.map((v,i)=>{
        const isLast = i === range.values.length - 1;
        const h = Math.round((v/maxVal)*170);
        return `<div class="bar-col">
                  <div class="bar-val">$${v}k</div>
                  <div class="bar" style="height:0px;background:${isLast ? 'var(--blue)' : '#c9d8fb'};transition:height .5s ease;" data-h="${h}"></div>
                  <div class="bar-label">${range.months[i]}</div>
                </div>`;
      }).join('');
      requestAnimationFrame(()=>{
        bars.querySelectorAll('.bar').forEach(b => b.style.height = b.dataset.h + 'px');
      });
      showToast(`Showing ${chip.textContent.trim()}`, 'info');
    });
  });

  /* ---------- Generate / Download reports ---------- */
  function handleGenerate(btn){
    const row = btn.closest('.report-row');
    const name = row.dataset.report;
    const original = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = `<span class="spin-icon" style="display:inline-block;">⟳</span> Generating`;
    setTimeout(()=>{
      btn.innerHTML = '✓ Generated';
      const meta = row.querySelector('[data-meta]');
      meta.textContent = 'Last generated Just now';
      showToast(`${name} generated`, 'ok');
      setTimeout(()=>{
        btn.disabled = false;
        btn.textContent = original;
      }, 1400);
    }, 1200);
  }

  function handleDownload(btn){
    const row = btn.closest('.report-row');
    const name = row.dataset.report;
    const format = row.dataset.format.toUpperCase();
    const original = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Downloading…';
    setTimeout(()=>{
      btn.textContent = original;
      btn.disabled = false;
      showToast(`${name}.${row.dataset.format} downloaded`, 'info');
    }, 900);
  }

  /* ---------- Dashboard entrance animations ---------- */
  function animateDashboard(){
    // Animate stat cards with staggered delay
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, i) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(15px)';
      card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      setTimeout(() => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, 100 + (i * 80));
    });
    
    // Animate panels with staggered delay
    const panels = document.querySelectorAll('.dash-grid-3 .panel');
    panels.forEach((panel, i) => {
      panel.style.opacity = '0';
      panel.style.transform = 'translateY(20px)';
      panel.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      setTimeout(() => {
        panel.style.opacity = '1';
        panel.style.transform = 'translateY(0)';
      }, 200 + (i * 120));
    });
    
    // Animate chart bars (spend by brand)
    const chartBars = document.querySelectorAll('.chart-bar-fill');
    chartBars.forEach((bar, i) => {
      const width = bar.style.width;
      bar.style.width = '0';
      setTimeout(() => {
        bar.style.width = width;
      }, 400 + (i * 60));
    });
    
    // Animate supplier items
    const supplierItems = document.querySelectorAll('.supplier-item');
    supplierItems.forEach((item, i) => {
      item.style.opacity = '0';
      item.style.transform = 'translateX(-10px)';
      item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      setTimeout(() => {
        item.style.opacity = '1';
        item.style.transform = 'translateX(0)';
      }, 300 + (i * 50));
    });
    
    // Initialize donut if canvas exists
    const donutCanvas = document.getElementById('dash-donut');
    if (donutCanvas && window.dashboardData && window.dashboardData.poStatus) {
      initDonutFromData(donutCanvas, window.dashboardData.poStatus);
    }
  }
  
  /* ---------- Donut chart from data ---------- */
  function initDonutFromData(canvas, statusData){
    if (!canvas || !statusData || Object.keys(statusData).length === 0) return;
    
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 70;
    const lineWidth = 22;
    
    const colors = {
      pending: '#f2994a',
      processing: '#2f6fed',
      approved: '#1fa971',
      rejected: '#eb5757',
      cancelled: '#7c88a3',
      completed: '#14b8a6'
    };
    
    const total = Object.values(statusData).reduce((sum, val) => sum + val, 0);
    let startAngle = -Math.PI / 2;
    
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    Object.entries(statusData).forEach(([status, count]) => {
      const sliceAngle = (count / total) * 2 * Math.PI;
      const endAngle = startAngle + sliceAngle;
      
      ctx.beginPath();
      ctx.arc(centerX, centerY, radius, startAngle, endAngle);
      ctx.strokeStyle = colors[status] || '#ccc';
      ctx.lineWidth = lineWidth;
      ctx.lineCap = 'butt';
      ctx.stroke();
      
      startAngle = endAngle;
    });
  }

