<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Record</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Google Font: Inter -->
    
    <style type="text/css">@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin-ext/opsz/normal.woff2);unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic/opsz/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek-ext/opsz/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/vietnamese/opsz/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek/opsz/normal.woff2);unicode-range:U+0370-0377,U+037A-037F,U+0384-038A,U+038C,U+038E-03A1,U+03A3-03FF;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin/opsz/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic-ext/opsz/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin-ext/opsz/italic.woff2);unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek-ext/opsz/italic.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic-ext/opsz/italic.woff2);unicode-range:U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin/opsz/italic.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/vietnamese/opsz/italic.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek/opsz/italic.woff2);unicode-range:U+0370-0377,U+037A-037F,U+0384-038A,U+038C,U+038E-03A1,U+03A3-03FF;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic/opsz/italic.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}</style>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <!-- The handful of things Tailwind utilities genuinely can't express
         (webkit autofill pseudo-state, custom select caret) stay as raw CSS -->
    <style>
        .search-box input:-webkit-autofill,
        .search-box input:-webkit-autofill:hover,
        .search-box input:-webkit-autofill:focus,
        .search-box input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #0B1E3D inset !important;
            -webkit-text-fill-color: #fff !important;
            transition: background-color 9999s ease-in-out 0s;
            color: #fff !important;
            font-size: 11px !important;
        }

        .filter-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M3.204 5h9.592L8 10.481 3.204 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 500;
            background: rgba(255,255,255,.06);
            color: #93abd3;
        }

        /* Pagination links rendered by Laravel's paginator (Tailwind view) */
        .pagination-wrap nav > div:first-child {
            display: none;
        }
    </style>
</head>

