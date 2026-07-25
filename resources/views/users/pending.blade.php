@php
    $portal = $portal ?? 'client';
    $active = $active ?? ($portal === 'admin' ? 'clients' : 'employees');
    $title = 'Pending Approvals';
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
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Pending Approvals</title>
    <link class="favicon" rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-color: #1B365D;
            --card-bg: #ffffff;
            --text-dark: #000000;
            --text-slate: #1e293b;
            --border-color: #e2e8f0;
            
            /* Status Badges */
            --badge-pending-bg: #fef3c7;
            --badge-pending-text: #d97706;
            
            /* Action Buttons */
            --btn-approve-bg: #dcfce7;
            --btn-approve-text: #15803d;
            --btn-reject-bg: #fef2f2;
            --btn-reject-text: #dc2626;
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

        /* 1. CONTENT SYSTEM OVERRIDES */
        .content-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
            width: 100%;
        }

        /* 2. MAIN HEADER CARD */
        .header-card {
            background: #e9eaef;
            border-radius: 30px;
            padding: 24px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-dark);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .page-title {
            font-size: 36px;
            font-weight: 850;
            letter-spacing: -0.5px;
            color: var(--text-dark);
        }

        .header-controls {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-container {
            background-color: #e9eff6;
            border-radius: 50px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            width: 320px;
        }

        .search-input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 16px;
            color: var(--text-dark);
            font-weight: 500;
            width: 100%;
        }

        .search-input::placeholder {
            color: #8fa0b5;
        }

        .action-button {
            background-color: #346DCB;
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 12px 32px;
            font-size: 16px;
            font-weight: 750;
            cursor: pointer;
            transition: opacity 0.15s ease;
            white-space: nowrap;
        }

        .action-button:hover {
            opacity: 0.9;
        }

        /* 3. MAIN CONTENT PANEL */
        .content-card {
            background-color: var(--card-bg);
            border-radius: 30px;
            padding: 40px;
            color: var(--text-dark);
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
        }

        .card-label-all {
            font-size: 18px;
            font-weight: 750;
            color: var(--text-dark);
        }

        .select-all-box {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 100;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            accent-color: #346DCB;
            cursor: pointer;
        }

        /* 4. EXPANSIVE TABLE */
        .table-view {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
        }

        .pending-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .pending-table th {
            padding: 18px 16px;
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        .pending-table td {
            padding: 20px 16px;
            font-size: 15px;
            color: var(--text-slate);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .pending-table tr:last-child td {
            border-bottom: none;
        }

        /* Status Pills */
        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 750;
        }

        .pill.pending {
            background-color: var(--badge-pending-bg);
            color: var(--badge-pending-text);
        }

        /* Action Grid */
        .actions-flex {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-mini {
            border: none;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 750;
            cursor: pointer;
            transition: filter 0.15s ease;
        }

        .btn-mini:hover {
            filter: brightness(0.95);
        }

        .btn-mini-approve {
            background-color: var(--btn-approve-bg);
            color: var(--btn-approve-text);
        }

        .btn-mini-reject {
            background-color: var(--btn-reject-bg);
            color: var(--btn-reject-text);
        }

        /* 5. DYNAMIC MODAL / DIALOGS */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(11, 26, 48, 0.75);
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
            max-width: 500px;
            border-radius: 30px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 850;
            margin-bottom: 4px;
        }

        .modal-text {
            font-size: 16px;
            color: var(--text-slate);
            line-height: 1.5;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-slate);
        }

        .form-control {
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: #346DCB;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 12px;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: var(--text-slate);
            border: none;
            border-radius: 50px;
            padding: 10px 24px;
            font-weight: 700;
            cursor: pointer;
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

        <!-- 1. INTEGRATED SIDEBAR GRID SYSTEM -->
        <div class="relative z-10 grid min-h-[calc(100vh-10rem)] grid-cols-[22rem_1fr] gap-6">
            
            <!-- LEFT SIDEBAR -->
            <aside class="rounded-[1.875rem] bg-white p-8 text-slate-950">
                <nav class="space-y-6 text-xl">
                    <a href="{{ route('users.index') }}" class="block font-medium hover:text-[#346DCB]">All Employees</a>
                    <a href="{{ route('users.pending') }}" class="block font-extrabold hover:text-[#346DCB]">Pending Approvals</a>
                    <a href="{{ route('users.roles') }}" class="block font-medium hover:text-[#346DCB]">Roles & Permissions</a>
                </nav>
            </aside>

            <!-- MAIN WORKSPACE -->
            <div class="content-container">
                
                <!-- MAIN HEADER CARD -->
                <div class="header-card">
                    <h1 class="page-title text-5xl font-bold">Pending Approvals</h1>
                    <div class="header-controls">
                        <div class="search-container">
                            <input type="text" id="pendingSearch" class="search-input" placeholder="Search pending accounts...">
                        </div>
                        <button class="action-button" onclick="openBulkApproveModal()" style="display: none;" id="bulkActionBtn">Approve Selected</button>
                    </div>
                </div>

                <!-- MAIN PANEL -->
                <div class="content-card">
                    <form action="{{ route('approvals.bulk-handle') }}" method="POST" id="bulkApprovalForm">
                        @csrf
                        <input type="hidden" name="action_type" id="bulkActionType" value="approve">
                        
                        <div class="card-meta-row">
                            <span class="card-label-all">Awaiting Review</span>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <button type="button" class="btn-mini btn-mini-reject" style="display: none;" id="bulkRejectBtn" onclick="submitBulkAction('reject')">Reject Selected</button>
                                <label class="select-all-box">
                                    <input type="checkbox" id="selectAll" class="checkbox-custom">
                                    <span>Select All</span>
                                </label>
                            </div>
                        </div>
                    </form>

                    <!-- TABLE VIEWPORT -->
                    <div class="table-view">
                        <table class="pending-table">
                            <thead>
                                <tr>
                                    <th width="40px"></th>
                                    <th>Employee ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Requested Role</th>
                                    <th>Status</th>
                                    <th style="text-align: right; padding-right: 16px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingUsers as $user)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" form="bulkApprovalForm" class="user-checkbox checkbox-custom">
                                        </td>
                                        <td style="font-weight: 800;">{{ $user->employee_id ?? 'N/A' }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->department }}</td>
                                        <td>{{ $user->requested_role ?? 'General Employee' }}</td>
                                        <td>
                                            <span class="pill pending">Pending</span>
                                        </td>
                                        <td>
                                            <div class="actions-flex">
                                                <button class="btn-mini btn-mini-approve" onclick="openSingleApproveModal(@json($user))">Approve</button>
                                                <button class="btn-mini btn-mini-reject" onclick="openSingleRejectModal(@json($user))">Reject</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" style="text-align: center; color: var(--text-slate); padding: 40px;">
                                            No accounts currently awaiting registration approval. Excellent work!
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

    <!-- APPROVAL ACTION MODAL -->
    <div class="modal-overlay" id="approvalModal">
        <div class="modal-container">
            <h2 class="modal-title" id="approvalTitle">Approve Registration</h2>
            <p class="modal-text" id="approvalText">Are you sure you want to approve this employee registration? They will immediately receive access to their workstation portal.</p>
            
            <form id="approvalForm" method="POST">
                @csrf
                <div class="form-group" id="roleSelectionGroup">
                    <label for="assigned_role">Assign Final System Role</label>
                    <select name="assigned_role" id="assigned_role" class="form-control" required style="background: white;">
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeApprovalModal()">Cancel</button>
                    <button type="submit" class="action-button" style="background-color: var(--btn-approve-text);">Approve Access</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REJECTION ACTION MODAL -->
    <div class="modal-overlay" id="rejectionModal">
        <div class="modal-container">
            <h2 class="modal-title">Reject Registration Request</h2>
            <p class="modal-text">This will decline the request and optionally send an email informing them of the review result.</p>
            
            <form id="rejectionForm" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rejection_reason">Reason for Rejection</label>
                    <input type="text" name="reason" id="rejection_reason" class="form-control" required placeholder="e.g. Invalid Employee ID, Department mismatch">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeRejectionModal()">Cancel</button>
                    <button type="submit" class="action-button" style="background-color: var(--btn-reject-text);">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Overlay Control Handlers
        function openSingleApproveModal(user) {
            document.getElementById('approvalForm').action = `/approvals/${user.id}/approve`;
            document.getElementById('approvalTitle').innerText = "Approve User Request";
            document.getElementById('approvalText').innerText = `Approve login profile and finalize system setup parameters for ${user.first_name} ${user.last_name}.`;
            document.getElementById('roleSelectionGroup').style.display = 'flex';
            document.getElementById('approvalModal').style.display = 'flex';
        }

        function closeApprovalModal() {
            document.getElementById('approvalModal').style.display = 'none';
        }

        function openSingleRejectModal(user) {
            document.getElementById('rejectionForm').action = `/approvals/${user.id}/reject`;
            document.getElementById('rejectionModal').style.display = 'flex';
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').style.display = 'none';
        }

        // Live Table Search filter 
        const searchInput = document.getElementById("pendingSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const query = this.value.toLowerCase();
                const tableRows = document.querySelectorAll(".pending-table tbody tr");
                
                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(query) ? "" : "none";
                });
            });
        }

        // Bulk Selection Actions
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkActionBtn = document.getElementById('bulkActionBtn');
        const bulkRejectBtn = document.getElementById('bulkRejectBtn');

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                userCheckboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkButtons();
            });
        }

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkButtons);
        });

        function toggleBulkButtons() {
            const anyChecked = Array.from(userCheckboxes).some(cb => cb.checked);
            bulkActionBtn.style.display = anyChecked ? 'inline-block' : 'none';
            bulkRejectBtn.style.display = anyChecked ? 'inline-block' : 'none';
        }

        function openBulkApproveModal() {
            document.getElementById('approvalForm').action = "{{ route('approvals.bulk-handle') }}";
            document.getElementById('approvalTitle').innerText = "Bulk Approve Accounts";
            document.getElementById('approvalText').innerText = "You are approving multiple access registrations at once. Please assign a default fallback role:";
            document.getElementById('roleSelectionGroup').style.display = 'flex';
            
            // Build temporary input elements mapping selected IDs to the approval form scope
            cleanupBulkFormInputs('approvalForm');
            appendCheckboxPayload('approvalForm');
            
            document.getElementById('approvalModal').style.display = 'flex';
        }

        function submitBulkAction(actionType) {
            if(actionType === 'reject') {
                const reason = prompt("Enter a rejection reason for the selected accounts:");
                if(!reason) return;
                
                document.getElementById('bulkActionType').value = 'reject';
                
                // Add dynamically generated input elements inside form prior to submittal
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'reason';
                input.value = reason;
                document.getElementById('bulkApprovalForm').appendChild(input);
                
                document.getElementById('bulkApprovalForm').submit();
            }
        }

        function appendCheckboxPayload(targetFormId) {
            const form = document.getElementById(targetFormId);
            userCheckboxes.forEach(cb => {
                if(cb.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = cb.value;
                    input.classList.add('dynamic-bulk-payload');
                    form.appendChild(input);
                }
            });
        }

        function cleanupBulkFormInputs(targetFormId) {
            const dynamicElements = document.querySelectorAll(`#${targetFormId} .dynamic-bulk-payload`);
            dynamicElements.forEach(el => el.remove());
        }
    </script>
</body>
</html>