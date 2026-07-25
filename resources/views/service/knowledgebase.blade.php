@php
    $portal = $portal ?? 'client';
    $active = $active ?? 'service-desk';
    $title = 'Knowledge Base';
    $navItems = $portal === 'admin'
        ? [
            ['label' => 'Registration', 'route' => route('admin.itsm.registration'), 'key' => 'registration'],
            ['label' => 'Client Management', 'route' => route('admin.itsm.clients'), 'key' => 'clients'],
            ['label' => 'Service Desk', 'route' => route('admin.itsm.service-desk'), 'key' => 'service-desk'],
        ]
        : [
            ['label' => 'Employee Management', 'route' => route('client.itsm.employees'), 'key' => 'employees'],
            ['label' => 'Service Desk', 'route' => route('client.itsm.service-desk'), 'key' => 'service-desk'],
            ['label' => 'Compliance Tracking', 'route' => route('client.itsm.compliance'), 'key' => 'compliance'],
            ['label' => 'Risk Management', 'route' => route('client.itsm.risk'), 'key' => 'risk'],
        ];

    // Static fallback database representing highly accurate ITSM framework articles
    $fallbackArticles = [
        [
            'id' => 1,
            'title' => 'Configuring SAML 2.0 Single Sign-On',
            'category' => 'Security',
            'target_module' => 'Identity Access',
            'author_name' => 'IT Sec Ops',
            'created_at' => '2026-01-15',
            'view_count' => 342,
            'content' => "## Overview\nThis document outlines how to establish an identity handshake utilizing SAML 2.0 with corporate endpoints.\n\n### Requirements\n1. Active Directory instance.\n2. IdP Metadata XML file.\n\n### Step-by-Step\n1. Access identity provider dashboard.\n2. Map assertions to email attributes.\n3. Save configuration and run verification test."
        ],
        [
            'id' => 2,
            'title' => 'Corporate Offboarding Protocol & IT Clearances',
            'category' => 'HR',
            'target_module' => 'Employee Lifecycle',
            'author_name' => 'HR Operations',
            'created_at' => '2026-02-10',
            'view_count' => 189,
            'content' => "## Overview\nStandard operating guidelines for de-provisioning internal employee access upon formal separation.\n\n### Protocol Checklist\n1. Retrieve corporate hardware within 48 hours.\n2. Revoke OAuth refresh tokens from SaaS accounts.\n3. Forward pending communications to the direct supervisor."
        ],
        [
            'id' => 3,
            'title' => 'Reconciling Ledger Discrepancies in ERP systems',
            'category' => 'Finance',
            'target_module' => 'ERP Financials',
            'author_name' => 'Audit Lead',
            'created_at' => '2026-03-01',
            'view_count' => 254,
            'content' => "## Overview\nRemediation guide for variance checks between sub-ledgers and the general corporate ledger ledger.\n\n### Corrective Steps\n1. Run a Variance Report matching period parameters.\n2. Identify double-entry transaction outliers.\n3. Submit adjusting journal vouchers for approval."
        ],
        [
            'id' => 4,
            'title' => 'Local Network Subnetting & IP Allocations',
            'category' => 'General',
            'target_module' => 'Network Ops',
            'author_name' => 'Net Admin',
            'created_at' => '2026-04-12',
            'view_count' => 411,
            'content' => "## Overview\nStandard CIDR subnet structures implemented across satellite workspaces and primary data centers.\n\n### Allocation Guidelines\n* Private space: 10.0.0.0/8\n* Static VPN segments: 10.50.0.0/16\n* Guest Network isolation rules remain strict."
        ]
    ];

    $articles = $articles ?? json_decode(json_encode($fallbackArticles));
    
    // Category counters calculated dynamically
    $categories = [
       'General' => count(array_filter((array)$articles, fn($item) => isset($item['category']) && $item['category'] === 'General')),
'HR'      => count(array_filter((array)$articles, fn($item) => isset($item['category']) && $item['category'] === 'HR')),
'Finance' => count(array_filter((array)$articles, fn($item) => isset($item['category']) && $item['category'] === 'Finance')),
'Security'=> count(array_filter((array)$articles, fn($item) => isset($item['category']) && $item['category'] === 'Security')),

    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Knowledge Base</title>
    <link class="favicon" rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-color: #1B365D;
            --card-bg: #FFFFFF;
            --text-dark: #0F172A;
            --text-slate: #64748B;
            --border-color: #E2E8F0;
            
            /* Accent & Interactions */
            --primary-blue: #2563EB;
            --primary-hover: #1D4ED8;
            --accent-bg: #F8FAFC;
            --badge-tag-bg: #F1F5F9;
            --badge-tag-text: #475569;
            
            /* Action Colors */
            --btn-view-bg: #EFF6FF;
            --btn-view-text: #1D4ED8;
            --btn-edit-bg: #F0FDF4;
            --btn-edit-text: #16A34A;
            --btn-delete-bg: #FEF2F2;
            --btn-delete-text: #DC2626;
        }

        body {
            background-color: var(--bg-color);
            color: #FFFFFF;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .nexora-header-wrapper {
            position: relative;
            z-index: 50;
        }

        .page-wrapper {
            position: relative;
            min-height: calc(100vh - 100px);
            padding: 32px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Watermark Background Logo */
        .bg-watermark {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 72rem;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            filter: blur(4px);
            pointer-events: none;
            z-index: 0;
        }

        /* CONTENT SYSTEM CONTAINERS */
        .content-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
            width: 100%;
        }

        /* HEADER BANNER CARD */
        .header-card {
            background: linear-gradient(135deg, #e9ebf0 100%, #e9ebf0 100%);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .portal-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-blue);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .page-title {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-dark);
            margin: 0;
        }

        .page-subtitle {
            font-size: 15px;
            color: var(--text-slate);
            margin: 0;
            font-weight: 400;
            line-height: 1.6;
            max-width: 800px;
        }

        /* QUICK CATEGORIES GRID */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .category-card {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 12px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-blue);
        }

        .category-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background-color: var(--accent-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            transition: background-color 0.2s ease;
        }

        .category-card:hover .category-icon-wrapper {
            background-color: var(--btn-view-bg);
        }

        .category-icon {
            width: 22px;
            height: 22px;
        }

        .category-text-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .category-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .category-count {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-slate);
        }

        /* MAIN DIRECTORY CARD */
        .content-card {
            background-color: var(--card-bg);
            border-radius: 20px;
            padding: 32px;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }

        .card-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .card-label-all {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.3px;
        }

        .search-container {
            background-color: var(--accent-bg);
            border-radius: 10px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            width: 280px;
            border: 1px solid var(--border-color);
            transition: border-color 0.2s ease;
        }

        .search-container:focus-within {
            border-color: var(--primary-blue);
        }

        .search-input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 13.5px;
            color: var(--text-dark);
            font-weight: 500;
            width: 100%;
        }

        .search-input::placeholder {
            color: #94A3B8;
        }

        .action-button {
            background-color: var(--primary-blue);
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease;
            white-space: nowrap;
        }

        .action-button:hover {
            background-color: var(--primary-hover);
        }

        /* ARTICLE DIRECTORY TABLE */
        .table-view {
            width: 100%;
            overflow-x: auto;
        }

        .articles-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .articles-table th {
            padding: 14px 16px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-slate);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1.5px solid var(--border-color);
            white-space: nowrap;
        }

        .articles-table td {
            padding: 16px;
            font-size: 13.5px;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .articles-table tr:hover td {
            background-color: var(--accent-bg);
        }

        .articles-table tr:last-child td {
            border-bottom: none;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .pill.tag {
            background-color: var(--badge-tag-bg);
            color: var(--badge-tag-text);
        }

        .actions-flex {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .btn-mini {
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-mini-view {
            background-color: var(--btn-view-bg);
            color: var(--btn-view-text);
        }

        .btn-mini-view:hover {
            background-color: #DBEAFE;
        }

        .btn-mini-edit {
            background-color: var(--btn-edit-bg);
            color: var(--btn-edit-text);
        }

        .btn-mini-edit:hover {
            background-color: #DCFCE7;
        }

        .btn-mini-delete {
            background-color: var(--btn-delete-bg);
            color: var(--btn-delete-text);
        }

        .btn-mini-delete:hover {
            background-color: #FEE2E2;
        }

        /* MODAL WINDOW SYSTEM */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(8px);
        }

        .modal-container {
            background: var(--card-bg);
            color: var(--text-dark);
            width: 100%;
            max-width: 650px;
            border-radius: 20px;
            padding: 32px;
            border: 1px solid var(--border-color);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-slate);
        }

        .form-control {
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            outline: none;
            background: var(--accent-bg);
            color: var(--text-dark);
            transition: all 0.15s ease;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            background: #FFFFFF;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-secondary {
            background-color: var(--accent-bg);
            color: var(--text-slate);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-secondary:hover {
            background-color: var(--badge-tag-bg);
            color: var(--text-dark);
        }

        /* RESPONSIVE LAYOUT */
        @media (max-width: 1024px) {
            .relative.grid {
                grid-template-columns: 1fr !important;
            }
            .categories-grid {
                grid-template-columns: 1fr 1fr !important;
            }
            aside {
                padding: 24px !important;
            }
            nav {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                gap: 16px;
            }
            nav a {
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>

    <div class="nexora-header-wrapper">
        <x-itsm-header
            :home-route="$portal === 'admin' ? route('admin.itsm.registration') : route('client.itsm.employees')"
            :active="$active"
            :nav-items="$navItems"
        />
    </div>

    <div class="page-wrapper">
        <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="bg-watermark">

        <div class="relative z-10 grid min-h-[calc(100vh-10rem)] grid-cols-[22rem_1fr] gap-6">
            
            <!-- LEFT SIDEBAR (PRESERVED INDEPENDENTLY) -->
            <aside class="rounded-[1.875rem] bg-white p-8 text-slate-950">
                <nav class="space-y-6 text-xl">
                    <a href="{{ route('client.itsm.service-desk') }}" class="block font-medium hover:text-[#346DCB]">Module Ticket Dashboard</a>
                    <a href="{{ route('client.itsm.service-desk.support') }}" class="block font-medium hover:text-[#346DCB]">Ask Nexora Support</a>
                    <a href="{{ route('client.itsm.service-desk.resolvedtickets') }}" class="block font-medium hover:text-[#346DCB]">Resolved Tickets</a>
                    <a href="{{ route('client.itsm.service-desk.knowledgebase') }}" class="block font-extrabold hover:text-[#346DCB]">Knowledge Base</a>
                </nav>  
            </aside>

            <!-- KNOWLEDGE BASE SERVICE DESK WORKSPACE -->
            <div class="content-container">
                
                <div class="header-card">
                    <span class="portal-label">Nexora Service Portal</span>
                    <h1 class="page-title">Technical Knowledge Directory</h1>
                    <p class="page-subtitle">Access accurate troubleshooting manuals, platform compliance rules, and direct network configuration protocols managed by system administrators.</p>
                </div>

                <!-- CARDS WITH CLEAN VECTOR ICONS (NO EMOJIS) -->
                <div class="categories-grid">
                    <div class="category-card" onclick="filterCategory('General')">
                        <div class="category-icon-wrapper">
                            <svg class="category-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div class="category-text-container">
                            <span class="category-label">General Setup</span>
                            <span class="category-count" id="count-General">{{ $categories['General'] }} Articles</span>
                        </div>
                    </div>

                    <div class="category-card" onclick="filterCategory('HR')">
                        <div class="category-icon-wrapper">
                            <svg class="category-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="category-text-container">
                            <span class="category-label">HR Modules</span>
                            <span class="category-count" id="count-HR">{{ $categories['HR'] }} Articles</span>
                        </div>
                    </div>

                    <div class="category-card" onclick="filterCategory('Finance')">
                        <div class="category-icon-wrapper">
                            <svg class="category-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="category-text-container">
                            <span class="category-label">Finance & ERP</span>
                            <span class="category-count" id="count-Finance">{{ $categories['Finance'] }} Articles</span>
                        </div>
                    </div>

                    <div class="category-card" onclick="filterCategory('Security')">
                        <div class="category-icon-wrapper">
                            <svg class="category-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="category-text-container">
                            <span class="category-label">IT Security</span>
                            <span class="category-count" id="count-Security">{{ $categories['Security'] }} Articles</span>
                        </div>
                    </div>
                </div>

                <!-- MAIN WORKSPACE DIRECTORY -->
                <div class="content-card">
                    <div class="card-meta-row">
                        <span class="card-label-all">Knowledge Base Articles</span>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <button class="btn-secondary" onclick="resetFilters()" style="padding: 10px 16px;">View All</button>
                            <div class="search-container">
                                <input type="text" id="kbSearch" class="search-input" placeholder="Search parameters...">
                            </div>
                            <button class="action-button" onclick="openCreateModal()">Create Article</button>
                        </div>
                    </div>

                    <div class="table-view">
                        <table class="articles-table" id="articlesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Target Module</th>
                                    <th>Author</th>
                                    <th>Created Date</th>
                                    <th>Views</th>
                                    <th style="text-align: right; padding-right: 16px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="articlesTableBody">
                                @foreach($articles as $article)
                                    <tr id="article-row-{{ $article->id }}" data-category="{{ $article->category }}" data-article-obj="{{ json_encode($article) }}">
                                        <td style="font-weight: 700;">KB-{{ $article->id }}</td>
                                        <td style="font-weight: 600;" class="article-title-cell">{{ $article->title }}</td>
                                        <td>
                                            <span class="pill tag article-category-cell">{{ $article->category }}</span>
                                        </td>
                                        <td class="article-module-cell">{{ $article->target_module }}</td>
                                        <td class="article-author-cell">{{ $article->author_name }}</td>
                                        <td>{{ $article->created_at }}</td>
                                        <td>{{ $article->view_count }}</td>
                                        <td>
                                            <div class="actions-flex">
                                                <button class="btn-mini btn-mini-view" onclick="triggerView({{ $article->id }})">View</button>
                                                <button class="btn-mini btn-mini-edit" onclick="triggerEdit({{ $article->id }})">Edit</button>
                                                <button class="btn-mini btn-mini-delete" onclick="triggerDelete({{ $article->id }})">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal-overlay" id="createModal">
        <div class="modal-container">
            <h2 class="modal-title">Publish KB Documentation</h2>
            <form id="createArticleForm" onsubmit="handleCreate(event)">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group">
                        <label for="create_title">Article Title</label>
                        <input type="text" id="create_title" class="form-control" required placeholder="e.g. Remote API SSL Authentication Gateway">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="create_category">Category Domain</label>
                            <select id="create_category" class="form-control" required>
                                <option value="General">General Setup</option>
                                <option value="HR">HR Modules</option>
                                <option value="Finance">Finance & ERP</option>
                                <option value="Security">IT Security</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="create_module">Target System/Module</label>
                            <input type="text" id="create_module" class="form-control" required placeholder="e.g. Active Directory Server">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="create_content">Document Content (Technical Manual)</label>
                        <textarea id="create_content" class="form-control" rows="6" required placeholder="Outline architectural steps and configurations..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="action-button">Publish Document</button>
                </div>
            </form>
        </div>
    </div>

    <!-- VIEW MODAL -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-container" style="max-width: 700px;">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                <span class="portal-label" id="viewCategory">CATEGORY</span>
                <h2 class="modal-title" id="viewTitle" style="margin-top: 4px;">Article Title</h2>
                <div style="font-size: 13px; color: var(--text-slate); margin-top: 6px;">
                    Authored by <strong id="viewAuthor" style="color: var(--text-dark);">Admin</strong> on <span id="viewDate">Date</span>
                </div>
            </div>
            
            <div style="max-height: 350px; overflow-y: auto; font-size: 14.5px; line-height: 1.6; color: var(--text-dark); padding: 8px 0; white-space: pre-line;" id="viewContent">
                Article contents...
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Close Document</button>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-container">
            <h2 class="modal-title">Edit Documentation Segment</h2>
            <form id="editArticleForm" onsubmit="handleUpdate(event)">
                <input type="hidden" id="edit_id">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group">
                        <label for="edit_title">Article Title</label>
                        <input type="text" id="edit_title" class="form-control" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="edit_category">Category Domain</label>
                            <select id="edit_category" class="form-control" required>
                                <option value="General">General Setup</option>
                                <option value="HR">HR Modules</option>
                                <option value="Finance">Finance & ERP</option>
                                <option value="Security">IT Security</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_module">Target System/Module</label>
                            <input type="text" id="edit_module" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_content">Document Content</label>
                        <textarea id="edit_content" class="form-control" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="action-button">Save Documentation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal management closures
        function openCreateModal() { document.getElementById('createModal').style.display = 'flex'; }
        function closeCreateModal() { document.getElementById('createModal').style.display = 'none'; document.getElementById('createArticleForm').reset(); }
        function closeViewModal() { document.getElementById('viewModal').style.display = 'none'; }
        function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

        // Trigger dynamic view modal
        function triggerView(id) {
            const row = document.getElementById(`article-row-${id}`);
            const article = JSON.parse(row.getAttribute('data-article-obj'));
            
            document.getElementById('viewCategory').innerText = `${article.category.toUpperCase()} Setup / target: ${article.target_module}`;
            document.getElementById('viewTitle').innerText = article.title;
            document.getElementById('viewAuthor').innerText = article.author_name;
            document.getElementById('viewDate').innerText = article.created_at;
            document.getElementById('viewContent').innerText = article.content;
            
            document.getElementById('viewModal').style.display = 'flex';
        }

        // Trigger dynamic edit modal values mapping
        function triggerEdit(id) {
            const row = document.getElementById(`article-row-${id}`);
            const article = JSON.parse(row.getAttribute('data-article-obj'));
            
            document.getElementById('edit_id').value = article.id;
            document.getElementById('edit_title').value = article.title;
            document.getElementById('edit_category').value = article.category;
            document.getElementById('edit_module').value = article.target_module;
            document.getElementById('edit_content').value = article.content;
            
            document.getElementById('editModal').style.display = 'flex';
        }

        // Dynamic Javascript Operations representing reactive API additions
        function handleCreate(event) {
            event.preventDefault();
            
            const nextId = Math.floor(Math.random() * 900) + 100;
            const newArticle = {
                id: nextId,
                title: document.getElementById('create_title').value,
                category: document.getElementById('create_category').value,
                target_module: document.getElementById('create_module').value,
                author_name: "Technical Writer",
                created_at: new Date().toISOString().split('T')[0],
                view_count: 0,
                content: document.getElementById('create_content').value
            };

            const tbody = document.getElementById('articlesTableBody');
            const newRowHtml = `
                <tr id="article-row-${newArticle.id}" data-category="${newArticle.category}" data-article-obj='${JSON.stringify(newArticle)}'>
                    <td style="font-weight: 700;">KB-${newArticle.id}</td>
                    <td style="font-weight: 600;" class="article-title-cell">${newArticle.title}</td>
                    <td>
                        <span class="pill tag article-category-cell">${newArticle.category}</span>
                    </td>
                    <td class="article-module-cell">${newArticle.target_module}</td>
                    <td class="article-author-cell">${newArticle.author_name}</td>
                    <td>${newArticle.created_at}</td>
                    <td>${newArticle.view_count}</td>
                    <td>
                        <div class="actions-flex">
                            <button class="btn-mini btn-mini-view" onclick="triggerView(${newArticle.id})">View</button>
                            <button class="btn-mini btn-mini-edit" onclick="triggerEdit(${newArticle.id})">Edit</button>
                            <button class="btn-mini btn-mini-delete" onclick="triggerDelete(${newArticle.id})">Delete</button>
                        </div>
                    </td>
                </tr>`;
            
            tbody.insertAdjacentHTML('beforeend', newRowHtml);
            closeCreateModal();
            updateStatsCounters();
        }

        // Dynamic inline document updates
        function handleUpdate(event) {
            event.preventDefault();
            const id = document.getElementById('edit_id').value;
            const row = document.getElementById(`article-row-${id}`);
            const oldArticle = JSON.parse(row.getAttribute('data-article-obj'));

            const updatedArticle = {
                ...oldArticle,
                title: document.getElementById('edit_title').value,
                category: document.getElementById('edit_category').value,
                target_module: document.getElementById('edit_module').value,
                content: document.getElementById('edit_content').value
            };

            row.setAttribute('data-article-obj', JSON.stringify(updatedArticle));
            row.setAttribute('data-category', updatedArticle.category);
            row.querySelector('.article-title-cell').innerText = updatedArticle.title;
            row.querySelector('.article-category-cell').innerText = updatedArticle.category;
            row.querySelector('.article-module-cell').innerText = updatedArticle.target_module;

            closeEditModal();
            updateStatsCounters();
        }

        // Dynamic Document deletion
        function triggerDelete(id) {
            if (confirm("Are you sure you want to permanently delete this technical document from the database index?")) {
                const row = document.getElementById(`article-row-${id}`);
                row.remove();
                updateStatsCounters();
            }
        }

        // Auto Counter Re-calculators
        function updateStatsCounters() {
            const categories = ['General', 'HR', 'Finance', 'Security'];
            categories.forEach(cat => {
                const count = document.querySelectorAll(`#articlesTableBody tr[data-category="${cat}"]`).length;
                document.getElementById(`count-${cat}`).innerText = `${count} Articles`;
            });
        }

        // Table filters
        const searchInput = document.getElementById("kbSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const query = this.value.toLowerCase();
                const tableRows = document.querySelectorAll("#articlesTableBody tr");
                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(query) ? "" : "none";
                });
            });
        }

        function filterCategory(categoryName) {
            const tableRows = document.querySelectorAll("#articlesTableBody tr");
            tableRows.forEach(row => {
                const rowCat = row.getAttribute("data-category");
                row.style.display = (rowCat === categoryName) ? "" : "none";
            });
        }

        function resetFilters() {
            const tableRows = document.querySelectorAll("#articlesTableBody tr");
            tableRows.forEach(row => { row.style.display = ""; });
            if (searchInput) searchInput.value = "";
        }
    </script>
</body>
</html>