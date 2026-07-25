<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Employee Attendance â€” {{ $employee->first_name }} {{ $employee->last_name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<style>
  body { font-family: 'Inter', system-ui, sans-serif; }
</style>
</head>
<body class="min-h-screen bg-[#1B3A6B] text-slate-200">

  @include('partials.navbar')

  <div class="w-full px-6 py-8" data-ajax-list>

    <nav class="mb-1 text-xs text-slate-400">
      <a href="{{ route('hr.reports-analytics.attendance-overview') }}" class="hover:text-slate-200">Attendance Record</a>
      <span class="mx-1">&gt;</span>
      <span class="text-sky-400">Employee Attendance</span>
    </nav>

    @php
      $fullName = trim($employee->first_name.' '.$employee->last_name);
      $hireDate = $employee->hire_date
          ? \Carbon\Carbon::parse($employee->hire_date)->format('d M Y')
          : 'â€”';
      $email = $employee->company_email ?: ($employee->email ?: 'â€”');
    @endphp

    <div class="h-[184px] mt-4 mb-6 flex flex-wrap items-center gap-6 rounded-xl bg-[#0B1E3D] px-6 py-2 ring-1 ring-white/5">
      <div class="flex items-center gap-4">
        @if ($employee->profile_picture)
          <img src="{{ asset('profile_pictures/'.$employee->profile_picture) }}" alt="{{ $fullName }}" class="h-16 w-16 rounded-full object-cover" />
        @else
          <div class="flex h-16 w-16 items-center justify-center rounded-full bg-sky-500/20">
            <i class="fa-solid fa-user text-2xl text-sky-300"></i>
          </div>
        @endif
        <div>
          <div class="text-base font-semibold text-white">{{ $fullName }}</div>
          <div class="text-sm text-slate-400">{{ $employee->employee_id }} â€” {{ $employee->position ?: 'â€”' }}</div>
          <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-400">
            <i class="fa-regular fa-envelope"></i> {{ $email }}
          </div>
          <div class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-400">
            <i class="fa-regular fa-building"></i> {{ $employee->department ?: 'â€”' }}
          </div>
        </div>
      </div>

      <div class="hidden h-14 w-px bg-white/10 sm:block"></div>

      <div>
        <div class="text-xs text-slate-400">Date of Joining</div>
        <div class="mt-1 text-sm font-semibold text-white">{{ $hireDate }}</div>
      </div>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="flex items-center gap-4 rounded-xl bg-[#0B1E3D] px-5 py-4 ring-1 ring-white/5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-sky-500/15">
          <i class="fa-regular fa-calendar-check text-sky-300"></i>
        </div>
        <div class="min-w-0">
          <div class="text-2xl font-semibold leading-tight text-white">{{ $stats['present'] }}</div>
          <div class="truncate text-xs text-slate-400">Present Days</div>
        </div>
      </div>

      <div class="flex items-center gap-4 rounded-xl bg-[#0B1E3D] px-5 py-4 ring-1 ring-white/5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-rose-500/15">
          <i class="fa-solid fa-clock-rotate-left text-rose-300"></i>
        </div>
        <div class="min-w-0">
          <div class="text-2xl font-semibold leading-tight text-white">{{ $stats['absent'] }}</div>
          <div class="truncate text-xs text-slate-400">Absent Days</div>
        </div>
      </div>

      <div class="flex items-center gap-4 rounded-xl bg-[#0B1E3D] px-5 py-4 ring-1 ring-white/5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-sky-500/15">
          <i class="fa-regular fa-calendar-xmark text-sky-300"></i>
        </div>
        <div class="min-w-0">
          <div class="text-2xl font-semibold leading-tight text-white">{{ $stats['leave'] }}</div>
          <div class="truncate text-xs text-slate-400">Leave Days</div>
        </div>
      </div>

      <div class="flex items-center gap-4 rounded-xl bg-[#0B1E3D] px-5 py-4 ring-1 ring-white/5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15">
          <i class="fa-regular fa-square-check text-emerald-300"></i>
        </div>
        <div class="min-w-0">
          <div class="text-2xl font-semibold leading-tight text-white">{{ $stats['total'] }}</div>
          <div class="truncate text-xs text-slate-400">Total Days</div>
        </div>
      </div>
    </div>

    <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
      <span class="text-sm text-slate-400">Total record: {{ $stats['total'] }}</span>
      @include('partials.per-page-filter', ['perPage' => $attendances->perPage()])
    </div>

    <div data-ajax-list-results class="transition-opacity duration-200">
      @include('reports-analytics.partials.employee-attendance-results')
    </div>

</div>

  {{-- Photo viewer modal --}}
  <div id="attendance-photo-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black/70 p-4" aria-hidden="true">
    <div class="relative w-full max-w-lg rounded-2xl bg-[#0B1E3D] p-4 shadow-2xl ring-1 ring-white/10">
      <div class="mb-3 flex items-center justify-between gap-3">
        <div id="attendance-photo-label" class="text-sm font-medium text-slate-200">Attendance Photo</div>
        <button
          type="button"
          id="attendance-photo-close"
          class="inline-flex h-9 items-center gap-2 rounded-lg bg-white/10 px-3 text-sm font-semibold text-white transition hover:bg-white/20"
        >
          <i class="fa-solid fa-xmark"></i>
          Exit
        </button>
      </div>
      <img id="attendance-photo-full" src="" alt="Attendance photo preview" class="max-h-[70vh] w-full rounded-xl object-contain bg-black/30">
    </div>
  </div>

  <script src="{{ asset('js/ajax-list.js') }}" defer></script>
  <script>
    (function () {
      const modal = document.getElementById('attendance-photo-modal');
      const fullImage = document.getElementById('attendance-photo-full');
      const label = document.getElementById('attendance-photo-label');
      const closeBtn = document.getElementById('attendance-photo-close');

      function openPhoto(src, title) {
        fullImage.src = src;
        label.textContent = title || 'Attendance Photo';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
      }

      function closePhoto() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        fullImage.src = '';
      }

      document.addEventListener('click', function (event) {
        const thumb = event.target.closest('.attendance-photo-thumb');
        if (thumb) {
          event.preventDefault();
          openPhoto(thumb.dataset.photoSrc, thumb.dataset.photoLabel);
          return;
        }

        if (event.target === modal) {
          closePhoto();
        }
      });

      closeBtn.addEventListener('click', closePhoto);

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
          closePhoto();
        }
      });
    })();
  </script>

</body>
</html>
