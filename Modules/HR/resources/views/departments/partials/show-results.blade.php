<div class="w-full max-w-[1859px] mx-auto bg-[#0B1E3D] rounded-[10px] overflow-x-hidden">
    <table class="w-full table-fixed border-collapse">
        <tbody>
            @forelse($departments as $department)
                <tr class="border-t border-white/[0.18] transition-colors duration-[250ms] hover:bg-[#21457f]">
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight w-[23%]">
                        @php
                            $genderClass = match(strtolower($department->gender ?? '')) {
                                'female' => 'text-[#ff8bd2]',
                                'male' => 'text-[#6ea9ff]',
                                default => 'text-white',
                            };
                        @endphp
                        <i class="fa-solid fa-circle-user text-2xl {{ $genderClass }} mr-2"></i>
                        {{ '2026' . str_pad($department->id, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight w-[23%]">{{ $department->first_name }} {{ $department->last_name }}</td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight w-[23%]">{{ $department->position }}</td>
                    <td class="p-4 text-[0.84375rem] text-center border-r border-white/[0.12] font-extralight w-[23%]">{{ $department->status }}</td>
                    <td class="p-4 text-[0.84375rem] text-center font-extralight w-[15%]">
                        <a href="{{ route('hr.employees.show', $department->id) }}" class="inline-block bg-[#132B52] text-white no-underline px-[21px] py-1.5 rounded-xl text-[0.6875rem] transition-all duration-[250ms] hover:bg-[#2e5ca3] hover:-translate-y-px">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-[30px] text-center text-[#b9c8e8] text-sm">
                        No employees found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.list-pagination', ['paginator' => $departments, 'label' => 'employees'])
