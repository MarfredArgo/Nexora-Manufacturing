@php
    $navLink = 'text-white no-underline text-xl py-2.5 px-[18px] flex items-center gap-2 rounded-full transition-all duration-250 hover:text-[#66A6FF] hover:bg-[#1B3A6B] hover:-translate-y-px hover:font-bold active:scale-[.97]';
    $navActive = 'text-[#66A6FF] bg-[#1B3A6B] font-bold';
    $dropLink = 'block no-underline text-[#C9DAF8] py-[11px] px-3.5 rounded-[10px] text-[13px] font-medium transition-all duration-200 hover:bg-[#f3f6fb] hover:text-[#2D7EFF]';
    $dropActive = 'bg-[#f3f6fb] text-[#2D7EFF]';

    $isDashboard = request()->routeIs('hr.dashboard');
    $isWorkforce = request()->routeIs('hr.employees.*') || request()->routeIs('hr.departments.*') || request()->routeIs('hr.drivers.*');
    $isEmployees = request()->routeIs('hr.employees.index') || request()->routeIs('hr.employees.show') || request()->routeIs('hr.employees.create');
    $isDepartments = request()->routeIs('hr.departments.*');
    $isDrivers = request()->routeIs('hr.drivers.*');
    $isOnboarding = request()->routeIs('hr.onboarding.*');
    $isReports = request()->routeIs('hr.reports-analytics.*');
    $isAttendance = request()->routeIs('hr.reports-analytics.attendance-overview') || request()->routeIs('hr.reports-analytics.employee-attendance');
    $isLeave = request()->routeIs('hr.reports-analytics.leave');
@endphp

