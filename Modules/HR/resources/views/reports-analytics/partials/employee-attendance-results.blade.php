<div class="overflow-hidden rounded-xl bg-[#0B1E3D] ring-1 ring-white/5">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead>
        <tr class="border-b border-white/5 bg-[#0B1E3D] text-xs uppercase tracking-wide text-slate-400">
          <th class="px-5 py-2 font-medium">Date</th>
          <th class="px-5 py-2 font-medium">Time In</th>
          <th class="px-5 py-2 font-medium">In Image</th>
          <th class="px-5 py-2 font-medium">Time Out</th>
          <th class="px-5 py-2 font-medium">Out Image</th>
          <th class="px-5 py-2 font-medium">Work Hours</th>
          <th class="px-5 py-2 font-medium">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($attendances as $i => $row)
          @php
            $status = $row->displayStatus();
            $statusClasses = $status === 'Present'
                ? 'bg-emerald-500/15 text-emerald-400'
                : 'bg-rose-500/15 text-rose-400';
            $showLateIn = $row->showsLateTimeInWarning();
            $showShortOut = $row->showsShortTimeOutWarning();
            $hoursTitle = 'Did not meet the required work hours';
            $inImage = $row->timeInImageUrl();
            $outImage = $row->timeOutImageUrl();
          @endphp
          <tr class="border-b border-white/5 last:border-none hover:bg-white/[0.03] {{ $i % 2 === 1 ? 'bg-white/[0.015]' : '' }}">
            <td class="px-5 py-2 text-slate-300 whitespace-nowrap">
              {{ \Carbon\Carbon::parse($row->attendance_date)->format('d M Y') }}
            </td>
            <td class="px-5 py-2 whitespace-nowrap">
              @if ($showLateIn)
                <span class="cursor-help font-medium text-red-500" title="{{ $hoursTitle }}">
                  {{ $row->formattedTimeIn() }}
                </span>
              @else
                <span class="text-slate-300">{{ $row->formattedTimeIn() }}</span>
              @endif
            </td>
            <td class="px-5 py-1.5">
              @if ($inImage)
                <button
                  type="button"
                  class="attendance-photo-thumb inline-flex h-9 w-9 overflow-hidden rounded-md border border-white/10 bg-black/20 p-0"
                  data-photo-src="{{ $inImage }}"
                  data-photo-label="Time In â€” {{ \Carbon\Carbon::parse($row->attendance_date)->format('d M Y') }}"
                  title="View In Image"
                >
                  <img src="{{ $inImage }}" alt="Time in photo" class="h-full w-full object-cover">
                </button>
              @else
                <span class="text-slate-500">â€”</span>
              @endif
            </td>
            <td class="px-5 py-2 whitespace-nowrap">
              @if ($showShortOut)
                <span class="cursor-help font-medium text-red-500" title="{{ $hoursTitle }}">
                  {{ $row->formattedTimeOut() }}
                </span>
              @else
                <span class="text-slate-300">{{ $row->formattedTimeOut() }}</span>
              @endif
            </td>
            <td class="px-5 py-1.5">
              @if ($outImage)
                <button
                  type="button"
                  class="attendance-photo-thumb inline-flex h-9 w-9 overflow-hidden rounded-md border border-white/10 bg-black/20 p-0"
                  data-photo-src="{{ $outImage }}"
                  data-photo-label="Time Out â€” {{ \Carbon\Carbon::parse($row->attendance_date)->format('d M Y') }}"
                  title="View Out Image"
                >
                  <img src="{{ $outImage }}" alt="Time out photo" class="h-full w-full object-cover">
                </button>
              @else
                <span class="text-slate-500">â€”</span>
              @endif
            </td>
            <td class="px-5 py-2 text-slate-300 whitespace-nowrap">{{ $row->formattedWorkHours() }}</td>
            <td class="px-5 py-2">
              <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-medium {{ $statusClasses }}">
                {{ $status }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-5 py-10 text-center text-slate-500">
              No attendance records for this employee.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@include('partials.list-pagination', ['paginator' => $attendances, 'label' => 'records'])
