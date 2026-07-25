@php
    $portal = $portal ?? 'client';
    $active = $active ?? ($portal === 'admin' ? 'clients' : 'employees');
    $title = 'Roles & Permissions';
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
    <title>Nexora | Roles & Permissions</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-color: #1B365D;
            --card-bg: #ffffff;
            --text-dark: #000000;
            --text-slate: #1e293b;
            --border-color: #e2e8f0;
            
            /* Status Badges */
            --badge-active-bg: #dcfce7;
            --badge-active-text: #15803d;
            --badge-inactive-bg: #f1f5f9;
            --badge-inactive-text: #475569;
            
            /* Action Buttons */
            --btn-edit-bg: #eff6ff;
            --btn-edit-text: #2563eb;
            --btn-delete-bg: #fef2f2;
            --btn-delete-text: #dc2626;
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
            font-size: 14px;
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
            font-weight: 600;
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

        .roles-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .roles-table th {
            padding: 18px 16px;
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        .roles-table td {
            padding: 20px 16px;
            font-size: 15px;
            color: var(--text-slate);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .roles-table tr:last-child td {
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

        .pill.active {
            background-color: var(--badge-active-bg);
            color: var(--badge-active-text);
        }

        .pill.inactive {
            background-color: var(--badge-inactive-bg);
            color: var(--badge-inactive-text);
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

        .btn-mini-edit {
            background-color: var(--btn-edit-bg);
            color: var(--btn-edit-text);
        }

        .btn-mini-delete {
            background-color: var(--btn-delete-bg);
            color: var(--btn-delete-text);
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
            max-width: 550px;
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

        .permissions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 12px;
            border: 1.5px solid var(--border-color);
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
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
                    <a href="{{ route('users.pending') }}" class="block font-medium hover:text-[#346DCB]">Pending Approvals</a>
                    <a href="{{ route('users.roles') }}" class="block font-extrabold hover:text-[#346DCB]">Roles & Permissions</a>
                </nav>
            </aside>

            <!-- MAIN WORKSPACE -->
            <div class="content-container">
                
                <!-- MAIN HEADER CARD -->
                <div class="header-card">
                    <h1 class="page-title text-5xl font-bold">Roles & Permissions</h1>
                    <div class="header-controls">
                        <div class="search-container">
                            <input type="text" id="roleSearch" class="search-input" placeholder="Search roles...">
                        </div>
                        <button class="action-button" onclick="openCreateModal()">Create Role</button>
                    </div>
                </div>

                <!-- MAIN PANEL -->
                <div class="content-card">
                    <form action="{{ route('roles.bulk-delete') }}" method="POST" id="bulkDeleteForm">
                        @csrf
                        <div class="card-meta-row">
                            <span class="card-label-all">Roles List</span>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <button type="submit" class="btn-mini btn-mini-delete" style="display: none;" id="bulkDeleteBtn">Delete Selected</button>
                                <label class="select-all-box">
                                    <input type="checkbox" id="selectAll" class="checkbox-custom">
                                    <span>Select All</span>
                                </label>
                            </div>
                        </div>
                    </form>

                    <!-- TABLE VIEWPORT -->
                    <div class="table-view">
                        <table class="roles-table">
                            <thead>
                                <tr>
                                    <th width="40px"></th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Assigned Users</th>
                                    <th>Department</th>
                                    <th>Permissions</th>
                                    <th>Status</th>
                                    <th style="text-align: right; padding-right: 16px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" form="bulkDeleteForm" class="role-checkbox checkbox-custom">
                                        </td>
                                        <td style="font-weight: 800;">{{ $role->name }}</td>
                                        <td>{{ $role->description }}</td>
                                        <td>{{ $role->users_count ?? 0 }}</td>
                                        <td>{{ $role->department }}</td>
                                        <td>
                                            @if(is_array($role->permissions))
                                                {{ implode(', ', $role->permissions) }}
                                            @else
                                                {{ $role->permissions }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="pill {{ $role->is_active ? 'active' : 'inactive' }}">
                                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="actions-flex">
                                                <button class="btn-mini btn-mini-edit" onclick="openEditModal(@json($role))">Edit</button>
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-mini btn-mini-delete">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="text-align: center; color: var(--text-slate); padding: 40px;">
                                            No database records found. Click "Create Role" to start adding permissions.
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

    <!-- CREATE ROLE MODAL -->
    <div class="modal-overlay" id="createModal">
        <div class="modal-container">
            <h2 class="modal-title">Create New Role</h2>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group">
                        <label for="create_name">Role Name</label>
                        <input type="text" name="name" id="create_name" class="form-control" required placeholder="e.g. Finance Officer">
                    </div>
                    <div class="form-group">
                        <label for="create_description">Description</label>
                        <input type="text" name="description" id="create_description" class="form-control" placeholder="Short scope summary">
                    </div>
                    <div class="form-group">
                        <label for="create_department">Department</label>
                        <input type="text" name="department" id="create_department" class="form-control" required placeholder="e.g. Finance">
                    </div>
                    
                    <!-- DATA KEY-IN CHECKBOXES -->
                    <div class="form-group">
                        <label>Key-In Permissions</label>
                        <div class="permissions-grid">
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" value="Read" class="checkbox-custom"> Read
                            </label>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" value="Write" class="checkbox-custom"> Write
                            </label>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" value="Approve" class="checkbox-custom"> Approve
                            </label>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" value="Delete" class="checkbox-custom"> Delete
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="permission-item">
                            <input type="checkbox" name="is_active" value="1" checked class="checkbox-custom"> Active Role Status
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="action-button">Save Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT ROLE MODAL -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-container">
            <h2 class="modal-title">Edit Role</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group">
                        <label for="edit_name">Role Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <input type="text" name="description" id="edit_description" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit_department">Department</label>
                        <input type="text" name="department" id="edit_department" class="form-control" required>
                    </div>
                    
                    <!-- DATA KEY-IN CHECKBOXES -->
                    <div class="form-group">
                        <label>Key-In Permissions</label>
                        <div class="permissions-grid">
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" id="perm_read" value="Read" class="checkbox-custom"> Read
                            </label>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" id="perm_write" value="Write" class="checkbox-custom"> Write
                            </label>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" id="perm_approve" value="Approve" class="checkbox-custom"> Approve
                            </label>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" id="perm_delete" value="Delete" class="checkbox-custom"> Delete
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="permission-item">
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="checkbox-custom"> Active Role Status
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="action-button">Update Details</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Overlay Controls
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'flex';
        }
        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }

        function openEditModal(role) {
            // Point action explicitly to route('roles.update', role.id) endpoint pattern
            document.getElementById('editForm').action = `/roles/${role.id}`;
            document.getElementById('edit_name').value = role.name;
            document.getElementById('edit_description').value = role.description || '';
            document.getElementById('edit_department').value = role.department;
            
            // Toggle active status checkbox
            document.getElementById('edit_is_active').checked = role.is_active == 1;

            // Reset all permission selections
            document.querySelectorAll('#editModal input[name="permissions[]"]').forEach(box => box.checked = false);
            
            // Map JSON serialized permissions to UI edit checkboxes
            if(role.permissions) {
                const perms = Array.isArray(role.permissions) ? role.permissions : JSON.parse(role.permissions);
                perms.forEach(perm => {
                    const cb = document.querySelector(`#editModal input[value="${perm}"]`);
                    if(cb) cb.checked = true;
                });
            }

            document.getElementById('editModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Live Table Filtering Search
        const searchInput = document.getElementById("roleSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const query = this.value.toLowerCase();
                const tableRows = document.querySelectorAll(".roles-table tbody tr");
                
                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(query) ? "" : "none";
                });
            });
        }

        // Bulk Selection Actions
        const selectAll = document.getElementById('selectAll');
        const roleCheckboxes = document.querySelectorAll('.role-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                roleCheckboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkDeleteButton();
            });
        }

        roleCheckboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkDeleteButton);
        });

        function toggleBulkDeleteButton() {
            const anyChecked = Array.from(roleCheckboxes).some(cb => cb.checked);
            bulkDeleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
        }
    </script>
</body>
</html>