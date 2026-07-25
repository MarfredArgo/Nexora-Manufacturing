<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ config('app.name', 'TechForge') }} | Built for Performance</title>
    
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
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505;
            color: #ffffff;
            overflow-x: hidden;
        }

        /* Ambient Radial Light Blurs */
        .ambient-light-1 {
            position: fixed;
            top: -20%;
            left: -20%;
            width: 70vw;
            height: 70vw;
            background: radial-gradient(circle, rgba(255, 107, 0, 0.35) 0%, rgba(255, 107, 0, 0) 65%);
            z-index: -1;
            pointer-events: none;
            animation: floatPulse1 20s ease-in-out infinite;
        }

        .ambient-light-2 {
            position: fixed;
            top: 35%;
            right: -20%;
            width: 80vw;
            height: 80vw;
            background: radial-gradient(circle, rgba(153, 0, 0, 0.4) 0%, rgba(153, 0, 0, 0) 65%);
            z-index: -1;
            pointer-events: none;
            animation: floatPulse2 25s ease-in-out infinite;
        }

        @keyframes floatPulse1 {
            0% {
                opacity: 0.3;
                transform: translate(0, 0) scale(0.8);
            }
            33% {
                opacity: 0.8;
                transform: translate(25vw, 15vh) scale(1.2);
            }
            66% {
                opacity: 0.4;
                transform: translate(-10vw, 30vh) scale(0.9);
            }
            100% {
                opacity: 0.3;
                transform: translate(0, 0) scale(0.8);
            }
        }

        @keyframes floatPulse2 {
            0% {
                opacity: 0.8;
                transform: translate(0, 0) scale(1.1);
            }
            33% {
                opacity: 0.3;
                transform: translate(-25vw, -15vh) scale(0.8);
            }
            66% {
                opacity: 0.7;
                transform: translate(15vw, -25vh) scale(1.3);
            }
            100% {
                opacity: 0.8;
                transform: translate(0, 0) scale(1.1);
            }
        }

        /* Orange Gradient Text */
        .text-gradient {
            background: linear-gradient(to right, #ffffff, #ffaa66);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        /* Preloader Animations */
        @keyframes spinFastOnce {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(720deg); }
        }
        .animate-spin-fast {
            animation: spinFastOnce 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        @keyframes slideTextOut {
            0% { max-width: 0; opacity: 0; padding-left: 0; }
            100% { max-width: 400px; opacity: 1; padding-left: 1.5rem; }
        }
        .animate-slide-text {
            animation: slideTextOut 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            animation-delay: 0.8s;
            overflow: hidden;
            white-space: nowrap;
            opacity: 0;
            max-width: 0;
        }
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
</head>
<body class="relative antialiased selection:bg-primary selection:text-white">

    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 bg-[#050505] z-[100] flex items-center justify-center transition-opacity duration-1000 ease-in-out">
        <script>
            if (!sessionStorage.getItem('techforge_visited')) {
                document.write(`
                    <div class="relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full animate-pulse"></div>
                        <div class="flex items-center relative z-10">
                            <img src="{{ Vite::asset('Modules/E-Commerce/Techforge/resources/img/Techforge_Logo.png') }}" alt="TechForge Logo" class="h-20 w-auto object-contain animate-spin-fast drop-shadow-[0_0_25px_rgba(255,107,0,0.6)]">
                            <span class="text-4xl md:text-5xl font-black text-white tracking-widest animate-slide-text">TECHFORGE</span>
                        </div>
                    </div>
                `);
            } else {
                document.getElementById('preloader').style.display = 'none';
            }
        </script>
    </div>

    <!-- Background Ambient Effects -->
    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>



    <x-navbar />

    <!-- Category Hero -->
    <main class="relative pt-32 pb-16 lg:pt-40 lg:pb-20 overflow-hidden w-full">
        <div class="w-full relative z-10 group" style="mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);">
            <div class="absolute inset-0 w-full h-full">
                <img src="https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Pre-Built PCs" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-[#050505] via-transparent to-[#050505] pointer-events-none"></div>
            </div>
            
            <div class="max-w-[1500px] mx-auto px-6 lg:px-8 relative z-10 py-16 md:py-24">
                <div class="w-full md:w-2/3">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-wide mb-4">Pre-Built PCs</h1>
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed max-w-lg">Browse our full range of prebuilt gaming PCs. Ready to ship directly to your door. Experience uncompromised performance, ready to ship directly to your door.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Wrapper for AJAX Tab/Pagination Loading -->
    <div id="search-results-container" class="transition-opacity duration-300">

    <!-- Category Content -->
    <form id="filter-form" method="GET" action="{{ route('ecommerce.prebuilt-pcs') }}" class="max-w-[1500px] mx-auto px-6 lg:px-8 pb-24 relative z-10 flex flex-col lg:flex-row gap-8">
        
        <!-- Product Filter Component -->
        <x-pcs-filter :counts="$counts" route="prebuilt-pcs" :globalMinPrice="$globalMinPrice" :globalMaxPrice="$globalMaxPrice" />

        <!-- Product Grid -->
        <div id="product-grid-area" class="flex-1 w-full lg:w-auto transition-opacity duration-300">
            
            <!-- Controls / Sort -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <p class="text-sm text-gray-400">Showing <span id="product-count" class="text-white font-bold">{{ $configs->count() }}</span> products</p>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <span class="text-xs text-gray-500 uppercase tracking-widest font-bold">Sort By</span>
                    <div class="relative w-full sm:w-48">
                        <select name="sort" class="w-full bg-black/40 border border-[#3a1810] rounded-xl py-2 pl-4 pr-10 text-sm text-white appearance-none cursor-pointer hover:border-[#5a2810] transition-colors focus:outline-none focus:border-primary">
                            <option {{ request('sort') == 'Recommended' ? 'selected' : '' }}>Recommended</option>
                            <option {{ request('sort') == 'Price: Low to High' ? 'selected' : '' }}>Price: Low to High</option>
                            <option {{ request('sort') == 'Price: High to Low' ? 'selected' : '' }}>Price: High to Low</option>
                            <option {{ request('sort') == 'Newest Arrivals' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option {{ request('sort') == 'Customer Reviews' ? 'selected' : '' }}>Customer Reviews</option>
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

    
    <!-- CTA Cards -->
    <div class="max-w-[1500px] mx-auto px-6 lg:px-8 pb-24 relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8">
        <a href="{{ url('/pc-configurator') }}" class="block liquid-glass rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition-all group overflow-hidden relative">
            <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-500">
                <i class="ph-fill ph-cpu text-[200px] text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Can't find what you need?</h3>
            <p class="text-gray-400 mb-6">Choose from our curated selection of customizable builds.</p>
            <span class="inline-flex items-center gap-2 text-primary font-bold">Customize a PC <i class="ph-bold ph-arrow-right group-hover:translate-x-2 transition-transform"></i></span>
        </a>
        <a href="{{ url('/build-pc') }}" class="block liquid-glass rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition-all group overflow-hidden relative">
            <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-500">
                <i class="ph-fill ph-hammer text-[200px] text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Build from Scratch</h3>
            <p class="text-gray-400 mb-6">Use PC Forge to pick every single component yourself.</p>
            <span class="inline-flex items-center gap-2 text-primary font-bold">Launch PC Forge <i class="ph-bold ph-arrow-right group-hover:translate-x-2 transition-transform"></i></span>
        </a>
    </div>

    <x-footer />


    

    <script>
        window.initialConfigs = @json($configs);
        window.appUrl = "{{ url('/') }}";
        
        document.addEventListener('DOMContentLoaded', function() {
            function bindMobileFilter() {
                const mobileFilterBtn = document.getElementById('mobile-filter-btn');
                const filterSidebar = document.getElementById('filter-sidebar');
                const closeFilterBtn = document.getElementById('close-filter-btn');
                if (mobileFilterBtn && filterSidebar && closeFilterBtn) {
                    mobileFilterBtn.addEventListener('click', () => {
                        filterSidebar.classList.remove('translate-x-full');
                        filterSidebar.classList.add('translate-x-0');
                    });
                    closeFilterBtn.addEventListener('click', () => {
                        filterSidebar.classList.remove('translate-x-0');
                        filterSidebar.classList.add('translate-x-full');
                    });
                }
            }
            bindMobileFilter();

            const configs = window.initialConfigs || [];
            const appUrl = window.appUrl || '';
            let currentPage = 1;
            const itemsPerPage = 6;
            
            const filterForm = document.getElementById('filter-form');
            const productGrid = document.getElementById('product-grid');
            const paginationContainer = document.getElementById('pagination-container');
            const productCountEl = document.getElementById('product-count');
            
            function augmentProcName(name) {
                if (!name) return '';
                if (!name.startsWith('AMD') && name.includes('Ryzen')) return 'AMD ' + name;
                if (!name.startsWith('Intel') && name.includes('Core')) return 'Intel ' + name;
                return name;
            }

            function augmentGpuName(name) {
                if (!name) return '';
                if (!name.startsWith('NVIDIA') && (name.includes('RTX') || name.includes('GTX'))) return 'NVIDIA ' + name;
                if (!name.startsWith('AMD') && name.includes('RX')) return 'AMD ' + name;
                return name;
            }
            
            function formatNumber(num) {
                return parseInt(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            function renderProducts(products) {
                if (!productGrid) return;
                
                // Update count
                if (productCountEl) {
                    productCountEl.textContent = products.length;
                }

                // Pagination calculations
                const totalPages = Math.ceil(products.length / itemsPerPage);
                if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const currentProducts = products.slice(startIndex, endIndex);

                // Render HTML
                if (currentProducts.length === 0) {
                    productGrid.innerHTML = `
                        <div class="col-span-1 sm:col-span-2 xl:col-span-3 py-20 flex flex-col items-center justify-center text-center bg-black/20 rounded-[2rem] border border-white/5">
                            <i class="ph ph-magnifying-glass text-6xl text-gray-600 mb-6"></i>
                            <h3 class="text-2xl font-bold text-white mb-2">No configurations found</h3>
                        </div>
                    `;
                    paginationContainer.innerHTML = '';
                    return;
                }

                let html = '';
                currentProducts.forEach(config => {
                    html += config.html_card;
                });

                productGrid.innerHTML = html;

                // Render Pagination
                let paginationHtml = '';
                if (totalPages > 1) {
                    paginationHtml += `
                        <button ${currentPage === 1 ? 'disabled' : ''} class="px-4 py-2 bg-[#2a110a] hover:bg-[#3a1810] disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-white font-bold transition-colors" data-page="${currentPage - 1}">Prev</button>
                    `;
                    
                    for (let i = 1; i <= totalPages; i++) {
                        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                            paginationHtml += `
                                <button class="px-4 py-2 ${currentPage === i ? 'bg-primary' : 'bg-[#2a110a] hover:bg-[#3a1810]'} rounded-lg text-white font-bold transition-colors" data-page="${i}">${i}</button>
                            `;
                        } else if (i === currentPage - 2 || i === currentPage + 2) {
                            paginationHtml += `<span class="px-4 py-2 text-gray-500">...</span>`;
                        }
                    }

                    paginationHtml += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} class="px-4 py-2 bg-[#2a110a] hover:bg-[#3a1810] disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-white font-bold transition-colors" data-page="${currentPage + 1}">Next</button>
                    `;
                }
                paginationContainer.innerHTML = paginationHtml;

                // Add event listeners to new pagination buttons
                const pageBtns = paginationContainer.querySelectorAll('button[data-page]');
                pageBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = parseInt(btn.dataset.page);
                        
                        const contentArea = document.getElementById('search-results-container');
                        window.scrollTo({
                            top: contentArea.getBoundingClientRect().top + window.scrollY - 100,
                            behavior: 'smooth'
                        });
                        applyFilters();
                    });
                });
            }

            function applyFilters() {
                if (!filterForm) return;

                const formData = new FormData(filterForm);
                const minPrice = parseFloat(formData.get('price_min')) || 0;
                const maxPrice = parseFloat(formData.get('price_max')) || 9999999;
                const processors = formData.getAll('processor[]');
                const gpus = formData.getAll('gpu[]');
                const rams = formData.getAll('ram[]');
                const storages = formData.getAll('storage[]');
                const sort = formData.get('sort') || 'Recommended';

                let filtered = configs.filter(product => {
                    // Price
                    if (product.price < minPrice || product.price > maxPrice) return false;

                    // Processor
                    if (processors.length > 0) {
                        const procName = augmentProcName(product.cpu?.name);
                        if (!processors.includes(procName)) return false;
                    }

                    // GPU
                    if (gpus.length > 0) {
                        const gpuName = augmentGpuName(product.gpu?.name);
                        if (!gpus.includes(gpuName)) return false;
                    }

                    // RAM
                    if (rams.length > 0) {
                        const ramName = product.ram?.name;
                        if (!rams.includes(ramName)) return false;
                    }

                    // Storage
                    if (storages.length > 0) {
                        const storageName = product.storage?.name;
                        if (storageName) {
                            const size = storageName.trim().split(' ')[0];
                            if (!storages.includes(size)) return false;
                        } else {
                            return false;
                        }
                    }

                    return true;
                });

                // Sort
                if (sort === 'Price: Low to High') {
                    filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
                } else if (sort === 'Price: High to Low') {
                    filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                } else if (sort === 'Newest Arrivals') {
                    filtered.sort((a, b) => b.id - a.id);
                } else if (sort === 'Customer Reviews') {
                    // Placeholder for reviews if available
                    filtered.sort((a, b) => b.id - a.id);
                } else {
                    // Default sort (Recommended)
                    filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                }

                renderProducts(filtered);
            }

            if (filterForm) {
                // Override the native submit method so Category.js calls this instead
                filterForm.submit = function() {
                    currentPage = 1;
                    applyFilters();
                };

                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    currentPage = 1;
                    applyFilters();
                });
                
                // Initial render
                applyFilters();
            }
        });
    </script>

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])

    <!-- Load our compiled JavaScript (You can remove LiquidGlass initialization from inside this file) -->
    @vite(['Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js', 'Modules/E-Commerce/Techforge/resources/js/Category/Category.js'])
</body>
</html>
