<div class="w-full max-w-[1859px] mx-auto bg-[#0B1E3D] rounded-[10px] overflow-x-hidden">
    <table class="w-full table-fixed border-collapse">
        <colgroup>
            <col style="width:19%">
            <col style="width:14%">
            <col style="width:11%">
            <col style="width:11%">
            <col style="width:11%">
            <col style="width:12%">
            <col style="width:11%">
            <col style="width:11%">
        </colgroup>
        <tbody>
            @forelse($employees as $employee)
                <tr class="border-t border-white/[0.18] transition-colors duration-[250ms] hover:bg-[#21457f]">
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">
                        @php
                            $genderClass = match(strtolower($employee->gender ?? '')) {
                                'female' => 'text-[#ff8bd2]',
                                'male' => 'text-[#6ea9ff]',
                                default => 'text-white',
                            };
                        @endphp
                        <i class="fa-solid fa-circle-user text-2xl {{ $genderClass }} mr-2"></i>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                        <span class="block text-[0.65rem] text-[#93abd3] font-light mt-0.5">{{ '2026' . str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">{{ $employee->department }}</td>
                    @php
                        $presentDays = (int) ($employee->present_days ?? 0);
                        $absentDays = (int) ($employee->absent_days ?? 0);
                        $leaveDays = (int) ($employee->leave_days ?? 0);
                        $recordedDays = $presentDays + $absentDays + $leaveDays;
                        $attendancePct = $recordedDays > 0
                            ? round(($presentDays / $recordedDays) * 100, 1)
                            : 0;
                        $statusLabel = $presentDays > 0 ? 'Active' : 'No Record';
                    @endphp
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">{{ $presentDays }}</td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">{{ $absentDays }}</td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">{{ $leaveDays }}</td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">{{ $attendancePct }}%</td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight">
                        <span class="status-badge">{{ $statusLabel }}</span>
                    </td>
                    <td class="p-4 text-[0.84375rem] text-center font-extralight">
                        <a href="{{ route('hr.reports-analytics.employee-attendance', $employee->id) }}" class="inline-block bg-[#132B52] text-white no-underline px-[21px] py-1.5 rounded-xl text-[0.6875rem] transition-all duration-[250ms] hover:bg-[#2e5ca3] hover:-translate-y-px">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-[30px] text-center text-[#b9c8e8] text-sm">
                        No employees found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.list-pagination', ['paginator' => $employees, 'label' => 'employees'])