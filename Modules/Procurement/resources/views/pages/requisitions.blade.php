@extends('procurement::layouts.app')

@section('title', 'Nexora ERP — Requisitions')

@section('content')
<section id="page-requisitions">
      @php
        $statusCounts = $statusCounts ?? collect($requisitions)->map(function ($req) {
            return strtolower(str_replace(' ', '', $req->status ?? 'Pending'));
        })->countBy();
      @endphp

      <div class="page-head">
        <h1>Requisitions</h1>
        <p>All purchase requisitions</p>
      </div>

      <div class="filter-tabs" id="req-tabs" style="margin-bottom:16px;">
        <div class="tab active" data-req-tab="requests" onclick="switchReqTab('requests', this)">Requests</div>
        <div class="tab" data-req-tab="defects" onclick="switchReqTab('defects', this)">Defect Items</div>
      </div>

      <div id="req-tab-requests">

      <div class="status-chart" id="requisition-status-chart">
        <div class="status-chart-item pending" data-status="pending" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);border-color:#ff9800;" onclick="filterByStatus('requisitions-table', 'pending', this)">
          <div class="status-label">Pending</div>
          <div class="status-count">{{ $statusCounts->get('pending', 0) }}</div>
        </div>
        <div class="status-chart-item processing" data-status="processing" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);border-color:#2196f3;" onclick="filterByStatus('requisitions-table', 'processing', this)">
          <div class="status-label">Processing</div>
          <div class="status-count">{{ $statusCounts->get('processing', 0) }}</div>
        </div>
        <div class="status-chart-item intransit" data-status="intransit" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);border-color:#2196f3;" onclick="filterByStatus('requisitions-table', 'intransit', this)">
          <div class="status-label">intransit</div>
          <div class="status-count">{{ $statusCounts->get('intransit', 0) }}</div>
        </div>
        <div class="status-chart-item delivered" data-status="delivered" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-color:#4caf50;" onclick="filterByStatus('requisitions-table', 'delivered', this)">
          <div class="status-label">Delivered</div>
          <div class="status-count">{{ $statusCounts->get('delivered', 0) }}</div>
        </div>
        <div class="status-chart-item completed" data-status="completed" style="background:linear-gradient(135deg,#e0f2f1,#b2dfdb);border-color:#009688;" onclick="filterByStatus('requisitions-table', 'completed', this)">
          <div class="status-label">Completed</div>
          <div class="status-count">{{ $statusCounts->get('completed', 0) }}</div>
        </div>
        <div class="status-chart-item cancelled" data-status="cancelled" style="background:linear-gradient(135deg,#f1f3f6,#e2e6ee);border-color:#7c88a3;" onclick="filterByStatus('requisitions-table', 'cancelled', this)">
          <div class="status-label">Cancelled</div>
          <div class="status-count">{{ $statusCounts->get('cancelled', 0) }}</div>
        </div>
      </div>

      <div class="panel">
        <div class="table-toolbar">
          <h2>Requisition List</h2>
          <div class="search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <input placeholder="Search" oninput="filterTable('requisitions-table', this.value)">
          </div>
          <button class="toolbar-btn" onclick="toggleFilterPanel('req-filter-panel', this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 5h18l-7 8v6l-4 2v-8L3 5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            Filter
          </button>
        </div>
        <div class="filter-panel hidden" id="req-filter-panel">
          <div class="filter-group">
            <label>Status</label>
            <select id="req-filter-status" onchange="applyReqFilter()">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="intransit">intransit</option>
              <option value="delivered">Delivered</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Date Range</label>
            <div class="date-range">
              <input type="date" id="req-filter-date-from" placeholder="From">
              <span class="date-range-sep">→</span>
              <input type="date" id="req-filter-date-to" placeholder="To">
            </div>
            <div class="date-presets">
              <button type="button" class="date-preset" onclick="setDatePreset('req','today',this)">Today</button>
              <button type="button" class="date-preset" onclick="setDatePreset('req','week',this)">This Week</button>
              <button type="button" class="date-preset" onclick="setDatePreset('req','month',this)">This Month</button>
            </div>
          </div>
          <div class="filter-group">
            <label>Priority</label>
            <select id="req-filter-priority" onchange="applyReqFilter()">
              <option value="">All Priority</option>
              <option value="urgent">Urgent</option>
              <option value="high">High</option>
              <option value="normal">Normal</option>
              <option value="low">Low</option>
            </select>
          </div>
          <div class="filter-actions">
            <button class="btn-text" onclick="clearReqFilter()">Clear</button>
            <button class="btn-primary" onclick="applyReqFilter()">Apply</button>
          </div>
        </div>

        <table class="data-table sortable-table" id="requisitions-table">
          <thead>
            <tr>
              <th class="sortable" data-key="req">REQ #<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="item">ITEM<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="qty">QTY<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="priority">PRIORITY<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="dept">DEPARTMENT<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th>REQUESTED BY</th>
              <th class="sortable" data-key="status">STATUS<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable sort-desc" data-key="date">DATE<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse($requisitions as $req)
              <tr data-id="{{ $req->id ?? '' }}" data-source="{{ $req->source_connection ?? '' }}" data-status="{{ strtolower(str_replace(' ', '', $req->status ?? 'Pending')) }}" data-date="{{ $req->request_date }}" data-uom="{{ $req->uom ?? 'pcs' }}" data-notes="{{ $req->notes ?? '' }}" data-po="{{ isset($req->po_number) ? $req->po_number : '' }}" data-has-po="{{ isset($req->po_number) && $req->po_number ? '1' : '0' }}">
                <td><a class="po-link">{{ $req->requisition_number }}</a></td>
                <td>{{ $req->item }}</td>
                <td>{{ $req->qty }}</td>
                @php
                  $priorityClass = strtolower($req->priority ?? 'normal');
                  if(!in_array($priorityClass, ['urgent','high','normal','low'])) {
                    $priorityClass = 'normal';
                  }
                @endphp
                <td><span class="priority-pill {{ $priorityClass }}">{{ strtoupper($req->priority ?? 'NORMAL') }}</span></td>
                <td>{{ $req->department }}</td>
                <td>{{ $req->requested_by }}</td>
                <td><span class="status-pill {{ strtolower(str_replace(' ', '', $req->status ?? 'Pending')) }}">{{ $req->status ?? 'Pending' }}</span></td>
                <td>{{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('M d, Y') : '—' }}</td>
                <td><span class="row-actions"><button title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button></span></td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align:center; padding:32px 16px; color:var(--text-muted);">
                  No requisitions yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div class="table-footer">
          <br>
          <div>Showing <b>{{ isset($requisitions) ? count($requisitions) : 0 }}</b> requisitions</div>
          <div class="pager"><button class="page-btn">‹</button><button class="page-btn active">1</button><button class="page-btn">›</button></div>
        </div>
      </div>

      </div>{{-- /#req-tab-requests --}}

      <div id="req-tab-defects" class="hidden">
        <div class="panel">
          <div class="table-toolbar">
            <h2>Defect Items</h2>
            <div class="search-box">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              <input placeholder="Search defect items...">
            </div>
          </div>
          <table class="data-table" id="defect-items-table">
            <thead>
              <tr>
                <th>DEFECT #</th>
                <th>ITEM</th>
                <th>QTY</th>
                <th>REASON</th>
                <th>REPORTED BY</th>
                <th>STATUS</th>
                <th>DATE</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" style="text-align:center; padding:40px 16px; color:var(--text-muted);">
                  Defect Items tracking is coming soon.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>{{-- /#req-tab-defects --}}
    </section>
@endsection