<body class="font-sans bg-[#18386d] text-white m-0 p-0">

    <!-- =====================================================
            TOP NAVBAR
        ====================================================== -->
    @include('partials.navbar')

    <div class="w-[96.82%] max-w-[1859px] mx-auto" data-ajax-list>
        
     <div class="w-full min-h-[60px] bg-[none] rounded-[14px] px-0 py-5 mb-4 flex items-center justify-between gap-4 flex-wrap">

    <form method="GET" action="{{ route('hr.reports-analytics.leave') }}" class="flex items-center gap-3 flex-wrap" id="filterForm">
        @if (request()->filled('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
        <div class="search-box w-[487px] h-[45px] bg-[#0B1E3D] rounded-lg flex items-center px-3 opacity-70">
                <i class="fa-solid fa-magnifying-glass text-[#9db5db] mr-2 text-[0.6875rem]"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search employees by ID or name"
                    class="w-full h-full bg-transparent border-none outline-none text-white text-[0.6875rem] placeholder:text-[#93abd3]"
                    autocomplete="off">
            </div>

            <div class="relative w-[220px] flex-none">
                <select
                    name="department"
                    class="filter-select w-[220px] h-[45px] bg-[#0B1E3D] opacity-70 text-[#93abd3] border-none outline-none rounded-lg pl-3.5 pr-8 text-[0.6875rem] cursor-pointer">

                    <option value="">All Departments</option>
                    <option value="Business Intelligence" {{ request('department') == 'Business Intelligence' ? 'selected' : '' }}>Business Intelligence</option>
                    <option value="E-commerce" {{ request('department') == 'E-commerce' ? 'selected' : '' }}>E-commerce</option>
                    <option value="Finance" {{ request('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
                    <option value="Human Resources" {{ request('department') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                    <option value="IT Service Management" {{ request('department') == 'IT Service Management' ? 'selected' : '' }}>IT Service Management</option>
                    <option value="Inventory Management" {{ request('department') == 'Inventory Management' ? 'selected' : '' }}>Inventory Management</option>
                    <option value="Order Management" {{ request('department') == 'Order Management' ? 'selected' : '' }}>Order Management</option>
                    <option value="Procurement Management" {{ request('department') == 'Procurement Management' ? 'selected' : '' }}>Procurement Management</option>
                    <option value="Production Management" {{ request('department') == 'Production Management' ? 'selected' : '' }}>Production Management</option>
                </select>
            </div>

     
    </form>

        @include('partials.per-page-filter', ['perPage' => $employees->perPage()])
</div>


        <!-- Leave stats -->
         <div class="grid grid-cols-4 gap-4 mt-4 mb-4">

        <div class="mt-4 mb-1 w-[432px] h-[142px] bg-[#0B1E3D] rounded-[20px] border border-white/[0.05] px-4 flex items-center gap-3">
            <div class="w-[39px] h-[39px] rounded-xl bg-white/[.05] flex items-center justify-center flex-none">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12L11 14L15 10" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 4H14L18 8V19C18 20.1046 17.1046 21 16 21H7C5.89543 21 5 20.1046 5 19V6C5 4.89543 5.89543 4 7 4Z" stroke="#DCEBFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div class="text-[11.9px] text-[#E7F0FF]">Total Reviewed</div>
                <div class="text-[22.2px] font-bold leading-none mt-0.5 employee-counter" data-target="{{ $totalReviewed ?? 0 }}">0</div>
            </div>
        </div>

        <div class="mt-4 mb-1 w-[432px] h-[142px] bg-[#0B1E3D] rounded-[20px] border border-white/[0.05] px-4 flex items-center gap-3">
            <div class="w-[39px] h-[39px] rounded-xl bg-green-500/20 flex items-center justify-center flex-none">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" stroke="#16A34A" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 12.5L10.5 15L16 9" stroke="#16A34A" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div class="text-[11.9px] text-[#E7F0FF]">Approved</div>
                <div class="text-[22.2px] font-bold leading-none mt-0.5 employee-counter" data-target="{{ $approvedCount ?? 0 }}">0</div>
            </div>
        </div>

        <div class="mt-4 mb-1 w-[432px] h-[142px] bg-[#0B1E3D] rounded-[20px] border border-white/[0.05] px-4 flex items-center gap-3">
            <div class="w-[39px] h-[39px] rounded-xl bg-[#DC2626]/20 flex items-center justify-center flex-none">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" stroke="#DC2626" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.5 9.5L14.5 14.5M14.5 9.5L9.5 14.5" stroke="#DC2626" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div class="text-[11.9px] text-[#E7F0FF]">Rejected</div>
                <div class="text-[22.2px] font-bold leading-none mt-0.5 employee-counter" data-target="{{ $rejectedCount ?? 0 }}">0</div>
            </div>
        </div>

        <div class="mt-4 mb-1 w-[432px] h-[142px] bg-[#0B1E3D] rounded-[20px] border border-white/[0.05] px-4 flex items-center gap-3">
            <div class="w-[39px] h-[39px] rounded-xl bg-[#0EA5E9]/20 flex items-center justify-center flex-none">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                    <path d="M3 10H21M7 3V5M17 3V5M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#0EA5E9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div class="text-[11.9px] text-[#E7F0FF]">Total Reports this Month</div>
                <div class="text-[22.2px] font-bold leading-none mt-0.5 employee-counter" data-target="{{ $reportsThisMonth ?? 0 }}">0</div>
            </div>
        </div>

        </div>

        
        

        

        <!-- =========================
            TABLE
        ========================== -->

       <!-- Header -->
<div class="w-full h-[47px] mx-auto mb-3 grid grid-cols-[16%_11%_11%_10%_14%_10%_11%_9%_8%] bg-[#0B1E3D] border border-white/[0.15] rounded-[10px] overflow-hidden">

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Employee</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Department</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Leave Type</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Submitted</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Leave Dates</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Leave ID</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Reviewed By</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Status</div>

    <div class="px-[10px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white">Report</div>

</div>

        <div data-ajax-list-results class="transition-opacity duration-200">
            @include('reports-analytics.partials.leave-results')
        </div>

</div>

    <script>
        const employeeCounters = document.querySelectorAll('.employee-counter');

        function animateEmployeeCounter(el) {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duration = 1450;
            const start = performance.now();

            function update(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(target * eased);
                el.textContent = current.toLocaleString();
                if (progress < 1) requestAnimationFrame(update);
            }

            requestAnimationFrame(update);
        }

        employeeCounters.forEach((counter) => animateEmployeeCounter(counter));
    </script>

    <script src="{{ asset('js/ajax-list.js') }}" defer></script>

</body>

</html>