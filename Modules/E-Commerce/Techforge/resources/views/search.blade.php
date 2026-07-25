<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Results - TechForge</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#ff6b00', hover: '#e56000', glow: 'rgba(255, 107, 0, 0.5)' },
                        dark: { bg: '#050505', surface: '#121212' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        };
    </script>
    

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505;
            color: #ffffff;
            overflow-x: hidden;
        }

        .ambient-light-1 {
            position: absolute;
            top: -20vh;
            left: -10vw;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(255,107,0,0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-light-2 {
            position: absolute;
            top: 40vh;
            right: -20vw;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(255,81,0,0.1) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #050505;
        }
        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ff6b00;
        }
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
</head>
<body class="relative antialiased selection:bg-primary selection:text-white">

    <!-- Background Ambient Effects -->
    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>

    <x-navbar />

    <!-- Search Hero -->
    <main class="relative pt-32 pb-8 lg:pt-40 lg:pb-12 overflow-hidden w-full">
        <div class="max-w-[1500px] mx-auto px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-wide mb-2">
                <span class="text-primary">{{ $totalResults }}</span> Results for "{{ $query }}"
            </h1>
        </div>
    </main>

    <!-- Wrapper for AJAX Tab/Pagination Loading -->
    <div id="search-results-container" class="transition-opacity duration-300">

    <!-- Category Tabs -->
    <div class="max-w-[1500px] mx-auto px-6 lg:px-8 relative z-10 mb-8 overflow-x-auto">
        <div class="flex items-center justify-center gap-4 border-b border-white/10 pb-4 min-w-max">
            <a href="{{ route('ecommerce.search', ['q' => $query, 'tab' => 'prebuilt']) }}" class="tab-link flex items-center gap-2 px-4 py-2 rounded-xl transition-all {{ $tab === 'prebuilt' ? 'bg-primary text-white font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Prebuilt PCs
                <span class="text-xs py-0.5 px-2 rounded-md {{ $tab === 'prebuilt' ? 'bg-black/30 text-white' : 'bg-white/10 text-gray-400' }}">{{ $prebuiltCount }}</span>
            </a>
            <a href="{{ route('ecommerce.search', ['q' => $query, 'tab' => 'custom']) }}" class="tab-link flex items-center gap-2 px-4 py-2 rounded-xl transition-all {{ $tab === 'custom' ? 'bg-primary text-white font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Custom PCs
                <span class="text-xs py-0.5 px-2 rounded-md {{ $tab === 'custom' ? 'bg-black/30 text-white' : 'bg-white/10 text-gray-400' }}">{{ $customCount }}</span>
            </a>
            <a href="{{ route('ecommerce.search', ['q' => $query, 'tab' => 'laptops']) }}" class="tab-link flex items-center gap-2 px-4 py-2 rounded-xl transition-all {{ $tab === 'laptops' ? 'bg-primary text-white font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Gaming Laptops
                <span class="text-xs py-0.5 px-2 rounded-md {{ $tab === 'laptops' ? 'bg-black/30 text-white' : 'bg-white/10 text-gray-400' }}">{{ $laptopCount }}</span>
            </a>
            <a href="{{ route('ecommerce.search', ['q' => $query, 'tab' => 'parts']) }}" class="tab-link flex items-center gap-2 px-4 py-2 rounded-xl transition-all {{ $tab === 'parts' ? 'bg-primary text-white font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Parts & Accessories
                <span class="text-xs py-0.5 px-2 rounded-md {{ $tab === 'parts' ? 'bg-black/30 text-white' : 'bg-white/10 text-gray-400' }}">{{ $partsCount }}</span>
            </a>
        </div>
    </div>

    <!-- Category Content -->
    <form id="filter-form" method="GET" action="{{ route('ecommerce.search') }}" class="max-w-[1500px] mx-auto px-6 lg:px-8 pb-24 relative z-10 flex flex-col lg:flex-row gap-8">
        
        <!-- Preserve Query and Tab in Form -->
        <input type="hidden" name="q" value="{{ $query }}">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <!-- Product Filter Component -->
        <div id="filter-sidebar-wrapper" style="{{ ($tab === 'parts' || $tab === 'laptops') ? 'display: none;' : '' }}">
            <x-search-filter :counts="$counts" route="search" :globalMinPrice="$globalMinPrice" :globalMaxPrice="$globalMaxPrice" />
        </div>

        <!-- Product Grid -->
        <div id="product-grid-area" class="flex-1 w-full lg:w-auto transition-opacity duration-300">
            
            <!-- Controls / Sort -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <p class="text-sm text-gray-400">Showing <span id="product-count" class="text-white font-bold">{{ $configs->count() }}</span> products</p>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <span class="text-xs text-gray-500 uppercase tracking-widest font-bold">Sort By</span>
                    <div class="relative w-full sm:w-48">
                        <select name="sort" onchange="document.getElementById('filter-form').requestSubmit()" class="w-full bg-black/40 border border-[#3a1810] rounded-xl py-2 pl-4 pr-10 text-sm text-white appearance-none cursor-pointer hover:border-[#5a2810] transition-colors focus:outline-none focus:border-primary">
                            <option {{ request('sort') == 'Recommended' ? 'selected' : '' }}>Recommended</option>
                            <option {{ request('sort') == 'Price: Low to High' ? 'selected' : '' }}>Price: Low to High</option>
                            <option {{ request('sort') == 'Price: High to Low' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- JS Populates here -->
            </div>
            
            <!-- Pagination -->
            <div id="pagination-container" class="mt-12 w-full flex justify-center gap-2">
                <!-- JS Populates here -->
            </div>

        </div>

    </form>
    </div>

    <x-footer />
    
    <script>
        window.initialConfigs = @json($configs);
        window.appUrl = "{{ url('/') }}";
    </script>
    
    <!-- Load our compiled JavaScript (You can remove LiquidGlass initialization from inside this file) -->
    @vite(['Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js', 'Modules/E-Commerce/Techforge/resources/js/Category/Category.js', 'Modules/E-Commerce/Techforge/resources/js/Pages/Search/Search.js'])
</body>
</html>
