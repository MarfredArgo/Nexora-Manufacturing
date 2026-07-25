<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Nexora Dashboard</title>
<style>
  :root {
    --bg-header: #0B1E3D;
    --bg-dark: #1B3A6B;
    --bg-card: #0B1E3D;
    --text-light: #FFFFFF;
    --text-muted: #9FB3D1;
    --border-soft: rgba(255,255,255,0.08);
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: var(--bg-dark);
    color: var(--text-light);
  }

  /* ===== Navbar ===== */
  .navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 40px;
    background: var(--bg-header);
    border-bottom: 1px solid var(--border-soft);
  }

.brand{
    display:flex;
    align-items:center;
    gap:14px;
}

.brand-logo{
    display:flex;
    align-items:center;
    gap:14px;
    text-decoration:none;
    color:inherit;
}

.brand-logo .title{
    color:#FFFFFF;
}

.brand-logo .subtitle{
    color:#3B82F6;
}

  .logo {
    width: 46px;
    height: 50px;
    object-fit: contain;
  }

  .brand-text .title {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 1px;
  }

  .brand-text .subtitle {
    font-size: 11px;
    color: #3B82F6;
    letter-spacing: 1px;
  }

  .nav-links {
    display: flex;
    gap: 36px;
  }

  .nav-links a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
  }

  .nav-links a.active {
    color: var(--text-light);
    font-weight: 700;
  }

  .nav-links a:hover {
    color: var(--text-light);
    text-shadow: 0 0 0.4px currentColor, 0 0 0.4px currentColor;
  }

  /* ===== Stats Row ===== */
  .stats-row {
    display: flex;
    gap: 24px;
    padding: 32px 40px 10px;
    flex-wrap: wrap;
  }

  .stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 22px 28px;
    flex: 1;
    min-width: 200px;
  }

  .stat-card .label {
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
  }

  .stat-card .value {
    font-size: 32px;
    font-weight: 700;
  }

  /*---> Board <----*/
  .board {
    display: flex;
    gap: 24px;
    padding: 28px 40px 60px;
    flex-wrap: wrap;
  }

  .column {
    background: var(--bg-card);
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    flex: 1;
    min-width: 280px;
    padding: 20px;
    height: 560px;
    display: flex;
    flex-direction: column;
  }

  .column-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border-soft);
    margin-bottom: 16px;
    flex-shrink: 0;
  }

  .column-body {
    overflow-y: auto;
    flex: 1;
    min-height: 0;
    padding-right: 4px;
  }

  /* Custom dark scrollbar */
  .column-body::-webkit-scrollbar,
  .side-list::-webkit-scrollbar {
    width: 6px;
  }

  .column-body::-webkit-scrollbar-track,
  .side-list::-webkit-scrollbar-track {
    background: transparent;
  }

  .column-body::-webkit-scrollbar-thumb,
  .side-list::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
  }

  .column-body::-webkit-scrollbar-thumb:hover,
  .side-list::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.28);
  }

  .column-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }

  .dot-new { background: #9FB3D1; }
  .dot-packing { background: #F59E0B; }
  .dot-shipped { background: #38BDF8; }

  .count-badge {
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-light);
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 20px;
  }

  /* ===== Hover-expand order card =====
     Collapsed: shows only the order id.
     Hover: reveals item name + status. */
  .order-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 14px;
    cursor: pointer;
    overflow: hidden;
    transition: background 0.2s ease, border-color 0.2s ease;
  }

  .order-card:hover {
    background: rgba(255,255,255,0.07);
    border-color: rgba(255,255,255,0.18);
  }

  .order-id {
    color: var(--text-muted);
    font-size: 13px;
    font-weight: 700;
  }

  .order-details {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.25s ease, margin-top 0.3s ease;
  }

  .order-card:hover .order-details {
    max-height: 160px;
    opacity: 1;
    margin-top: 10px;
  }

  .order-item {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .empty-state {
    color: var(--text-muted);
    font-size: 13px;
    padding: 10px 0;
  }

  .tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 12px;
    margin-bottom: 8px;
  }

  .tag-new { background: rgba(255,255,255,0.1); color: var(--text-muted); }
  .tag-packing { background: #6B4A1E; color: #FBD38D; }
  .tag-shipped { background: #1E5A6B; color: #7DD3E8; }
  .tag-transit { background: #1E3A6B; color: #93C5FD; }
  .tag-delivered { background: #1E5A3A; color: #86EFAC; }
  .tag-cancelled { background: #4A1E1E; color: #F3A9A9; }

  /* Priority tags (based on order age) */
  .tag-low { background: #6B2B2B; color: #F3A9A9; }
  .tag-medium { background: #6B5A1E; color: #FBE38D; }
  .tag-high { background: #6B1E1E; color: #FB8D8D; }

  /* Status (left) + priority (right) sit on the same row */
  .tag-row {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    margin-bottom: 8px;
  }

  .tag-row .tag {
    margin-bottom: 0;
  }

  .order-due {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
  }

  .order-meta {
    font-size: 12px;
    color: var(--text-muted);
  }

  /* ===== Sidebar (Alerts + Activity) ===== */
  .sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
    width: 340px;
    flex-shrink: 0;
  }

  .side-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 20px;
    height: 268px;
    display: flex;
    flex-direction: column;
  }

  .side-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-soft);
    flex-shrink: 0;
  }

  .side-list {
    overflow-y: auto;
    flex: 1;
    min-height: 0;
    padding-right: 4px;
  }

  .side-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
  }

  .live-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
  }

  .live-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #4ADE80;
  }

  .alert-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    font-size: 13px;
  }

  .alert-row:last-child { border-bottom: none; }

  .alert-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .activity-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    font-size: 13px;
    color: var(--text-light);
  }

  .activity-row:last-child { border-bottom: none; }

