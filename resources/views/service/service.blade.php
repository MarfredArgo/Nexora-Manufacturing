@php
    $portal = $portal ?? 'client';
    $active = $active ?? 'service-desk';
    $title = $title ?? 'Service Desk';
    $subtitle = $subtitle ?? 'Track and manage ITSM tickets';
    $tickets = $tickets ?? collect();
    $ticketType = $ticketType ?? 'erp_module';
    $canProcessPasswordResets = $canProcessPasswordResets ?? false;
    $canCreateTicket = $canCreateTicket ?? ($portal === 'client' && $ticketType === 'nexora_support');
    $canUpdateTicket = $canUpdateTicket ?? ($portal === 'admin' || ($portal === 'client' && $ticketType === 'erp_module'));
    $updateMode = $updateMode ?? 'full';
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
    $storeRoute = $ticketType === 'nexora_support'
        ? route('client.itsm.service-desk.support.store')
        : route('client.itsm.service-desk.store');
    $updateTemplate = $portal === 'admin'
        ? route('admin.itsm.service-desk.update', ['ticket' => '__ID__'])
        : route('client.itsm.service-desk.update', ['ticket' => '__ID__']);
    $createLabel = 'Ask Nexora Support';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | {{ $title }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#1B365D] font-sans text-white">
    <div class="flex min-h-screen flex-col">
        <x-itsm-header
            :home-route="$portal === 'admin' ? route('admin.itsm.registration') : route('client.itsm.employees')"
            :active="$active"
            :nav-items="$navItems"
        />

        <main class="relative flex-1 overflow-hidden p-6">
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[64rem] -translate-x-1/2 -translate-y-1/2 opacity-10 blur-sm">

            <section class="relative z-10 grid gap-6 lg:grid-cols-[22rem_1fr]">
                <aside class="rounded-[1.875rem] bg-white p-8 text-slate-950">
                    <nav class="space-y-6 text-xl">
                        @if ($portal === 'admin')
                            <a href="{{ route('admin.itsm.service-desk') }}" class="block font-extrabold">Nexora Support Queue</a>
                            <a href="#" class="block font-medium hover:text-[#346DCB]">Assigned Requests</a>
                            <a href="#" class="block font-medium hover:text-[#346DCB]">Knowledge Base</a>
                            <a href="#" class="block font-medium hover:text-[#346DCB]">SLA Review</a>
                        @else
                            <a href="{{ route('client.itsm.service-desk') }}" class="block {{ $ticketType === 'erp_module' ? 'font-extrabold' : 'font-medium hover:text-[#346DCB]' }}">Module Ticket Dashboard</a>
                            <a href="{{ route('client.itsm.service-desk.support') }}" class="block {{ $ticketType === 'client_password_reset' ? 'font-extrabold' : 'font-medium hover:text-[#346DCB]' }}">Account Recovery</a>
                            <a href="#" class="block font-medium hover:text-[#346DCB]">Resolved Tickets</a>
                            <a href="#" class="block font-medium hover:text-[#346DCB]">Knowledge Base</a>
                        @endif
                    </nav>
                </aside>

                <div class="space-y-6">
                    <div class="rounded-[1.875rem] bg-white/90 px-10 py-8 text-slate-950">
                        <p class="text-sm font-semibold uppercase tracking-wide text-[#346DCB]">{{ $portal === 'admin' ? 'Nexora admin portal' : 'Company admin portal' }}</p>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-4">
                            <h1 class="text-5xl font-bold">{{ $title }}</h1>
                            @if ($canCreateTicket)
                                <button type="button" id="openCreateTicket" class="rounded-full bg-[#346DCB] px-5 py-2 font-semibold text-white transition hover:bg-[#2554a3]">{{ $createLabel }}</button>
                            @endif
                        </div>
                        <p class="mt-3 text-lg text-slate-600">{{ $subtitle }}</p>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-md bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="rounded-md bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('reset_credentials'))
                        <div class="rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
                            <p class="font-bold">One-time reset credentials</p>
                            <p class="mt-2">Username: <span class="font-mono">{{ session('reset_credentials.username') }}</span></p>
                            <p>Password: <span class="font-mono">{{ session('reset_credentials.password') }}</span></p>
                            <p class="mt-2">Provide these securely. The user must change the password on their next sign-in.</p>
                        </div>
                    @endif

                    <div class="grid gap-6 xl:grid-cols-4">
                        <div class="rounded-2xl bg-white p-6 text-slate-950">
                            <p class="text-sm font-semibold text-slate-500">Open Tickets</p>
                            <p class="mt-3 text-4xl font-bold">{{ $tickets->where('status', 'Open')->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 text-slate-950">
                            <p class="text-sm font-semibold text-slate-500">In Progress</p>
                            <p class="mt-3 text-4xl font-bold">{{ $tickets->where('status', 'In Progress')->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 text-slate-950">
                            <p class="text-sm font-semibold text-slate-500">Pending Review</p>
                            <p class="mt-3 text-4xl font-bold">{{ $tickets->where('status', 'Pending Review')->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 text-slate-950">
                            <p class="text-sm font-semibold text-slate-500">Resolved</p>
                            <p class="mt-3 text-4xl font-bold">{{ $tickets->where('status', 'Resolved')->count() }}</p>
                        </div>
                    </div>

                    <div class="rounded-[1.875rem] bg-white p-8 text-slate-950">
                        <div class="mb-6 flex items-center justify-between gap-4">
                            <h2 class="text-2xl font-bold">Recent Requests</h2>
                            <input type="text" id="ticketSearch" placeholder="Search" class="h-10 w-64 rounded border border-slate-300 px-3 text-sm">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse" id="ticketsTable">
                                <thead>
                                    <tr class="border-b-2 border-slate-200 text-left text-sm uppercase tracking-wide text-slate-500">
                                        <th class="py-3">Ticket</th>
                                        <th class="py-3">{{ $portal === 'admin' ? 'Client' : 'Requester' }}</th>
                                        <th class="py-3">Module</th>
                                        <th class="py-3">Subject</th>
                                        <th class="py-3">Category</th>
                                        <th class="py-3">Priority</th>
                                        <th class="py-3">Status</th>
                                        @if ($canUpdateTicket)
                                            <th class="py-3 text-right">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @forelse ($tickets as $ticket)
                                        <tr
                                            class="border-b border-slate-200"
                                            data-id="{{ $ticket->id }}"
                                            data-requester="{{ e($ticket->requester) }}"
                                            data-module="{{ e($ticket->module) }}"
                                            data-category="{{ e($ticket->category) }}"
                                            data-priority="{{ e($ticket->priority) }}"
                                            data-status="{{ e($ticket->status) }}"
                                            data-subject="{{ e($ticket->subject) }}"
                                            data-description="{{ e($ticket->description) }}"
                                        >
                                            <td class="py-4 font-semibold">{{ $ticket->ticket_no }}</td>
                                            <td class="py-4">{{ $portal === 'admin' ? ($ticket->client_name ?? 'Internal') : ($ticket->requester ?? 'Company user') }}</td>
                                            <td class="py-4">{{ $ticket->module ?? ($ticket->ticket_type === 'nexora_support' ? 'Nexora Platform' : 'General') }}</td>
                                            <td class="py-4">{{ $ticket->subject }}</td>
                                            <td class="py-4">{{ $ticket->category }}</td>
                                            <td class="py-4">{{ $ticket->priority }}</td>
                                            <td class="py-4">{{ $ticket->status }}</td>
                                            @if ($canUpdateTicket)
                                                <td class="py-4 text-right">
                                                    @if ($canProcessPasswordResets && $ticket->category === 'Password Reset' && $ticket->status !== 'Resolved')
                                                        <details class="mb-2 text-left">
                                                            <summary class="cursor-pointer rounded-md bg-[#346DCB] px-3 py-1 text-center font-semibold text-white hover:bg-[#2554a3]">Set temporary password</summary>
                                                            <form method="POST" action="{{ route('client.itsm.service-desk.support.reset-password', $ticket) }}" class="mt-2 w-64 rounded border border-slate-200 bg-slate-50 p-3">
                                                                @csrf
                                                                <label class="mb-1 block text-xs font-semibold text-slate-700">Temporary password</label>
                                                                <input type="password" name="temporary_password" minlength="10" required autocomplete="new-password" class="mb-2 h-9 w-full rounded border border-slate-300 px-2 text-xs">
                                                                <label class="mb-1 block text-xs font-semibold text-slate-700">Confirm password</label>
                                                                <input type="password" name="temporary_password_confirmation" minlength="10" required autocomplete="new-password" class="mb-2 h-9 w-full rounded border border-slate-300 px-2 text-xs">
                                                                <button type="submit" class="w-full rounded bg-[#132B52] px-2 py-1.5 text-xs font-semibold text-white hover:bg-[#0b1e3d]">Reset and resolve</button>
                                                            </form>
                                                        </details>
                                                    @endif
                                                    @if ($updateMode !== 'password_reset')
                                                        <button type="button" class="edit-ticket rounded-md border border-slate-300 px-3 py-1 font-semibold hover:bg-slate-100">
                                                            {{ $updateMode === 'status_only' ? 'Resolve' : 'Edit' }}
                                                        </button>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $canUpdateTicket ? 8 : 7 }}" class="py-12 text-center text-slate-500">No tickets found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <div id="ticketModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-6">
            <div class="w-full max-w-3xl rounded-2xl bg-white p-8 text-slate-950 shadow-2xl">
                <div class="mb-6 flex items-center justify-between">
                    <h2 id="ticketModalTitle" class="text-2xl font-bold">Create Ticket</h2>
                    <button type="button" id="closeTicketModal" class="text-2xl font-bold text-slate-500 hover:text-slate-950">&times;</button>
                </div>

                <form id="ticketForm" method="POST" action="{{ $storeRoute }}" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    @csrf
                    <input type="hidden" name="_method" id="ticketMethod" value="POST">
                    <input type="hidden" name="ticket_type" value="{{ $ticketType }}">

                    @if ($updateMode === 'status_only')
                        <label class="block md:col-span-2">
                            <span class="mb-2 block text-sm font-semibold">Resolution Status</span>
                            <select name="status" id="ticket_status" class="h-11 w-full rounded border border-slate-300 px-3">
                                <option>In Progress</option>
                                <option>Resolved</option>
                                <option>Closed</option>
                            </select>
                        </label>
                    @else
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold">Requester</span>
                            <input type="text" name="requester" id="ticket_requester" class="h-11 w-full rounded border border-slate-300 px-3">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold">Category</span>
                            <input type="text" name="category" id="ticket_category" required class="h-11 w-full rounded border border-slate-300 px-3">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold">Area</span>
                            <select name="module" id="ticket_module" class="h-11 w-full rounded border border-slate-300 px-3">
                                @if ($ticketType === 'nexora_support')
                                    <option>Nexora Platform</option>
                                    <option>Account & Access</option>
                                    <option>Billing & Subscription</option>
                                    <option>System Configuration</option>
                                    <option>Other</option>
                                @else
                                    <option>HR</option>
                                    <option>Business Intelligence</option>
                                    <option>Finance</option>
                                    <option>Inventory</option>
                                    <option>Operations</option>
                                    <option>Procurement</option>
                                    <option>Sales</option>
                                    <option>General ERP</option>
                                @endif
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold">Priority</span>
                            <select name="priority" id="ticket_priority" class="h-11 w-full rounded border border-slate-300 px-3">
                                <option>Low</option>
                                <option selected>Medium</option>
                                <option>High</option>
                                <option>Critical</option>
                            </select>
                        </label>

                        @if ($portal === 'admin')
                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold">Status</span>
                                <select name="status" id="ticket_status" class="h-11 w-full rounded border border-slate-300 px-3">
                                    <option>Open</option>
                                    <option>In Progress</option>
                                    <option>Pending Review</option>
                                    <option>Resolved</option>
                                    <option>Closed</option>
                                </select>
                            </label>
                        @endif

                        <label class="block md:col-span-2">
                            <span class="mb-2 block text-sm font-semibold">Subject</span>
                            <input type="text" name="subject" id="ticket_subject" required class="h-11 w-full rounded border border-slate-300 px-3">
                        </label>

                        <label class="block md:col-span-2">
                            <span class="mb-2 block text-sm font-semibold">Description</span>
                            <textarea name="description" id="ticket_description" rows="4" class="w-full rounded border border-slate-300 px-3 py-2"></textarea>
                        </label>
                    @endif

                    <div class="flex justify-end gap-3 pt-5 md:col-span-2">
                        <button type="button" id="cancelTicketModal" class="rounded-md border border-slate-300 px-5 py-2 font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                        <button type="submit" id="ticketSubmitButton" class="rounded-md bg-[#346DCB] px-5 py-2 font-semibold text-white hover:bg-[#2554a3]">Save ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const storeRoute = @json($storeRoute);
        const updateTemplate = @json($updateTemplate);
        const ticketModal = document.getElementById('ticketModal');
        const ticketForm = document.getElementById('ticketForm');
        const ticketMethod = document.getElementById('ticketMethod');
        const ticketModalTitle = document.getElementById('ticketModalTitle');
        const ticketSubmitButton = document.getElementById('ticketSubmitButton');
        const updateMode = @json($updateMode);

        function setTicketField(id, value) {
            const field = document.getElementById(id);
            if (field) field.value = value ?? '';
        }

        function openTicketModal(row = null) {
            ticketForm.action = row ? updateTemplate.replace('__ID__', row.dataset.id) : storeRoute;
            ticketMethod.value = row ? 'PATCH' : 'POST';
            ticketModalTitle.textContent = row
                ? (updateMode === 'status_only' ? 'Resolve Module Ticket' : 'Edit Ticket')
                : 'Ask Nexora Support';
            if (ticketSubmitButton) {
                ticketSubmitButton.textContent = row
                    ? (updateMode === 'status_only' ? 'Update status' : 'Save changes')
                    : 'Submit ticket';
            }
            setTicketField('ticket_requester', row?.dataset.requester);
            setTicketField('ticket_module', row?.dataset.module || @json($ticketType === 'nexora_support' ? 'Nexora Platform' : 'General ERP'));
            setTicketField('ticket_category', row?.dataset.category);
            setTicketField('ticket_priority', row?.dataset.priority || 'Medium');
            setTicketField('ticket_status', row?.dataset.status || (updateMode === 'status_only' ? 'Resolved' : 'Open'));
            setTicketField('ticket_subject', row?.dataset.subject);
            setTicketField('ticket_description', row?.dataset.description);
            ticketModal.classList.remove('hidden');
            ticketModal.classList.add('flex');
        }

        function closeTicketModal() {
            ticketModal.classList.add('hidden');
            ticketModal.classList.remove('flex');
        }

        document.getElementById('openCreateTicket')?.addEventListener('click', () => openTicketModal());
        document.getElementById('closeTicketModal')?.addEventListener('click', closeTicketModal);
        document.getElementById('cancelTicketModal')?.addEventListener('click', closeTicketModal);
        ticketModal?.addEventListener('click', (event) => {
            if (event.target === ticketModal) closeTicketModal();
        });

        document.querySelectorAll('.edit-ticket').forEach((button) => {
            button.addEventListener('click', () => openTicketModal(button.closest('tr')));
        });

        document.getElementById('ticketSearch')?.addEventListener('input', (event) => {
            const query = event.target.value.toLowerCase();
            document.querySelectorAll('#ticketsTable tbody tr').forEach((row) => {
                row.classList.toggle('hidden', !row.textContent.toLowerCase().includes(query));
            });
        });
    </script>
</body>
</html>
