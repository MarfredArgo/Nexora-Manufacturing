  /* ---------- Record modals (view / edit / delete) ---------- */
  const PO_ITEM_HINTS = {
    'Cables & Misc':'Cable kits and accessory bundle',
    'Storage':'SSD and NAS storage units',
    'Power Supplies':'Server-grade PSU units',
    'Components':'Motherboards, RAM, and board-level parts',
    'Cases & Cooling':'Cases, fans, and cooling accessories'
  };

  function htmlEscape(v){
    return String(v ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
  }
  function textFrom(node){ return (node?.textContent || '').trim(); }
  function parseMoney(v){ return Number(String(v || '').replace(/[^0-9.]/g,'')) || 0; }
  function money(v){ return '₱' + Number(v || 0).toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:2}); }
  function supplierNameFromCell(cell){
    if(!cell) return '';
    const pill = cell.querySelector('.supplier-pill');
    if(!pill) return textFrom(cell);
    const clone = pill.cloneNode(true);
    const badge = clone.querySelector('.supplier-badge');
    if(badge) badge.remove();
    return textFrom(clone);
  }
  function supplierBadgeColor(name){
    const map = {'GigaCore Components':'#22c55e','Global Tech Supply':'#0ea5e9','MegaStar Trading':'#f2994a','Primo Electronics':'#22c55e','Quantum Motherboards':'#7a5af0','Silverline PSU Ltd':'#eb5757','Silverline PSU Ltd.':'#eb5757','TechWholesale PH':'#2f6fed','Trident RAM Supply':'#0ea5e9'};
    return map[name] || randomColor(name || 'supplier');
  }
  function supplierPill(name){ return `<span class="supplier-pill"><span class="supplier-badge" style="background:${supplierBadgeColor(name)}">${initials(name || 'NA')}</span>${htmlEscape(name || 'Unknown Supplier')}</span>`; }
  function deliveryBadge(status){
    const cls = String(status || 'scheduled').toLowerCase().replace(/\s+/g,'');
    const label = status || 'Scheduled';
    return `<span class="del-status ${cls}"><span class="dstat-dot"></span>${htmlEscape(label)}</span>`;
  }
  function statusPill(status){
    const raw = String(status || 'Pending');
    const clsMap = {'Approved':'approved','Pending':'pending','Processing':'processing','Rejected':'rejected','Completed':'completed','Paid':'paid','Unpaid':'unpaid','intransit':'intransit','Delivered':'delivered','Delayed':'delayed','Scheduled':'scheduled','Active':'approved','Inactive':'pending','Blacklisted':'rejected'};
    const cls = clsMap[raw] || raw.toLowerCase().replace(/\s+/g,'');
    return `<span class="status-pill ${cls}">${htmlEscape(raw)}</span>`;
  }
  function getTableType(row){
    const id = row?.closest('table')?.id;
    return ({'po-table':'po','suppliers-table':'supplier','requisitions-table':'req','invoices-table':'invoice','deliveries-table':'delivery'})[id] || '';
  }
  function resolveSupplierByPO(po){
    const found = [...document.querySelectorAll('#po-table tbody tr')].find(r => textFrom(r.children[0]) === po);
    return found ? supplierNameFromCell(found.children[1]) : '—';
  }
  function normalizePoStatus(status){
    const map = {
      'approved': 'approved',
      'pending': 'pending',
      'rejected': 'rejected',
      'cancelled': 'cancelled',
      'processing': 'processing',
      'completed': 'completed'
    };
    const key = String(status || '').trim().toLowerCase();
    return map[key] || key;
  }

  function persistPurchaseOrderStatus(row, status){
    if(!row || !row.dataset.id) return Promise.resolve();
    const id = row.dataset.id;
    const normalizedStatus = normalizePoStatus(status);
    return fetch(procurementUrl(`purchase-orders/${id}`), {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: new URLSearchParams({ status: normalizedStatus }).toString()
    }).then(async response => {
      if (!response.ok) {
        throw new Error(`PO status update failed (${response.status})`);
      }

      return response.json();
    });
  }
  function persistRequisitionStatus(row, status){
    if(!row || !row.dataset.id) return Promise.resolve();
    const id = row.dataset.id;
    return fetch(procurementUrl(`requisitions/${id}`), {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: new URLSearchParams({ status: status }).toString()
    }).then(async response => {
      if (!response.ok) {
        throw new Error(`Requisition status update failed (${response.status})`);
      }

      return response.json();
    });
  }
  function syncRelatedRequisitionStatusForPO(row, poStatus){
    if(!row) return;
    const lookupRef = row.dataset.reqRef || textFrom(row.children[0]);
    const reqRow = findReqRowByRef(lookupRef);
    if(!reqRow) return;
    const reqStatus = poStatus === 'Approved' ? 'Processing' : (poStatus === 'Rejected' ? 'Pending' : (poStatus === 'Completed' ? 'Completed' : poStatus));
    updateRequisitionStatus(lookupRef, reqStatus);
    persistRequisitionStatus(reqRow, reqStatus);
  }
  function inferSupplierCategory(name){
    const found = [...document.querySelectorAll('#po-table tbody tr')].find(r => supplierNameFromCell(r.children[1]) === name);
    return found ? textFrom(found.children[2]) : 'General Procurement';
  }
  function getSupplierProducts(row){
    const raw = row?.dataset?.products || '';
    if(!raw) return [];
    try { return JSON.parse(raw); } catch { return []; }
  }

  function getSupplierCatalogEntry(name){
    const key = String(name || '').trim();
    if(!key) return null;
    // First check client-side cached catalog
    if(window.SUPPLIER_CATALOG && window.SUPPLIER_CATALOG[key]){
      return window.SUPPLIER_CATALOG[key];
    }
    // Fallback to reading from suppliers table rows in DOM
    const supplierRow = [...document.querySelectorAll('#suppliers-table tbody tr')].find(r => supplierNameFromCell(r.children[0]) === key || textFrom(r.children[0]) === key);
    if(supplierRow){
      const rowBrand = supplierRow.dataset.category || supplierRow.dataset.brand || '';
      const products = getSupplierProducts(supplierRow);
      const entry = { brand: rowBrand || key, products: products.map(p => ({ name: p.name, unitPrice: Number(p.price || p.unitPrice || 0) })) };
      // cache for later
      window.SUPPLIER_CATALOG = window.SUPPLIER_CATALOG || {};
      window.SUPPLIER_CATALOG[key] = entry;
      return entry;
    }
    return null;
  }

  function findPoRowByNumber(poNumber){
    return [...document.querySelectorAll('#po-table tbody tr')].find(row => textFrom(row.children[0]) === poNumber);
  }

  function findReqRowByRef(ref){
    return [...document.querySelectorAll('#requisitions-table tbody tr')].find(row => textFrom(row.children[0]) === ref || row.dataset.reqRef === ref || row.dataset.po === ref);
  }

  function updateRequisitionStatus(ref, status){
    const reqRow = findReqRowByRef(ref);
    if(!reqRow) return;
    updateRowStatus(reqRow, status);
    if(reqRow.children[6]) reqRow.children[6].innerHTML = statusPill(status);
  }

  function buildRecord(row){
    const type = getTableType(row);
    if(type === 'po'){
      const po = textFrom(row.children[0]);
      const supplier = supplierNameFromCell(row.children[1]);
      const item = row.dataset.item || textFrom(row.children[2]) || 'Procurement item';
      const qty = Number(row.dataset.qty || textFrom(row.children[3]) || 0) || 0;
      const amount = Number(row.dataset.amount || parseMoney(textFrom(row.children[4])) || 0);
      const unitPrice = Number(row.dataset.unitPrice || (qty ? amount / qty : parseMoney(textFrom(row.children[3])))) || 0;
      const priority = row.dataset.priority || 'Normal';
      const delivery = row.dataset.delivery || 'Scheduled';
      const status = textFrom(row.children[6]);
      const date = textFrom(row.children[7]);
      const category = row.dataset.brand || row.dataset.category || 'General Procurement';
      return {type, key:po, title:`Purchase Order · ${po}`, po, supplier, category, item, qty, amount, unitPrice:unitPrice ? money(unitPrice) : '—', delivery, priority, status, date, time:row.dataset.time || '09:00 AM', expected:row.dataset.expected || '—', requestedBy:row.dataset.requestedBy || 'Procurement Team', remarks:row.dataset.remarks || 'Standard purchase order workflow.'};
    }
    if(type === 'supplier'){
      const name = supplierNameFromCell(row.children[0]);
      const products = getSupplierProducts(row);
      const brand = row.dataset.brand || row.dataset.category || 'General Procurement';
      return {type, key:name, title:`Supplier · ${name}`, sid:row.dataset.sid || '', name, contact:textFrom(row.children[2]), email:textFrom(row.children[3]), phone:textFrom(row.children[4]) || '', address:textFrom(row.children[5]) || '', category:brand, status:row.dataset.status || 'Active', terms:row.dataset.terms || 'Net 30', lastActivity:row.dataset.lastActivity || 'Recent PO activity', products};
    }
    if(type === 'req'){
      const ref = textFrom(row.children[0]);
      return {type, key:ref, title:`Requisition · ${ref}`, ref, item:textFrom(row.children[1]), qty:Number(textFrom(row.children[2])) || 0, delivery:textFrom(row.children[3]), dept:textFrom(row.children[4]), requester:textFrom(row.children[5]), status:textFrom(row.children[6]), date:textFrom(row.children[7]), time:row.dataset.time || '10:30 AM', uom:row.dataset.uom || 'pcs', notes:row.dataset.notes || `Requested for ${textFrom(row.children[4])} operations.`, po:textFrom(row.dataset.po || ''), hasPO: row.dataset.hasPo === '1'};
    }
    if(type === 'invoice'){
      const inv = textFrom(row.children[0]);
      const po = textFrom(row.children[1]);
      const supplier = row.dataset.supplier || resolveSupplierByPO(po);
      return {type, key:inv, title:`Invoice · ${inv}`, inv, po, supplier, date:textFrom(row.children[2]), dueDate:row.dataset.dueDate || textFrom(row.children[2]), amount:parseMoney(textFrom(row.children[3])), status:textFrom(row.children[4]), method:row.dataset.method || 'Bank Transfer', notes:row.dataset.notes || 'Invoice recorded against the linked purchase order.'};
    }
    if(type === 'delivery'){
      const ship = textFrom(row.children[0]);
      return {type, key:ship, title:`Shipment · ${ship}`, ship, po:textFrom(row.children[1]), supplier:supplierNameFromCell(row.children[2]), stage:row.dataset.stage || '0', status:textFrom(row.children[4]), date:textFrom(row.children[5]), note:row.dataset.note || 'Shipment tracking entry.', carrier:row.dataset.carrier || 'Assigned carrier'};
    }
    return {type:'', key:'', title:'Record'};
  }
  function setViewActions(left, right, po){
    const rejectBtn = document.getElementById('modal-reject-btn');
    const approveBtn = document.getElementById('modal-approve-btn');
    const poBtn = document.getElementById('modal-po-btn');
    const bind = (btn, cfg, fallbackClass) => {
      if(!cfg){ btn.style.display = 'none'; btn.onclick = null; return; }
      btn.style.display = '';
      btn.textContent = cfg.label;
      btn.className = `btn ${cfg.className || fallbackClass}`;
      btn.onclick = cfg.onClick;
    };
    bind(left ? rejectBtn : rejectBtn, left, 'btn-view');
    bind(right ? approveBtn : approveBtn, right, 'btn-approve');
    if(poBtn){
      if(!po){ poBtn.style.display = 'none'; poBtn.onclick = null; }
      else{ poBtn.style.display = ''; poBtn.onclick = po.onClick; }
    }
  }

  function setViewModalHeader(row, record){
    const modal = document.getElementById('view-modal');
    const editBtn = document.getElementById('modal-header-edit-btn');
    const deleteBtn = document.getElementById('modal-header-delete-btn');
    const isSupplier = record?.type === 'supplier';
    modal.__row = row || null;
    editBtn.style.display = isSupplier ? '' : 'none';
    deleteBtn.style.display = isSupplier ? '' : 'none';
    if(isSupplier && row){
      editBtn.onclick = () => openEditModal(row);
      deleteBtn.onclick = () => openDeleteModal(row);
    } else {
      editBtn.onclick = null;
      deleteBtn.onclick = null;
    }
  }
  function updateRowStatus(row, status){
    const type = getTableType(row);
    row.dataset.status = String(status || '').toLowerCase().replace(/\s+/g,'-');
    const pillCell = type === 'delivery' ? row.children[5] : (type === 'invoice' ? row.children[4] : (type === 'req' ? row.children[6] : row.children[6]));
    if(pillCell) pillCell.innerHTML = statusPill(status);
  }
  function renderViewRecord(row){
    const record = buildRecord(row);
    document.getElementById('modal-title').textContent = record.title;
    setViewModalHeader(row, record);
    let body = '';
    if(record.type === 'po'){
      body = `<div class="detail-grid"><div class="detail-card"><h4>Order overview</h4><div class="modal-row"><span>PO number</span><span>${htmlEscape(record.po)}</span></div><div class="modal-row"><span>Supplier</span><span>${htmlEscape(record.supplier)}</span></div><div class="modal-row"><span>Brand</span><span>${htmlEscape(record.category)}</span></div><div class="modal-row"><span>Item</span><span>${htmlEscape(record.item)}</span></div><div class="modal-row"><span>Quantity</span><span>${record.qty || '—'}</span></div></div><div class="detail-card"><h4>Commercial details</h4><div class="modal-row"><span>Total amount</span><span>${money(record.amount)}</span></div><div class="modal-row"><span>Unit price</span><span>${record.unitPrice}</span></div><div class="modal-row"><span>Priority</span><span>${priorityBadge(record.priority || 'Normal')}</span></div><div class="modal-row"><span>Delivery status</span><span>${htmlEscape(record.delivery)}</span></div><div class="modal-row"><span>Status</span><span>${htmlEscape(record.status)}</span></div><div class="modal-row"><span>Date & time</span><span>${htmlEscape(record.date)} · ${htmlEscape(record.time)}</span></div></div><div class="detail-card full"><h4>Workflow</h4><div class="modal-row"><span>Requested by</span><span>${htmlEscape(record.requestedBy)}</span></div><div class="modal-row"><span>Expected delivery</span><span>${htmlEscape(record.expected)}</span></div></div></div><div class="detail-note"><b>Remarks</b><br>${htmlEscape(record.remarks)}</div>`;
      const statusKey = String(record.status || '').toLowerCase();
      if(statusKey === 'pending'){
        setViewActions(
          {label:'Reject PO', className:'btn-reject', onClick:()=>{ persistPurchaseOrderStatus(row, 'Rejected').then(() => { updateRowStatus(row, 'Rejected'); syncRelatedRequisitionStatusForPO(row, 'Rejected'); closeViewModal(); showToast(`${record.po} rejected`, 'no'); }).catch(() => showToast('Unable to reject this PO. It was not changed.', 'no')); }},
          {label:'Approve PO', className:'btn-approve', onClick:()=>{ persistPurchaseOrderStatus(row, 'Approved').then(() => { updateRowStatus(row, 'Approved'); syncRelatedRequisitionStatusForPO(row, 'Approved'); closeViewModal(); showToast(`${record.po} approved for fulfillment`, 'ok'); }).catch(() => showToast('Unable to approve this PO. It remains pending.', 'no')); }}
        );
      } else if(statusKey === 'approved'){
        setViewActions(
          {label:'Close', className:'btn-view', onClick:closeViewModal},
          {label:'Cancel PO', className:'btn-danger', onClick:()=> openCancelModalFromRow(row)}
        );
      } else {
        setViewActions(
          {label:'Close', className:'btn-view', onClick:closeViewModal},
          null
        );
      }
    } else if(record.type === 'supplier'){
      const productsMarkup = record.products?.length ? `<div class="supplier-product-inline">${record.products.map(p => `<span class="supplier-product-tag">${htmlEscape(p.name || 'Product')} · ${htmlEscape(p.sku || 'SKU')} · ₱${Number(p.price || 0).toFixed(2)}</span>`).join('')}</div>` : '<div class="modal-helper">No products added.</div>';
      body = `<div class="detail-grid"><div class="detail-card"><h4>Supplier profile</h4><div class="modal-row"><span>Name</span><span>${htmlEscape(record.name)}</span></div><div class="modal-row"><span>Contact</span><span>${htmlEscape(record.contact)}</span></div><div class="modal-row"><span>Email</span><span>${htmlEscape(record.email)}</span></div><div class="modal-row"><span>Phone</span><span>${htmlEscape(record.phone)}</span></div></div><div class="detail-card"><h4>Commercial profile</h4><div class="modal-row"><span>Brand</span><span>${htmlEscape(record.category)}</span></div><div class="modal-row"><span>Status</span><span>${htmlEscape(record.status)}</span></div><div class="modal-row"><span>Payment terms</span><span>${htmlEscape(record.terms)}</span></div><div class="modal-row"><span>Last activity</span><span>${htmlEscape(record.lastActivity)}</span></div></div><div class="detail-card full"><h4>Products</h4>${productsMarkup}</div><div class="detail-card full"><h4>Address</h4><div style="font-size:13px; line-height:1.55;">${htmlEscape(record.address)}</div></div></div>`;
      setViewActions({label:'Close', className:'btn-view', onClick:closeViewModal}, null);
    } else if(record.type === 'req'){
      body = `<div class="detail-grid"><div class="detail-card"><h4>Request details</h4><div class="modal-row"><span>Requisition no.</span><span>${htmlEscape(record.ref)}</span></div><div class="modal-row"><span>Item</span><span>${htmlEscape(record.item)}</span></div><div class="modal-row"><span>Quantity</span><span>${record.qty} ${htmlEscape(record.uom)}</span></div><div class="modal-row"><span>Delivery status</span><span>${htmlEscape(record.delivery)}</span></div></div><div class="detail-card"><h4>Request workflow</h4><div class="modal-row"><span>Department</span><span>${htmlEscape(record.dept)}</span></div><div class="modal-row"><span>Requested by</span><span>${htmlEscape(record.requester)}</span></div><div class="modal-row"><span>Status</span><span>${htmlEscape(record.status)}</span></div><div class="modal-row"><span>Date & time</span><span>${htmlEscape(record.date)} · ${htmlEscape(record.time)}</span></div></div></div><div class="detail-note"><b>Justification</b><br>${htmlEscape(record.notes)}</div>`;
      const poBtn = record.hasPO ? null : {label:'Create Purchase Order', className:'btn-primary', onClick:()=>{ convertReqToPO(record.ref, record.item, record.qty); closeViewModal(); }};
      setViewActions({label:'Close', className:'btn-view', onClick:closeViewModal}, null, poBtn);
    } else if(record.type === 'invoice'){
      body = `<div class="detail-grid"><div class="detail-card"><h4>Invoice overview</h4><div class="modal-row"><span>Invoice no.</span><span>${htmlEscape(record.inv)}</span></div><div class="modal-row"><span>PO number</span><span>${htmlEscape(record.po)}</span></div><div class="modal-row"><span>Supplier</span><span>${htmlEscape(record.supplier)}</span></div><div class="modal-row"><span>Invoice date</span><span>${htmlEscape(record.date)}</span></div></div><div class="detail-card"><h4>Payment details</h4><div class="modal-row"><span>Amount</span><span>${money(record.amount)}</span></div><div class="modal-row"><span>Due date</span><span>${htmlEscape(record.dueDate)}</span></div><div class="modal-row"><span>Payment method</span><span>${htmlEscape(record.method)}</span></div><div class="modal-row"><span>Status</span><span>${htmlEscape(record.status)}</span></div></div></div><div class="detail-note"><b>Notes</b><br>${htmlEscape(record.notes)}</div>`;
      if(record.status !== 'Paid') setViewActions({label:'Flag issue', className:'btn-reject', onClick:()=>{ closeViewModal(); showToast(`${record.inv} flagged for review`, 'info'); }},{label:'Mark as paid', className:'btn-approve', onClick:()=>{ updateRowStatus(row,'Paid'); row.dataset.notes = 'Marked paid from view modal.'; closeViewModal(); showToast(`${record.inv} marked as paid`, 'ok'); }});
      else setViewActions({label:'Delete', className:'btn-reject', onClick:()=> openDeleteModal(row)},{label:'Edit', className:'btn-approve', onClick:()=> openEditModal(row)});
    }
    document.getElementById('modal-body').innerHTML = body;
    document.getElementById('view-modal').classList.add('open');
  }
  function openViewModal(btn){
    const queueRow = btn.closest('.queue-row');
    setViewModalHeader(null, null);
    if(queueRow){
      const d = queueRow.dataset;
      document.getElementById('modal-title').textContent = `${d.ref} · ${d.title}`;
      document.getElementById('modal-body').innerHTML = `<div class="detail-grid"><div class="detail-card"><h4>Approval details</h4><div class="modal-row"><span>Reference</span><span>${htmlEscape(d.ref)}</span></div><div class="modal-row"><span>Requested by</span><span>${htmlEscape(d.requester)}</span></div><div class="modal-row"><span>Submitted</span><span>${htmlEscape(d.submitted)}</span></div><div class="modal-row"><span>Amount</span><span>${htmlEscape(d.amount)}</span></div></div><div class="detail-card"><h4>Workflow action</h4><div class="modal-row"><span>Queue type</span><span>${htmlEscape(d.type?.toUpperCase() || 'APP')}</span></div><div class="modal-row"><span>Decision path</span><span>Manager review</span></div><div class="modal-row"><span>Status</span><span>Awaiting sign-off</span></div><div class="modal-row"><span>Recommended action</span><span>${d.type === 'inv' ? 'Mark as paid / approve' : 'Approve or reject'}</span></div></div></div><div class="detail-note"><b>Notes</b><br>${htmlEscape(d.note || 'No additional notes.')}</div>`;
      setViewActions({label:'Reject', className:'btn-reject', onClick:()=>{ handleDecision(queueRow.querySelector('.btn-reject'),'reject'); closeViewModal(); }},{label:d.type === 'inv' ? 'Approve payment' : 'Approve', className:'btn-approve', onClick:()=>{ handleDecision(queueRow.querySelector('.btn-approve'),'approve'); closeViewModal(); }});
      document.getElementById('view-modal').classList.add('open');
      return;
    }
    const row = btn.closest('tr');
    if(row) renderViewRecord(row);
  }
  function closeViewModal(){
    document.getElementById('view-modal').classList.remove('open');
  }

  function buildEditFields(record){
    if(record.type === 'po') return `
      <div class="form-field"><label>PO number</label><input name="po" value="${htmlEscape(record.po)}" readonly></div>
      <div class="form-field"><label>Supplier</label><input name="supplier" value="${htmlEscape(record.supplier)}"></div>
      <div class="form-field"><label>Brand</label><input name="category" value="${htmlEscape(record.category)}"></div>
      <div class="form-field"><label>Item</label><input name="item" value="${htmlEscape(record.item)}"></div>
      <div class="form-field"><label>Quantity</label><input type="number" min="0" name="qty" value="${record.qty}"></div>
      <div class="form-field"><label>Total amount</label><input type="number" min="0" step="0.01" name="amount" value="${record.amount}"></div>
      <div class="form-field"><label>Priority</label><select name="priority"><option ${record.priority==='Urgent'?'selected':''}>Urgent</option><option ${record.priority==='High'?'selected':''}>High</option><option ${record.priority==='Normal' || !record.priority?'selected':''}>Normal</option><option ${record.priority==='Low'?'selected':''}>Low</option></select></div>
      <div class="form-field"><label>Delivery status</label><select name="delivery"><option ${record.delivery==='Scheduled'?'selected':''}>Scheduled</option><option ${record.delivery==='intransit'?'selected':''}>intransit</option><option ${record.delivery==='Delivered'?'selected':''}>Delivered</option><option ${record.delivery==='Delayed'?'selected':''}>Delayed</option></select></div>
      <div class="form-field"><label>Status</label><select name="status"><option ${record.status==='Pending'?'selected':''}>Pending</option><option ${record.status==='Approved'?'selected':''}>Approved</option><option ${record.status==='Rejected'?'selected':''}>Rejected</option><option ${record.status==='Completed'?'selected':''}>Completed</option></select></div>
      <div class="form-field"><label>Date</label><input name="date" value="${htmlEscape(record.date)}"></div>
      <div class="form-field"><label>Time</label><input name="time" value="${htmlEscape(record.time)}"></div>
      <div class="form-field full"><label>Remarks</label><textarea name="remarks">${htmlEscape(record.remarks)}</textarea></div>`;
    if(record.type === 'supplier') return `
      <div class="form-field"><label>Supplier ID</label><input name="sid" value="${htmlEscape(record.sid)}" readonly></div>
      <div class="form-field"><label>Supplier Name <span class="req">*</span></label><input name="name" value="${htmlEscape(record.name)}" required></div>
      <div class="form-field"><label>Contact Person <span class="req">*</span></label><input name="contact" value="${htmlEscape(record.contact)}" required></div>
      <div class="form-field"><label>Email <span class="req">*</span></label><input type="email" name="email" value="${htmlEscape(record.email)}" required></div>
      <div class="form-field"><label>Phone Number <span class="req">*</span></label><input name="phone" value="${htmlEscape(record.phone)}" required></div>
      <div class="form-field"><label>Brand <span class="req">*</span></label><input name="brand" value="${htmlEscape(record.category || '')}" required></div>
      <div class="form-field full"><label>Products</label><div id="edit-supplier-products-list" class="product-chip-list"></div><input type="hidden" name="productsJson" id="edit-supplier-products-json" value="[]"><button type="button" class="btn btn-small" style="margin-top:8px;" onclick="openSupplierProductModal()">+ Add Product</button></div>
      <div class="form-field full"><label>Address <span class="req">*</span></label><textarea name="address" required>${htmlEscape(record.address)}</textarea></div>`;
    if(record.type === 'req') return `
      <div class="form-field"><label>Requisition no.</label><input name="ref" value="${htmlEscape(record.ref)}" readonly></div>
      <div class="form-field"><label>Requested by</label><input name="requester" value="${htmlEscape(record.requester)}"></div>
      <div class="form-field"><label>Department</label><input name="dept" value="${htmlEscape(record.dept)}"></div>
      <div class="form-field"><label>Item</label><input name="item" value="${htmlEscape(record.item)}"></div>
      <div class="form-field"><label>Quantity</label><input type="number" min="0" name="qty" value="${record.qty}"></div>
      <div class="form-field"><label>Unit</label><input name="uom" value="${htmlEscape(record.uom)}"></div>
      <div class="form-field"><label>Delivery status</label><select name="delivery"><option ${record.delivery==='Scheduled'?'selected':''}>Scheduled</option><option ${record.delivery==='intransit'?'selected':''}>intransit</option><option ${record.delivery==='Delivered'?'selected':''}>Delivered</option><option ${record.delivery==='Delayed'?'selected':''}>Delayed</option></select></div>
      <div class="form-field"><label>Status</label><select name="status"><option ${record.status==='Pending'?'selected':''}>Pending</option><option ${record.status==='Approved'?'selected':''}>Approved</option><option ${record.status==='Rejected'?'selected':''}>Rejected</option></select></div>
      <div class="form-field"><label>Date</label><input name="date" value="${htmlEscape(record.date)}"></div>
      <div class="form-field"><label>Time</label><input name="time" value="${htmlEscape(record.time)}"></div>
      <div class="form-field full"><label>Justification</label><textarea name="notes">${htmlEscape(record.notes)}</textarea></div>`;
    if(record.type === 'invoice') return `
      <div class="form-field"><label>Invoice no.</label><input name="inv" value="${htmlEscape(record.inv)}" readonly></div>
      <div class="form-field"><label>PO number</label><input name="po" value="${htmlEscape(record.po)}"></div>
      <div class="form-field"><label>Supplier</label><input name="supplier" value="${htmlEscape(record.supplier)}"></div>
      <div class="form-field"><label>Invoice date</label><input name="date" value="${htmlEscape(record.date)}"></div>
      <div class="form-field"><label>Due date</label><input name="dueDate" value="${htmlEscape(record.dueDate)}"></div>
      <div class="form-field"><label>Amount</label><input type="number" min="0" step="0.01" name="amount" value="${record.amount}"></div>
      <div class="form-field"><label>Payment method</label><input name="method" value="${htmlEscape(record.method)}"></div>
      <div class="form-field"><label>Status</label><select name="status"><option ${record.status==='Unpaid'?'selected':''}>Unpaid</option><option ${record.status==='Paid'?'selected':''}>Paid</option><option ${record.status==='Overdue'?'selected':''}>Overdue</option></select></div>
      <div class="form-field full"><label>Notes</label><textarea name="notes">${htmlEscape(record.notes)}</textarea></div>`;
    if(record.type === 'delivery') return `
      <div class="form-field"><label>Shipment no.</label><input name="ship" value="${htmlEscape(record.ship)}" readonly></div>
      <div class="form-field"><label>PO number</label><input name="po" value="${htmlEscape(record.po)}"></div>
      <div class="form-field"><label>Supplier</label><input name="supplier" value="${htmlEscape(record.supplier)}"></div>
      <div class="form-field"><label>Carrier</label><input name="carrier" value="${htmlEscape(record.carrier)}"></div>
      <div class="form-field"><label>Status</label><select name="status"><option ${record.status==='Scheduled'?'selected':''}>Scheduled</option><option ${record.status==='intransit'?'selected':''}>intransit</option><option ${record.status==='Delayed'?'selected':''}>Delayed</option><option ${record.status==='Delivered'?'selected':''}>Delivered</option></select></div>
      <div class="form-field"><label>Date</label><input name="date" value="${htmlEscape(record.date)}"></div>
      <div class="form-field full"><label>Tracking note</label><textarea name="note">${htmlEscape(record.note)}</textarea></div>`;
    return '';
  }
  function openEditModal(row){
    const record = buildRecord(row);
    const modal = document.getElementById('edit-modal');
    modal.__row = row;
    document.getElementById('edit-modal-title').textContent = `Edit ${record.title}`;
    document.getElementById('edit-modal-body').innerHTML = buildEditFields(record);
    if(record.type === 'supplier'){
      setSupplierProductEditor('edit-supplier-products-list', 'edit-supplier-products-json');
      supplierProductDraft = Array.isArray(getSupplierProducts(row)) ? getSupplierProducts(row) : [];
      renderSupplierProductList();
    }
    document.getElementById('edit-record-form').dataset.type = record.type;
    modal.classList.add('open');
  }
  function closeEditModal(){ document.getElementById('edit-modal').classList.remove('open'); }
  function saveEditRecord(e){
    e.preventDefault();
    const modal = document.getElementById('edit-modal');
    const row = modal.__row;
    if(!row) return;
    const type = e.target.dataset.type;
    const d = Object.fromEntries(new FormData(e.target).entries());
    if(type === 'po'){
      const qty = Number(d.qty || 0);
      const amount = Number(d.amount || 0);
      const unitPrice = qty ? amount / qty : 0;
      row.dataset.item = d.item || '';
      row.dataset.time = d.time || '';
      row.dataset.remarks = d.remarks || '';
      row.dataset.brand = d.category || '';
      row.dataset.qty = qty;
      row.dataset.unitPrice = unitPrice;
      row.dataset.amount = amount;
      row.dataset.priority = d.priority || 'Normal';
      row.dataset.delivery = d.delivery || 'Scheduled';
      row.children[1].innerHTML = supplierPill(d.supplier);
      row.children[2].textContent = d.item || '—';
      row.children[3].innerHTML = `<b>${money(unitPrice)}</b>`;
      row.children[4].innerHTML = `<b>${money(amount)}</b>`;
      row.children[5].innerHTML = priorityBadge(d.priority || 'Normal');
      row.children[6].innerHTML = statusPill(d.status);
      row.children[7].textContent = d.date;
      row.dataset.status = String(d.status || '').toLowerCase();
      // Persist PO update to backend if we have an id
      const id = row.dataset.id;
      if(id){
        fetch(procurementUrl(`purchase-orders/${id}`), {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ status: d.status || '', amount: String(amount || 0), remarks: d.remarks || '' }).toString()
        }).then(r => r.json()).then(() => {}).catch(() => showToast('Unable to save PO changes to server.', 'no'));
      }
    } else if(type === 'supplier'){
      let products = [];
      try { products = JSON.parse(d.productsJson || d.products || '[]'); } catch { products = []; }
      row.dataset.sid = d.sid || '';
      row.dataset.category = d.brand || '';
      row.dataset.brand = d.brand || '';
      row.dataset.status = 'Active';
      row.dataset.terms = 'Net 30';
      row.dataset.lastActivity = 'Updated';
      row.dataset.products = JSON.stringify(products);
      row.children[0].innerHTML = `<div class="supplier-pill-cell">${supplierPill(d.name)}</div>`;
      row.children[1].textContent = d.brand || '—';
      row.children[2].textContent = d.contact || '—';
      row.children[3].textContent = d.email || '—';
      // Ensure phone and address cells are updated if present
      if(row.children[4]) row.children[4].textContent = d.phone || '—';
      if(row.children[5]) row.children[5].textContent = d.address || '—';
      // Persist supplier update to backend if we have an id
      const supId = row.dataset.id;
      if(supId){
        fetch(procurementUrl(`suppliers/${supId}`), {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ name: d.name || '', contact: d.contact || '', email: d.email || '', phone: d.phone || '', address: d.address || '', brand: d.brand || '', productsJson: JSON.stringify(products) }).toString()
        }).then(r => r.json()).then(() => {}).catch(() => showToast('Unable to save supplier changes to server.', 'no'));
      }
    } else if(type === 'req'){
      row.dataset.uom = d.uom || '';
      row.dataset.notes = d.notes || '';
      row.dataset.time = d.time || '';
      row.children[1].textContent = d.item;
      row.children[2].textContent = d.qty || '0';
      row.children[3].innerHTML = deliveryBadge(d.delivery);
      row.children[4].textContent = d.dept;
      row.children[5].textContent = d.requester;
      row.children[6].innerHTML = statusPill(d.status);
      row.children[7].textContent = d.date;
      row.dataset.status = String(d.status || '').toLowerCase();
      // Persist requisition update to backend if id exists
      const reqId = row.dataset.id;
      if(reqId){
        fetch(procurementUrl(`requisitions/${reqId}`), {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ status: d.status || '', notes: d.notes || '' }).toString()
        }).then(r => r.json()).then(() => {}).catch(() => showToast('Unable to save requisition changes to server.', 'no'));
      }
    } else if(type === 'invoice'){
      row.dataset.supplier = d.supplier || '';
      row.dataset.dueDate = d.dueDate || '';
      row.dataset.method = d.method || '';
      row.dataset.notes = d.notes || '';
      row.children[1].textContent = d.po;
      row.children[2].textContent = d.date;
      row.children[3].innerHTML = `<b>${money(d.amount)}</b>`;
      row.children[4].innerHTML = statusPill(d.status);
      row.dataset.amount = Number(d.amount || 0);
      row.dataset.status = String(d.status || '').toLowerCase();
      const invId = row.dataset.id;
      if(invId){
        fetch(`/invoices/${invId}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ status: d.status || '', amount: d.amount || '', notes: d.notes || '' }).toString()
        }).then(r => r.json()).then(() => {}).catch(() => showToast('Unable to save invoice changes to server.', 'no'));
      }
    } else if(type === 'delivery'){
      row.dataset.po = d.po || '';
      row.dataset.sup = d.supplier || '';
      row.dataset.carrier = d.carrier || '';
      row.dataset.note = d.note || '';
      row.dataset.date = d.date || '';
      row.dataset.stage = ({'Scheduled':'0','intransit':'2','Delayed':'1','Delivered':'4'})[d.status] || '0';
      row.children[1].innerHTML = `<a class="po-link">${htmlEscape(d.po)}</a>`;
      row.children[2].innerHTML = supplierPill(d.supplier);
      row.children[4].innerHTML = statusPill(d.status);
      row.children[5].textContent = d.date;
      row.dataset.status = String(d.status || '').toLowerCase().replace(/\s+/g,'');
      const delId = row.dataset.id;
      if(delId){
        fetch(procurementUrl(`deliveries/${delId}`), {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ status: d.status || '', remarks: d.note || '' }).toString()
        }).then(r => r.json()).then(() => {}).catch(() => showToast('Unable to save delivery changes to server.', 'no'));
      }
    }
    closeEditModal();
    showToast('Record updated successfully', 'ok');
  }

  function openDeleteModal(row){
    const record = buildRecord(row);
    const modal = document.getElementById('delete-modal');
    modal.__row = row;
    document.getElementById('delete-modal-title').textContent = `Delete ${record.title}`;
    document.getElementById('delete-modal-target').textContent = record.key || record.title;
    document.getElementById('delete-confirm-input').value = '';
    document.getElementById('delete-continue-btn').disabled = true;
    document.getElementById('delete-final-confirm').style.display = 'none';
    document.getElementById('delete-confirm-btn').style.display = 'none';
    modal.classList.add('open');
  }
  function closeDeleteModal(){ document.getElementById('delete-modal').classList.remove('open'); }
  function handleDeletePhrase(v){ document.getElementById('delete-continue-btn').disabled = String(v || '').trim().toLowerCase() !== 'delete'; }
  function continueDeleteFlow(){
    document.getElementById('delete-final-confirm').style.display = 'block';
    document.getElementById('delete-confirm-btn').style.display = '';
  }
  function confirmDeleteRecord(){
    const modal = document.getElementById('delete-modal');
    const row = modal.__row;
    if(row){
      const type = getTableType(row);
      const id = row.dataset.id;
      if(id){
        const urlMap = { 'po': 'purchase-orders/', 'supplier': 'suppliers/', 'req': 'requisitions/', 'delivery': 'deliveries/' };
        const base = urlMap[type];
        if(base){
          fetch(procurementUrl(`${base}${id}`), { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } }).then(response => {
            if (!response.ok) throw new Error('Delete failed');
            row.remove();
            refreshTabCounts();
            showToast('Record deleted', 'no');
          }).catch(()=>{ showToast('Unable to delete record on server.', 'no'); });
          closeDeleteModal();
          return;
        }
      }
      row.remove();
    }
    closeDeleteModal();
    refreshTabCounts();
    showToast('Record deleted', 'no');
  }

  function initRowActionButtons(){
    const viewBtn = `<button title="View" onclick="openViewModal(this)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button>`;
    const trackBtn = `<button title="Track" onclick="openTrackModal(this)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2C7 2 4 6 4 10c0 5.5 8 12 8 12s8-6.5 8-12c0-4-3-8-8-8z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="2"/></svg></button>`;
    document.querySelectorAll('table .row-actions').forEach(wrap => {
      const tableId = wrap.closest('table')?.id;
      if(tableId === 'deliveries-table') {
        wrap.innerHTML = trackBtn;
        return;
      }
      const singleViewTables = ['po-table','requisitions-table'];
      if(singleViewTables.includes(tableId)) {
        wrap.innerHTML = viewBtn;
        return;
      }
      [...wrap.children].forEach((btn, idx) => {
        btn.title = idx === 0 ? 'View' : (idx === 1 ? 'Edit' : 'Delete');
      });
    });
    if(!document.getElementById('queue-empty')){
      const empty = document.createElement('div');
      empty.id = 'queue-empty';
      empty.style.cssText = 'display:none;padding:16px;border:1px dashed var(--border);border-radius:12px;color:var(--muted);margin-top:12px;text-align:center;';
      empty.textContent = 'No approval items match the current filter.';
      document.getElementById('approval-tabs')?.insertAdjacentElement('afterend', empty);
    }
  }
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('table .row-actions button');
    if(!btn) return;
    const row = btn.closest('tr');
    const tableId = row?.closest('table')?.id;
    if(tableId === 'deliveries-table') {
      openTrackModal(btn);
      return;
    }
    const singleViewTables = ['po-table','requisitions-table'];
    if(singleViewTables.includes(tableId)) {
      openViewModal(btn);
      return;
    }
    const idx = [...btn.parentElement.children].indexOf(btn);
    if(idx === 0) openViewModal(btn);
    else if(idx === 1) openEditModal(row);
    else if(idx === 2) openDeleteModal(row);
  });
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape'){ closeViewModal(); closeEditModal(); closeDeleteModal(); } });
