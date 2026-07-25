<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Nexora E-commerce' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    <!-- Load Phosphor Icons for the sidebar -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --c-sidebar-bg: #f1f1f1;
            --c-sidebar-hover: #e5e5e5;
            --c-header-bg: #1a1a1a;
            --c-bg: #fcfcfc;
            --c-text: #1a1a1a;
            --c-text-muted: #666;
            --c-border: #e6e6e6;
            --c-primary: #1d4e89;
            --c-primary-hover: #163e6d;
            font-family: Inter, Arial, sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--c-bg); color: var(--c-text); display: flex; flex-direction: column; min-height: 100vh; }

        /* Top Header */
        .shopify-header {
            height: 56px;
            background: var(--c-header-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .header-logo { display: flex; align-items: center; text-decoration: none; }
        .header-logo img { height: 32px; object-fit: contain; }
        .header-search {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 6px;
            padding: 6px 12px;
            color: #fff;
            font-size: 14px;
            width: 400px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header-search i { font-size: 16px; opacity: 0.7; }
        .header-search span { opacity: 0.7; flex: 1; }
        .header-search .shortcut { background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px; font-size: 11px; }

        .header-actions { display: flex; align-items: center; gap: 16px; color: #fff; }
        .header-actions i { font-size: 20px; cursor: pointer; opacity: 0.8; transition: opacity 0.2s; }
        .header-actions i:hover { opacity: 1; }

        .user-menu { position: relative; }
        .user-button { display: grid; place-items: center; width: 32px; height: 32px; padding: 0; border: 0; border-radius: 50%; background: #4caf50; color: #fff; font-weight: 600; font-size: 13px; cursor: pointer; }
        .user-dropdown { visibility: hidden; position: absolute; z-index: 20; top: 40px; right: 0; width: 220px; overflow: hidden; border-radius: 8px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.15); opacity: 0; transform: translateY(-8px); transition: .16s ease; border: 1px solid var(--c-border); }
        .user-menu[data-open="true"] .user-dropdown { visibility: visible; opacity: 1; transform: translateY(0); }
        .user-dropdown a, .user-dropdown button { display: block; width: 100%; padding: 12px 16px; border: 0; background: #fff; color: var(--c-text); font: 500 14px Inter, Arial, sans-serif; text-align: left; text-decoration: none; cursor: pointer; }
        .user-dropdown a:hover, .user-dropdown button:hover { background: #f5f5f5; }
        .user-dropdown form { margin: 0; }
        .user-dropdown form button { color: #dc2626; border-top: 1px solid var(--c-border); }

        /* Layout Structure */
        .layout-wrapper { display: flex; flex: 1; overflow: hidden; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: var(--c-sidebar-bg);
            border-right: 1px solid var(--c-border);
            display: flex;
            flex-direction: column;
            padding: 16px 12px;
        }
        .sidebar-nav { flex: 1; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 8px 12px; border-radius: 6px;
            color: var(--c-text); text-decoration: none;
            font-size: 14px; font-weight: 500; transition: background 0.1s;
        }
        .sidebar-link:hover { background: var(--c-sidebar-hover); }
        .sidebar-link.active { background: #fff; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .sidebar-link i { font-size: 18px; color: var(--c-text-muted); }
        .sidebar-link.active i { color: var(--c-text); }
        .sidebar-section-title {
            font-size: 12px; font-weight: 600; color: var(--c-text-muted);
            margin: 16px 0 8px 12px; text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* Main Area */
        .main-area { flex: 1; overflow-y: auto; padding: 32px 48px; background: var(--c-bg); }

        /* Global Styles inside Main Area */
        .page-heading { margin-bottom: 32px; }
        h1 { font-size: 24px; font-weight: 600; color: var(--c-text); }
        .company-subtitle { color: var(--c-text-muted); font-size: 14px; margin-top: 4px; }

        .card { background: #fff; border: 1px solid var(--c-border); border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; border-bottom: 1px solid var(--c-border); text-align: left; }
        th { color: var(--c-text-muted); font-size: 13px; font-weight: 500; }
        td { font-size: 14px; }

        button, .button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 6px; padding: 8px 16px; background: var(--c-text); color: #fff; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; transition: background 0.15s; }
        .button:hover, button:hover { background: #333; }
        .button.alt { background: #fff; color: var(--c-text); border: 1px solid #ccc; }
        .button.alt:hover { background: #f5f5f5; border-color: #999; }

        input, textarea, select { width: 100%; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px; padding: 10px 12px; color: var(--c-text); font: inherit; font-size: 14px; transition: border-color 0.15s; }
        input:focus, textarea:focus, select:focus { border-color: var(--c-primary); outline: none; box-shadow: 0 0 0 2px rgba(29, 78, 137, 0.1); }
        label { display: block; margin-top: 16px; color: var(--c-text); font-size: 14px; font-weight: 500; }

        .hint { margin: 6px 0 0; color: var(--c-text-muted); font-size: 13px; }
        .success { margin-bottom: 24px; border-radius: 8px; padding: 14px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; font-size: 14px; display: flex; align-items: center; gap: 8px;}
        .error { color: #d32f2f; font-size: 13px; margin-top: 4px;}

        /* Layout Editor Specifics overriding */
        .editor-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(320px, .8fr); gap: 24px; align-items: start; }
        .section-card { margin-top: 16px; padding: 20px; border: 1px solid var(--c-border); border-radius: 10px; background: #fafafa; }
        .section-card h3 { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
        .section-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .toggle { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .toggle input { width: auto; margin: 0; accent-color: var(--c-primary); transform: scale(1.1); }
        .field-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 16px; }
        .publish-note { border-left: 3px solid var(--c-primary); background: #f0f4f8; padding: 14px; font-size: 14px; margin-bottom: 20px; border-radius: 0 8px 8px 0; }

        @media (max-width: 900px) {
            .editor-grid { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .header-search { display: none; }
            .main-area { padding: 20px; }
        }
    </style>
</head>
<body>
    @if(!($hideLayout ?? false))
    <header class="shopify-header">
        <a class="header-logo" href="{{ route('ecommerce.admin.dashboard') }}">
            <img src="{{ asset('images/Banner Transparent.png') }}" style="filter: brightness(0) invert(1);" alt="Nexora Logo">
        </a>

        <div class="header-search">
            <i class="ph ph-magnifying-glass"></i>
            <span>Search</span>
            <div class="shortcut">CTRL K</div>
        </div>

        <div class="header-actions">
            <i class="ph ph-bell"></i>
            <div class="user-menu" data-user-menu>
                @php
                    $companyName = auth('ecommerce_admin')->user()?->getCompany()?->company_name ?? 'Store';
                    $initials = strtoupper(substr($companyName, 0, 2));
                @endphp
                <button type="button" class="user-button" data-user-menu-button aria-label="Open user menu" aria-expanded="false">
                    {{ $initials }}
                </button>
                <div class="user-dropdown" data-user-menu-dropdown>
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--c-border); background: #fafafa;">
                        <strong>{{ $companyName }}</strong>
                    </div>
                    @php($slug = auth('ecommerce_admin')->user()?->getCompany()?->ecommerce_slug)
                    @if($slug)
                        <a href="{{ route('ecommerce.home', ['store' => $slug]) }}" target="_blank" rel="noopener">Open Storefront</a>
                    @endif
                    <form method="post" action="{{ route('ecommerce.admin.logout') }}">@csrf<button type="submit" style="color: #d32f2f; background: none; text-align: left; padding: 0;">Log Out</button></form>
                </div>
            </div>
        </div>
    </header>
    @endif

    <div class="layout-wrapper">
        @if(!($hideLayout ?? false))
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a class="sidebar-link {{ request()->routeIs('ecommerce.admin.dashboard') ? 'active' : '' }}" href="{{ route('ecommerce.admin.dashboard') }}">
                    <i class="ph ph-house"></i> Home
                </a>
                <a class="sidebar-link {{ request()->routeIs('ecommerce.admin.orders') ? 'active' : '' }}" href="{{ route('ecommerce.admin.orders') }}">
                    <i class="ph ph-shopping-cart"></i> Orders
                </a>
                <a class="sidebar-link {{ request()->routeIs('ecommerce.admin.listings*') ? 'active' : '' }}" href="{{ route('ecommerce.admin.listings') }}">
                    <i class="ph ph-tag"></i> Products
                </a>

                <div class="sidebar-section-title">Sales Channels</div>
                <a class="sidebar-link {{ request()->routeIs('ecommerce.admin.layout.*') ? 'active' : '' }}" href="{{ route('ecommerce.admin.layout.edit') }}">
                    <i class="ph ph-storefront"></i> {{ auth('ecommerce_admin')->user()?->getCompany()?->company_name ?? 'Online Store' }}
                </a>
            </nav>

            <nav style="margin-top: auto;">
                <a class="sidebar-link" href="#" onclick="alert('Settings coming soon!'); return false;">
                    <i class="ph ph-gear"></i> Settings
                </a>
            </nav>
        </aside>
        @endif

        <main class="main-area" style="{{ ($hideLayout ?? false) ? 'padding: 0;' : '' }}">
            @if(request()->routeIs('ecommerce.admin.dashboard'))
                <!-- Dashboard handles its own header -->
            @else
                <div class="page-heading">
                    <h1>{{ $heading ?? 'E-commerce Admin' }}</h1>
                </div>
            @endif

            @if (session('success'))
                <div class="success"><i class="ph ph-check-circle" style="font-size: 18px;"></i> {{ session('success') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        document.querySelectorAll('[data-user-menu]').forEach((menu) => {
            const button = menu.querySelector('[data-user-menu-button]');
            button?.addEventListener('click', (event) => {
                event.stopPropagation();
                const open = menu.dataset.open !== 'true';
                menu.dataset.open = open ? 'true' : 'false';
                button.setAttribute('aria-expanded', String(open));
            });
            window.addEventListener('click', () => {
                menu.dataset.open = 'false';
                button?.setAttribute('aria-expanded', 'false');
            });
        });
    </script>
</body>
</html>
