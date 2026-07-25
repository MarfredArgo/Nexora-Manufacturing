  /* ---------- Generic table filter (search box) ---------- */
  function filterTable(tableId, q){
    q = (q||'').trim().toLowerCase();
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
    rows.forEach(row => {
      const txt = row.innerText.toLowerCase();
      row.style.display = (!q || txt.indexOf(q) >= 0) ? '' : 'none';
    });
  }

  function filterByStatus(tableId, status, element){
    const chartContainer = element.closest('.status-chart');
    
    // Check if clicking the same active item - if so, clear filter
    if(element.classList.contains('active')){
      element.classList.remove('active');
      const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
      rows.forEach(row => row.style.display = '');
      return;
    }
    
    // Update active state on clicked element
    if(chartContainer){
      chartContainer.querySelectorAll('.status-chart-item').forEach(item => item.classList.remove('active'));
      element.classList.add('active');
    }
    
    // Filter table rows
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
    rows.forEach(row => {
      if(!status || row.dataset.status === status){
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function updateStatusCounts(){
    // Update requisition status counts
    const reqRows = document.querySelectorAll('#requisitions-table tbody tr');
    const reqCounts = {pending:0, processing:0, 'intransit':0, delivered:0, completed:0, cancelled:0};
    reqRows.forEach(row => {
      const s = row.dataset.status;
      if(reqCounts[s] !== undefined) reqCounts[s]++;
    });
    document.querySelectorAll('#requisition-status-chart .status-chart-item').forEach(item => {
      const status = item.dataset.status;
      item.querySelector('.status-count').textContent = reqCounts[status] || 0;
    });
    
    // Update PO status counts
    const poRows = document.querySelectorAll('#po-table tbody tr');
    const poCounts = {pending:0, approved:0, rejected:0, cancelled:0, processing:0, completed:0};
    poRows.forEach(row => {
      const s = row.dataset.status;
      if(poCounts[s] !== undefined) poCounts[s]++;
    });
    document.querySelectorAll('#po-status-chart .status-chart-item').forEach(item => {
      const status = item.dataset.status;
      item.querySelector('.status-count').textContent = poCounts[status] || 0;
    });
    
    // Update delivery status counts
    const delRows = document.querySelectorAll('#deliveries-table tbody tr');
    const delCounts = {pending:0, scheduled:0, 'intransit':0, delayed:0, delivered:0, completed:0, cancelled:0};
    delRows.forEach(row => {
      const s = row.dataset.status;
      if(delCounts[s] !== undefined) delCounts[s]++;
    });
    document.querySelectorAll('#delivery-status-chart .status-chart-item').forEach(item => {
      const status = item.dataset.status;
      item.querySelector('.status-count').textContent = delCounts[status] || 0;
    });
  }

  /* ---------- Filter Panel Functions ---------- */
  function toggleFilterPanel(panelId, btn){
    const panel = document.getElementById(panelId);
    if(!panel) return;
    panel.classList.toggle('hidden');
    btn.classList.toggle('active', !panel.classList.contains('hidden'));
  }
  
  function applyPOFilter(){
    const status = document.getElementById('po-filter-status').value;
    const dateFrom = document.getElementById('po-filter-date-from').value;
    const dateTo = document.getElementById('po-filter-date-to').value;
    const amount = document.getElementById('po-filter-amount').value;
    const rows = document.querySelectorAll('#po-table tbody tr');
    rows.forEach(row => {
      let show = true;
      if(status && row.dataset.status !== status) show = false;
      if(dateFrom && row.dataset.date < dateFrom) show = false;
      if(dateTo && row.dataset.date > dateTo) show = false;
      if(amount){
        const rowAmt = parseFloat(row.dataset.amount||0);
        if(amount === '0-10000' && rowAmt >= 10000) show = false;
        if(amount === '10000-50000' && (rowAmt < 10000 || rowAmt > 50000)) show = false;
        if(amount === '50000+' && rowAmt <= 50000) show = false;
      }
      row.style.display = show ? '' : 'none';
    });
  }
  
  function clearPOFilter(){
    document.getElementById('po-filter-status').value = '';
    document.getElementById('po-filter-date-from').value = '';
    document.getElementById('po-filter-date-to').value = '';
    document.getElementById('po-filter-amount').value = '';
    document.querySelectorAll('#po-table tbody tr').forEach(row => row.style.display = '');
  }

  function applySupplierFilter(){
    const status = (document.getElementById('supplier-filter-status')?.value || '').toLowerCase();
    const brand = (document.getElementById('supplier-filter-brand')?.value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('#suppliers-table tbody tr');
    rows.forEach(row => {
      const rowStatus = (row.dataset.status || 'active').toLowerCase();
      const rowBrand = (row.dataset.category || '').trim().toLowerCase();
      const show = (!status || rowStatus === status) && (!brand || rowBrand.includes(brand));
      row.style.display = show ? '' : 'none';
    });
  }

  function clearSupplierFilter(){
    document.getElementById('supplier-filter-status').value = '';
    document.getElementById('supplier-filter-brand').value = '';
    document.querySelectorAll('#suppliers-table tbody tr').forEach(row => row.style.display = '');
  }

  function applyReqFilter(){
    const status = (document.getElementById('req-filter-status')?.value || '').toLowerCase();
    const dateFrom = document.getElementById('req-filter-date-from')?.value || '';
    const dateTo = document.getElementById('req-filter-date-to')?.value || '';
    const priority = (document.getElementById('req-filter-priority')?.value || '').toLowerCase();
    const rows = document.querySelectorAll('#requisitions-table tbody tr');
    rows.forEach(row => {
      const rowStatus = (row.dataset.status || '').toLowerCase();
      const rowDate = (row.dataset.date || '').toLowerCase();
      const rowPriority = (row.dataset.priority || '').toLowerCase();
      let show = true;
      if(status && rowStatus !== status) show = false;
      if(dateFrom && rowDate < dateFrom) show = false;
      if(dateTo && rowDate > dateTo) show = false;
      if(priority && rowPriority !== priority) show = false;
      row.style.display = show ? '' : 'none';
    });
  }
  
  function clearReqFilter(){
    document.getElementById('req-filter-status').value = '';
    document.getElementById('req-filter-date-from').value = '';
    document.getElementById('req-filter-date-to').value = '';
    document.getElementById('req-filter-priority').value = '';
    document.querySelectorAll('#requisitions-table tbody tr').forEach(row => row.style.display = '');
  }

  function applyDelFilter(){
    const status = (document.getElementById('delivery-filter-status')?.value || '').toLowerCase();
    const dateFrom = document.getElementById('delivery-filter-date-from')?.value || '';
    const dateTo = document.getElementById('delivery-filter-date-to')?.value || '';
    const supplier = (document.getElementById('delivery-filter-supplier')?.value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('#deliveries-table tbody tr');
    rows.forEach(row => {
      const rowStatus = (row.dataset.status || '').toLowerCase();
      const rowDate = (row.dataset.date || '').toLowerCase();
      const rowSupplier = (row.dataset.sup || '').trim().toLowerCase();
      let show = true;
      if(status && rowStatus !== status) show = false;
      if(dateFrom && rowDate < dateFrom) show = false;
      if(dateTo && rowDate > dateTo) show = false;
      if(supplier && !rowSupplier.includes(supplier)) show = false;
      row.style.display = show ? '' : 'none';
    });
  }

  function clearDelFilter(){
    document.getElementById('delivery-filter-status').value = '';
    document.getElementById('delivery-filter-date-from').value = '';
    document.getElementById('delivery-filter-date-to').value = '';
    document.getElementById('delivery-filter-supplier').value = '';
    document.querySelectorAll('#deliveries-table tbody tr').forEach(row => row.style.display = '');
  }

  /* ---------- Status filter tabs (PO / Req / Inv / Deliveries) ---------- */
  function bindStatusTabs(tabsId, tableId){
    const tabs = document.querySelectorAll('#' + tabsId + ' .tab');
    tabs.forEach(t => {
      t.addEventListener('click', () => {
        tabs.forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const f = t.dataset.filter;
        document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
          row.style.display = (f === 'all' || row.dataset.status === f) ? '' : 'none';
        });
      });
    });
  }
  bindStatusTabs('po-filter-tabs', 'po-table');
  bindStatusTabs('req-filter-tabs', 'requisitions-table');
  bindStatusTabs('inv-filter-tabs', 'invoices-table');
  bindStatusTabs('del-filter-tabs', 'deliveries-table');

  /* ---------- Sortable columns (up/down arrows) ---------- */
  function parseSortValue(td, key){
    if(!td) return '';
    const row = td.closest('tr');
    if(row && row.dataset[key] != null) return row.dataset[key];
    if(row && key && row.dataset[key.toLowerCase()] != null) return row.dataset[key.toLowerCase()];
    let t = td.innerText.trim();
    if(/^₱?[\d,]+(\.\d+)?$/.test(t.replace(/\s/g,''))){
      return parseFloat(t.replace(/[^0-9.]/g,''));
    }
    const d = Date.parse(t);
    if(!isNaN(d)) return d;
    return t.toLowerCase();
  }

  document.querySelectorAll('.sortable-table').forEach(table => {
    const headers = table.querySelectorAll('th.sortable');
    headers.forEach((th, idx) => {
      th.addEventListener('click', () => {
        const currentAsc = th.classList.contains('sort-asc');
        const currentDesc = th.classList.contains('sort-desc');
        headers.forEach(h => h.classList.remove('sort-asc','sort-desc'));
        const asc = !currentAsc;
        th.classList.add(asc ? 'sort-asc' : 'sort-desc');

        const key = th.dataset.key || '';
        const tbody = table.querySelector('tbody');
        const rows = [...tbody.querySelectorAll('tr')];
        rows.sort((a,b) => {
          const av = parseSortValue(a.children[idx], key);
          const bv = parseSortValue(b.children[idx], key);
          if(av < bv) return asc ? -1 : 1;
          if(av > bv) return asc ? 1 : -1;
          return 0;
        });
        rows.forEach(r => tbody.appendChild(r));
      });
    });
  });

  /* ---------- Dashboard PO tab switcher ---------- */
  document.querySelectorAll('#dash-po-tabs .tab').forEach(t => {
    t.addEventListener('click', () => {
      document.querySelectorAll('#dash-po-tabs .tab').forEach(x => x.classList.remove('active'));
      t.classList.add('active');
      const f = t.dataset.filter;
      const rows = document.querySelectorAll('#dash-po-table tbody tr');
      rows.forEach(r => {
        if(f === 'recent'){ r.style.display = ''; }
        else { r.style.display = (r.dataset.status === f) ? '' : 'none'; }
      });
    });
  });

