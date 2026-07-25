@php
$today = \Carbon\Carbon::now();

$scheduleData = collect($workOrders)
    ->reject(fn ($order) => $order['status'] === 'Cancelled')
    ->map(function ($order) use ($today) {
        $rawDue  = preg_replace('/^(Due|Completed)\s+/i', '', $order['due']);
        $dueDate = \Carbon\Carbon::parse($rawDue);
        $daysDiff = round($today->diffInDays($dueDate, false));

        if ($order['status'] === 'Finished') {
            $priority   = 'completed';
            $label      = 'Completed';
            $badgeClass = 'bg-nexora-corporate text-white';
            $barColor   = 'bg-nexora-corporate';
        } elseif ($daysDiff < 0) {
            $priority = 'overdue'; $label = 'Overdue';
            $badgeClass = 'bg-nexora-stat-red text-white'; $barColor = 'bg-nexora-danger';
        } elseif ($daysDiff <= 3) {
            $priority = 'high'; $label = 'High';
            $badgeClass = 'bg-nexora-stat-orange text-white'; $barColor = 'bg-nexora-warning';
        } elseif ($daysDiff <= 7) {
            $priority = 'medium'; $label = 'Medium';
            $badgeClass = 'bg-nexora-stat-yellow text-gray-800'; $barColor = 'bg-nexora-caution';
        } else {
            $priority = 'low'; $label = 'Low';
            $badgeClass = 'bg-nexora-stat-green text-white'; $barColor = 'bg-nexora-success';
        }

        return array_merge($order, [
            'days_remaining' => $daysDiff,
            'priority'       => $priority,
            'priority_label' => $label,
            'priority_class' => $badgeClass,
            'bar_color'      => $barColor,
        ]);
    })
    ->sortBy(function ($p) {
        $rank = match ($p['priority']) {
            'overdue'   => 0,
            'high'      => 1,
            'medium'    => 2,
            'low'       => 3,
            'completed' => 4,
        };
        return ($rank * 100000) + $p['days_remaining'];
    });

$counts = [
    'all'       => $scheduleData->count(),
    'overdue'   => $scheduleData->where('priority', 'overdue')->count(),
    'high'      => $scheduleData->where('priority', 'high')->count(),
    'medium'    => $scheduleData->where('priority', 'medium')->count(),
    'low'       => $scheduleData->where('priority', 'low')->count(),
    'completed' => $scheduleData->where('priority', 'completed')->count(),
];
@endphp

