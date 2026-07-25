  /* ---------- Add Modals (PO / Supplier / Req / Delivery / Invoice) ---------- */
  const ADD_MODAL_MAP = {
    po: 'add-po-modal',
    supplier: 'add-supplier-modal',
    req: 'add-req-modal',
    delivery: 'add-delivery-modal',
    invoice: 'add-invoice-modal'
  };

  // simple auto-increment counters (initial values based on existing sample data)
  const NEXT_ID = { po: 420, req: 45, dr: 232, inv: 3, sup: 20 };
  const ID_COUNTS = { po: 419, req: 44, dr: 231 }; // Track highest used number per year
  const procurementUrl = window.procurementUrl || ((path = '') => `/procurement/${String(path).replace(/^\/+/, '')}`);

  function pad(n, len){ return String(n).padStart(len, '0'); }
  function todayISO(){ return new Date().toISOString().slice(0,10); }
  function fmtDate(iso){
    if(!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
  }
  function initials(name){
    return name.split(/\s+/).filter(Boolean).slice(0,2).map(w=>w[0].toUpperCase()).join('');
  }
  function randomColor(seed){
    const colors = ['#2f6fed','#22c55e','#f2994a','#7a5af0','#eb5757','#0ea5e9','#1fa971','#e0338c'];
    let h = 0; for(const c of seed) h = (h*31 + c.charCodeAt(0)) & 0xffff;
    return colors[h % colors.length];
  }

  function priorityBadge(label = 'Normal'){
    const raw = String(label || 'Normal').trim();
    const normalized = raw.toUpperCase();
    const type = ['URGENT','HIGH','LOW'].includes(normalized) ? normalized.toLowerCase() : 'normal';
    return `<span class="priority-pill ${type}">${htmlEscape(normalized)}</span>`;
  }

  function refreshDeliverySupplierOptions(){
    const select = document.getElementById('delivery-filter-supplier');
    if(!select) return;
    const selected = select.value || '';
    const supplierNames = [...document.querySelectorAll('#suppliers-table tbody tr')]
      .map(row => supplierNameFromCell(row.children[0]) || textFrom(row.children[0]))
      .filter(Boolean);
    const uniqueNames = [...new Set(supplierNames)];
    select.innerHTML = '<option value="">All Suppliers</option>' + uniqueNames.map(name => `<option value="${htmlEscape(name)}">${htmlEscape(name)}</option>`).join('');
    select.value = uniqueNames.includes(selected) ? selected : '';
  }

  function setModalFieldValue(modal, name, value){
    const field = modal.querySelector(`[name="${name}"]`);
    if(field) field.value = value;
  }

  function refreshPoSupplierOptions(modal){
    const form = modal?.querySelector('#add-po-form');
    if(!form) return Promise.resolve();
    const supplierField = form.querySelector('[name="supplier"]');
    if(!supplierField) return;
    const selectedSupplier = supplierField.value || '';
    const supplierRows = [...document.querySelectorAll('#suppliers-table tbody tr')]
      .map(row => {
        const supplierName = supplierNameFromCell(row.children[0]) || textFrom(row.children[0]);
        if(!supplierName) return null;
        return { name: supplierName, brand: row.dataset.category || row.dataset.brand || '', warehouseId: row.dataset.warehouseId || '' };
      })
      .filter(Boolean);
    if(supplierRows.length > 0){
      supplierField.innerHTML = '<option value="">Select supplier...</option>' + supplierRows.map(s => `<option value="${htmlEscape(s.name)}">${htmlEscape(s.name)}</option>`).join('');
      supplierField.value = supplierRows.some(s => s.name === selectedSupplier) ? selectedSupplier : '';
      // populate client-side catalog from rows
      window.SUPPLIER_CATALOG = window.SUPPLIER_CATALOG || {};
      supplierRows.forEach(s => { if(!window.SUPPLIER_CATALOG[s.name]) window.SUPPLIER_CATALOG[s.name] = { brand: s.brand || s.name, warehouseId: s.warehouseId || '', products: [] }; });
      populatePoItemSelect(form);
      refreshDeliverySupplierOptions();
      return Promise.resolve();
    }
    // No supplier rows on this page — fetch suppliers JSON from server
    return fetch(procurementUrl('suppliers'), { headers: { 'Accept': 'application/json' } })
      .then(res => res.json())
      .then(json => {
        const list = (json?.data || []);
        supplierField.innerHTML = '<option value="">Select supplier...</option>' + list.map(s => `<option value="${htmlEscape(s.name)}">${htmlEscape(s.name)}</option>`).join('');
        supplierField.value = list.some(s => s.name === selectedSupplier) ? selectedSupplier : '';
        window.SUPPLIER_CATALOG = window.SUPPLIER_CATALOG || {};
        list.forEach(s => {
          window.SUPPLIER_CATALOG[s.name] = { brand: s.brand || s.name, warehouseId: s.warehouse_id || '', products: (s.products || []).map(p => ({ name: p.name, unitPrice: Number(p.price || p.unitPrice || 0) })) };
        });
        populatePoItemSelect(form);
        refreshDeliverySupplierOptions();
      }).catch(() => {
        // ignore errors; leave supplierField as-is
      });
  }

  function addSupplierOptionToPoForm(name){
    if(!name) return;
    const modal = document.getElementById('add-po-modal');
    if(!modal) return;
    const supplierField = modal.querySelector('[name="supplier"]');
    if(!supplierField) return;
    const exists = [...supplierField.options].some(opt => opt.value === name);
    if(exists) return;
    const option = document.createElement('option');
    option.value = name;
    option.textContent = name;
    supplierField.appendChild(option);
  }

  function populatePoItemSelect(form, selectedItem = ''){
    const supplierField = form?.querySelector('[name="supplier"]');
    const itemField = form?.querySelector('[name="item"]');
    const brandField = form?.querySelector('[name="brand"]');
    const warehouseField = form?.querySelector('[name="warehouse_id"]');
    if(!itemField) return;
    const supplierName = (supplierField?.value || '').trim();
    const entry = getSupplierCatalogEntry(supplierName);
    const products = (entry?.products || []).filter(product => product && product.name);
    itemField.innerHTML = '<option value="">Select item...</option>' + products.map(product => {
      const name = String(product.name || '').trim();
      const unitPrice = Number(product.unitPrice || product.price || 0);
      return `<option value="${htmlEscape(name)}" data-unit-price="${unitPrice}">${htmlEscape(name)}</option>`;
    }).join('');
    if(brandField) brandField.value = entry?.brand || '';
    if(warehouseField && entry?.warehouseId) warehouseField.value = String(entry.warehouseId);
    if(selectedItem){
      itemField.value = selectedItem;
    } else if(form.__poCurrentItem){
      itemField.value = form.__poCurrentItem;
    } else {
      itemField.value = '';
    }
  }

  function bindPoFormAutofill(modal){
    const form = modal?.querySelector('#add-po-form');
    if(!form || form.__poAutofillBound) return;
    form.__poAutofillBound = true;
    const supplierField = form.querySelector('[name="supplier"]');
    const itemField = form.querySelector('[name="item"]');
    const qtyField = form.querySelector('[name="qty"]');
    const unitPriceField = form.querySelector('[name="unitPrice"]');
    const amountField = form.querySelector('[name="amount"]');
    const brandField = form.querySelector('[name="brand"]');
    const update = () => applyPoAutofill(form);
    supplierField?.addEventListener('change', () => {
      populatePoItemSelect(form);
      update();
    });
    itemField?.addEventListener('change', () => {
      form.__poCurrentItem = itemField.value;
      update();
    });
    itemField?.addEventListener('input', () => {
      form.__poCurrentItem = itemField.value;
      update();
    });
    qtyField?.addEventListener('input', update);
    qtyField?.addEventListener('change', update);
    unitPriceField?.addEventListener('input', update);
    unitPriceField?.addEventListener('change', update);
    brandField?.addEventListener('input', update);
  }

  function applyPoAutofill(form){
    if(!form) return;
    const supplierField = form.querySelector('[name="supplier"]');
    const itemField = form.querySelector('[name="item"]');
    const qtyField = form.querySelector('[name="qty"]');
    const unitPriceField = form.querySelector('[name="unitPrice"]');
    const amountField = form.querySelector('[name="amount"]');
    const brandField = form.querySelector('[name="brand"]');
    const supplierName = (supplierField?.value || '').trim();
    const entry = getSupplierCatalogEntry(supplierName);
    if(entry){
      if(brandField && (!brandField.value || brandField.value === supplierName)) brandField.value = entry.brand || supplierName;
      const itemValue = (itemField?.value || '').trim().toLowerCase();
      const selectedOption = itemField?.selectedOptions?.[0];
      const selectedUnitPrice = Number(selectedOption?.dataset?.unitPrice || 0);
      const match = (entry.products || []).find(p => String(p.name || '').toLowerCase().includes(itemValue));
      if(match && unitPriceField && (!unitPriceField.value || Number(unitPriceField.value) === 0)) unitPriceField.value = Number(match.unitPrice || 0);
      else if(selectedUnitPrice && unitPriceField && (!unitPriceField.value || Number(unitPriceField.value) === 0)) unitPriceField.value = selectedUnitPrice;
    }
    const qty = Number(qtyField?.value || 0);
    const unitPrice = Number(unitPriceField?.value || 0);
    if(amountField) amountField.value = (qty * unitPrice).toFixed(2);
  }

  function refreshDeliveryPoOptions(){
    const modal = document.getElementById('add-delivery-modal');
    const poField = modal?.querySelector('[name="po"]');
    if(!poField) return;

    const currentValue = poField.value || '';
    const approvedRows = [...document.querySelectorAll('#po-table tbody tr')].filter(row => {
      const status = String(row.dataset.status || textFrom(row.children[6]) || '').toLowerCase().trim();
      return status === 'approved';
    });
    const noApprovedText = 'No approved purchase orders available';

    if(approvedRows.length){
      poField.innerHTML = '<option value="">Select PO...</option>' + approvedRows.map(row => {
        const poNumber = htmlEscape(textFrom(row.children[0]));
        return `<option value="${poNumber}"${poNumber === currentValue ? ' selected' : ''}>${poNumber}</option>`;
      }).join('');
      window.APPROVED_PO_CACHE = approvedRows.reduce((acc,row) => {
        const poNumber = textFrom(row.children[0]);
        acc[poNumber] = {
          po: poNumber,
          supplier: supplierNameFromCell(row.children[1]),
          item: row.dataset.item || textFrom(row.children[2]) || '',
          qty: row.dataset.qty || '',
          unitPrice: row.dataset.unitPrice || '',
          status: String(row.dataset.status || textFrom(row.children[6]) || '').toLowerCase().trim(),
          expected: row.dataset.expected || ''
        };
        return acc;
      }, {});
      return;
    }

    poField.innerHTML = `<option value="">${noApprovedText}</option>`;
    fetch(procurementUrl('purchase-orders/approved'), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => res.ok ? res.json() : Promise.reject()).then(data => {
      if(!Array.isArray(data)) throw new Error('Invalid response');
      if(!data.length){
        poField.innerHTML = `<option value="">${noApprovedText}</option>`;
        window.APPROVED_PO_CACHE = {};
        return;
      }
      window.APPROVED_PO_CACHE = data.reduce((acc, item) => {
        if(item.po_number){
          acc[item.po_number] = {
            po: item.po_number,
            supplier: item.supplier_name || '—',
            item: item.item || '',
            qty: item.qty || '',
            unitPrice: item.unit_price || '',
            status: String(item.status || 'approved').toLowerCase().trim(),
            expected: item.expected_delivery_date || ''
          };
        }
        return acc;
      }, {});
      poField.innerHTML = '<option value="">Select PO...</option>' + data.map(po => {
        const poNumber = htmlEscape(po.po_number || '');
        return `<option value="${poNumber}"${poNumber === currentValue ? ' selected' : ''}>${poNumber}</option>`;
      }).join('');
    }).catch(() => {
      poField.innerHTML = `<option value="">${noApprovedText}</option>`;
    });
  }

  function getPoInfo(poNumber){
    const trimmed = String(poNumber || '').trim();
    if(!trimmed) return null;
    const domRow = findPoRowByNumber(trimmed);
    if(domRow){
      return {
        po: trimmed,
        supplier: resolveSupplierByPO(trimmed) || '',
        item: domRow.dataset.item || textFrom(domRow.children[2]) || '',
        qty: domRow.dataset.qty || '',
        unitPrice: domRow.dataset.unitPrice || '',
        status: String(domRow.dataset.status || textFrom(domRow.children[6]) || '').toLowerCase().trim(),
        expected: domRow.dataset.expected || ''
      };
    }
    return window.APPROVED_PO_CACHE?.[trimmed] || null;
  }

  function bindDeliveryPoAutofill(modal){
    const form = modal?.querySelector('#add-delivery-form');
    if(!form || form.__deliveryPoBound) return;
    form.__deliveryPoBound = true;
    const poField = form.querySelector('[name="po"]');
    const supplierField = form.querySelector('[name="supplier"]');
    const itemField = form.querySelector('[name="items"]');
    const qtyField = form.querySelector('[name="qty"]');
    const unitPriceField = form.querySelector('[name="unit_price"]');
    const amountField = form.querySelector('[name="amount"]');
    const update = () => {
      const poNumber = (poField?.value || '').trim();
      if(!poNumber) return;
      const poInfo = getPoInfo(poNumber);
      if(!poInfo) return;
      if(supplierField){
        supplierField.value = poInfo.supplier || '';
      }
      if(itemField && !itemField.value){
        itemField.value = poInfo.item || '';
      }
      if(qtyField && (!qtyField.value || Number(qtyField.value) === 0)){
        qtyField.value = poInfo.qty || '';
      }
      if(unitPriceField && (!unitPriceField.value || Number(unitPriceField.value) === 0)){
        unitPriceField.value = poInfo.unitPrice || '';
      }
      if(amountField){
        const qty = Number(qtyField?.value || 0);
        const unitPrice = Number(unitPriceField?.value || 0);
        amountField.value = qty && unitPrice ? (qty * unitPrice).toFixed(2) : amountField.value;
      }
    };
    poField?.addEventListener('change', update);
    poField?.addEventListener('input', update);
    qtyField?.addEventListener('input', update);
    qtyField?.addEventListener('change', update);
    unitPriceField?.addEventListener('input', update);
    unitPriceField?.addEventListener('change', update);
  }

  function openAddModal(kind, reqData = null){
    const modal = document.getElementById(ADD_MODAL_MAP[kind]);
    if(!modal) return;
    
    // Handle PO modal title for requisition conversion
    const poTitle = modal.querySelector('#add-po-modal h3');
    if(poTitle && kind === 'po'){
      if(reqData && reqData.reqNum){
        poTitle.textContent = `Create New Purchase - REQ ${reqData.reqNum}`;
      } else {
        poTitle.textContent = 'Create New Purchase Order';
      }
    }

    if(kind==='supplier'){
      setSupplierProductEditor('supplier-products-list', 'supplier-products-json');
      resetSupplierProductDraft();
    }

    // Pre-fill auto-generated IDs & default dates
    const yr = new Date().getFullYear();
    if(kind==='po'){
      bindPoFormAutofill(modal);
      const poNum = ID_COUNTS.po + 1;
      setModalFieldValue(modal, 'po', `PO-${yr}-${pad(poNum,4)}`);
      const exp = new Date(); exp.setDate(exp.getDate()+7);
      setModalFieldValue(modal, 'expected', exp.toISOString().slice(0,10));
      refreshPoSupplierOptions(modal);
      
      // Auto-fill from requisition data — only the Qty (and the reference
      // number) should carry over. Item/Brand/Supplier are chosen by the
      // person creating the PO, not copied from the request.
      if(reqData){
        if(reqData.reqNum) setModalFieldValue(modal, 'reqRef', reqData.reqNum);
        if(reqData.qty) setModalFieldValue(modal, 'qty', reqData.qty);
      }
      setTimeout(() => {
        const poForm = modal.querySelector('#add-po-form');
        // wait for supplier options/catalog to be ready before populating items
        refreshPoSupplierOptions(modal).then(() => {
          applyPoAutofill(poForm);
        });
      }, 60);
    } else if(kind==='req'){
      const reqNum = ID_COUNTS.req + 1;
      setModalFieldValue(modal, 'rq', `REQ-${yr}-${pad(reqNum,4)}`);
      setModalFieldValue(modal, 'dateReq', todayISO());
    } else if(kind==='delivery'){
      const shpNum = ID_COUNTS.dr + 1;
      setModalFieldValue(modal, 'dr', `SHP-${yr}-${pad(shpNum,4)}`);
      setModalFieldValue(modal, 'delDate', todayISO());
      refreshDeliveryPoOptions();
      bindDeliveryPoAutofill(modal);
    } else if(kind==='invoice'){
      setModalFieldValue(modal, 'inv', `INV-${yr}-${pad(NEXT_ID.inv,3)}`);
      setModalFieldValue(modal, 'invDate', todayISO());
      const due = new Date(); due.setDate(due.getDate()+30);
      setModalFieldValue(modal, 'dueDate', due.toISOString().slice(0,10));
    } else if(kind==='supplier'){
      setModalFieldValue(modal, 'sid', `SUP-${pad(NEXT_ID.sup,4)}`);
    }

    modal.classList.add('open');
    // focus first editable input
    setTimeout(()=>{
      const focusable = modal.querySelector('input:not([readonly]), select, textarea');
      if(focusable) focusable.focus();
    }, 60);
  }

  function closeAddModal(kind){
    const modal = document.getElementById(ADD_MODAL_MAP[kind]);
    if(modal){
      modal.classList.remove('open');
      const form = modal.querySelector('form');
      if(form) form.reset();
      
      // Reset PO modal title when closing
      if(kind === 'po'){
        const poTitle = modal.querySelector('h3');
        if(poTitle) poTitle.textContent = 'Create New Purchase Order';
      }
    }
    if(kind==='supplier'){
      resetSupplierProductDraft();
    }
  }

  // Convert Requisition to PO
  function convertReqToPO(reqNum, item, qty){
    const reqData = { reqNum, item, qty };
    openAddModal('po', reqData);
  }

  let cancelPOData = null;
  function createPOFromView(){
    const modal = document.getElementById('view-modal');
    const row = modal.__row;
    if(!row) return;
    const record = buildRecord(row);
    if(record.type === 'req'){
      convertReqToPO(record.ref, record.item, record.qty);
      closeViewModal();
    }
  }
  let supplierProductDraft = [];
  let supplierProductCounter = 1;
  let supplierProductEditor = { listId: 'supplier-products-list', hiddenId: 'supplier-products-json' };

  function setSupplierProductEditor(listId, hiddenId){
    supplierProductEditor = { listId, hiddenId };
  }

  function resetSupplierProductDraft(){
    supplierProductDraft = [];
    supplierProductCounter = 1;
    renderSupplierProductList();
  }

  function renderSupplierProductList(){
    const list = document.getElementById(supplierProductEditor.listId);
    const hidden = document.getElementById(supplierProductEditor.hiddenId);
    if(list){
      if(!supplierProductDraft.length){
        list.innerHTML = '<div class="product-list-empty">No products added yet.</div>';
      } else {
        list.innerHTML = supplierProductDraft.map((item, idx) => `
          <div class="product-chip">
            <span>${htmlEscape(item.name || 'Unnamed product')}</span>
            <span class="meta">${htmlEscape(item.sku || 'SKU pending')} · ₱${Number(item.price || 0).toFixed(2)}</span>
            <button type="button" class="remove" onclick="removeSupplierProduct(${idx})" title="Remove">×</button>
          </div>
        `).join('');
      }
    }
    if(hidden){ hidden.value = JSON.stringify(supplierProductDraft); }
  }

  function removeSupplierProduct(index){
    supplierProductDraft.splice(index, 1);
    renderSupplierProductList();
  }

  function openSupplierProductModal(){
    const modal = document.getElementById('add-supplier-product-modal');
    const form = document.getElementById('add-supplier-product-form');
    if(!modal || !form) return;

    const skuInput = form.querySelector('[name="productSku"]');
    const skuType = form.querySelector('[name="productSkuType"]');
    if(skuType) skuType.value = 'auto';
    form.reset();
    if(skuInput){
      skuInput.readOnly = true;
      skuInput.value = generateSupplierProductSku('');
    }
    modal.classList.add('open');
    const nameField = form.querySelector('[name="productName"]');
    if(nameField){ nameField.focus(); }
  }

  function closeSupplierProductModal(){
    const modal = document.getElementById('add-supplier-product-modal');
    if(modal){
      modal.classList.remove('open');
      const form = document.getElementById('add-supplier-product-form');
      if(form) form.reset();
    }
  }

  function updateSupplierProductSkuType(select){
    const form = document.getElementById('add-supplier-product-form');
    const skuInput = form?.querySelector('[name="productSku"]');
    if(!skuInput) return;
    if(select.value === 'manual'){
      skuInput.readOnly = false;
      skuInput.focus();
    } else {
      skuInput.readOnly = true;
      const nameField = form?.querySelector('[name="productName"]');
      skuInput.value = nameField ? generateSupplierProductSku(nameField.value) : `SKU-${String(supplierProductCounter).padStart(3, '0')}`;
    }
  }

  function syncSupplierProductSku(nameInput){
    const form = document.getElementById('add-supplier-product-form');
    const skuType = form?.querySelector('[name="productSkuType"]');
    const skuInput = form?.querySelector('[name="productSku"]');
    if(!skuType || !skuInput) return;
    if(skuType.value !== 'manual'){
      skuInput.value = generateSupplierProductSku(nameInput.value);
    }
  }

  function generateSupplierProductSku(name){
    const trimmed = (name || '').trim();
    const base = trimmed ? trimmed.toUpperCase().replace(/[^A-Z0-9]+/g, '').slice(0, 8) : 'PRD';
    const suffix = String(supplierProductCounter).padStart(3, '0');
    return `${base}${suffix}`;
  }

  function submitSupplierProduct(e){
    e.preventDefault();
    const form = e.target;
    const d = Object.fromEntries(new FormData(form).entries());
    const name = (d.productName || '').trim();
    const price = Number(d.productPrice || 0);
    if(!name){ return; }
    const sku = (d.productSku || '').trim() || generateSupplierProductSku(name);
    supplierProductDraft.push({ name, sku, price });
    supplierProductCounter += 1;
    renderSupplierProductList();
    form.reset();
    const skuInput = form.querySelector('[name="productSku"]');
    if(skuInput){ skuInput.value = generateSupplierProductSku(''); }
    const nameField = form.querySelector('[name="productName"]');
    if(nameField){ nameField.focus(); }
  }

  function openCancelModal(btn){
    const row = btn.closest('tr');
    const poNum = row?.cells[0]?.textContent?.trim() || 'this PO';
    cancelPOData = { row, poNum };
    document.getElementById('cancel-po-number').textContent = poNum;
    document.getElementById('cancel-po-modal').classList.add('open');
  }
  function openCancelModalFromRow(row){
    const poNum = row?.cells[0]?.textContent?.trim() || 'this PO';
    cancelPOData = { row, poNum };
    document.getElementById('cancel-po-number').textContent = poNum;
    document.getElementById('cancel-po-modal').classList.add('open');
    closeViewModal();
  }
  function closeCancelModal(){
    document.getElementById('cancel-po-modal').classList.remove('open');
    cancelPOData = null;
  }
  function confirmCancelPO(){
    if(cancelPOData && cancelPOData.row){
      cancelPOData.row.dataset.status = 'cancelled';
      cancelPOData.row.classList.add('cancelled-row');
      cancelPOData.row.cells[6].innerHTML = '<span class="status-pill cancelled">Cancelled</span>';
      showToast(`PO ${cancelPOData.poNum} cancelled`, 'no');
    }
    closeCancelModal();
  }

  document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape'){
      Object.keys(ADD_MODAL_MAP).forEach(closeAddModal);
      closeCancelModal();
    }
  });

  /* ---------- Submit handlers ---------- */

  function submitAddPO(e){
    e.preventDefault();
    const d = Object.fromEntries(new FormData(e.target).entries());
    const qtyNum = Number(d.qty || 0);
    const unitPriceNum = Number(d.unitPrice || 0);
    const amountNum = Number(d.amount || 0) || (qtyNum * unitPriceNum);
    const poDate = todayISO();
    const priorityLabel = d.priority || 'Normal';

    fetch(procurementUrl('purchase-orders'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: new URLSearchParams({
        po: d.po,
        supplier: d.supplier,
        brand: d.brand || '',
        item: d.item || '',
        qty: String(qtyNum),
        unitPrice: String(unitPriceNum),
        amount: String(amountNum),
        priority: priorityLabel,
        expected: d.expected || '',
        warehouse_id: d.warehouse_id || '',
        createdBy: d.createdBy || '',
        remarks: d.remarks || '',
        reqRef: d.reqRef || ''
      }).toString()
    }).then(res => res.json().then(json => ({ ok: res.ok, json }))).then(({ ok, json }) => {
      if(!ok){
        showToast(json?.message || 'Unable to save purchase order right now.', 'no');
        return;
      }
      if(json && json.po_number) d.po = json.po_number;
      const table = document.querySelector('#po-table tbody');
      if(table){
        const tr = document.createElement('tr');
        tr.dataset.status = 'pending';
        tr.dataset.date = poDate;
        tr.dataset.amount = amountNum;
        tr.dataset.item = d.item || '';
        tr.dataset.expected = d.expected || '';
        tr.dataset.remarks = d.remarks || '';
        tr.dataset.requestedBy = d.createdBy || 'Procurement Team';
        tr.dataset.supplier = d.supplier || '';
        tr.dataset.brand = d.brand || '';
        tr.dataset.qty = qtyNum;
        tr.dataset.unitPrice = unitPriceNum;
        tr.dataset.reqRef = d.reqRef || '';
        tr.dataset.priority = priorityLabel;
        tr.dataset.delivery = 'Pending';
        tr.innerHTML = `
          <td><a class="po-link">${d.po}</a></td>
          <td>${supplierPill(d.supplier || 'Unknown Supplier')}</td>
          <td>${htmlEscape(d.item || '—')}</td>
          <td>${money(unitPriceNum)}</td>
          <td><b>${money(amountNum)}</b></td>
          <td>${priorityBadge(priorityLabel)}</td>
          <td>${statusPill('Pending')}</td>
          <td>${fmtDate(poDate)}</td>
          <td><span class="row-actions"><button title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button><button title="Edit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></button><button class="del" title="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span></td>`;
        if(json && (json.id || (json.data && json.data.id))) tr.dataset.id = json.id || json.data.id;
        table.prepend(tr);
      }
      NEXT_ID.po++;
      ID_COUNTS.po++;
      if(d.reqRef){
        const reqRow = findReqRowByRef(d.reqRef);
        if(reqRow){
          reqRow.dataset.po = d.po;
          reqRow.dataset.status = 'processing';
          reqRow.children[6].innerHTML = statusPill('Processing');
          updateRowStatus(reqRow, 'Processing');
          const reqId = reqRow.dataset.id;
          if(reqId){
            fetch(procurementUrl(`requisitions/${reqId}`), {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
              },
              body: new URLSearchParams({ status: 'Processing' }).toString()
            }).then(() => {}).catch(() => {
              console.warn('Unable to persist requisition status update for', reqId);
            });
          }
        }
      }
      initRowActionButtons();
      updateStatusCounts();
      showToast(`Purchase Order ${d.po} created`, 'ok');
      closeAddModal('po');
    }).catch(() => {
      showToast('Unable to save purchase order right now.', 'no');
    });
  }

  function submitAddSupplier(e){
    e.preventDefault();
    const d = Object.fromEntries(new FormData(e.target).entries());
    const products = Array.isArray(JSON.parse(d.productsJson || '[]')) ? JSON.parse(d.productsJson || '[]') : [];

    fetch(procurementUrl('suppliers'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: new URLSearchParams({
        sid: d.sid || '',
        name: d.name || '',
        contact: d.contact || '',
        email: d.email || '',
        phone: d.phone || '',
        address: d.address || '',
        brand: d.brand || '',
        status: d.status || 'active',
        warehouse_id: d.warehouse_id || '',
        productsJson: JSON.stringify(products)
      }).toString()
    }).then(async res => {
      const json = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(json?.message || 'Unable to save supplier right now.');
      return json;
    }).then(json => {
      const table = document.querySelector('#suppliers-table tbody');
      if(table){
        const tr = document.createElement('tr');
        tr.dataset.sid = d.sid || '';
        tr.dataset.category = d.brand || '';
        tr.dataset.brand = d.brand || '';
        tr.dataset.warehouseId = d.warehouse_id || '';
        tr.dataset.status = (d.status || 'active').replace(/^./, m => m.toUpperCase());
        tr.dataset.terms = 'Net 30';
        tr.dataset.lastActivity = 'Newly onboarded';
        tr.dataset.itemName = products[0]?.name || '';
        tr.dataset.sku = products[0]?.sku || '';
        tr.dataset.unitPrice = products[0]?.price || '';
        tr.dataset.products = JSON.stringify(products);
        tr.innerHTML = `
          <td><div class="supplier-pill-cell">${supplierPill(d.name)}</div></td>
          <td>${htmlEscape(d.brand || '—')}</td>
          <td>${htmlEscape(d.contact || '—')}</td>
          <td>${htmlEscape(d.email || '—')}</td>
          <td>${htmlEscape(d.phone || '—')}</td>
          <td>${htmlEscape(d.address || '—')}</td>
          <td><span class="row-actions"><button title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button><button title="Edit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></button><button class="del" title="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span></td>`;
        if(json && (json.id || (json.data && json.data.id))) tr.dataset.id = json.id || json.data.id;
        table.prepend(tr);
      }
      NEXT_ID.sup++;
      // supplier IDs are NEXT_ID-based; no change to ID_COUNTS for suppliers
      initRowActionButtons();
      addSupplierOptionToPoForm(d.name);
      refreshPoSupplierOptions(document.getElementById('add-po-modal'));
      refreshDeliverySupplierOptions();
      showToast(`Supplier ${d.name} added successfully`, 'ok');
      closeAddModal('supplier');
    }).catch(error => {
      showToast(error.message || 'Unable to save supplier right now.', 'no');
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    refreshDeliverySupplierOptions();
  });

  function submitAddReq(e){
    e.preventDefault();
    const d = Object.fromEntries(new FormData(e.target).entries());

    fetch(procurementUrl('requisitions'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: new URLSearchParams({
        rq: d.rq,
        item: d.item || '',
        qty: d.qty || '1',
        uom: d.uom || 'pcs',
        dept: d.dept || '',
        requester: d.requester || '',
        dateReq: d.dateReq || '',
        notes: d.notes || ''
      }).toString()
    }).then(res => res.json()).then(json => {
      const table = document.querySelector('#requisitions-table tbody');
      if(table){
        const tr = document.createElement('tr');
        tr.dataset.status = 'pending';
        tr.dataset.date = d.dateReq;
        tr.dataset.uom = d.uom || 'pcs';
        tr.dataset.notes = d.notes || '';
        tr.innerHTML = `
          <td><a class="po-link">${d.rq}</a></td>
          <td>${d.item}</td>
          <td>${d.qty}</td>
          <td>${deliveryBadge('Scheduled')}</td>
          <td>${d.dept}</td>
          <td>${d.requester}</td>
          <td>${statusPill('Pending')}</td>
          <td>${fmtDate(d.dateReq)}</td>
          <td><span class="row-actions"><button title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button><button title="Edit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></button><button class="del" title="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span></td>`;
        if(json && (json.id || (json.data && json.data.id))) tr.dataset.id = json.id || json.data.id;
        table.prepend(tr);
      }
      NEXT_ID.req++;
      ID_COUNTS.req++;
      initRowActionButtons();
      showToast(`Requisition ${d.rq} submitted for approval`, 'ok');
      closeAddModal('req');
    }).catch(() => {
      showToast('Unable to save requisition right now.', 'no');
    });
  }

  function submitAddDelivery(e){
    e.preventDefault();
    const d = Object.fromEntries(new FormData(e.target).entries());
    const poInfo = getPoInfo(d.po || '');
    if(!poInfo || poInfo.status !== 'approved'){
      showToast('Only approved purchase orders can be logged in deliveries.', 'info');
      return;
    }
    const expectedDate = poInfo.expected || '';
    const isDelayed = Boolean(expectedDate && new Date(expectedDate) < new Date(todayISO()));
    const statusLabel = isDelayed ? 'Delayed' : 'intransit';
    const stage = isDelayed ? '1' : '2';

    fetch(procurementUrl('deliveries'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: new URLSearchParams({
        dr: d.dr,
        po: d.po,
        supplier: d.supplier,
        items: d.items || '',
        qty: d.qty || '1',
        delDate: d.delDate || '',
        status: statusLabel,
        remarks: d.remarks || ''
      }).toString()
    }).then(res => res.json().then(json => ({ ok: res.ok, json }))).then(({ ok, json }) => {
      if(!ok){
        showToast(json?.message || 'Only approved purchase orders can be logged in deliveries.', 'info');
        return;
      }
      const shipmentNumber = json?.shipment_number || json?.data?.shipment_number || d.dr;
      const table = document.querySelector('#deliveries-table tbody');
      const poRow = findPoRowByNumber(d.po || '');
      if(table){
        const tr = document.createElement('tr');
        tr.dataset.status = isDelayed ? 'delayed' : 'intransit';
        tr.dataset.date = d.delDate;
        tr.dataset.ship = shipmentNumber;
        tr.dataset.po = d.po;
        tr.dataset.sup = d.supplier;
        tr.dataset.stage = stage;
        tr.dataset.note = d.remarks || `${d.items} · Qty ${d.qty}`;
        tr.dataset.carrier = 'Assigned carrier';
        tr.dataset.expected = expectedDate;
        tr.innerHTML = `
          <td><a class="po-link">${htmlEscape(shipmentNumber)}</a></td>
          <td><a class="po-link">${d.po}</a></td>
          <td>${supplierPill(d.supplier)}</td>
          <td>${htmlEscape(d.items || '—')}</td>
          <td>${fmtDate(expectedDate)}</td>
          <td>${statusPill(statusLabel)}</td>
          <td>${fmtDate(d.delDate)}</td>
          <td><span class="row-actions"><button title="Track" onclick="openTrackModal(this)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button></span></td>`;
        if(json && (json.id || (json.data && json.data.id))) tr.dataset.id = json.id || json.data.id;
        table.prepend(tr);
      }
      if(poRow){
        poRow.dataset.status = 'processing';
        poRow.children[6].innerHTML = statusPill('Processing');
      }
      const reqRow = findReqRowByRef(d.po);
      if(reqRow){
        updateRowStatus(reqRow, 'intransit');
        reqRow.children[6].innerHTML = statusPill('intransit');
        reqRow.dataset.status = 'intransit';
        persistRequisitionStatus(reqRow, 'intransit');
      }
      const shipmentSequence = Number(String(shipmentNumber).match(/(\d+)$/)?.[1] || 0);
      ID_COUNTS.dr = Math.max(ID_COUNTS.dr, shipmentSequence);
      NEXT_ID.dr = ID_COUNTS.dr + 1;
      initRowActionButtons();
      updateStatusCounts();
      showToast(`Delivery ${shipmentNumber} logged`, 'ok');
      closeAddModal('delivery');
    }).catch(() => {
      showToast('Unable to save delivery right now.', 'no');
    });
  }

  function submitAddInvoice(e){
    e.preventDefault();
    const d = Object.fromEntries(new FormData(e.target).entries());
    const table = document.querySelector('#invoices-table tbody');
    if(!table){ closeAddModal('invoice'); return; }
    const statusLabel = ({unpaid:'Unpaid', partial:'Overdue', paid:'Paid', overdue:'Overdue'})[d.status] || 'Unpaid';
    const tr = document.createElement('tr');
    tr.dataset.status = statusLabel.toLowerCase();
    tr.dataset.date = d.invDate;
    tr.dataset.amount = Number(d.amount || 0);
    tr.dataset.supplier = d.supplier || '';
    tr.dataset.dueDate = fmtDate(d.dueDate);
    tr.dataset.method = d.method || '';
    tr.dataset.notes = d.notes || '';
    tr.innerHTML = `
      <td><a class="po-link">${d.inv}</a></td>
      <td><a class="po-link">${d.po}</a></td>
      <td>${fmtDate(d.invDate)}</td>
      <td><b>${money(d.amount)}</b></td>
      <td>${statusPill(statusLabel)}</td>
      <td><span class="row-actions"><button title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button><button title="Edit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></button><button class="del" title="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span></td>`;
    table.prepend(tr);
    NEXT_ID.inv++;
    initRowActionButtons();
    showToast(`Invoice ${d.inv} recorded`, 'ok');
    closeAddModal('invoice');
  }
