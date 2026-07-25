<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>

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

    window.addEventListener('load', () => {
  const loader = document.getElementById('page-loader');
  setTimeout(() => {
    loader.classList.add('hide');
  }, 400); // small delay so it doesn't flash too fast
});
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

        .loader {
  --color-1: #2D7EFF;
  --size: 1px;
  width: calc(48 * var(--size));
  height: calc(48 * var(--size));
  border-radius: 50%;
  display: inline-block;
  border-top: calc(3 * var(--size)) solid var(--color-1);
  border-right: calc(3 * var(--size)) solid transparent;
  box-sizing: border-box;
  animation: rotation 1s linear infinite;
}
@keyframes rotation {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
#page-loader {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #132C5B;
  transition: opacity .4s ease, visibility .4s ease;
}
#page-loader.hide {
  opacity: 0;
  visibility: hidden;
}
    </style>
</head>

<div id="page-loader">
  <span class="loader"></span>
</div>

<body class="font-sans bg-[#18386d] text-white m-0 p-0">

    <!-- =====================================================
            TOP NAVBAR
        ====================================================== -->
    @include('partials.navbar')

    <div class="w-[96.82%] max-w-[1859px] mx-auto" data-ajax-list>

        <div class="flex justify-between items-center gap-5 py-2.5 flex-wrap">
        <form method="GET" action="{{ route('hr.employees.index') }}" class="flex justify-start items-center gap-5 flex-wrap">
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

                    <option value="department_asc" {{ request('sort') == 'department_asc' ? 'selected' : '' }}>
                        Department (A-Z)
                    </option>

                    <option value="department_desc" {{ request('sort') == 'department_desc' ? 'selected' : '' }}>
                        Department (Z-A)
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

        @include('partials.per-page-filter', ['perPage' => $employees->perPage()])
        </div>

        <!-- =========================
            TABLE
        ========================== -->

        <!-- Header -->
       <div class="w-full h-[47px] mx-auto mb-3 grid grid-cols-[21.5%_21.5%_21.5%_21.5%_15%] bg-[#0B1E3D] border border-white/[0.15] rounded-[10px] overflow-hidden">

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Employee ID</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Name</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Department</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white border-r border-white/[0.15]">Position</div>

            <div class="px-[18px] py-[15px] text-center text-[11.9px] font-light uppercase tracking-wide text-white">Action</div>

        </div>

        <div data-ajax-list-results class="transition-opacity duration-200">
            @include('employees.partials.list-results')
        </div>

    </div>

    <script src="{{ asset('js/ajax-list.js') }}" defer></script>

</body>

</html>
