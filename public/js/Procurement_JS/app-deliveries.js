  /* ---------- Delivery tracking modal ---------- */
  function openTrackModal(btn){
    const row = btn.closest('tr');
    const d = row.dataset;
    const stage = parseInt(d.stage || 0, 10);
    const ship = d.ship || textFrom(row.children[0]);
    const supplier = d.sup || supplierNameFromCell(row.children[2]);
    const dateLabel = d.date || row.getAttribute('data-date') || textFrom(row.children[6]);
    let currentStatus = (d.status || '').toLowerCase();
    const expectedDate = d.expected || '';
    if (!['delivered','completed'].includes(currentStatus) && expectedDate && new Date(expectedDate) < new Date(todayISO())) {
      currentStatus = 'delayed';
      row.dataset.status = 'delayed';
      row.dataset.stage = '1';
      row.children[5].innerHTML = statusPill('Delayed');
    }
    document.getElementById('track-title').textContent = `${ship} · ${supplier}`;
    document.getElementById('track-body').innerHTML = `
      <div class="detail-grid">
        <div class="detail-card"><h4>Shipment summary</h4><div class="modal-row"><span>Shipment no.</span><span>${ship}</span></div><div class="modal-row"><span>PO number</span><span>${d.po || textFrom(row.children[1])}</span></div><div class="modal-row"><span>Supplier</span><span>${supplier}</span></div><div class="modal-row"><span>Status</span><span>${textFrom(row.children[5])}</span></div></div>
        <div class="detail-card"><h4>Tracking info</h4><div class="modal-row"><span>Date</span><span>${dateLabel}</span></div><div class="modal-row"><span>Carrier</span><span>${d.carrier || 'Assigned carrier'}</span></div><div class="modal-row"><span>Current stage</span><span>${Math.min(stage + 1, 5)} / 5</span></div><div class="modal-row"><span>Delivery note</span><span>${d.note || 'No additional note'}</span></div></div>
      </div>
    `;
    const markReceivedBtn = document.getElementById('mark-received-btn');
    const markCompletedBtn = document.getElementById('mark-completed-btn');
    if (currentStatus === 'delivered') {
      markReceivedBtn.style.display = 'none';
      markCompletedBtn.style.display = 'block';
    } else if (currentStatus === 'intransit' || currentStatus === 'delayed') {
      markReceivedBtn.style.display = 'block';
      markCompletedBtn.style.display = 'none';
    } else {
      markReceivedBtn.style.display = 'none';
      markCompletedBtn.style.display = 'none';
    }
    document.getElementById('track-modal').__row = row;
    document.getElementById('track-modal').classList.add('open');
  }
  function closeTrackModal(){
    document.getElementById('track-modal').classList.remove('open');
  }
  function markReceived(){
    const row = document.getElementById('track-modal').__row;
    if(row){
      row.dataset.status = 'delivered';
      row.dataset.stage = '4';
      row.children[5].innerHTML = statusPill('Delivered');
      const poRow = findPoRowByNumber(row.dataset.po || '');
      if(poRow){
        poRow.dataset.status = 'processing';
        poRow.children[6].innerHTML = statusPill('Processing');
      }
      // Persist delivery status to backend when possible
      const delId = row.dataset.id;
      if(delId){
        fetch(procurementUrl(`deliveries/${delId}`), { method: 'PUT', headers: { 'Content-Type':'application/x-www-form-urlencoded', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: new URLSearchParams({ status: 'delivered', remarks: row.dataset.note || '' }).toString() }).then(()=>{}).catch(()=>showToast('Unable to persist delivery status to server.', 'no'));
      }
      // Persist related PO status if present
      const relatedPoRow = findPoRowByNumber(row.dataset.po || '');
      if(relatedPoRow && relatedPoRow.dataset.id){
        fetch(procurementUrl(`purchase-orders/${relatedPoRow.dataset.id}`), { method: 'PUT', headers: { 'Content-Type':'application/x-www-form-urlencoded', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: new URLSearchParams({ status: 'processing' }).toString() }).then(()=>{}).catch(()=>{});
      }
      const reqRow = findReqRowByRef(row.dataset.po || '');
      if(reqRow){
        updateRequisitionStatus(row.dataset.po || '', 'Delivered');
        persistRequisitionStatus(reqRow, 'Delivered');
      }
      showToast(`${row.dataset.ship} marked as received`, 'ok');
    }
    closeTrackModal();
  }

  function markCompleted(){
    const row = document.getElementById('track-modal').__row;
    if(row){
      row.dataset.status = 'completed';
      row.dataset.stage = '4';
      row.children[5].innerHTML = statusPill('Completed');
      const poRow = findPoRowByNumber(row.dataset.po || '');
      if(poRow){
        poRow.dataset.status = 'completed';
        poRow.children[6].innerHTML = statusPill('Completed');
      }
      const delId = row.dataset.id;
      if(delId){
        fetch(procurementUrl(`deliveries/${delId}`), { method: 'PUT', headers: { 'Content-Type':'application/x-www-form-urlencoded', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: new URLSearchParams({ status: 'completed', remarks: row.dataset.note || '' }).toString() }).then(()=>{}).catch(()=>showToast('Unable to persist delivery status to server.', 'no'));
      }
      const relatedPoRow2 = findPoRowByNumber(row.dataset.po || '');
      if(relatedPoRow2 && relatedPoRow2.dataset.id){
        fetch(procurementUrl(`purchase-orders/${relatedPoRow2.dataset.id}`), { method: 'PUT', headers: { 'Content-Type':'application/x-www-form-urlencoded', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: new URLSearchParams({ status: 'completed' }).toString() }).then(()=>{}).catch(()=>{});
      }
      const reqRow2 = findReqRowByRef(row.dataset.po || '');
      if(reqRow2){
        updateRequisitionStatus(row.dataset.po || '', 'Completed');
        persistRequisitionStatus(reqRow2, 'Completed');
      }
      showToast(`${row.dataset.ship} marked as completed`, 'ok');
    }
    closeTrackModal();
  }
