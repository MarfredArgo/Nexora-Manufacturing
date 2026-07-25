@php
    $portal = $portal ?? 'client';
    $active = $active ?? 'service-desk';
    $title = 'Resolved Tickets';
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

    // Guaranteeing we strictly filter and show only Resolved tickets
    $onlyResolvedTickets = isset($resolvedTickets) 
        ? (is_array($resolvedTickets) ? collect($resolvedTickets) : $resolvedTickets)->where('status', 'Resolved')
        : collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Resolved Tickets</title>
    <link class="favicon" rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-color: #1B365D;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #f1f5f9;
            --subtle-gray-bg: #f8fafc;
            
            /* Status Colors */
            --badge-resolved-bg: #e2fbf0;
            --badge-resolved-text: #0d9488;
            --priority-low-bg: #f1f5f9;
            --priority-low-text: #475569;
            --priority-high-bg: #fef2f2;
            --priority-high-text: #dc2626;

            /* Interactive Elements */
            --btn-reopen-bg: #fff7ed;
            --btn-reopen-text: #c2410c;
            --btn-view-bg: #f0f9ff;
            --btn-view-text: #0369a1;
        }

        body {
            background-color: var(--bg-color);
            color: #ffffff;
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
            padding: 24px 32px;
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
            opacity: 0.08;
            filter: blur(4px);
            pointer-events: none;
            z-index: 0;
        }

        /* CONTENT SYSTEM OVERRIDES */
        .content-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
            width: 100%;
        }

        /* MAIN HERO PANEL REDESIGN (A modern cohesive banner card) */
        .hero-banner-card {
            background: linear-gradient(135deg, #e9ebf0 100%, #e9ebf0 100%);
            border-radius: 24px;
            padding: 36px 40px;
            color: var(--text-dark);
            box-shadow: 0 10px 30px rgba(11, 26, 48, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .hero-text-side {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .portal-label {
            font-size: 12px;
            font-weight: 700;
            color: #346DCB;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .hero-title {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--text-dark);
            margin: 0;
        }

        .hero-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            margin: 0;
            font-weight: 400;
            max-width: 600px;
            line-height: 1.5;
        }

        /* Inline Stat Badge in Hero Panel */
        .resolved-counter-badge {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 140px;
        }

        .counter-label {
            font-size: 11px;
            font-weight: 700;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .counter-val {
            font-size: 32px;
            font-weight: 900;
            color: #14532d;
            line-height: 1;
            margin-top: 4px;
        }

        /* MAIN CONTENT PANEL */
        .workspace-card {
            background-color: var(--card-bg);
            border-radius: 24px;
            padding: 32px;
            color: var(--text-dark);
            box-shadow: 0 10px 30px rgba(11, 26, 48, 0.08);
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 63vh;
        }

        .workspace-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }

        .workspace-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        

        .search-wrapper {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            width: 280px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .search-wrapper:focus-within {
            border-color: #346DCB;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(52, 109, 203, 0.1);
        }

        .search-box {
            border: none;
            background: transparent;
            outline: none;
            font-size: 13px;
            color: var(--text-dark);
            font-weight: 500;
            width: 100%;
        }

        .search-box::placeholder {
            color: #94a3b8;
        }

        /* TABLE IMPLEMENTATION */
        .table-viewport {
            width: 100%;
            overflow-x: auto;
        }

        .app-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }

        .app-table th {
            padding: 14px 16px;
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
            background-color: var(--subtle-gray-bg);
        }

        .app-table th:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .app-table th:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .app-table td {
            padding: 16px;
            font-size: 13.5px;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .app-table tr:hover td {
            background-color: #f8fafc;
        }

        .ticket-id {
            font-weight: 750;
            color: #346DCB;
            font-family: monospace;
            font-size: 14px;
        }

        .subject-col {
            font-weight: 600;
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Badges & Micro layouts */
        .custom-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 750;
        }

        .custom-badge.status-resolved {
            background-color: var(--badge-resolved-bg);
            color: var(--badge-resolved-text);
        }

        .custom-badge.prio-low {
            background-color: var(--priority-low-bg);
            color: var(--priority-low-text);
        }

        .custom-badge.prio-high {
            background-color: var(--priority-high-bg);
            color: var(--priority-high-text);
        }

        /* Row Action Grid */
        .action-cell {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .action-pill-btn {
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .action-pill-btn-view {
            background-color: var(--btn-view-bg);
            color: var(--btn-view-text);
        }

        .action-pill-btn-view:hover {
            background-color: #e0f2fe;
        }

        .action-pill-btn-reopen {
            background-color: var(--btn-reopen-bg);
            color: var(--btn-reopen-text);
        }

        .action-pill-btn-reopen:hover {
            background-color: #ffedd5;
        }

        /* MODAL OVERLAYS */
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
            backdrop-filter: blur(4px);
        }

        .modal-container {
            background: var(--card-bg);
            color: var(--text-dark);
            width: 100%;
            max-width: 580px;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
            display: flex;
            flex-direction: column;
            gap: 20px;
            border: 1px solid var(--border-color);
        }

        .modal-header-row {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 850;
            margin: 0;
            color: var(--text-dark);
        }

        .modal-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin: 4px 0 0 0;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 12px 16px;
            background: var(--subtle-gray-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .detail-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            color: var(--text-dark);
            font-weight: 500;
            line-height: 1.5;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .btn-secondary {
            background-color: var(--subtle-gray-bg);
            color: #475569;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .relative.grid {
                grid-template-columns: 1fr !important;
            }
            aside {
                padding: 24px !important;
            }
            nav {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                space-y-0: cubic-bezier(0, 0, 0, 0) !important;
                gap: 16px;
            }
            nav a {
                margin: 0 !important;
            }
            .hero-banner-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 28px;
            }
            .resolved-counter-badge {
                align-self: stretch;
                align-items: flex-start;
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
        <!-- Watermark Background Logo -->
        <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="bg-watermark">

        <!-- INTEGRATED SIDEBAR GRID SYSTEM -->
        <div class="relative z-10 grid min-h-[calc(100vh-10rem)] grid-cols-[22rem_1fr] gap-6">
            
            <!-- LEFT SIDEBAR -->
            <aside class="rounded-[1.875rem] bg-white p-8 text-slate-950">
                <nav class="space-y-6 text-xl">
                    <a href="{{ route('client.itsm.service-desk') }}" class="block font-medium hover:text-[#346DCB]">Module Ticket Dashboard</a>
                    <a href="{{ route('client.itsm.service-desk.support') }}" class="block font-medium hover:text-[#346DCB]">Ask Nexora Support</a>
                    <a href="{{ route('client.itsm.service-desk.resolvedtickets') }}" class="block font-extrabold hover:text-[#346DCB]">Resolved Tickets</a>
                    <a href="{{ route('client.itsm.service-desk.knowledgebase') }}" class="block font-medium hover:text-[#346DCB]">Knowledge Base</a>
                </nav>
            </aside>

            <!-- MAIN WORKSPACE -->
            <div class="content-container">
                
                <!-- REDESIGNED HERO BANNER -->
                <div class="hero-banner-card">
                    <div class="hero-text-side">
                        <span class="portal-label">Company Admin Portal</span>
                        <h1 class="hero-title">Resolved ERP Tickets</h1>
                        <p class="hero-subtitle">Review successfully resolved queries, technical diagnostic summaries, and resolution logs generated by your integrated system modules.</p>
                    </div>
                    <div class="resolved-counter-badge">
                        <span class="counter-label">All-Time Resolved</span>
                        <span class="counter-val">{{ count($onlyResolvedTickets) }}</span>
                    </div>
                </div>

                <!-- MAIN WORKSPACE PANEL -->
                <div class="workspace-card">
                    <div class="workspace-meta-row">
                        <span class="workspace-title">Historical Resolutions</span>
                        <div class="search-wrapper">
                            <input type="text" id="ticketSearch" class="search-box" placeholder="Filter by requester, subject, or key...">
                        </div>
                    </div>

                    <!-- DATA TABLE -->
                    <div class="table-viewport">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Requester</th>
                                    <th>Module</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th style="text-align: right; padding-right: 16px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($onlyResolvedTickets as $ticket)
                                    <tr>
                                        <td class="ticket-id">#{{ $ticket->id }}</td>
                                        <td style="font-weight: 600;">{{ $ticket->requester_name }}</td>
                                        <td>{{ $ticket->module_scope }}</td>
                                        <td class="subject-col" title="{{ $ticket->subject }}">{{ $ticket->subject }}</td>
                                        <td>{{ $ticket->category }}</td>
                                        <td>
                                            <span class="custom-badge {{ $ticket->priority === 'High' ? 'prio-high' : 'prio-low' }}">
                                                {{ $ticket->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="custom-badge status-resolved">Resolved</span>
                                        </td>
                                        <td>
                                            <div class="action-cell">
                                                <button class="action-pill-btn action-pill-btn-view" onclick="openDetailsModal(@json($ticket))">View Summary</button>
                                                <form action="{{ route('tickets.reopen', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reopen this ticket?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="action-pill-btn action-pill-btn-reopen">Reopen</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 50px;">
                                            No resolved tickets were found matching your systems criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- RESOLVED DETAILS MODAL -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal-container">
            <div class="modal-header-row">
                <h2 class="modal-title">Ticket Resolution Summary</h2>
                <p class="modal-subtitle">Details compiled upon ticket closure</p>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div class="detail-row">
                    <span class="detail-label">Original Issue Summary</span>
                    <span class="detail-value" id="modalSubject">Subject here...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Resolution Actions Taken</span>
                    <span class="detail-value" id="modalResolution">Resolution details here...</span>
                </div>
                <div class="detail-row" style="grid-template-columns: 1fr 1fr; display: grid; gap: 12px; background: transparent; padding: 0; border: none;">
                    <div class="detail-row">
                        <span class="detail-label">Resolved By</span>
                        <span class="detail-value" id="modalResolver">Staff name</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Resolved Timestamp</span>
                        <span class="detail-value" id="modalDate">Timestamp</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDetailsModal()">Close Details View</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Event Handlers
        function openDetailsModal(ticket) {
            document.getElementById('modalSubject').innerText = ticket.subject;
            document.getElementById('modalResolution').innerText = ticket.resolution_text || "This ticket was verified, debugged, and successfully finalized by technical operations.";
            document.getElementById('modalResolver').innerText = ticket.resolved_by || "Automated Nexora Process";
            document.getElementById('modalDate').innerText = ticket.resolved_at || "N/A";
            
            document.getElementById('detailsModal').style.display = 'flex';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Live Table Search Filters
        const searchInput = document.getElementById("ticketSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const query = this.value.toLowerCase();
                const tableRows = document.querySelectorAll(".app-table tbody tr");
                
                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(query) ? "" : "none";
                });
            });
        }
    </script>
</body>
</html>