.sub-order {
  color: #5FCB8A;
}

.sub-pack {
  color: #F39A9A;
}

.sub-ship {
  color: #9FB3CC;
}

.sub-deliver {
  color: #5FCB8A;
}

  /* ===== Nav actions (links + profile grouped on the right) ===== */
  .nav-actions {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .nav-divider {
    width: 1px;
    height: 22px;
    background: rgba(255,255,255,0.18);
  }

  /* ===== Profile menu ===== */
  .profile-menu {
    position: relative;
  }

  .profile-trigger {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-header);
    padding: 0;
  }

  .profile-trigger img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .profile-trigger:hover {
    border-color: rgba(255,255,255,0.35);
  }

  .profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    background: var(--bg-header);
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    min-width: 190px;
    padding: 6px;
    display: none;
    flex-direction: column;
    box-shadow: 0 12px 28px rgba(0,0,0,0.35);
    z-index: 100;
  }

  .profile-dropdown.open {
    display: flex;
  }

  .profile-dropdown a,
  .profile-dropdown button {
    display: block;
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    color: var(--text-light);
    font-family: inherit;
    font-size: 14px;
    font-weight: 500;
    padding: 10px 12px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
  }

  .profile-dropdown a:hover,
  .profile-dropdown button:hover {
    background: rgba(255,255,255,0.08);
  }

  .profile-dropdown .divider {
    height: 1px;
    background: var(--border-soft);
    margin: 4px 0;
  }
</style>

