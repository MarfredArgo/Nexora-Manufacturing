@extends('procurement::layouts.app')

@section('title', 'Nexora ERP — Dashboard')

@section('content')
<section id="page-dashboard">
      @php
        $dashboardTotalSpend = $totalSpendFormatted ?? ('₱' . number_format($totalSpend, 2));
      @endphp
      <div class="page-head">
        <h1>Procurement</h1>
        <p>Manage purchase orders, suppliers, and requisitions.</p>
      </div>

      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">ACTIVE POS</div>
          <div class="stat-value" id="dash-stat-po">{{ $poCount }}</div>
          <div class="stat-sub">{{ $poCount > 0 ? $dashboardTotalSpend . ' total spend' : 'No purchase orders yet' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">SUPPLIERS</div>
          <div class="stat-value" id="dash-stat-sup">{{ $supplierCount }}</div>
          <div class="stat-sub">{{ $supplierCount > 0 ? 'Active suppliers' : 'No supplier data yet' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">REQUISITIONS</div>
          <div class="stat-value" id="dash-stat-req">{{ $requisitionCount }}</div>
          <div class="stat-sub">{{ $requisitionCount > 0 ? 'Open requisitions' : 'No requisitions yet' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">DELIVERIES</div>
          <div class="stat-value" id="dash-stat-inv">{{ $deliveryCount }}</div>
          <div class="stat-sub">{{ $deliveryCount > 0 ? ($pendingDeliveries > 0 ? $pendingDeliveries . ' in progress' : 'All shipments settled') : 'No deliveries yet' }}</div>
        </div>
      </div>
      
      <div class="dash-grid-3">
        <div class="panel">
          <h2>Spend by Category</h2>
          <div class="panel-sub">
            {{ count($spendByCategory) > 0 ? $dashboardTotalSpend . ' total' : 'No spend data' }}
            @if(count($spendByCategoryAll ?? []) > 5)
              <a href="#" class="panel-sub-action" onclick="event.preventDefault(); openSpendByCategoryModal();">View all →</a>
            @endif
          </div>
          @if(count($spendByCategory) > 0)
          <div class="chart-bar-list" id="chart-spend-category">
            @foreach($spendByCategory as $item)
            <div class="chart-bar-item-h" title="{{ $item->category }} · {{ $item->formatted_total ?? '₱' . number_format($item->total, 2) }}">
              <div class="chart-bar-item-h-top">
                <span class="chart-bar-label">{{ $item->category }}</span>
                <span class="chart-bar-value">{{ $item->formatted_total ?? '₱' . number_format($item->total, 2) }}</span>
              </div>
              <div class="chart-bar-track">
                <div class="chart-bar-fill" style="width: {{ $spendByCategory->max('total') > 0 ? ($item->total / $spendByCategory->max('total')) * 100 : 0 }}%"></div>
              </div>
            </div>
            @endforeach
          </div>
          @else
          <div style="padding:32px 12px; text-align:center; color:var(--text-muted);">
            No spend data available.
          </div>
          @endif
        </div>

        <div class="panel">
          <h2>PO Status Split</h2>
          <div class="panel-sub">{{ $poCount }} total purchase orders</div>
          @if(count($poStatusBreakdown) > 0)
          <div class="donut-chart-container">
            <canvas id="dash-donut" width="200" height="200"></canvas>
            <div class="donut-center" id="dash-donut-center">
              <div class="donut-center-val">{{ $poCount }}</div>
              <div class="donut-center-label">total purchase orders</div>
            </div>
            <div class="donut-tooltip" id="dash-donut-tooltip"></div>
          </div>
          <div class="donut-legend" id="dash-donut-legend">
            @foreach($poStatusBreakdown as $status => $count)
            <div class="donut-legend-item" data-status="{{ $status }}" data-count="{{ $count }}" data-percent="{{ $poCount > 0 ? round(($count / $poCount) * 100) : 0 }}">
              <span class="donut-legend-dot status-{{ $status }}"></span>
              <span class="donut-legend-label" title="{{ ucfirst($status) }}">{{ ucfirst($status) }}</span>
              <span class="donut-legend-value">{{ $poCount > 0 ? round(($count / $poCount) * 100) : 0 }}%</span>
            </div>
            @endforeach
          </div>
          @else
          <div style="padding:32px 12px; text-align:center; color:var(--text-muted);">
            No PO status data available.
          </div>
          @endif
        </div>

        <div class="panel">
          <h2>Top Suppliers</h2>
          <div class="panel-sub">By total PO spend</div>
          @if(count($topSuppliers ?? []) > 0)
          <div class="top-supplier-list" id="dash-top-suppliers">
            @foreach($topSuppliers ?? [] as $index => $supplier)
            <div class="top-supplier-row">
              <span class="ts-rank {{ $index === 0 ? 'top' : '' }}">{{ $index + 1 }}</span>
              <div class="ts-body">
                <div class="ts-name">{{ $supplier->name }}</div>
              </div>
              <div class="ts-val">{{ $supplier->formatted_total_spend }}</div>
            </div>
            @endforeach
          </div>
          @else
          <div style="padding:32px 12px; text-align:center; color:var(--text-muted);">
            No top suppliers to display.
          </div>
          @endif
        </div>
      </div>

      <div class="dash-po-grid">
      <div class="panel">
        <div class="filter-tabs" id="dash-po-tabs">
          <div class="tab active" data-filter="recent">Recent Purchase Orders</div>
          <a href="{{ route('procurement.purchase-orders.index') }}" style="margin-left:auto; color:var(--blue); font-weight:600; font-size:13px;">View all purchase orders →</a>
        </div>
        <table class="data-table" id="dash-po-table">
          <thead>
            <tr>
              <th class="sortable" data-key="po">PO#</th>
              <th class="sortable" data-key="supplier">SUPPLIER</th>
              <th class="sortable" data-key="amount">AMOUNT</th>
              <th class="sortable" data-key="priority">PRIORITY</th>
              <th class="sortable" data-key="status">STATUS</th>
              <th class="sortable sort-desc" data-key="date">DATE</th>
            </tr>
          </thead>
          <tbody>
            @if(count($recentPOs) > 0)
              @foreach($recentPOs as $po)
              <tr>
                <td><strong>{{ $po->po_number }}</strong></td>
                <td>{{ $suppliersMap[$po->supplier_id] ?? 'N/A' }}</td>
                <td>₱{{ number_format($po->amount, 2) }}</td>
                <td><span class="priority-pill {{ $po->priority }}">{{ ucfirst($po->priority) }}</span></td>
                <td><span class="status-pill {{ $po->status }}">{{ ucfirst($po->status) }}</span></td>
                <td>{{ \Carbon\Carbon::parse($po->order_date)->format('M d, Y') }}</td>
              </tr>
              @endforeach
            @else
            <tr>
              <td colspan="6" style="text-align:center; padding:32px 16px; color:var(--text-muted);">
                No purchase orders yet.
              </td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
        <div class="panel dash-del-panel">
          <h2><span class="live-pulse"></span>Delivery Status</h2>
          <div class="panel-sub">{{ $deliveryCount }} total / {{ $pendingDeliveries }} in-transit</div>
          <div class="dash-del-list" id="dash-del-list">
            @if(count($recentDeliveries) > 0)
              @foreach($recentDeliveries as $delivery)
              <div class="dash-del-item">
                <div class="dash-del-header">
                  <strong>{{ preg_match('/(\d+)$/', (string) $delivery->shipment_number, $m) ? $m[1] : $delivery->shipment_number }}{{ isset($delivery->po_number) && $delivery->po_number ? ' · ' . $delivery->po_number : '' }}</strong>
                  <span class="status-pill {{ $delivery->status }}">{{ ucfirst($delivery->status) }}</span>
                </div>
                <div class="dash-del-meta">
                  {{ $deliverySuppliersMap[$delivery->supplier_id] ?? 'N/A' }} • {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}
                </div>
              </div>
              @endforeach
            @else
            <div style="padding:32px 12px; text-align:center; color:var(--text-muted);">
              No deliveries to display.
            </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    <div class="modal-overlay" id="spend-by-category-modal" onclick="if(event.target===this) closeSpendByCategoryModal()">
      <div class="modal-box form-modal">
        <div class="modal-head">
          <div>
            <h3>Spend by Category</h3>
            <p style="font-size:12px;color:var(--muted);margin-top:3px;">All categories, ranked by total purchase order spend.</p>
          </div>
          <button class="modal-close" onclick="closeSpendByCategoryModal()">✕</button>
        </div>
        <div style="padding:16px 24px 4px;">
          <input type="text" id="spend-by-category-search" placeholder="Search category..." oninput="filterSpendByCategoryModal(this.value)"
                 style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; outline:none;">
        </div>
        <div class="chart-bar-list chart-bar-list-h" id="spend-by-category-modal-list" style="padding:12px 24px 24px; max-height:56vh; overflow-y:auto;"></div>
      </div>
    </div>

    @php
    $poStatusJson = json_encode($poStatusBreakdown);
    $spendByCategoryAllJson = json_encode(($spendByCategoryAll ?? collect())->map(fn($i) => ['category' => $i->category, 'total' => (float) $i->total, 'formatted' => $i->formatted_total])->values());
    @endphp

    <script>
      window.dashboardData = {
        poStatus: {!! $poStatusJson !!},
        spendByCategoryAll: {!! $spendByCategoryAllJson !!}
      };
    </script>
@endsection
