
  /* ---------- Page switching ---------- */
  const ALL_PAGES = ['dashboard','purchase-orders','suppliers','requisitions','invoices','deliveries','approvals','reports'];
  function toggleNotifPanel(e){
    e?.stopPropagation();
    const panel = document.getElementById('notif-panel');
    if(!panel) return;
    const willOpen = !panel.classList.contains('open');
    panel.classList.toggle('open');
    if(willOpen){ loadNotifications(panel); }
  }

  async function loadNotifications(panel){
    if(!panel) return;
    panel.innerHTML = '<div style="padding:12px;text-align:center;color:#7b8796">Loading notifications…</div>';
    try{
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      // Fetch recent requisitions
      const [reqRes, delRes] = await Promise.all([
        fetch(procurementUrl('requisitions'), { headers }),
        fetch(procurementUrl('deliveries'), { headers })
      ]);
      const reqJson = await safeJson(reqRes);
      const delJson = await safeJson(delRes);
      const reqs = Array.isArray(reqJson)
        ? reqJson
        : (reqJson && Array.isArray(reqJson.data) ? reqJson.data : []);
      const dels = Array.isArray(delJson)
        ? delJson
        : (delJson && Array.isArray(delJson.data) ? delJson.data : []);

      // Build items
      const items = [];
      // Prioritize pending requisitions
      (reqs || []).slice(0,5).forEach(r => {
        items.push({ type: 'req', title: r.rq || r.ref || r.id || 'Requisition', text: `${r.item || ''} · Qty ${r.qty || r.quantity || ''}`, meta: r.requester || r.dept || '' });
      });
      // Add deliveries with relevant statuses
      (dels || []).slice(0,5).forEach(d => {
        items.push({ type: 'del', title: d.dr || d.id || d.ref || 'Delivery', text: `${d.items || d.items || ''} · Qty ${d.qty || ''}`, meta: d.status || '' });
      });

      if(items.length === 0){
        panel.innerHTML = `<div class="notif-item ok"><span class="notif-icon">✓</span><div class="notif-content"><strong>No alerts</strong>You have no new notifications right now.<small>System · live</small></div></div>`;
        updateNavCounts(0,0);
        return;
      }

      panel.innerHTML = '';
      let reqCount = 0, delCount = 0;
      items.forEach(it => {
        const div = document.createElement('div');
        div.className = 'notif-item ' + (it.type === 'req' ? 'warn' : 'ok');
        div.innerHTML = `<span class="notif-icon">${it.type==='req' ? 'R' : 'D'}</span><div class="notif-content"><strong>${escapeHtml(it.title)}</strong><div>${escapeHtml(it.text)}</div><small>${escapeHtml(it.meta)}</small></div>`;
        panel.appendChild(div);
        if(it.type === 'req') reqCount++; else if(it.type === 'del') delCount++;
      });
      updateNavCounts(reqCount, delCount);
    }catch(err){
      panel.innerHTML = `<div style="padding:12px;color:#c34">Unable to load notifications</div>`;
      console.error('loadNotifications', err);
    }
  }

  async function safeJson(res){
    try{ return await res.json(); }catch(e){ return null; }
  }

  function escapeHtml(s){ return (s||'').toString().replace(/[&<>"]+/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]||c)); }

  function updateNavCounts(reqCount, delCount){
    const reqBadge = document.querySelector("a[href*='requisitions'] .nav-badge");
    const delBadge = document.querySelector("a[href*='deliveries'] .nav-badge");
    if(reqBadge){ reqBadge.textContent = reqCount; reqBadge.classList.toggle('red', reqCount>0); }
    if(delBadge){ delBadge.textContent = delCount; delBadge.classList.toggle('red', delCount>0); }
  }

  document.addEventListener('click', (e)=>{
    const panel = document.getElementById('notif-panel');
    if(panel && !panel.contains(e.target) && !e.target.closest('.notif-badge')){
      panel.classList.remove('open');
    }
  });

  function showPage(page, navEl){
    ALL_PAGES.forEach(p => {
      const sec = document.getElementById('page-' + p);
      if(sec) sec.classList.toggle('hidden', p !== page);
    });
    const active = document.getElementById('page-' + page);
    if(active){
      active.style.animation = 'none';
      void active.offsetWidth;
      active.style.animation = '';
    }
    document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
    if(navEl){ navEl.classList.add('active'); }

    // Trigger entrance animations that depend on layout
    if(page === 'dashboard') animateDashboard();
    if(page === 'deliveries') animateDeliveryPipeline();
    
    // Update status counts when viewing status chart pages
    if(page === 'requisitions' || page === 'purchase-orders' || page === 'deliveries'){
      updateStatusCounts();
    }
  }

  function animateDashboard(){
    // Bars grow
    document.querySelectorAll('#dash-category-bars .bar').forEach(b => { b.style.height = '0px'; });
    setTimeout(()=>{
      document.querySelectorAll('#dash-category-bars .bar').forEach(b => { b.style.height = (b.dataset.h||0) + 'px'; });
    }, 60);
    // Donut redraw
    document.querySelectorAll('#po-status-donut .donut-seg').forEach(seg => {
      seg.setAttribute('stroke-dasharray', '0 427.3');
    });
    setTimeout(()=>{
      document.querySelectorAll('#po-status-donut .donut-seg').forEach(seg => {
        seg.setAttribute('stroke-dasharray', seg.dataset.dasharray);
        seg.setAttribute('stroke-dashoffset', seg.dataset.dashoffset);
      });
    }, 120);
    // Top supplier bars
    document.querySelectorAll('#page-dashboard .ts-bar-fill').forEach(bar => { bar.style.width = '0'; });
    setTimeout(()=>{
      document.querySelectorAll('#page-dashboard .ts-bar-fill').forEach(bar => { bar.style.width = bar.dataset.w; });
    }, 240);
  }

  function animateDeliveryPipeline(){
    document.querySelectorAll('#page-deliveries .ts-bar-fill').forEach(bar => { bar.style.width = '0'; });
    setTimeout(()=>{
      document.querySelectorAll('#page-deliveries .ts-bar-fill').forEach(bar => { bar.style.width = bar.dataset.w; });
    }, 180);
  }

  /* ---------- Toasts ---------- */
  function showToast(message, kind){
    const stack = document.getElementById('toast-stack');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `<span class="toast-dot ${kind}"></span><span>${message}</span>`;
    stack.appendChild(toast);
    setTimeout(()=>{
      toast.classList.add('leaving');
      setTimeout(()=> toast.remove(), 260);
    }, 2600);
  }

  /* ---------- Stat helpers ---------- */
  function bumpStat(id, delta){
    const el = document.getElementById(id);
    if(!el) return;
    el.textContent = Math.max(0, parseInt(el.textContent,10) + delta);
    el.classList.remove('bump');
    void el.offsetWidth;
    el.classList.add('bump');
  }

  function refreshTabCounts(){
    const rows = document.querySelectorAll('#approval-tabs ~ .queue-row, .queue-row');
    const all = document.querySelectorAll('.queue-row').length;
    const po = document.querySelectorAll('.queue-row[data-type="po"]').length;
    const req = document.querySelectorAll('.queue-row[data-type="req"]').length;
    const inv = document.querySelectorAll('.queue-row[data-type="inv"]').length;
    const countAllEl = document.getElementById('count-all');
    if(countAllEl) countAllEl.textContent = all;
    const countPoEl = document.getElementById('count-po');
    if(countPoEl) countPoEl.textContent = po;
    const countReqEl = document.getElementById('count-req');
    if(countReqEl) countReqEl.textContent = req;
    const countInvEl = document.getElementById('count-inv');
    if(countInvEl) countInvEl.textContent = inv;
    const approvalBadgeEl = document.getElementById('approval-nav-badge');
    if(approvalBadgeEl) approvalBadgeEl.textContent = all;
    const statPendingEl = document.getElementById('stat-pending');
    if(statPendingEl) statPendingEl.textContent = all;
    const sub = document.getElementById('stat-pending-sub');
    if(sub) sub.textContent = all === 0 ? '✓ All clear' : '● Needs action';
    checkEmpty();
  }

  function checkEmpty(){
    const activeFilter = document.querySelector('#approval-tabs .tab.active')?.dataset.filter || 'all';
    const visible = [...document.querySelectorAll('.queue-row')].filter(r =>
      !r.classList.contains('removing') && (activeFilter==='all' || r.dataset.type===activeFilter)
    );
    const emptyEl = document.getElementById('queue-empty');
    if(emptyEl) emptyEl.classList.toggle('show', visible.length === 0);
  }

  /* ---------- Approve / Reject ---------- */
  function handleDecision(btn, action){
    const row = btn.closest('.queue-row');
    const ref = row.dataset.ref;
    btn.closest('.qactions').querySelectorAll('button').forEach(b => b.disabled = true);
    row.classList.add('removing');

    if(action === 'approve'){
      bumpStat('stat-approved', 1);
      showToast(`${ref} approved`, 'ok');
    } else {
      bumpStat('stat-rejected', 1);
      showToast(`${ref} rejected`, 'no');
    }

    setTimeout(()=>{
      row.remove();
      refreshTabCounts();
    }, 340);
  }
