@extends('procurement::layouts.app')

@section('title', 'Nexora ERP — Purchase Orders')

@section('content')
<section id="page-purchase-orders">
      @php
        $statusCounts = $statusCounts ?? collect($purchaseOrders)->map(function ($po) {
            return strtolower(str_replace([' ', '_'], '-', $po->status ?? 'pending'));
        })->countBy();
      @endphp

      <div class="page-head">
        <h1>Purchase Orders</h1>
        <p>All purchase orders for Techforge PC Solutions</p>
      </div>

      <div class="status-chart" id="po-status-chart">
        <div class="status-chart-item pending" data-status="pending" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);border-color:#ff9800;" onclick="filterByStatus('po-table', 'pending', this)">
          <div class="status-label">Pending</div>
          <div class="status-count">{{ $statusCounts->get('pending', 0) }}</div>
        </div>
        <div class="status-chart-item approved" data-status="approved" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-color:#4caf50;" onclick="filterByStatus('po-table', 'approved', this)">
          <div class="status-label">Approved</div>
          <div class="status-count">{{ $statusCounts->get('approved', 0) }}</div>
        </div>
        <div class="status-chart-item rejected" data-status="rejected" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);border-color:#f44336;" onclick="filterByStatus('po-table', 'rejected', this)">
          <div class="status-label">Rejected</div>
          <div class="status-count">{{ $statusCounts->get('rejected', 0) }}</div>
        </div>
        <div class="status-chart-item cancelled" data-status="cancelled" style="background:linear-gradient(135deg,#f1f3f6,#e2e6ee);border-color:#7c88a3;" onclick="filterByStatus('po-table', 'cancelled', this)">
          <div class="status-label">Cancelled</div>
          <div class="status-count">{{ $statusCounts->get('cancelled', 0) }}</div>
        </div>
        <div class="status-chart-item processing" data-status="processing" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);border-color:#2196f3;" onclick="filterByStatus('po-table', 'processing', this)">
          <div class="status-label">Processing</div>
          <div class="status-count">{{ $statusCounts->get('processing', 0) }}</div>
        </div>
        <div class="status-chart-item delivered" data-status="delivered" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-color:#4caf50;" onclick="filterByStatus('po-table', 'delivered', this)">
          <div class="status-label">Delivered</div>
          <div class="status-count">{{ $statusCounts->get('delivered', 0) }}</div>
        </div>
        <div class="status-chart-item completed" data-status="completed" style="background:linear-gradient(135deg,#e0f2f1,#b2dfdb);border-color:#009688;" onclick="filterByStatus('po-table', 'completed', this)">
          <div class="status-label">Completed</div>
          <div class="status-count">{{ $statusCounts->get('completed', 0) }}</div>
        </div>
      </div>

      <div class="panel">
        <div class="table-toolbar">
          <h2>All Purchase Orders</h2>
          <div class="search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <input placeholder="Search..." oninput="filterTable('po-table', this.value)">
          </div>
          
          <button class="toolbar-btn" onclick="toggleFilterPanel('po-filter-panel', this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 5h18l-7 8v6l-4 2v-8L3 5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            Filter
          </button>
          <button class="toolbar-btn primary" onclick="openAddModal('po')">+ New PO</button>
        </div>
        <div class="filter-panel hidden" id="po-filter-panel">
          <div class="filter-group">
            <label>Status</label>
            <select id="po-filter-status" onchange="applyPOFilter()">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="cancelled">Cancelled</option>
              <option value="delivered">Delivered</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Date Range</label>
            <div class="date-range">
              <input type="date" id="po-filter-date-from" placeholder="From">
              <span class="date-range-sep">→</span>
              <input type="date" id="po-filter-date-to" placeholder="To">
            </div>
            <div class="date-presets">
              <button type="button" class="date-preset" onclick="setDatePreset('po','today',this)">Today</button>
              <button type="button" class="date-preset" onclick="setDatePreset('po','week',this)">This Week</button>
              <button type="button" class="date-preset" onclick="setDatePreset('po','month',this)">This Month</button>
            </div>
          </div>
          <div class="filter-group">
            <label>Amount</label>
            <select id="po-filter-amount" onchange="applyPOFilter()">
              <option value="">Any Amount</option>
              <option value="0-10000">Under ₱10,000</option>
              <option value="10000-50000">₱10,000 - ₱50,000</option>
              <option value="50000+">Over ₱50,000</option>
            </select>
          </div>
          <div class="filter-actions">
            <button class="btn-text" onclick="clearPOFilter()">Clear</button>
            <button class="btn-primary" onclick="applyPOFilter()">Apply</button>
          </div>
        </div>

        <table class="data-table sortable-table" id="po-table">
          <thead>
            <tr>
              <th class="sortable" data-key="po">PO NUMBER<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="supplier">SUPPLIER<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="item">ITEM<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th>TOTAL AMOUNT</th>
              <th class="sortable" data-key="priority">PRIORITY<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="status">STATUS<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable sort-desc" data-key="date">DATE<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($purchaseOrders) && count($purchaseOrders))
              @foreach($purchaseOrders as $p)
                @php
                  $sname = $p->supplier_name ?? '';
                  $parts = preg_split('/\s+/', trim($sname));
                  $initials = '';
                  if($parts){
                    $initials = strtoupper(substr($parts[0],0,1));
                    if(count($parts) > 1) $initials .= strtoupper(substr($parts[count($parts)-1],0,1));
                  }
                  $colorMap = ['GigaCore Components'=>'#22c55e','Global Tech Supply'=>'#0ea5e9','MegaStar Trading'=>'#f2994a','Primo Electronics'=>'#22c55e','Quantum Motherboards'=>'#7a5af0','Silverline PSU Ltd'=>'#eb5757','Silverline PSU Ltd.'=>'#eb5757','TechWholesale PH'=>'#2f6fed','Trident RAM Supply'=>'#0ea5e9'];
                  $colors = ['#2f6fed','#22c55e','#f2994a','#7a5af0','#eb5757','#0ea5e9','#1fa971','#e0338c'];
                  $badgeColor = $colorMap[$sname] ?? null;
                  if(!$badgeColor){
                    $h = 0; foreach(str_split($sname ?? '') as $ch) $h = ($h*31 + ord($ch)) & 0xffff;
                    $badgeColor = $colors[$h % count($colors)];
                  }
                @endphp
                @php
                  // Only the first ordered item is shown in the table; the rest are
                  // visible in the row's details modal. Total amount is re-read from
                  // the PO's actual item rows (falls back to the stored amount).
                  $poItems = collect($poItemsByOrder[$p->id] ?? []);
                  $firstItemName = $poItems->first()['name'] ?? (trim(explode(',', (string) ($p->item ?? ''))[0]) ?: '');
                  $moreItemCount = max(0, $poItems->count() - 1);
                  $orderedTotal = $poItems->count()
                    ? $poItems->sum(fn($it) => (float) ($it['qty'] ?? 0) * (float) ($it['unitPrice'] ?? 0))
                    : (float) ($p->amount ?? 0);
                @endphp
                <tr data-id="{{ $p->id }}" data-item="{{ $p->item ?? '' }}" data-items='@json($poItemsByOrder[$p->id] ?? [])' data-qty="{{ $p->qty ?? 0 }}" data-amount="{{ $orderedTotal }}" data-unit-price="{{ $p->unit_price ?? 0 }}" data-priority="{{ $p->priority ?? 'normal' }}" data-status="{{ strtolower(str_replace([' ', '_'], '-', $p->status ?? 'pending')) }}" data-expected="{{ $p->expected_delivery_date ?? '' }}" data-req-ref="{{ $p->requisition_reference ?? '' }}">
                  <td><a class="po-link">{{ $p->po_number }}</a></td>
                  <td><div class="supplier-pill-cell"><span class="supplier-pill"><span class="supplier-badge" style="background: {{ $badgeColor }}">{{ $initials }}</span>{{ $p->supplier_name ?? '—' }}</span></div></td>
                  <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis" title="{{ $p->item ?? '' }}">{{ $firstItemName ?: '—' }}@if($moreItemCount > 0)<span class="item-more">+{{ $moreItemCount }} more</span>@endif</td>
                  <td><b>₱{{ number_format($orderedTotal, 2) }}</b></td>
                  @php
                    $priorityClass = strtolower($p->priority ?? 'normal');
                    if(!in_array($priorityClass, ['urgent','high','normal','low'])) {
                      $priorityClass = 'normal';
                    }
                    $statusClass = strtolower(str_replace(' ', '-', $p->status ?? 'pending'));
                  @endphp
                  <td><span class="priority-pill {{ $priorityClass }}">{{ strtoupper($p->priority ?? 'NORMAL') }}</span></td>
                  <td><span class="status-pill {{ $statusClass }}">{{ ucfirst($p->status ?? 'pending') }}</span></td>
                  <td>{{ $p->order_date ?? '' }}</td>
                  <td><span class="row-actions"><button title="View">👁</button><button title="Edit">✎</button><button class="del" title="Delete">🗑</button></span></td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="8" style="text-align:center; padding:32px 16px; color:var(--text-muted);">No purchase orders yet.</td>
              </tr>
            @endif
          </tbody>
        </table>
        <div class="table-footer">
          <br>
          <div>Showing <b>{{ isset($purchaseOrders) ? count($purchaseOrders) : 0 }}</b> purchase orders</div>
          <div class="pager"><button class="page-btn">‹</button><button class="page-btn active">1</button><button class="page-btn">›</button></div>
        </div>
      </div>
    </section>
@endsection
