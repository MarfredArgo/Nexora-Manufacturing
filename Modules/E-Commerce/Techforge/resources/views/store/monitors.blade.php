<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ config('app.name', 'TechForge') }} | Accessories</title>
    
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
    </style>
    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
    <!-- Lenis Smooth Scrolling -->
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.19/bundled/lenis.min.js"></script>
</head>
<body class="relative antialiased min-h-screen flex flex-col selection:bg-primary selection:text-white">

    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>

    <x-navbar />

    <main class="flex-grow pt-32 lg:pt-40 pb-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            
            <!-- Banner -->
            <div class="mb-12 relative overflow-hidden rounded-2xl bg-gradient-to-r from-zinc-900 to-black border border-white/5 p-12">
                <div class="relative z-10">
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-wide mb-4">
                        All Gaming <span class="text-primary">Monitors</span>
                    </h1>
                    <p class="text-gray-400 text-lg max-w-2xl">
                        Experience crystal clear visuals, lightning-fast refresh rates, and immersive gameplay with our premium selection of gaming monitors.
                    </p>
                </div>
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1600861194942-f883de0dfe96?auto=format&fit=crop&w=1200&q=80')] opacity-20 bg-cover bg-center mix-blend-luminosity"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
            </div>

            <!-- Removed Category Filters -->

            <!-- Main Content Area: Sidebar + Grid -->
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                <!-- Sidebar Filter -->
                <div id="filter-sidebar" class="w-full lg:w-[280px] shrink-0 liquid-glass-heavy border border-white/5 rounded-2xl p-6 hidden transition-opacity duration-300">
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-white/10">
                        <h2 class="text-2xl font-bold text-white tracking-wide">Filter</h2>
                        <button id="reset-filters-btn" class="text-xs font-bold tracking-wider text-[#FF6B00] hover:text-white transition-colors uppercase">Reset All</button>
                    </div>

                    <!-- Dynamic Filters Container -->
                    <div id="dynamic-filters" class="flex flex-col">
                        <!-- Accordions populated by JS -->
                    </div>
                </div>

                <!-- Product Grid Area -->
                <div id="products-wrapper" class="w-full lg:flex-1 min-w-0">
                <div id="grid-fade-container" class="transition-opacity duration-300 ease-out opacity-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12" id="products-grid">
                        @foreach($items as $item)
                            <div class="product-item hidden" data-category="{{ $item->filter_key }}" data-filters="{{ $item->filter_data }}">
                                <x-store-item-card 
                                    class="w-full h-full"
                                    :category="$item->category" 
                                    :name="$item->name" 
                                    :price="$item->price" 
                                    :image="$item->image_url ?? $item->image" 
                                    :rating="$item->rating"
                                    :reviews="$item->reviews"
                                    :sale="$item->sale" 
                                    :originalPrice="$item->originalPrice ?? null" 
                                />
                            </div>
                        @endforeach
                    </div>

                    <div id="no-products-message" class="hidden text-center py-20 text-gray-400">
                        <i class="ph ph-magnifying-glass text-4xl mb-4 text-white/20"></i>
                        <p class="text-lg">No monitors match your filters.</p>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="flex justify-center mt-12 gap-2" id="pagination-container">
                    <!-- Pagination buttons generated by JS -->
                </div>
            </div>

            </div> <!-- End Flex Container -->

        </div>
    </main>

    <x-footer />

    <script>
        // Initialize Lenis
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            gestureOrientation: 'vertical',
            smoothWheel: true,
            wheelMultiplier: 1,
            smoothTouch: false,
            touchMultiplier: 2,
            infinite: false,
        })

        function raf(time) {
            lenis.raf(time)
            requestAnimationFrame(raf)
        }

        requestAnimationFrame(raf)

        // Client-Side Filtering and Pagination
        document.addEventListener('DOMContentLoaded', () => {
            const items = Array.from(document.querySelectorAll('.product-item'));
            const paginationContainer = document.getElementById('pagination-container');
            const gridFadeContainer = document.getElementById('grid-fade-container');
            const noProductsMessage = document.getElementById('no-products-message');
            const dynamicFiltersContainer = document.getElementById('dynamic-filters');
            const filterSidebar = document.getElementById('filter-sidebar');
            const resetFiltersBtn = document.getElementById('reset-filters-btn');
            
            let currentFilter = 'all';
            let currentPage = 1;
            const itemsPerPage = 9;
            let activeFilters = {}; // Key: Array of selected values

            resetFiltersBtn.addEventListener('click', () => {
                activeFilters = {};
                // Uncheck all checkboxes and reset custom UI
                document.querySelectorAll('#dynamic-filters input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                    
                    const customBox = cb.nextElementSibling;
                    const text = customBox.nextElementSibling;
                    const checkIcon = customBox.querySelector('i');
                    
                    customBox.classList.add('bg-[#050505]', 'border-white/20');
                    customBox.classList.remove('bg-[#FF6B00]', 'border-[#FF6B00]');
                    checkIcon.classList.add('opacity-0');
                    checkIcon.classList.remove('opacity-100');
                    text.classList.add('text-gray-400');
                    text.classList.remove('text-white');
                });
                
                currentPage = 1;
                updateUI();
            });

            function buildDynamicFilters() {
                dynamicFiltersContainer.innerHTML = '';
                activeFilters = {};

                const categoryItems = items.filter(item => item.dataset.category === currentFilter);
                if (categoryItems.length === 0) return;

                // Extract all possible filter keys and their unique values with counts
                const availableFilters = {};
                categoryItems.forEach(item => {
                    const filters = JSON.parse(item.dataset.filters || '{}');
                    Object.entries(filters).forEach(([key, value]) => {
                        if (value) {
                            if (!availableFilters[key]) availableFilters[key] = {};
                            if (!availableFilters[key][value]) availableFilters[key][value] = 0;
                            availableFilters[key][value]++;
                        }
                    });
                });

                // Generate Accordions
                let hasFilters = false;
                
                const catCollator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                const filterEntries = Object.entries(availableFilters).sort((a, b) => {
                    if (a[0] === 'Brand') return 1;
                    if (b[0] === 'Brand') return -1;
                    return catCollator.compare(a[0], b[0]);
                });

                filterEntries.forEach(([key, valueCounts]) => {
                    if (Object.keys(valueCounts).length > 1) { // Only show filter if there's more than one option
                        hasFilters = true;
                        
                        const accordionGroup = document.createElement('div');
                        accordionGroup.className = 'border-b border-white/5 py-4 first:pt-0 last:border-0';
                        
                        // Header
                        const header = document.createElement('button');
                        header.className = 'w-full flex items-center justify-between text-left group';
                        
                        const title = document.createElement('div');
                        title.className = 'flex items-center gap-2';
                        // Icon mapping
                        let iconClass = 'ph-funnel';
                        if (key.toLowerCase() === 'brand') iconClass = 'ph-tag';
                        if (key.toLowerCase() === 'switch') iconClass = 'ph-keyboard';
                        if (key.toLowerCase() === 'wireless') iconClass = 'ph-wifi-high';
                        if (key.toLowerCase() === 'sensor') iconClass = 'ph-crosshair';
                        if (key.toLowerCase() === 'size') iconClass = 'ph-arrows-out';
                        if (key.toLowerCase() === 'channels') iconClass = 'ph-speaker-hifi';
                        if (key.toLowerCase() === 'type') iconClass = 'ph-list';
                        
                        title.innerHTML = `<i class="ph-bold ${iconClass} text-[#FF6B00]"></i> <span class="text-sm font-bold tracking-wider uppercase text-white">${key}</span>`;
                        
                        const chevron = document.createElement('i');
                        chevron.className = 'ph-bold ph-caret-down text-gray-500 group-hover:text-white transition-all duration-300';
                        
                        header.appendChild(title);
                        header.appendChild(chevron);
                        
                        // Body
                        const bodyWrapper = document.createElement('div');
                        bodyWrapper.className = 'grid transition-all duration-300 ease-in-out grid-rows-[1fr] opacity-100';
                        
                        const body = document.createElement('div');
                        body.className = 'overflow-hidden flex flex-col gap-3 mt-4';
                        
                        const collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                        const sortedValues = Object.keys(valueCounts).sort(collator.compare);
                        sortedValues.forEach(val => {
                            const count = valueCounts[val];
                            const label = document.createElement('label');
                            label.className = 'flex items-center gap-3 cursor-pointer group/label';
                            
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.value = val;
                            checkbox.className = 'hidden';
                            
                            // Custom checkbox UI
                            const customBox = document.createElement('div');
                            customBox.className = 'w-4 h-4 rounded border border-white/20 flex items-center justify-center transition-colors group-hover/label:border-white/50 bg-[#050505] shrink-0';
                            const checkIcon = document.createElement('i');
                            checkIcon.className = 'ph-bold ph-check text-[10px] text-white opacity-0 transition-opacity';
                            customBox.appendChild(checkIcon);
                            
                            const text = document.createElement('span');
                            text.className = 'text-sm font-medium text-gray-400 group-hover/label:text-white transition-colors';
                            text.innerText = `${val} (${count})`;
                            
                            // Sync checkbox state
                            checkbox.addEventListener('change', (e) => {
                                if (e.target.checked) {
                                    customBox.classList.remove('bg-[#050505]', 'border-white/20');
                                    customBox.classList.add('bg-[#FF6B00]', 'border-[#FF6B00]');
                                    checkIcon.classList.remove('opacity-0');
                                    checkIcon.classList.add('opacity-100');
                                    text.classList.remove('text-gray-400');
                                    text.classList.add('text-white');
                                    
                                    if (!activeFilters[key]) activeFilters[key] = [];
                                    activeFilters[key].push(val);
                                } else {
                                    customBox.classList.add('bg-[#050505]', 'border-white/20');
                                    customBox.classList.remove('bg-[#FF6B00]', 'border-[#FF6B00]');
                                    checkIcon.classList.add('opacity-0');
                                    checkIcon.classList.remove('opacity-100');
                                    text.classList.add('text-gray-400');
                                    text.classList.remove('text-white');
                                    
                                    if (activeFilters[key]) {
                                        activeFilters[key] = activeFilters[key].filter(v => v !== val);
                                        if (activeFilters[key].length === 0) delete activeFilters[key];
                                    }
                                }
                                currentPage = 1;
                                updateUI();
                            });
                            
                            label.appendChild(checkbox);
                            label.appendChild(customBox);
                            label.appendChild(text);
                            body.appendChild(label);
                        });
                        
                        bodyWrapper.appendChild(body);
                        
                        // Accordion toggle logic
                        let isOpen = true;
                        header.addEventListener('click', () => {
                            isOpen = !isOpen;
                            if (isOpen) {
                                bodyWrapper.classList.remove('grid-rows-[0fr]', 'opacity-0');
                                bodyWrapper.classList.add('grid-rows-[1fr]', 'opacity-100');
                                body.classList.add('mt-4');
                                chevron.classList.remove('-rotate-90');
                            } else {
                                bodyWrapper.classList.add('grid-rows-[0fr]', 'opacity-0');
                                bodyWrapper.classList.remove('grid-rows-[1fr]', 'opacity-100');
                                body.classList.remove('mt-4');
                                chevron.classList.add('-rotate-90');
                            }
                        });
                        
                        accordionGroup.appendChild(header);
                        accordionGroup.appendChild(bodyWrapper);
                        dynamicFiltersContainer.appendChild(accordionGroup);
                    }
                });

                if (hasFilters) {
                    filterSidebar.classList.remove('hidden');
                    filterSidebar.classList.add('block');
                } else {
                    filterSidebar.classList.remove('block');
                    filterSidebar.classList.add('hidden');
                }
            }

            function updateUI(rebuildFilters = false) {
                if (rebuildFilters) {
                    buildDynamicFilters();
                }

                // Fade out
                gridFadeContainer.style.opacity = '0';
                
                setTimeout(() => {
                    // Update active category button styles (Removed)
                    // Filter items by category AND dynamic filters
                    const filteredItems = items.filter(item => {
                        // 1. Category check
                        if (currentFilter !== 'all' && item.dataset.category !== currentFilter) return false;
                        
                        // 2. Dynamic filters check (Array intersection)
                        if (Object.keys(activeFilters).length > 0) {
                            const itemFilters = JSON.parse(item.dataset.filters || '{}');
                            for (const [key, selectedValues] of Object.entries(activeFilters)) {
                                if (selectedValues && selectedValues.length > 0) {
                                    if (!selectedValues.includes(String(itemFilters[key]))) {
                                        return false; // Item's value is not in the array of checked boxes
                                    }
                                }
                            }
                        }
                        
                        return true;
                    });

                    // Pagination logic
                    const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                    if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
                    
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;

                    // Show/hide items
                    items.forEach(item => {
                        item.classList.add('hidden');
                    });
                    
                    filteredItems.slice(startIndex, endIndex).forEach(item => {
                        item.classList.remove('hidden');
                    });

                    // Update empty state
                    if (filteredItems.length === 0) {
                        noProductsMessage.classList.remove('hidden');
                    } else {
                        noProductsMessage.classList.add('hidden');
                    }

                    // Render pagination buttons
                    renderPagination(totalPages);

                    // Fade in
                    gridFadeContainer.style.opacity = '1';

                    // Update URL silently
                    const url = new URL(window.location);
                    if(currentFilter === 'all') {
                        url.searchParams.delete('category');
                    } else {
                        url.searchParams.set('category', currentFilter);
                    }
                    if(currentPage > 1) {
                        url.searchParams.set('page', currentPage);
                    } else {
                        url.searchParams.delete('page');
                    }
                    window.history.replaceState({}, '', url);
                    
                }, 300); // Wait for fade out
            }

            function renderPagination(totalPages) {
                paginationContainer.innerHTML = '';
                if (totalPages <= 1) return;

                // Prev button
                const prevBtn = document.createElement('button');
                prevBtn.innerHTML = '<i class="ph-bold ph-caret-left"></i>';
                prevBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center transition-colors ${currentPage === 1 ? 'text-gray-600 border border-white/5 cursor-not-allowed' : 'text-white border border-white/10 hover:border-primary hover:text-primary'}`;
                prevBtn.disabled = currentPage === 1;
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updateUI(); lenis.scrollTo('#filter-sidebar', { offset: -150, duration: 1.2 }); } };
                paginationContainer.appendChild(prevBtn);

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.innerText = i;
                    if (i === currentPage) {
                        pageBtn.className = 'w-10 h-10 rounded-xl flex items-center justify-center font-bold bg-primary text-white shadow-[0_0_15px_rgba(255,107,0,0.4)]';
                    } else {
                        pageBtn.className = 'w-10 h-10 rounded-xl flex items-center justify-center font-bold text-gray-400 border border-white/10 hover:border-primary hover:text-primary transition-colors hover:shadow-[0_0_10px_rgba(255,107,0,0.2)]';
                    }
                    pageBtn.onclick = () => { currentPage = i; updateUI(); lenis.scrollTo('#filter-sidebar', { offset: -150, duration: 1.2 }); };
                    paginationContainer.appendChild(pageBtn);
                }

                // Next button
                const nextBtn = document.createElement('button');
                nextBtn.innerHTML = '<i class="ph-bold ph-caret-right"></i>';
                nextBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center transition-colors ${currentPage === totalPages ? 'text-gray-600 border border-white/5 cursor-not-allowed' : 'text-white border border-white/10 hover:border-primary hover:text-primary'}`;
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updateUI(); lenis.scrollTo('#filter-sidebar', { offset: -150, duration: 1.2 }); } };
                paginationContainer.appendChild(nextBtn);
            }

            // Initial setup (read query params on first load)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('page')) {
                currentPage = parseInt(urlParams.get('page')) || 1;
            }
            updateUI(true);
        });
    </script>
</body>
</html>
