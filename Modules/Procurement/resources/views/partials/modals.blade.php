{{-- Real next PO / shipment sequence numbers (see ProcurementServiceProvider),
     so the add-PO / log-delivery modals auto-fill numbers that won't collide. --}}
<script>
  window.nextPoSeq = {{ (int) ($nextPoSeq ?? 0) }};
  window.nextShipmentSeq = {{ (int) ($nextShipmentSeq ?? 0) }};
</script>

<div class="modal-overlay" id="view-modal" onclick="if(event.target===this) closeViewModal()">
  <div class="modal-box" style="width:620px;max-width:92vw;">
    <div class="modal-head">
      <h3 id="modal-title">Record details</h3>
      <div style="display:flex;align-items:center;gap:8px;">
        <button type="button" id="modal-header-edit-btn" class="modal-close" title="Edit" style="display:none;" onclick="if(document.getElementById('view-modal').__row) openEditModal(document.getElementById('view-modal').__row)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" id="modal-header-delete-btn" class="modal-close" title="Delete" style="display:none;" onclick="if(document.getElementById('view-modal').__row) openDeleteModal(document.getElementById('view-modal').__row)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="modal-close" onclick="closeViewModal()">✕</button>
      </div>
    </div>
    <div id="modal-body" style="padding:8px 0 0;"></div>
    <div class="modal-actions">
      <button type="button" class="btn btn-view" id="modal-reject-btn" onclick="closeViewModal()">Close</button>
      <button type="button" class="btn btn-primary" id="modal-po-btn" style="display:none;" onclick="createPOFromView()">Create Purchase Order</button>
      <button type="button" class="btn btn-approve" id="modal-approve-btn">Action</button>
    </div>
  </div>
</div>

<!-- Edit record modal -->
<div class="modal-overlay" id="edit-modal" onclick="if(event.target===this) closeEditModal()">
  <div class="modal-box form-modal" style="width:720px;max-width:94vw;">
    <div class="modal-head">
      <div>
        <h3 id="edit-modal-title">Edit record</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">Update the selected record.</p>
      </div>
      <button class="modal-close" onclick="closeEditModal()">✕</button>
    </div>
    <form id="edit-record-form" onsubmit="saveEditRecord(event)">
      <div id="edit-modal-body"></div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete record modal -->
<div class="modal-overlay" id="delete-modal" onclick="if(event.target===this) closeDeleteModal()">
  <div class="modal-box confirm-modal">
    <div class="modal-head">
      <div>
        <h3 id="delete-modal-title">Delete record</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">Confirm deletion to continue.</p>
      </div>
      <button class="modal-close" onclick="closeDeleteModal()">✕</button>
    </div>
    <div style="font-size:13px;line-height:1.6;">
      <p>Type <b>delete</b> to confirm removing <b id="delete-modal-target">this record</b>.</p>
      <input id="delete-confirm-input" class="inline-input" oninput="handleDeletePhrase(this.value)" placeholder="Type delete here">
      <div id="delete-final-confirm" style="display:none;margin-top:12px;">
        <p style="font-weight:700;color:var(--red);">This action cannot be undone.</p>
      </div>
    </div>
    <div class="modal-actions">
      <button type="button" class="btn btn-cancel" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="btn btn-danger" id="delete-continue-btn" onclick="continueDeleteFlow()" disabled>Continue</button>
      <button type="button" class="btn btn-danger" id="delete-confirm-btn" onclick="confirmDeleteRecord()" style="display:none;">Delete</button>
    </div>
  </div>
</div>

<!-- Shipment tracking modal -->
<div class="modal-overlay" id="track-modal" onclick="if(event.target===this) closeTrackModal()">
  <div class="modal-box" style="width:520px;">
    <div class="modal-head">
      <h3 id="track-title">Shipment tracking</h3>
      <button class="modal-close" onclick="closeTrackModal()">✕</button>
    </div>
    <div id="track-body"></div>
    <div class="modal-actions">
      <button class="btn btn-view" style="flex:1" onclick="closeTrackModal()">Close</button>
      <button id="mark-completed-btn" class="btn btn-primary" style="flex:1;display:none;" onclick="markCompleted()">Mark Completed</button>
    </div>
  </div>
</div>



<!-- ============ ADD MODALS (PO / Supplier / Delivery) ============ -->

<!-- Add Purchase Order -->
<div class="modal-overlay" id="add-po-modal" onclick="if(event.target===this) closeAddModal('po')">
  <div class="modal-box form-modal-lg">
    <div class="modal-head">
      <div>
        <h3>Create New Purchase Order</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">Fill in the details to submit a new PO for approval.</p>
      </div>
      <button class="modal-close" onclick="closeAddModal('po')">✕</button>
    </div>
   
    <form id="add-po-form" onsubmit="submitAddPO(event)">
      <input type="hidden" name="reqRef" value="">
      {{-- Priority is inherited from the linked requisition (set by the JS when
           converting a requisition to a PO) so a PO always matches its
           requisition's priority; defaults to Normal for a direct PO. --}}
      <input type="hidden" name="priority" value="Normal">
      <div class="form-grid">
        <div class="form-field">
          <label>PO Number <span class="req">*</span></label>
          <input name="po" required readonly>
          <span class="hint">Auto-generated. You may edit if needed.</span>
        </div>
        <div class="form-field">
          <label>Supplier <span class="req">*</span></label>
          <select name="supplier" required>
            <option value="">Select supplier...</option>
            @foreach($suppliers ?? collect() as $supplier)
              <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-field full">
          <label>Items <span class="req">*</span></label>
          <div id="po-items-rows"></div>
          <button type="button" class="btn btn-small" style="margin-top:4px;" onclick="addPoItemRow(document.getElementById('add-po-modal'))">+ Add Item</button>
          <span class="hint">Select a supplier first to load its categories and items.</span>
        </div>

        <div class="form-field">
          <label>Total Amount (₱)</label>
          <input type="number" name="amount" value="0.00" readonly>
        </div>
        <div class="form-field">
          <label>Payment Method</label>
          <select name="paymentMethod">
            <option value="">Select payment method...</option>
            <option value="Cash">Cash</option>
            <option value="Check">Check</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Credit Card">Credit Card</option>
            <option value="COD">Cash on Delivery (COD)</option>
          </select>
        </div>
        <div class="form-field">
          <label>Created By</label>
          <input name="createdBy" value="Justine Nocuenca">
        </div>
        <div class="form-field">
          <label>Expected Delivery <span class="req">*</span></label>
          <input type="date" name="expected" required>
        </div>
        <div class="form-field full">
          <label>Remarks</label>
          <textarea name="remarks" placeholder="Any additional notes for the approver..."></textarea>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeAddModal('po')">Cancel</button>
        <button type="submit" class="btn btn-submit">Submit for Approval</button>
      </div>
    </form>
  </div>
</div>

{{-- Template for one PO item row (Category -> Item cascading, Qty, Unit Price,
     Amount). Cloned by addPoItemRow() in app-forms.js; a <template> keeps the
     markup out of the initial DOM and avoids re-escaping headaches. --}}
<template id="po-item-row-template">
  <div class="po-item-row">
    <div class="form-field">
      <label>Category <span class="req">*</span></label>
      <select class="po-item-category" required>
        <option value="">Select category...</option>
      </select>
    </div>
    <div class="form-field">
      <label>Item <span class="req">*</span></label>
      <select class="po-item-name" required disabled>
        <option value="">Select item...</option>
      </select>
    </div>
    <div class="form-field">
      <label>Qty <span class="req">*</span></label>
      <input type="number" class="po-item-qty" min="1" step="1" value="1" required>
    </div>
    <div class="form-field">
      <label>Unit Price (₱)</label>
      <input type="number" class="po-item-price" min="0" step="0.01" placeholder="0.00" readonly>
    </div>
    <div class="form-field">
      <label>Amount</label>
      <input type="text" class="po-item-amount" value="₱0.00" readonly>
    </div>
    <button type="button" class="po-item-remove" title="Remove item" onclick="removePoItemRow(this)">×</button>
  </div>
</template>

<!-- Add Supplier -->
<div class="modal-overlay" id="add-supplier-modal" onclick="if(event.target===this) closeAddModal('supplier')">
  <div class="modal-box form-modal">
    <div class="modal-head">
      <div>
        <h3>Add New Supplier</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">Register a new supplier record.</p>
      </div>
      <button class="modal-close" onclick="closeAddModal('supplier')">✕</button>
    </div>
    <form id="add-supplier-form" onsubmit="submitAddSupplier(event)">
      <div class="form-grid">
        <div class="form-field">
          <label>Supplier ID <span class="req">*</span></label>
          <input name="sid" required readonly>
        </div>
        <div class="form-field">
          <label>Supplier Name <span class="req">*</span></label>
          <input name="name" placeholder="e.g. TechSource Inc." required>
        </div>
        <div class="form-field">
          <label>Contact Person <span class="req">*</span></label>
          <input name="contact" placeholder="Full name" required>
        </div>
        <div class="form-field">
          <label>Email <span class="req">*</span></label>
          <input type="email" name="email" placeholder="name@company.com" required>
        </div>
        <div class="form-field">
          <label>Phone Number <span class="req">*</span></label>
          <input name="phone" placeholder="+63 9XX XXX XXXX" required>
        </div>
        <div class="form-field">
          <label>Brand <span class="req">*</span></label>
          <input name="brand" placeholder="e.g. Dell, HP" required>
        </div>
        <div class="form-field full">
          <label>Products</label>
          <div id="supplier-products-list" class="product-chip-list">
            <div class="product-list-empty">No products added yet.</div>    
          </div>
          <input type="hidden" name="productsJson" id="supplier-products-json" value="[]">
          <button type="button" class="btn btn-small" style="margin-top:8px;" onclick="openSupplierProductModal()">+ Add Product</button>
        </div>
        <div class="form-field full">
          <label>Address <span class="req">*</span></label>
          <textarea name="address" placeholder="Full business address" required></textarea>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeAddModal('supplier')">Cancel</button>
        <button type="submit" class="btn btn-submit">Save Supplier</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Supplier Product -->
<div class="modal-overlay" id="add-supplier-product-modal" onclick="if(event.target===this) closeSupplierProductModal()">
  <div class="modal-box form-modal">
    <div class="modal-head">
      <div>
        <h3>Add Product</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">Add a product SKU and supply price.</p>
      </div>
      <button class="modal-close" onclick="closeSupplierProductModal()">✕</button>
    </div>
    <form id="add-supplier-product-form" onsubmit="submitSupplierProduct(event)">
      <div class="form-grid">
        <div class="form-field full">
          <label>Product Name <span class="req">*</span></label>
          <input name="productName" placeholder="e.g. NAS Storage 8TB" required onchange="syncSupplierProductSku(this)">
        </div>
        <div class="form-field">
          <label>Category</label>
          <input name="productCategory" placeholder="e.g. Storage">
        </div>
        <div class="form-field">
          <label>SKU code type</label>
          <select name="productSkuType" onchange="updateSupplierProductSkuType(this)">
            <option value="auto" selected>Auto-generated</option>
            <option value="manual">Manual</option>
          </select>
        </div>
        <div class="form-field">
          <label>SKU code</label>
          <input name="productSku" readonly>
        </div>
        <div class="form-field">
          <label>Supply price (₱) <span class="req">*</span></label>
          <input type="number" name="productPrice" min="0" step="0.01" placeholder="0.00" required>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeSupplierProductModal()">Cancel</button>
        <button type="submit" class="btn btn-submit">Add Product</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Delivery -->
<div class="modal-overlay" id="add-delivery-modal" onclick="if(event.target===this) closeAddModal('delivery')">
  <div class="modal-box form-modal">
    <div class="modal-head">
      <div>
        <h3>Log New Delivery</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">Record an incoming shipment linked to a purchase order.</p>
      </div>
      <button class="modal-close" onclick="closeAddModal('delivery')">✕</button>
    </div>
   
    <form id="add-delivery-form" onsubmit="submitAddDelivery(event)">
      <div class="form-grid">
        <div class="form-field">
          <label>Delivery No. <span class="req">*</span></label>
          <input name="dr" required readonly>
        </div>
        <div class="form-field">
          <label>PO Number <span class="req">*</span></label>
          <select name="po" required>
            <option value="">Select PO...</option>
          </select>
        </div>
        <div class="form-field">
          <label>Supplier <span class="req">*</span></label>
          <input name="supplier" placeholder="Auto-fill / edit" required>
        </div>
        <div class="form-field">
          <label>Delivery Date <span class="req">*</span></label>
          <input type="date" name="delDate" required>
        </div>
        <div class="form-field">
          <label>Carrier</label>
          <input type="text" name="carrier" placeholder="e.g. LBC, J&T, in-house fleet">
        </div>

       <div class="form-field">
          <label>Total Amount</label>
          <input type="text" name="amount" placeholder="₱0.00" readonly>
        </div>
        </div>
      <div class="form-field full">
          <label>Items Purchased</label>
          <div id="delivery-items-chips" class="product-chip-list">
            <div class="product-list-empty">Select a PO to load its items.</div>
          </div>
          <input type="hidden" name="items" id="delivery-items-value">
        </div>
 <div class="form-field full">
          <label>Warehouse <span class="req">*</span></label>
          <select name="warehouse_id" id="delivery-warehouse-select" required>
            <option value="">Select the receiving warehouse...</option>
            @foreach($warehouses ?? collect() as $warehouse)
              <option value="{{ $warehouse->id }}">{{ $warehouse->name }}{{ $warehouse->address ? ' — '.$warehouse->address : '' }}</option>
            @endforeach
          </select>
      
        
       
        <div class="form-field full">
          <label>Remarks</label>
          <textarea name="remarks" placeholder="Damage report, condition, etc."></textarea>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeAddModal('delivery')">Cancel</button>
        <button type="submit" class="btn btn-submit">Log Delivery</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="cancel-po-modal" onclick="if(event.target===this) closeCancelModal()">
  <div class="modal-box form-modal" style="max-width:400px;">
    <div class="modal-head">
      <div>
        <h3>Cancel Purchase Order</h3>
        <p style="font-size:12px;color:var(--muted);margin-top:3px;">This action cannot be undone.</p>
      </div>
      <button class="modal-close" onclick="closeCancelModal()">✕</button>
    </div>
    <div style="padding:20px;text-align:center;">
      <p>Are you sure you want to cancel this Purchase Order?</p>
      <p id="cancel-po-number" style="font-weight:bold;color:var(--primary);margin:10px 0;"></p>
    </div>
    <div class="modal-actions">
      <button type="button" class="btn btn-cancel" onclick="closeCancelModal()">No, Keep</button>
      <button type="button" class="btn btn-danger" onclick="confirmCancelPO()">Yes, Cancel PO</button>
    </div>
  </div>
</div>
