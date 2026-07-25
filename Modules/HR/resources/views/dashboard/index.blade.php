<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="icon" href="{{ asset('images/Nexora_Logo_Transparent(1).png') }}" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            navy: {
              bg: '#132C5B',
              deep: '#0B1E3D',
              panel: '#10233d',
              card: '#173767',
              top: '#132B52',
            },
            accent: {
              DEFAULT: '#2D7EFF',
              light: '#3F8CFF',
              soft: '#66A6FF',
            },
          },
          keyframes: {
            pageFade: {
              from: { opacity: 0, transform: 'translateY(8px)' },
              to:   { opacity: 1, transform: 'translateY(0)' },
            },
            cardIn: {
              from: { opacity: 0, transform: 'translateY(16px)' },
              to:   { opacity: 1, transform: 'translateY(0)' },
            },
            heroFloat: {
              '0%,100%': { transform: 'translateY(-50%) rotate(0deg)' },
              '50%':     { transform: 'translateY(calc(-50% - 8px)) rotate(3deg)' },
            },
            growBar: {
              from: { transform: 'scaleY(0)' },
              to:   { transform: 'scaleY(1)' },
            },
          },
          animation: {
            pageFade: 'pageFade .9s ease forwards',
            cardIn: 'cardIn .7s cubic-bezier(.2,.8,.2,1) forwards',
            heroFloat: 'heroFloat 8s ease-in-out infinite',
            growBar: 'growBar .9s cubic-bezier(.2,.8,.2,1) forwards',
          },
        },
      },
    };
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    body {
      background:
        radial-gradient(circle at 15% 15%, rgba(61,132,255,.08), transparent 24%),
        radial-gradient(circle at 85% 8%, rgba(61,132,255,.06), transparent 18%),
        linear-gradient(180deg, #183667 0%, #132C5B 100%);
    }
    /* Grid lines behind the attendance bars â€” easiest kept as a small utility, Tailwind has no clean bg-stripe primitive */
    .att-grid-lines {
      background: linear-gradient(to top,
        transparent 0 19%, rgba(255,255,255,.04) 19% 20%,
        transparent 20% 39%, rgba(255,255,255,.04) 39% 40%,
        transparent 40% 59%, rgba(255,255,255,.04) 59% 60%,
        transparent 60% 79%, rgba(255,255,255,.04) 79% 80%,
        transparent 80% 100%);
    }
    .tilt { transform-style: preserve-3d; }
  </style>
</head>
<body class="w-full min-h-screen bg-navy-bg text-white font-sans opacity-0 animate-pageFade">

  @include('partials.navbar')


  <!-- =====================================================
       MAIN CONTENT
  ====================================================== -->
  <main class="w-full px-3 md:px-5 pb-5 pt-3">
    <div class="flex flex-col gap-1.5 max-w-[1820px] mx-auto">

      <!-- Welcome card -->
      <!-- FIX: the old markup hard-coded width:1818px on the inner flex box, which overflowed the
           rounded, overflow-hidden card and made the right edge render with square corners.
           Using w-full here lets the card's own `rounded-3xl overflow-hidden` do the rounding. -->
      <article class="tilt opacity-0 animate-cardIn rounded-3xl overflow-hidden bg-navy-deep">
        <div class="w-full h-[142px] flex items-stretch justify-between gap-5 px-0 py-px relative">
          <div class="flex flex-col justify-start pt-2 pl-6">
            <div class="text-[11.9px] font-medium tracking-wide text-[#F4F8FF] uppercase">
              HR OPERATIONS <span class="text-red-500 ml-1">â€¢ LIVE</span>
            </div>
            <h1 class="text-white text-2xl md:text-3xl font-bold mt-0.5">
              Welcome back, {{ session('employee_name') }}
            </h1>
            <div class="mt-3.5 text-[11.9px] italic leading-snug text-[#90A7CC] max-w-[400px]">
              Here's your organizational workforce snapshot: track headcount, attendance and hiring at a glance.
            </div>
          </div>

          <div class="flex-1 relative flex justify-end items-center overflow-hidden">
            <div class="absolute right-[70px] top-1/2 -translate-y-1/2 w-[180px] h-[180px] md:w-[250px] md:h-[250px]
                        opacity-70 pointer-events-none select-none animate-heroFloat
                        [filter:drop-shadow(0_0_20px_rgba(45,126,255,.20))_drop-shadow(0_0_40px_rgba(45,126,255,.12))]">
              <img src="{{ asset('images/Nexora_Logo_Transparent(1).png') }}" alt="Hero Logo" class="w-full h-full object-contain">
            </div>
          </div>
        </div>
      </article>

      <!-- Stats -->
      <article class="tilt opacity-0 animate-cardIn [animation-delay:.15s] overflow-x-auto">
        <div class="flex flex-row gap-[29px] pt-[5.5px] pr-[5px] pb-[5px] pl-[8px] w-[1818px] max-w-none">

          <!-- Total Employees -->
          <div class="w-[584px] shrink-0 h-[70px] rounded-[20px] bg-navy-deep border border-white/[.05] px-4 py-1.5 flex items-start justify-between">
            <div class="flex items-start gap-3">
              <div class="w-[39px] h-[39px] mt-0.5 rounded-xl grid place-items-center bg-white/[.05] shrink-0">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5">
                  <circle cx="9" cy="10" r="3" stroke="#DCEBFF" stroke-width="1.8"/>
                  <circle cx="16.3" cy="11.2" r="2.4" stroke="#DCEBFF" stroke-width="1.8"/>
                  <path d="M4.8 18.4C6 15.8 7.9 14.7 10.1 14.7C12.3 14.7 14.1 15.8 15.3 18.4" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round"/>
                  <path d="M15.4 18.2C16 16.8 17.2 16.1 18.4 16.1C19.5 16.1 20.4 16.5 21 17.4" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
              </div>
              <div>
                <div class="text-[11.9px] text-[#E7F0FF] mt-px">Total Employees</div>
                <div class="flex items-end gap-2 mt-0">
                  <div class="counter text-[22.2px] font-bold leading-none tracking-tight" data-target="{{ $employeeCount }}">0</div>
                  <div class="text-[8.7px] text-[#93A9CC] -mt-px">vs. last month</div>
                </div>
              </div>
            </div>
            <div class="h-[18px] px-2.5 rounded-full inline-flex items-center justify-center text-[7px] font-extrabold bg-[#350808] text-red-500 shadow-[inset_0_1px_0_rgba(255,255,255,.06)]">-4.5%</div>
          </div>

          <!-- Present Today -->
          <div class="w-[583px] shrink-0 h-[70px] rounded-[20px] bg-navy-deep border border-white/[.05] px-4 py-1.5 flex items-start justify-between">
            <div class="flex items-start gap-3">
              <div class="w-[39px] h-[39px] mt-0.5 rounded-xl grid place-items-center bg-white/[.05] shrink-0">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5">
                  <circle cx="12" cy="12" r="7" stroke="#DCEBFF" stroke-width="1.8"/>
                  <path d="M12 8V12L14.8 14.2" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div>
                <div class="text-[11.9px] text-[#E7F0Ff] mt-px">Present Today</div>
                <div class="flex items-end gap-2 mt-0">
                  <div class="counter text-[22.2px] font-bold leading-none tracking-tight" data-target="{{ $presentToday }}">0</div>
                  <div class="text-[8.7px] text-[#93A9CC] -mt-px">98.6% of workforce</div>
                </div>
              </div>
            </div>
            <div class="h-[18px] px-2.5 rounded-full inline-flex items-center justify-center text-[7px] font-extrabold bg-[#084B20] text-[#4EDB7F] shadow-[inset_0_1px_0_rgba(255,255,255,.06)]">+2.3%</div>
          </div>

          <!-- On Leave -->
          <div class="w-[585px] shrink-0 h-[70px] rounded-[20px] bg-navy-deep border border-white/[.05] px-4 py-1.5 flex items-start justify-between">
            <div class="flex items-start gap-3">
              <div class="w-[39px] h-[39px] mt-0.5 rounded-xl grid place-items-center bg-white/[.05] shrink-0">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5">
                  <rect x="5" y="6" width="14" height="13" rx="2" stroke="#DCEBFF" stroke-width="1.8"/>
                  <path d="M8 4V8" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round"/>
                  <path d="M16 4V8" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
              </div>
              <div>
                <div class="text-[11.9px] text-[#E7F0FF] mt-px">On Leave</div>
                <div class="flex items-end gap-2 mt-0">
                  <div class="counter text-[22.2px] font-bold leading-none tracking-tight" data-target="3">0</div>
                  <div class="text-[8.7px] text-[#93A9CC] -mt-px">Today, across 3 depts.</div>
                </div>
              </div>
            </div>
            <div class="h-[18px] px-2.5 rounded-full inline-flex items-center justify-center text-[7px] font-extrabold bg-[#350808] text-red-500 shadow-[inset_0_1px_0_rgba(255,255,255,.06)]">-6.1%</div>
          </div>
        </div>
      </article>

      <!-- Trend + Attendance -->
      <section class="grid grid-cols-1 xl:grid-cols-[66.6%_33%] gap-3 items-stretch h-[580px]">

        <!-- Workforce Trend -->
        <div class="flex flex-col bg-[rgba(16,35,69,.98)] border border-[rgba(104,147,219,.08)] rounded-[20px] px-5 py-3 overflow-hidden">
          <div class="flex justify-between items-start gap-5">
            <div>
              <h2 class="text-lg font-semibold text-white flex items-center gap-2.5">
                Workforce Trend
                <span class="bg-[#0d5d28] text-[#3cff82] px-2.5 py-1 rounded-full text-[10px] font-semibold">+6.7%</span>
              </h2>
              <p class="mt-1.5 text-[11px] text-[rgba(219,232,255,.45)]">Hires vs. Exits across the year</p>
            </div>
            <div class="border border-white/10 bg-[#17345f] rounded-full px-4 py-2.5 text-[10px] text-white/80 whitespace-nowrap">
              Jan - Dec 2026
            </div>
          </div>

          <div class="w-full flex-1 overflow-x-auto -mt-4 pb-4">
            <svg viewBox="0 0 760 360" class="w-full min-w-[760px] h-[380px] block">
              <!-- Y LABELS -->
              <text x="-45" y="40" class="fill-[rgba(219,232,255,.7)] text-[11px]">50</text>
              <text x="-45" y="72" class="fill-[rgba(219,232,255,.7)] text-[11px]">45</text>
              <text x="-45" y="104" class="fill-[rgba(219,232,255,.7)] text-[11px]">40</text>
              <text x="-45" y="136" class="fill-[rgba(219,232,255,.7)] text-[11px]">35</text>
              <text x="-45" y="168" class="fill-[rgba(219,232,255,.7)] text-[11px]">30</text>
              <text x="-45" y="200" class="fill-[rgba(219,232,255,.7)] text-[11px]">25</text>
              <text x="-45" y="232" class="fill-[rgba(219,232,255,.7)] text-[11px]">20</text>
              <text x="-45" y="264" class="fill-[rgba(219,232,255,.7)] text-[11px]">15</text>
              <text x="-45" y="296" class="fill-[rgba(219,232,255,.7)] text-[11px]">10</text>
              <text x="-45" y="328" class="fill-[rgba(219,232,255,.7)] text-[11px]">5</text>

              <!-- GRID -->
              <g stroke="rgba(196,214,255,.18)" stroke-width="1">
                <line x1="2" y1="40" x2="900" y2="40"/>
                <line x1="2" y1="72" x2="900" y2="72"/>
                <line x1="2" y1="104" x2="900" y2="104"/>
                <line x1="2" y1="136" x2="900" y2="136"/>
                <line x1="2" y1="168" x2="900" y2="168"/>
                <line x1="2" y1="200" x2="900" y2="200"/>
                <line x1="2" y1="232" x2="900" y2="232"/>
                <line x1="2" y1="264" x2="900" y2="264"/>
                <line x1="2" y1="296" x2="900" y2="296"/>
                <line x1="2" y1="328" x2="900" y2="328"/>
              </g>

              <!-- X LABELS -->
              <text x="70" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">JAN</text>
              <text x="190" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">FEB</text>
              <text x="312" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">MAR</text>
              <text x="435" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">APR</text>
              <text x="560" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">MAY</text>
              <text x="682" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">JUN</text>
              <text x="805" y="350" class="fill-[rgba(219,232,255,.6)] text-[11px]">JUL</text>

              <!-- BLUE -->
              <path fill="none" stroke="#1f7ff6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                    class="[filter:drop-shadow(0_0_4px_rgba(31,127,246,.2))]"
                    d="M70 200 L95 180 L120 160 L145 140 L170 122 L195 110 L220 102 L245 95 L270 95 L295 102 L320 112 L345 126 L370 160 L395 190 L420 220 L445 240 L470 248 L495 248 L520 230 L545 195 L570 150 L595 125 L620 125 L645 125 L670 105 L695 98 L720 80 L742 50"/>

              <!-- RED -->
              <path fill="none" stroke="#ea170c" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                    class="[filter:drop-shadow(0_0_3px_rgba(234,23,12,.15))]"
                    d="M70 285 L88 265 L106 285 L124 295 L142 300 L160 295 L178 280 L196 290 L214 290 L232 275 L250 235 L268 235 L286 250 L304 280 L322 305 L340 310 L358 320 L376 320 L394 305 L412 280 L430 245 L448 225 L466 225 L484 225 L502 210 L520 190 L538 190 L556 200 L574 210 L592 225 L610 285 L628 285 L646 285 L664 270 L682 280 L700 250 L718 230 L742 220"/>
            </svg>
          </div>
        </div>

        <!-- Attendance Rate -->
        <div class="w-full flex flex-col bg-[rgba(16,35,69,.98)] border border-[rgba(104,147,219,.08)] rounded-3xl p-4 shadow-[inset_0_1px_0_rgba(255,255,255,.03)]">
          <div class="flex justify-between items-start mb-6">
            <div>
              <h3 class="text-lg font-semibold text-white">Attendance Rate</h3>
              <p class="mt-1 text-xs text-[rgba(219,232,255,.45)]">Company-wide average Â· {{ number_format($totalPresentDaysYear ?? 0) }} present days YTD</p>
            </div>
            <div class="text-right">
              <div class="text-[34px] font-bold text-white leading-none">{{ number_format($overallAttendanceRate ?? 0, 2) }}%</div>
              @php $change = $rateChange ?? 0; @endphp
              <div class="inline-flex justify-center items-center mt-2.5 px-3 h-6 rounded-full {{ $change >= 0 ? 'bg-[#0b6328] text-[#35ff7a]' : 'bg-[#5c1a1a] text-[#ff7a7a]' }} text-[10px] font-semibold">
                {{ $change >= 0 ? 'â†‘' : 'â†“' }} {{ number_format(abs($change), 1) }}%
              </div>
            </div>
          </div>

          <div class="relative flex-1 min-h-[300px] pb-4">
            <div class="absolute left-0 top-0 bottom-8 w-8 flex flex-col justify-between text-[13px] text-[rgba(219,232,255,.45)]">
              <span>100%</span>
              <span>80%</span>
              <span>60%</span>
              <span>40%</span>
              <span>20%</span>
            </div>

            <div class="absolute left-10 right-0 top-0 bottom-8 overflow-x-auto overflow-y-hidden" style="scrollbar-width:thin;scrollbar-color:rgba(99,148,220,.55) rgba(255,255,255,.06);">
              <div class="relative h-full" style="min-width: {{ max(count($monthlyAttendance ?? []) * 72, 520) }}px;">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                  <div class="border-t border-white/[.08]"></div>
                  <div class="border-t border-white/[.08]"></div>
                  <div class="border-t border-white/[.08]"></div>
                  <div class="border-t border-white/[.08]"></div>
                  <div class="border-t border-white/[.08]"></div>
                </div>

                <div class="absolute inset-0 flex justify-around items-end gap-2 px-1">
                  @foreach($monthlyAttendance ?? [] as $index => $monthData)
                    @php
                      $barHeight = max(2, min(100, (float) $monthData['rate']));
                      $delay = 0.15 + ($index * 0.08);
                      $isCurrent = ($monthData['month_number'] ?? null) === ($currentMonth ?? null);
                    @endphp
                    <div class="flex flex-col items-center h-full justify-end shrink-0 w-12" title="{{ $monthData['month_name'] }}: {{ $monthData['present_days'] }} present days ({{ $monthData['rate'] }}%)">
                      <div class="w-6 rounded-full {{ $isCurrent ? 'bg-[#3F8CFF]' : 'bg-[#1B6FC8]' }} animate-growBar origin-bottom" style="height:{{ $barHeight }}%; animation-delay:{{ $delay }}s;"></div>
                      <span class="mt-3 text-xs {{ $isCurrent ? 'text-white font-semibold' : 'text-[rgba(219,232,255,.6)]' }}">{{ strtoupper($monthData['month']) }}</span>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <script>
    /* COUNTER ANIMATION */
    document.querySelectorAll('.counter').forEach((counter, index) => {
      setTimeout(() => animateCounter(counter), 320 + index * 110);
    });
    function animateCounter(el){
      const target = parseInt(el.dataset.target, 10);
      const duration = 1450;
      const start = performance.now();
      function update(now){
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(target * eased).toLocaleString();
        if (progress < 1) requestAnimationFrame(update);
      }
      requestAnimationFrame(update);
    }

    /* SUBTLE CARD TILT */
    document.querySelectorAll('.tilt').forEach(card => {
      let raf = null;
      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        const rotateY = (px - 0.5) * 4.6;
        const rotateX = (0.5 - py) * 4.2;
        if (raf) cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          card.style.transform = `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
      });
      card.addEventListener('mouseleave', () => {
        if (raf) cancelAnimationFrame(raf);
        card.style.transform = 'perspective(900px) rotateX(0deg) rotateY(0deg)';
      });
    });
  </script>
</body>
</html>