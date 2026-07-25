<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap">

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
    </style>
</head>

<body class="font-sans bg-[#18386d] text-white m-0 p-0">

    <!-- =====================================================
            TOP NAVBAR
        ====================================================== -->
    @include('partials.navbar')

    <div class="w-[96.82%] max-w-[1859px] mx-auto" data-ajax-list>

        <div class="flex justify-between items-center gap-5 py-2.5 flex-wrap">
        <form method="GET" action="{{ route('hr.departments.show', request()->route('slug')) }}" class="flex justify-start items-center gap-5 flex-wrap">
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

            <div class="relative w-[180px] flex-none">
                <select
                    name="sort"
                    class="filter-select w-[180px] h-[45px] bg-[#0B1E3D] opacity-70 text-[#93abd3] border-none outline-none rounded-lg pl-3.5 pr-8 text-[0.6875rem] cursor-pointer">

                    <option value="">Default</option>

                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                        Name (A-Z)
                    </option>

                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                        Name (Z-A)
                    </option>

                    <option value="id_asc" {{ request('sort') == 'id_asc' ? 'selected' : '' }}>
                        Employee ID (Ascending)
                    </option>

                    <option value="id_desc" {{ request('sort') == 'id_desc' ? 'selected' : '' }}>
                        Employee ID (Descending)
                    </option>

                    <option value="position_asc" {{ request('sort') == 'position_asc' ? 'selected' : '' }}>
                        Position (A-Z)
                    </option>

                    <option value="position_desc" {{ request('sort') == 'position_desc' ? 'selected' : '' }}>
                        Position (Z-A)
                    </option>

                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                        Newest Employee
                    </option>

                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                        Oldest Employee
                    </option>

                </select>
            </div>

        </form>

        @include('partials.per-page-filter', ['perPage' => $departments->perPage()])
        </div>

        <!-- WELCOME SECTION -->
        <div class="mt-2.5 mb-5 py-2.5 px-1">
            <h1 class="text-[22px] font-bold tracking-[2px] text-white mb-1.5">{{ strtoupper($departmentName) }}</h1>
          
        </div>

        <!-- =========================
            TABLE
        ========================== -->

        <!-- Header -->
        <div class="w-full h-[47px] mx-auto mb-3 grid grid-cols-[21.5%_21.5%_21.5%_21.5%_15%] bg-[#0B1E3D] border border-white/[0.15] rounded-[10px] overflow-hidden">

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Employee ID</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Name</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Position</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Status</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white">Action</div>

        </div>

        <div data-ajax-list-results class="transition-opacity duration-200">
            @include('departments.partials.show-results')
        </div>

</div>

    <script src="{{ asset('js/ajax-list.js') }}" defer></script>

</body>

</html>