<div class="flex flex-col h-full p-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 flex-shrink-0 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-nexora-deep-navy">Project Schedule & Timeline</h2>
            <p class="text-sm text-nexora-slate-500 mt-1">View all projects, deadlines, and priority levels</p>
        </div>

        <div class="relative w-full md:w-64">
            <span class="inline-block" aria-hidden="true">&#8226;</span>
            <input type="text" id="scheduleSearch" placeholder="Search projects..." oninput="filterScheduleSearch()"
                   class="w-full pl-8 pr-3 py-1.5 rounded-md bg-nexora-steel-blue/50 text-nexora-deep-navy text-xs placeholder-nexora-navy/50 border border-nexora-corporate focus:outline-none focus:border-nexora-deep-navy">
        </div>
    </div>

    {{-- Filter Controls --}}
    <div class="flex flex-wrap gap-3 flex-shrink-0 mb-6">
        <button onclick="filterSchedule('all', event)" data-filter="all" class="filter-sched px-3 py-1.5 rounded bg-nexora-corporate text-white text-sm transition-colors">
            All Projects <span class="opacity-75">({{ $counts['all'] }})</span>
        </button>
        <button onclick="filterSchedule('overdue', event)" data-filter="overdue" class="filter-sched px-3 py-1.5 rounded bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
            Overdue <span class="opacity-75">({{ $counts['overdue'] }})</span>
        </button>
        <button onclick="filterSchedule('high', event)" data-filter="high" class="filter-sched px-3 py-1.5 rounded bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
            High Priority <span class="opacity-75">({{ $counts['high'] }})</span>
        </button>
        <button onclick="filterSchedule('medium', event)" data-filter="medium" class="filter-sched px-3 py-1.5 rounded bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
            Medium Priority <span class="opacity-75">({{ $counts['medium'] }})</span>
        </button>
        <button onclick="filterSchedule('low', event)" data-filter="low" class="filter-sched px-3 py-1.5 rounded bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
            Low Priority <span class="opacity-75">({{ $counts['low'] }})</span>
        </button>
        <button onclick="filterSchedule('completed', event)" data-filter="completed" class="filter-sched px-3 py-1.5 rounded bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
            Completed <span class="opacity-75">({{ $counts['completed'] }})</span>
        </button>
    </div>

    {{-- Timeline View: the only section that scrolls --}}
    <div class="bg-white rounded-lg border border-nexora-corporate/30 shadow-sm flex-1 min-h-0 flex flex-col overflow-hidden mb-6">
        <div class="overflow-auto flex-1 min-h-0 [&::-webkit-scrollbar]:hidden">
            <table class="w-full text-sm sortable-table" data-table-id="schedule">
                <thead class="bg-nexora-slate-100 border-b border-nexora-corporate/30 sticky top-0 z-10">
                    <tr class="bg-white">
                        <th class="text-left p-3 text-nexora-deep-navy font-semibold sortable" data-sort-type="text">Project ID</th>
                        <th class="text-left p-3 text-nexora-deep-navy font-semibold sortable" data-sort-type="text">Project Name</th>
                        <th class="text-center p-3 text-nexora-deep-navy font-semibold sortable" data-sort-type="text">Status</th>
                        <th class="text-center p-3 text-nexora-deep-navy font-semibold sortable" data-sort-type="text">Due Date</th>
                        <th class="text-center p-3 text-nexora-deep-navy font-semibold sortable" data-sort-type="number">Time Remaining</th>
                        <th class="text-center p-3 text-nexora-deep-navy font-semibold sortable" data-sort-type="text">Priority</th>
                        <th class="p-3 text-nexora-deep-navy font-semibold">Timeline</th>
                    </tr>
                </thead>
                <tbody id="schedule-body">
                    @forelse($scheduleData as $originalIndex => $project)
                    @php
                        $statusStyle = $statusStyles[$project['status']] ?? ['pill' => 'bg-gray-400 text-white'];
                        $days = $project['days_remaining'];
                        $timelineWidth = max(5, min(100, 100 - ($days * 3)));
                    @endphp
                    <tr class="schedule-row border-b border-nexora-corporate/20 hover:bg-nexora-slate-50 hover:shadow-sm cursor-pointer transition"
                        data-priority="{{ $project['priority'] }}"
                        data-name="{{ $project['name'] }}"
                        onclick="location.href='?page=orders&sub=status&order={{ $originalIndex }}'">
                        <td class="p-3 font-mono text-xs text-nexora-slate-500" data-sort-value="{{ $project['id'] }}">{{ $project['id'] }}</td>
                        <td class="p-3 font-medium text-nexora-deep-navy" data-sort-value="{{ $project['name'] }}">{{ $project['name'] }}</td>
                        <td class="p-3 text-center" data-sort-value="{{ $project['status'] }}">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusStyle['pill'] }}">
                                {{ $project['status'] }}
                            </span>
                        </td>
                        <td class="p-3 text-center text-nexora-deep-navy" data-sort-value="{{ $project['due'] }}">{{ $project['due'] }}</td>
                        <td class="p-3 text-center font-medium" data-sort-value="{{ $days }}">
                            @if($project['priority'] === 'completed')
                                <span class="text-nexora-corporate font-semibold">Completed</span>
                            @elseif($days < 0)
                                <span class="text-red-600 font-semibold">{{ abs($days) }} days overdue</span>
                            @elseif($days === 0)
                                <span class="text-orange-600 font-semibold">Due today</span>
                            @else
                                <span class="text-nexora-deep-navy">{{ $days }} day{{ $days !== 1 ? 's' : '' }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-center" data-sort-value="{{ $project['priority'] }}">
                            <span class="px-2.5 py-1 rounded text-xs font-semibold {{ $project['priority_class'] }}">
                                {{ $project['priority_label'] }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300 {{ $project['bar_color'] }}"
                                     style="width: {{ $timelineWidth }}%;"></div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-nexora-slate-500 italic">No projects found in schedule</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <p id="schedule-no-results" class="hidden p-8 text-center text-nexora-slate-500 italic">No projects match your search/filter</p>
        </div>
    </div>

    {{-- Triage Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 flex-shrink-0">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="font-semibold text-red-800 mb-2">Overdue</h4>
            <p class="text-2xl font-bold text-red-700">{{ $counts['overdue'] }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="font-semibold text-red-800 mb-2">High Priority</h4>
            <p class="text-2xl font-bold text-red-700">{{ $counts['high'] }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <h4 class="font-semibold text-orange-800 mb-2">Medium Priority</h4>
            <p class="text-2xl font-bold text-orange-700">{{ $counts['medium'] }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-semibold text-green-800 mb-2">Low Priority</h4>
            <p class="text-2xl font-bold text-green-700">{{ $counts['low'] }}</p>
        </div>
        <div class="bg-nexora-slate-100 border border-nexora-corporate/30 rounded-lg p-4">
            <h4 class="font-semibold text-nexora-deep-navy mb-2">Completed</h4>
            <p class="text-2xl font-bold text-nexora-corporate">{{ $counts['completed'] }}</p>
        </div>
    </div>

</div>

<script>initSortableTables();</script>