<header class="w-full h-[150px] bg-[#132B52] flex items-center justify-between pl-[1px] pr-[5px] border-b border-white/5 shadow-[0_1px_0_rgba(255,255,255,.03)_inset] sticky top-0 z-[1000]">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" class="h-[86px] w-auto object-contain block" alt="Header Logo">
    </div>

    <div class="flex items-center gap-7">
        <nav class="flex items-center gap-px">
            <div class="relative group">
                <a href="{{ route('hr.dashboard') }}"
                   class="{{ $navLink }} {{ $isDashboard ? $navActive : '' }}">
                    Dashboard
                </a>
            </div>

            <div class="relative group">
                <a href="{{ route('hr.employees.index') }}"
                   class="{{ $navLink }} {{ $isWorkforce ? $navActive : '' }}">
                    Workforce
                    <svg class="w-3.5 h-3.5 opacity-80 transition-transform duration-300 origin-center group-hover:rotate-180 group-hover:opacity-100" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="absolute top-[120%] left-1/2 -translate-x-1/2 translate-y-2.5 w-[220px] bg-[#132B52] rounded-[18px] shadow-[0_20px_45px_rgba(0,0,0,.25),inset_0_1px_0_rgba(21,21,21,.7)] p-2.5 opacity-0 invisible transition-all duration-300 z-[999] group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">
                    <a href="{{ route('hr.employees.index') }}"
                       class="{{ $dropLink }} {{ $isEmployees ? $dropActive : '' }}">Employee List</a>
                    <a href="{{ route('hr.departments.index') }}"
                       class="{{ $dropLink }} {{ $isDepartments ? $dropActive : '' }}">Department List</a>
                    <a href="{{ route('hr.drivers.index') }}"
                       class="{{ $dropLink }} {{ $isDrivers ? $dropActive : '' }}">Delivery Drivers</a>
                </div>
            </div>

            <div class="relative group">
                <a href="{{ route('hr.onboarding.step1') }}"
                   class="{{ $navLink }} {{ $isOnboarding ? $navActive : '' }}">
                    Employee Onboarding
                </a>
            </div>

            <div class="relative group">
                <a href="{{ route('hr.reports-analytics.attendance-overview') }}"
                   class="{{ $navLink }} {{ $isReports ? $navActive : '' }}">
                    Reports and Analytics
                    <svg class="w-3.5 h-3.5 opacity-80 transition-transform duration-300 origin-center group-hover:rotate-180 group-hover:opacity-100" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="absolute top-[120%] left-1/2 -translate-x-1/2 translate-y-2.5 w-[220px] bg-[#132B52] rounded-[18px] shadow-[0_20px_45px_rgba(0,0,0,.25),inset_0_1px_0_rgba(21,21,21,.7)] p-2.5 opacity-0 invisible transition-all duration-300 z-[999] group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">
                    <a href="{{ route('hr.reports-analytics.attendance-overview') }}"
                       class="{{ $dropLink }} {{ $isAttendance ? $dropActive : '' }}">Attendance Record</a>
                    <a href="{{ route('hr.reports-analytics.leave') }}"
                       class="{{ $dropLink }} {{ $isLeave ? $dropActive : '' }}">Leave Record</a>
                </div>
            </div>

            <div class="relative group">
                <a href="{{ route('hr.employee.dashboard') }}"
                   class="{{ $navLink }}">
                    Employee Management
                    <svg class="w-3.5 h-3.5 opacity-80 transition-transform duration-300 origin-center group-hover:rotate-180 group-hover:opacity-100" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="absolute top-[120%] left-1/2 -translate-x-1/2 translate-y-2.5 w-[220px] bg-[#132B52] rounded-[18px] shadow-[0_20px_45px_rgba(0,0,0,.25),inset_0_1px_0_rgba(21,21,21,.7)] p-2.5 opacity-0 invisible transition-all duration-300 z-[999] group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">
                    <a href="{{ route('hr.reports-analytics.leave') }}" class="{{ $dropLink }}">Leave Record</a>
                    <span class="block text-[#C9DAF8]/60 py-[11px] px-3.5 text-[13px] font-medium cursor-not-allowed" title="This HR feature has not been implemented yet">Resignation Management</span>
                </div>
            </div>
        </nav>

        <div class="relative group mr-[15px]">
            <div class="w-11 h-11 rounded-full grid place-items-center bg-white/[.06] shadow-[inset_0_0_0_1px_rgba(255,255,255,.06)] cursor-pointer" aria-label="Profile">
                <svg class="w-10 h-10" viewBox="0 0 36 36" fill="none">
                    <circle cx="18" cy="18" r="17" fill="white" opacity=".97"/>
                    <circle cx="18" cy="13" r="5.2" fill="#223B63"/>
                    <path d="M8.8 28.3C10.7 23.8 14.1 21.7 18 21.7C21.9 21.7 25.3 23.8 27.2 28.3" fill="#223B63"/>
                </svg>
            </div>

            <div class="absolute top-[120%] right-0 left-auto translate-y-2.5 w-[200px] bg-[#132B52] rounded-2xl shadow-[0_20px_45px_rgba(0,0,0,.25),inset_0_1px_0_rgba(21,21,21,.7)] p-2 opacity-0 invisible transition-all duration-300 z-[999] group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">
                <a href="{{ route('hr.employee.dashboard') }}"
                   class="flex items-center gap-2 no-underline text-[#C9DAF8] py-2.5 px-3 rounded-[10px] text-[13px] font-semibold transition-all duration-200 hover:bg-[#f3f6fb] hover:text-[#2D7EFF]">
                    <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none">
                        <path d="M3 12l9-8 9 8M5 10.5V20h5v-5h4v5h5v-9.5"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Employee Dashboard
                </a>
                <form method="POST" action="{{ route('hr.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left bg-none border-none cursor-pointer">
                        <span class="flex items-center gap-2 no-underline text-[#FFB4B4] py-2.5 px-3 rounded-[10px] text-[13px] font-semibold transition-all duration-200 hover:bg-[#2c1414] hover:text-[#ff6b6b]">
                            <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none">
                                <path d="M15 17l5-5-5-5M20 12H9M13 5H7a2 2 0 00-2 2v10a2 2 0 002 2h6"
                                      stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Logout
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