</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="brand brand-logo">
      <img class="logo" src="{{ asset('orderfulfillment/logo/Nexora_Logo_Transparent.png') }}" alt="Nexora Logo">
      <div class="brand-text">
          <div class="title">NEXORA</div>
          <div class="subtitle">ENTERPRISE RESOURCE PLANNING</div>
      </div>
    </div>
    <div class="nav-actions">
      <div class="nav-links">
        <a href="{{ route('order-fulfillment.dashboard') }}" class="active">Dashboard</a>
        <a href="{{ route('order-fulfillment.orders') }}">Orders</a>
        <a href="{{ route('order-fulfillment.packing') }}">Packing</a>
        <a href="{{ route('order-fulfillment.shipping') }}">Shipping</a>
        <a href="{{ route('order-fulfillment.return') }}">Returns</a>
      </div>
      <div class="nav-divider"></div>
      <div class="profile-menu" id="profileMenu">
        <button type="button" class="profile-trigger" id="profileTrigger" aria-label="Account menu">
          <img src="{{ asset('orderfulfillment/logo/pf.png') }}" alt="Profile">
        </button>
        <div class="profile-dropdown" id="profileDropdown">
          <a href="{{ route('order-fulfillment.dashboard') }}">Employee Dashboard</a>
          <div class="divider"></div>
          <form method="POST" action="{{ route('order-fulfillment.logout') }}" style="margin:0;">
            @csrf
            <button type="submit">Log out</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="label">Total orders</div>
      <div class="value">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card">
      <div class="label">In packing</div>
      <div class="value">{{ $inPackingCount }}</div>
    </div>
    <div class="stat-card">
      <div class="label">In shipping</div>
      <div class="value">{{ $inShippingCount }}</div>
    </div>
    <div class="stat-card">
      <div class="label">Delivery rate</div>
      <div class="value">{{ $onTimeRate }}%</div>
    </div>
  </div>

  <!-- Board + Sidebar -->
  @php
    // Class per color tier — kept local to this file because the CSS
    // classes here (.tag-*) are named differently than order.blade.php
    // (.status-*) and shipping.blade.php (.status-tag.tag-*). The text
    // for every one of these comes from OrderStatus::label() below, so
    // the wording itself can't drift between tabs even though the CSS
    // class names do.
    $statusClassByTier = [
      'new'       => 'tag-new',
      'packing'   => 'tag-packing',
      'shipped'   => 'tag-shipped',
      'transit'   => 'tag-transit',
      'delivered' => 'tag-delivered',
      'cancelled' => 'tag-cancelled',
    ];
    $statusMap = [];
    foreach (['NEW', 'PACKING', 'READY_TO_SHIP', 'SHIPPED', 'OUT_FOR_DELIVERY', 'DELIVERED', 'DELAYED', 'CANCELLED'] as $key) {
      $statusMap[$key] = [
        'label' => \Modules\OrderFulfillment\Helpers\OrderStatus::label($key),
        'class' => $statusClassByTier[\Modules\OrderFulfillment\Helpers\OrderStatus::tier($key)],
      ];
    }
  @endphp
  <div class="board">

    <!-- ORDERS -->
    <div class="column">
      <div class="column-header">
        <div class="column-title"><span class="dot dot-new"></span> ORDERS</div>
        <div class="count-badge">{{ $newOrders->count() }} orders</div>
      </div>

      <div class="column-body">
        @forelse ($newOrders as $order)
          @php
            $priority     = \Modules\OrderFulfillment\Helpers\OrderPriority::dashboard($order->created_at ?? null);
            $status       = $statusMap[strtoupper($order->status)] ?? ['label' => strtoupper($order->status), 'class' => 'tag-new'];
            $statusIsNew  = strtoupper($order->status) === 'NEW';
            // Never show two "NEW" tags on the same card, and never show a
            // "NEW" priority once the order has moved past the NEW status.
            if ($priority['label'] === 'NEW') {
                $showPriority = !$statusIsNew;
                if ($showPriority) {
                    $priority = ['label' => 'LOW', 'class' => 'tag-low'];
                }
            } else {
                $showPriority = true;
            }
          @endphp
          <div class="order-card">
            <div class="order-id">{{ $order->id }}</div>
            <div class="order-details">
              <div class="order-item">{{ $order->customer_name }}</div>
              <div class="tag-row">
                <span class="tag {{ $status['class'] }}">{{ $status['label'] }}</span>
                @if ($showPriority)
                <span class="tag {{ $priority['class'] }}">{{ $priority['label'] }}</span>
                @endif
              </div>
              @if (!empty($order->due_date))
                <div class="order-due">Due: {{ \Carbon\Carbon::parse($order->due_date)->format('F j') }}</div>
              @endif
            </div>
          </div>
        @empty
          <div class="empty-state">No new orders.</div>
        @endforelse
      </div>
    </div>

    <!-- PACKING -->
    <div class="column">
      <div class="column-header">
        <div class="column-title"><span class="dot dot-packing"></span> PACKING</div>
        <div class="count-badge">{{ $packingOrders->count() }} orders</div>
      </div>

      <div class="column-body">
        @forelse ($packingOrders as $order)
          @php
            $priority     = \Modules\OrderFulfillment\Helpers\OrderPriority::dashboard($order->created_at ?? null);
            $status       = $statusMap[strtoupper($order->status)] ?? ['label' => strtoupper($order->status), 'class' => 'tag-packing'];
            $statusIsNew  = strtoupper($order->status) === 'NEW';
            // Never show two "NEW" tags on the same card, and never show a
            // "NEW" priority once the order has moved past the NEW status.
            if ($priority['label'] === 'NEW') {
                $showPriority = !$statusIsNew;
                if ($showPriority) {
                    $priority = ['label' => 'LOW', 'class' => 'tag-low'];
                }
            } else {
                $showPriority = true;
            }
          @endphp
          <div class="order-card">
            <div class="order-id">{{ $order->id }}</div>
            <div class="order-details">
              <div class="order-item">{{ $order->customer_name }}</div>
              <div class="tag-row">
                <span class="tag {{ $status['class'] }}">{{ $status['label'] }}</span>
                @if ($showPriority)
                <span class="tag {{ $priority['class'] }}">{{ $priority['label'] }}</span>
                @endif
              </div>
              @if (!empty($order->due_date))
                <div class="order-due">Due: {{ \Carbon\Carbon::parse($order->due_date)->format('F j') }}</div>
              @endif
            </div>
          </div>
        @empty
          <div class="empty-state">Nothing in packing.</div>
        @endforelse
      </div>
    </div>

    <!-- SHIPPED
         $shippedOrders needs to come from the controller as everything
         that has REACHED shipping or later — not literally status ==
         'SHIPPED'. Otherwise an order vanishes from this column the
         moment shipping.blade.php advances it to OUT_FOR_DELIVERY or
         DELIVERED. In the controller that builds this view, that's:
           $shippedOrders = Order::whereIn('status', [
               'SHIPPED', 'OUT_FOR_DELIVERY', 'DELIVERED',
           ])->latest()->get();
         The per-row status/priority tags below already render whatever
         the order's real current status is, so the card still shows
         accurate info — it just no longer disappears from the board. -->
    <div class="column">
      <div class="column-header">
        <div class="column-title"><span class="dot dot-shipped"></span> SHIPPED</div>
        <div class="count-badge">{{ $shippedOrders->count() }} orders</div>
      </div>

      <div class="column-body">
        @forelse ($shippedOrders as $order)
          @php
            $priority     = \Modules\OrderFulfillment\Helpers\OrderPriority::dashboard($order->created_at ?? null);
            $status       = $statusMap[strtoupper($order->status)] ?? ['label' => strtoupper($order->status), 'class' => 'tag-shipped'];
            $statusIsNew  = strtoupper($order->status) === 'NEW';
            // Never show two "NEW" tags on the same card, and never show a
            // "NEW" priority once the order has moved past the NEW status.
            if ($priority['label'] === 'NEW') {
                $showPriority = !$statusIsNew;
                if ($showPriority) {
                    $priority = ['label' => 'LOW', 'class' => 'tag-low'];
                }
            } else {
                $showPriority = true;
            }
          @endphp
          <div class="order-card">
            <div class="order-id">{{ $order->id }}</div>
            <div class="order-details">
              <div class="order-item">{{ $order->customer_name }}</div>
              <div class="tag-row">
                <span class="tag {{ $status['class'] }}">{{ $status['label'] }}</span>
                @if ($showPriority)
                <span class="tag {{ $priority['class'] }}">{{ $priority['label'] }}</span>
                @endif
              </div>
              @if (!empty($order->due_date))
                <div class="order-due">Due: {{ \Carbon\Carbon::parse($order->due_date)->format('F j') }}</div>
              @endif
            </div>
          </div>
        @empty
          <div class="empty-state">Nothing shipped yet.</div>
        @endforelse
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <div class="side-panel">
        <div class="side-header">
          <div class="side-title">🔔 Alerts</div>
        </div>

        <div class="side-list" id="alertsList" data-empty-text="No alerts.">
          @forelse ($alerts as $order)
            <div class="alert-row">
              <div class="alert-left">
                <span>📦 New order {{ $order->id }} received</span>
              </div>
            </div>
          @empty
            <div class="empty-state">No alerts.</div>
          @endforelse
        </div>
      </div>

      <div class="side-panel">
        <div class="side-header">
          <div class="side-title">📈 Activity feed</div>
          <div class="live-badge"><span class="live-dot"></span> Live</div>
        </div>

        <div class="side-list" id="activityFeedList" data-empty-text="No recent activity.">
          @forelse ($activity as $order)
            <div class="activity-row" data-activity-id="{{ $order->id }}-{{ $order->status ?? '' }}">{{ $order->activity_icon }} {{ $order->activity_message }}</div>
          @empty
            <div class="empty-state">No recent activity.</div>
          @endforelse
        </div>
      </div>
    </div>

  </div>

  <script>
    /* =====================================================================
       Live notify: picks up status changes made anywhere (Orders, Packing,
       Shipping) and reflects them here without a refresh.
       Requires GET /activity/recent?since=<ISO timestamp> to exist server
       side — see the ActivityController snippet provided alongside this
       file. Every other page (order.blade.php, shipping.blade.php) polls
       the same endpoint, so a driver getting assigned on the Shipping tab
       shows up here within one poll interval.
       ===================================================================== */
    (function () {
      const POLL_MS = 8000;
      let since = new Date().toISOString();

      function rowHtml(item) {
        if (item.type === 'alert') {
          return '<div class="alert-row"><div class="alert-left"><span>' +
                 (item.icon || '📦') + ' ' + item.message + '</span></div></div>';
        }
        return '<div class="activity-row" data-activity-id="' + item.id + '">' +
               (item.icon || '📈') + ' ' + item.message + '</div>';
      }

      function prepend(container, items) {
        if (!container || !items.length) return;
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) emptyState.remove();
        items.forEach(function (item) {
          container.insertAdjacentHTML('afterbegin', rowHtml(item));
        });
      }

      async function poll() {
        try {
          const res = await fetch('/activity/recent?since=' + encodeURIComponent(since));
          if (!res.ok) return;
          const data = await res.json();
          if (data.items && data.items.length) {
            prepend(document.getElementById('alertsList'), data.items.filter(i => i.type === 'alert'));
            prepend(document.getElementById('activityFeedList'), data.items.filter(i => i.type === 'activity'));
          }
          if (data.now) since = data.now;
        } catch (e) {
          // Silently retry on the next interval — a missed poll shouldn't
          // spam the console or interrupt whoever is using the dashboard.
        }
      }

      setInterval(poll, POLL_MS);
    })();
  </script>

  <script>
    (function () {
      const menu = document.getElementById('profileMenu');
      const trigger = document.getElementById('profileTrigger');
      const dropdown = document.getElementById('profileDropdown');
      if (!menu || !trigger || !dropdown) return;

      trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
      });

      document.addEventListener('click', function (e) {
        if (!menu.contains(e.target)) {
          dropdown.classList.remove('open');
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') dropdown.classList.remove('open');
      });
    })();
  </script>

</body>
</html>