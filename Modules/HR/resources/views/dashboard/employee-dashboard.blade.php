<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Dashboard</title>
  <link rel="icon" href="{{ asset('images/Nexora_Logo_Transparent(1).png') }}" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background:
        radial-gradient(circle at 15% 15%, rgba(61,132,255,.08), transparent 24%),
        radial-gradient(circle at 85% 8%, rgba(61,132,255,.06), transparent 18%),
        linear-gradient(180deg, #183667 0%, #132C5B 100%);
    }
  </style>
</head>
<body class="min-h-screen bg-[#132C5B] text-white font-sans">
  <div class="max-w-6xl mx-auto px-4 py-8">
    <header class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Employee Dashboard</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-bold">Welcome back, {{ session('employee_name') }}</h1>
        <p class="mt-3 text-slate-300 max-w-2xl">
          @if(!empty($isHr))
            You are viewing the employee dashboard. Use the button below to return to the HR dashboard anytime.
          @else
            This is your secured employee dashboard. You can only stay here and log out from this area.
          @endif
        </p>
      </div>
      <div class="flex flex-col gap-3 sm:items-end">
        @if(!empty($isHr))
          <a href="{{ route('hr.dashboard') }}"
             class="rounded-2xl bg-[#2D7EFF] px-5 py-3 text-white font-semibold transition hover:bg-[#4D95FF] no-underline text-center">
            Back to HR Dashboard
          </a>
        @endif
        <div class="rounded-3xl bg-[#0B1E3D] border border-white/10 px-5 py-4 text-right">
          <p class="text-sm text-slate-400">Department</p>
          <p class="mt-2 text-xl font-semibold text-white">{{ session('employee_department') }}</p>
        </div>
      </div>
    </header>

    <section class="grid gap-6 md:grid-cols-2">
      <article class="rounded-3xl border border-white/10 bg-[#10233D] p-6 shadow-[0_20px_60px_rgba(0,0,0,.18)]">
        <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Your headcount view</p>
        <div class="mt-6 flex items-end justify-between gap-4">
          <div>
            <p class="text-5xl font-bold">{{ $employeeCount }}</p>
            <p class="mt-2 text-sm text-slate-300">Registered employees in the system</p>
          </div>
          <div class="rounded-3xl bg-[#1B3A6B] px-4 py-3 text-sm text-slate-200">Employee-only view</div>
        </div>
      </article>

      <article class="rounded-3xl border border-white/10 bg-[#10233D] p-6 shadow-[0_20px_60px_rgba(0,0,0,.18)]">
        <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Navigation</p>
        <div class="mt-6 space-y-4">
          <div class="rounded-3xl bg-[#132B52] p-4 text-slate-200">
            <p class="font-semibold">You may use this page only.</p>
            <p class="mt-2 text-sm text-slate-400">Other pages are restricted and will redirect back here.</p>
          </div>
          <div class="rounded-3xl bg-[#132B52] p-4 text-slate-200">
            <p class="font-semibold">Sign out safely</p>
            <form method="POST" action="{{ route('hr.logout') }}" class="mt-3">
              @csrf
              <button type="submit" class="rounded-2xl bg-[#2D7EFF] px-5 py-3 text-white font-semibold transition hover:bg-[#4D95FF]">
                Logout
              </button>
            </form>
          </div>
        </div>
      </article>
    </section>
  </div>
</body>
</html>
