<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ config('app.name', 'TechForge') }} | Forge Store</title>
    
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

        .hide-scroll-bar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scroll-bar::-webkit-scrollbar {
            display: none;
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
                <!-- Using a placeholder image for parts -->
                <img src="https://images.unsplash.com/photo-1591488320449-011701bb6704?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="PC Parts and Accessories" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-[#050505] via-transparent to-[#050505] pointer-events-none"></div>
            </div>
            
            <div class="max-w-[1500px] mx-auto px-6 lg:px-8 relative z-10 py-16 md:py-24">
                <div class="w-full md:w-2/3">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-wide mb-4">Forge Store</h1>
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed max-w-lg">Explore premium PC parts, high-end monitors, and gaming accessories to elevate your setup.</p>
                </div>
            </div>
        </div>
    </main>

    <div class="max-w-[1500px] mx-auto px-6 lg:px-8 pb-24 relative z-10">
        <h2 class="text-3xl font-black text-white tracking-wide mb-8 text-center">Shop by category</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Accessories -->
            <a href="{{ route('ecommerce.store.accessories') }}" class="group relative overflow-hidden rounded-2xl liquid-glass-heavy border border-white/5 hover:border-primary/50 transition-all duration-300 p-8 flex flex-col items-center justify-center text-center aspect-[4/3] md:aspect-auto md:h-64">
                <i class="ph ph-headphones text-5xl text-gray-500 group-hover:text-primary transition-colors mb-4 drop-shadow-[0_0_15px_rgba(255,107,0,0)] group-hover:drop-shadow-[0_0_15px_rgba(255,107,0,0.5)]"></i>
                <h3 class="text-xl font-bold text-white mb-2">Accessories</h3>
                <p class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Keyboards, mice, headsets & more.</p>
            </a>

            <!-- Monitors -->
            <a href="{{ route('ecommerce.store.monitors') }}" class="group relative overflow-hidden rounded-2xl liquid-glass-heavy border border-white/5 hover:border-primary/50 transition-all duration-300 p-8 flex flex-col items-center justify-center text-center aspect-[4/3] md:aspect-auto md:h-64">
                <i class="ph ph-desktop text-5xl text-gray-500 group-hover:text-primary transition-colors mb-4 drop-shadow-[0_0_15px_rgba(255,107,0,0)] group-hover:drop-shadow-[0_0_15px_rgba(255,107,0,0.5)]"></i>
                <h3 class="text-xl font-bold text-white mb-2">Monitors</h3>
                <p class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">High refresh rate gaming displays.</p>
            </a>

            <!-- PC Parts -->
            <a href="{{ route('ecommerce.store.pc-parts') }}" class="group relative overflow-hidden rounded-2xl liquid-glass-heavy border border-white/5 hover:border-primary/50 transition-all duration-300 p-8 flex flex-col items-center justify-center text-center aspect-[4/3] md:aspect-auto md:h-64">
                <i class="ph ph-cpu text-5xl text-gray-500 group-hover:text-primary transition-colors mb-4 drop-shadow-[0_0_15px_rgba(255,107,0,0)] group-hover:drop-shadow-[0_0_15px_rgba(255,107,0,0.5)]"></i>
                <h3 class="text-xl font-bold text-white mb-2">PC Parts</h3>
                <p class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Processors, GPUs, Motherboards.</p>
            </a>
        </div>
    </div>

    <!-- Deals Sections -->
    <div class="max-w-[1500px] mx-auto px-6 lg:px-8 pb-24 relative z-10 flex flex-col gap-16">
        
        <!-- Gaming Accessories -->
        <section>
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-wide flex items-center gap-3">
                        <i class="ph ph-headphones text-primary"></i> Accessory Deals
                    </h2>
                    <p class="text-gray-400 mt-2">Score huge discounts on premium gaming gear.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('accessories-carousel', -1)">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>
                        <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('accessories-carousel', 1)">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="accessories-carousel" class="flex gap-6 overflow-x-auto pb-8 pt-4 -mx-6 px-6 lg:-mx-8 lg:px-8 scroll-px-6 lg:scroll-px-8 snap-x snap-mandatory hide-scroll-bar scroll-smooth" style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);">
                @foreach($accessories as $item)
                    <x-store-item-card 
                        class="w-[85vw] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] shrink-0 snap-start"
                        :category="$item->category" 
                        :name="$item->name" 
                        :price="$item->price" 
                        :image="$item->image_url" 
                        :rating="$item->rating"
                        :reviews="$item->reviews"
                        :sale="$item->sale" 
                        :originalPrice="$item->originalPrice ?? null" 
                    />
                @endforeach
            </div>
        </section>

        <!-- Monitors -->
        <section>
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-wide flex items-center gap-3">
                        <i class="ph ph-desktop text-primary"></i> Monitor Deals
                    </h2>
                    <p class="text-gray-400 mt-2">Save big on ultra-smooth visual experiences.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('monitors-carousel', -1)">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>
                        <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('monitors-carousel', 1)">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="monitors-carousel" class="flex gap-6 overflow-x-auto pb-8 pt-4 -mx-6 px-6 lg:-mx-8 lg:px-8 scroll-px-6 lg:scroll-px-8 snap-x snap-mandatory hide-scroll-bar scroll-smooth" style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);">
                @foreach($monitors as $item)
                    <x-store-item-card 
                        class="w-[85vw] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] shrink-0 snap-start"
                        :category="$item->category" 
                        :name="$item->name" 
                        :price="$item->price" 
                        :image="$item->image_url" 
                        :rating="$item->rating"
                        :reviews="$item->reviews"
                        :sale="$item->sale" 
                        :originalPrice="$item->originalPrice ?? null" 
                    />
                @endforeach
            </div>
        </section>

        <!-- PC Parts -->
        <section>
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-wide flex items-center gap-3">
                        <i class="ph ph-cpu text-primary"></i> PC Part Deals
                    </h2>
                    <p class="text-gray-400 mt-2">Upgrade your system's performance for less.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('pc-parts-carousel', -1)">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>
                        <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('pc-parts-carousel', 1)">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="pc-parts-carousel" class="flex gap-6 overflow-x-auto pb-8 pt-4 -mx-6 px-6 lg:-mx-8 lg:px-8 scroll-px-6 lg:scroll-px-8 snap-x snap-mandatory hide-scroll-bar scroll-smooth" style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);">
                @foreach($pcParts as $item)
                    <x-store-item-card 
                        class="w-[85vw] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] shrink-0 snap-start"
                        :category="$item->category" 
                        :name="$item->name" 
                        :price="$item->price" 
                        :image="$item->image_url ?? $item->image" 
                        :rating="$item->rating"
                        :reviews="$item->reviews"
                        :sale="$item->sale" 
                        :originalPrice="$item->originalPrice ?? null" 
                    />
                @endforeach
            </div>
        </section>
    </div>

    <x-footer />

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])
    @vite(['Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js', 'Modules/E-Commerce/Techforge/resources/js/Category/Category.js'])
    <script>
        function scrollCarousel(id, direction) {
            const el = document.getElementById(id);
            if (!el) return;
            const card = el.querySelector('.store-item-card');
            if (!card) return;
            
            const amount = (card.offsetWidth + 24) * direction;
            const start = el.scrollLeft;
            const duration = 400;
            let startTime = null;

            el.style.scrollSnapType = 'none';

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                
                // easeOutQuart
                const ease = 1 - Math.pow(1 - progress, 4);
                
                el.scrollLeft = start + (amount * ease);
                
                if (timeElapsed < duration) {
                    requestAnimationFrame(animation);
                } else {
                    el.style.scrollSnapType = '';
                }
            }
            
            requestAnimationFrame(animation);
        }
    </script>
</body>
</html>
