<aside class="sidebar">
    <div class="sidebar-title-wrap">
      <div class="sidebar-title">Procurement</div>
      <div style="position:relative;">
        <button class="notif-badge" type="button" onclick="toggleNotifPanel(event)" title="Alerts">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 4a4 4 0 0 0-4 4v1.3c0 .7-.2 1.4-.6 2L6 13v1h12v-1l-1.4-1.7a3.7 3.7 0 0 1-.6-2V8a4 4 0 0 0-4-4Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 17a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          @if(($lowStockAlertCount ?? 0) > 0)
            <span class="nav-badge red">{{ $lowStockAlertCount }}</span>
          @endif
        </button>
        <div class="notif-panel" id="notif-panel">
          @if(($lowStockAlertCount ?? 0) > 0)
            @foreach(($lowStockAlerts ?? collect()) as $alert)
              <div class="notif-item warn">
                <span class="notif-icon">!</span>
                <div class="notif-content">
                  <strong>{{ $alert->item_name ?? 'Unnamed item' }}</strong>
                  <div>Only {{ $alert->stock }} units left</div>
                  <small>{{ $alert->sku ?: 'No SKU' }} · reorder at {{ $alert->reorder_threshold ?? 0 }}</small>
                </div>
              </div>
            @endforeach
          @else
            <div class="notif-item ok">
              <span class="notif-icon">✓</span>
              <div class="notif-content">
                <strong>No alerts</strong>
                You have no new notifications right now.
                <small>System · live</small>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
    <div class="sidebar-desc">Manage purchase orders, suppliers, and requisitions.</div>

    <a href="{{ route('procurement.dashboard') }}" class="nav-item {{ request()->routeIs('procurement.dashboard') ? 'active' : '' }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="2"/><rect x="13" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="2"/><rect x="3" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="2"/><rect x="13" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="2"/></svg>Dashboard
    </a>
    <a href="{{ route('procurement.purchase-orders.index') }}" class="nav-item {{ request()->routeIs('procurement.purchase-orders.*') ? 'active' : '' }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 4h16v16H4z" stroke="currentColor" stroke-width="2"/><path d="M8 9h8M8 13h8" stroke="currentColor" stroke-width="2"/></svg>Purchase Orders
    </a>
    <a href="{{ route('procurement.suppliers.index') }}" class="nav-item {{ request()->routeIs('procurement.suppliers.*') ? 'active' : '' }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 21V8l9-5 9 5v13" stroke="currentColor" stroke-width="2"/><path d="M9 21v-6h6v6" stroke="currentColor" stroke-width="2"/></svg>Suppliers
    </a>
    <a href="{{ route('procurement.requisitions.index') }}" class="nav-item {{ request()->routeIs('procurement.requisitions.*') ? 'active' : '' }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l3 3v15H6z" stroke="currentColor" stroke-width="2"/><path d="M9 11h6M9 15h6" stroke="currentColor" stroke-width="2"/></svg>Requisitions<span class="nav-badge {{ (($requisitionCount ?? 0) > 0) ? 'red' : '' }}">{{ $requisitionCount ?? 0 }}</span>
    </a>
    <a href="{{ route('procurement.deliveries.index') }}" class="nav-item {{ request()->routeIs('procurement.deliveries.*') ? 'active' : '' }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="2"/><circle cx="17" cy="18" r="2" stroke="currentColor" stroke-width="2"/></svg>Deliveries
    </a>
  </aside>
