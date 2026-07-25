@php
    $storefrontCompany = request()->attributes->get('ecommerce_company');
    $storefrontName = $storefrontCompany?->company_name ?: 'Nexora Store';
    $storefrontVisitKey = 'storefront_visited_'.($storefrontCompany?->ecommerce_slug ?: 'store');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ $storefrontName }} | Nexora Storefront</title>
    
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
    
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        @keyframes glint {
            0% { transform: translateX(-100%) skewX(-12deg); }
            100% { transform: translateX(200%) skewX(-12deg); }
        }
        .animate-gradient-x {
            background-size: 200% auto;
            animation: gradientX 3s linear infinite;
        }
        @keyframes gradientX {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
</head>
<body class="relative antialiased selection:bg-primary selection:text-white">

    <!-- Preloader -->
    <div id="preloader" data-visit-key="{{ $storefrontVisitKey }}" class="fixed inset-0 bg-[#050505] z-[100] flex items-center justify-center transition-opacity duration-1000 ease-in-out">
        <script>
            if (!sessionStorage.getItem(@json($storefrontVisitKey))) {
                document.write(`
                    <div class="relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full animate-pulse"></div>
                        <div class="flex items-center relative z-10">
                            <img src="{{ asset('ecommerce/Nexora_Logo.png') }}" alt="{{ $storefrontName }} logo" class="h-20 w-auto object-contain animate-spin-fast drop-shadow-[0_0_25px_rgba(255,107,0,0.6)]">
                            <span class="text-4xl md:text-5xl font-black text-white tracking-widest animate-slide-text">{{ $storefrontName }}</span>
                        </div>
                    </div>
                `);
            } else {
                document.write(`
                    <div class="w-16 h-16 border-4 border-white/10 border-t-primary rounded-full animate-spin shadow-[0_0_20px_rgba(255,107,0,0.3)]"></div>
                `);
            }
        </script>
    </div>

    <!-- Background Ambient Effects -->
    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>



    <x-navbar />

    <!-- Hero Section -->
    <main class="relative pt-[140px] lg:pt-[180px] pb-0 overflow-hidden flex flex-col items-center min-h-screen justify-start">
        
        <!-- Hero Content (Split Layout) -->
        <div class="relative w-full max-w-7xl mx-auto px-6 z-20 flex flex-col lg:flex-row items-center lg:items-center justify-between gap-12 lg:gap-8 flex-grow mb-12 lg:mb-16 mt-10">
            
            <!-- Left Column: Typography -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center relative z-30">
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black uppercase leading-[1.1] tracking-wider text-white mb-8 relative drop-shadow-xl">
                    Experience<br>
                    <span class="text-primary drop-shadow-[0_0_15px_rgba(255,107,0,0.5)]">Unrivaled</span><br>
                    Performance.
                </h1>
                
                <!-- Description -->
                <p class="text-gray-400 text-sm sm:text-base max-w-md leading-relaxed mb-10 font-medium">
                    Every component selected for peak performance. Every build stress-tested for 72 hours. Zero thermal throttling. No compromises. Only victory.
                </p>
                
                <!-- Buttons -->
                <div class="flex flex-wrap items-center gap-4 mb-16">
                    <a href="{{ route('ecommerce.prebuilt-pcs') }}" class="bg-primary text-black px-8 py-3.5 font-black hover:bg-white transition-colors uppercase tracking-widest text-xs sm:text-sm shadow-[0_0_20px_rgba(255,107,0,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)]">
                        SHOP ALL BUILDS &rarr;
                    </a>
                    <a href="#" class="bg-transparent text-white border border-white/20 px-8 py-3.5 font-black hover:border-white transition-colors uppercase tracking-widest text-xs sm:text-sm">
                        BUILD YOUR OWN
                    </a>
                </div>
                
                <!-- Stats Row -->
                <div class="grid grid-cols-3 gap-4 sm:gap-8 border-t border-white/10 pt-8 max-w-md">
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-white mb-1">4,200+</div>
                        <div class="text-gray-500 text-[10px] sm:text-xs uppercase tracking-widest font-bold">Units Shipped</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-white mb-1">4.9&starf;</div>
                        <div class="text-gray-500 text-[10px] sm:text-xs uppercase tracking-widest font-bold">Avg Rating</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-white mb-1">72 hr</div>
                        <div class="text-gray-500 text-[10px] sm:text-xs uppercase tracking-widest font-bold">Avg Delivery</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Image Frame & Thumbnails -->
            <div class="w-full lg:w-1/2 flex justify-center lg:justify-end mt-4 lg:mt-0 relative group z-20">
                <!-- Diagonal background accent line -->
                <div class="absolute -inset-20 bg-gradient-to-tr from-transparent via-primary/5 to-transparent transform -skew-x-12 pointer-events-none"></div>
                
                <div class="flex flex-col gap-6 w-full max-w-[500px]">
                    <!-- Outer Frame Wrapper -->
                    <div class="relative w-full aspect-[4/3] lg:aspect-[4/5] xl:aspect-square">
                        <!-- Inner Image Container -->
                        <div class="absolute inset-0 w-full h-full overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] group/card">
                            
                            <!-- Corner Brackets (Inside relative container, perfectly aligned to edges) -->
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-primary z-20 pointer-events-none"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-primary z-20 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-primary z-20 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-primary z-20 pointer-events-none"></div>

                            <!-- Image -->
                            <img id="hero-main-img" src="{{ $customConfigs[0]->image_url ?? '' }}" alt="Featured PC" class="w-full h-full object-cover transition-opacity duration-700 opacity-90 group-hover/card:opacity-100 mix-blend-lighten">
                            
                            <!-- Overlay -->
                            <div class="absolute bottom-0 inset-x-0 h-1/2 bg-gradient-to-t from-[#050505] via-[#050505]/60 to-transparent flex flex-col justify-end p-6 sm:p-8 pointer-events-none z-10">
                                <div class="flex justify-between items-end w-full">
                                    <div>
                                        <div id="hero-badge" class="text-primary text-[10px] font-black uppercase tracking-widest mb-1">FEATURED BUILD</div>
                                        <h3 id="hero-title" class="text-white text-2xl sm:text-3xl font-black uppercase tracking-tight">{{ $customConfigs[0]->name ?? 'PHANTOM V4' }}</h3>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">FROM</div>
                                        <div id="hero-price" class="text-primary text-xl sm:text-2xl font-black">₱{{ number_format($customConfigs[0]->price ?? 105500, 0) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnails Gallery -->
                    <div class="w-full flex justify-between gap-2 sm:gap-3 z-40 overflow-x-hidden" id="hero-thumbnails-container">
                        @foreach($customConfigs as $index => $config)
                        <button data-tier="{{ strtolower($config->tier) }}" class="hero-thumbnail flex-1 h-14 sm:h-20 {{ $index === 0 ? 'border-2 border-primary shadow-[0_0_20px_rgba(255,107,0,0.2)]' : 'border border-white/20 hover:border-primary/50' }} bg-[#050505] relative overflow-hidden group cursor-pointer transition-colors rounded-lg">
                            <img src="{{ $config->image_url }}" class="w-full h-full object-cover mix-blend-lighten {{ $index === 0 ? 'opacity-90' : 'opacity-40 group-hover:opacity-80 grayscale group-hover:grayscale-0' }} transition-opacity">
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black via-black/80 to-transparent p-2">
                                <div class="text-[8px] sm:text-[10px] font-black tracking-widest uppercase text-center {{ $index === 0 ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">{{ $config->tier }}</div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            
        </div>


    <!-- Features Marquee -->
    <div class="w-full relative z-20 mt-auto overflow-hidden py-3 liquid-glass border-y border-white/5 backdrop-blur-xl">
        <!-- Mask for fading text at edges without affecting the glass background -->
        <div class="w-full h-full flex" style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
            <div class="flex animate-marquee items-center w-max">
            <!-- Repeated sets for continuous scrolling -->
            <div class="flex items-center gap-6 sm:gap-12 px-3 sm:px-6">
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">CERTIFIED BUILD TECHNICIANS</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">RTX 4090 IN STOCK</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">3-YEAR WARRANTY INCLUDED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">FREE SHIPPING OVER ₱50,000</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">ZERO THERMAL THROTTLING</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">72-HOUR STRESS TESTED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div></div>
            <div class="flex items-center gap-6 sm:gap-12 px-3 sm:px-6">
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">CERTIFIED BUILD TECHNICIANS</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">RTX 4090 IN STOCK</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">3-YEAR WARRANTY INCLUDED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">FREE SHIPPING OVER ₱50,000</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">ZERO THERMAL THROTTLING</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">72-HOUR STRESS TESTED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div></div>
            <div class="flex items-center gap-6 sm:gap-12 px-3 sm:px-6">
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">CERTIFIED BUILD TECHNICIANS</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">RTX 4090 IN STOCK</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">3-YEAR WARRANTY INCLUDED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">FREE SHIPPING OVER ₱50,000</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">ZERO THERMAL THROTTLING</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">72-HOUR STRESS TESTED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div></div>
            <div class="flex items-center gap-6 sm:gap-12 px-3 sm:px-6">
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">CERTIFIED BUILD TECHNICIANS</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">RTX 4090 IN STOCK</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">3-YEAR WARRANTY INCLUDED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">FREE SHIPPING OVER ₱50,000</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">ZERO THERMAL THROTTLING</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div>
            <div class="flex items-center gap-6 sm:gap-12">
                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">72-HOUR STRESS TESTED</span>
                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
            </div></div>
        </div>
        </div>
    </div>
    
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tiersData = {
                @foreach($customConfigs as $config)
                '{{ strtolower($config->tier) }}': {
                    title: '{{ addslashes($config->name) }}',
                    price: '₱{{ number_format($config->price, 0) }}',
                    badge: '{{ strtoupper($config->tier) }} BUILD',
                    image: '{{ $config->image_url }}'
                },
                @endforeach
            };

            const thumbnails = document.querySelectorAll('.hero-thumbnail');
            const mainImg = document.getElementById('hero-main-img');
            const titleEl = document.getElementById('hero-title');
            const priceEl = document.getElementById('hero-price');
            const oldPriceEl = document.getElementById('hero-old-price');
            const badgeEl = document.getElementById('hero-badge');
            const gpuEl = document.getElementById('hero-gpu');
            const cpuEl = document.getElementById('hero-cpu');
            const ramEl = document.getElementById('hero-ram');
            const descEl = document.getElementById('hero-desc');

            
            let autoScrollInterval;
            
            function startAutoScroll() {
                autoScrollInterval = setInterval(() => {
                    let activeIndex = -1;
                    thumbnails.forEach((t, index) => {
                        if (t.classList.contains('border-primary')) activeIndex = index;
                    });
                    
                    if (activeIndex !== -1) {
                        let nextIndex = (activeIndex + 1) % thumbnails.length;
                        thumbnails[nextIndex].click();
                    }
                }, 5000);
            }
            
            startAutoScroll();

            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', function() {
                    
                    // Reset interval on manual click
                    clearInterval(autoScrollInterval);
                    startAutoScroll();

                    const tier = this.getAttribute('data-tier');
                    const data = tiersData[tier];

                    if(data) {
                        // Update text contents
                        if(titleEl) titleEl.textContent = data.title;
                        if(priceEl) priceEl.textContent = data.price;
                        if(oldPriceEl) oldPriceEl.textContent = data.oldPrice;
                        // if(badgeEl) badgeEl.textContent = data.badge;
                        if(gpuEl) gpuEl.textContent = data.gpu;
                        if(cpuEl) cpuEl.textContent = data.cpu;
                        if(ramEl) ramEl.textContent = data.ram;
                        if(descEl) descEl.textContent = data.desc;

                        // Add fade effect for image
                        if(mainImg) {
                            mainImg.style.transition = 'opacity 0.3s ease-in-out';
                            mainImg.style.opacity = 0;
                            setTimeout(() => {
                                mainImg.src = data.image;
                                mainImg.style.opacity = 1;
                            }, 300);
                        }

                        // Reset all thumbnails styles
                        thumbnails.forEach(t => {
                            t.className = 'hero-thumbnail flex-1 h-14 sm:h-20 border border-white/20 bg-[#050505] relative overflow-hidden group cursor-pointer hover:border-primary/50 transition-colors rounded-lg';
                            const img = t.querySelector('img');
                            if(img) img.className = 'w-full h-full object-cover mix-blend-lighten opacity-40 group-hover:opacity-80 transition-opacity grayscale group-hover:grayscale-0';
                            const text = t.querySelector('.text-white');
                            if(text) text.className = 'text-white text-[8px] sm:text-[10px] font-black tracking-widest uppercase text-gray-400 group-hover:text-white text-center';
                        });

                        // Set active styles for clicked thumbnail
                        this.className = 'hero-thumbnail flex-1 h-14 sm:h-20 border-2 border-primary bg-[#050505] relative overflow-hidden group cursor-pointer shadow-[0_0_20px_rgba(255,107,0,0.2)] rounded-lg';
                        const activeImg = this.querySelector('img');
                        if(activeImg) activeImg.className = 'w-full h-full object-cover mix-blend-lighten opacity-90 group-hover:opacity-100 transition-opacity transform group-hover:scale-110 duration-700';
                        const activeText = this.querySelector('.text-white');
                        if(activeText) activeText.className = 'text-white text-[8px] sm:text-[10px] font-black tracking-widest uppercase text-center';
                    }
                });
            });
        });
    
    </script>
</main>

    <!-- Select Your Tier -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10 pt-20">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-16 border-b border-white/10 pb-8">
            <div>
                <h2 class="text-5xl sm:text-6xl font-black text-white uppercase tracking-tight leading-none">Select<br>Your Tier</h2>
            </div>
            <div class="mt-6 lg:mt-0 max-w-sm text-right">
                <p class="text-gray-400 text-sm font-medium">Four configurations. Every one tested under load for 72 hours before it leaves our facility.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach($customConfigs as $index => $config)
            <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl border border-white/5 flex flex-col group hover:border-primary/50 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] transition-all duration-500 relative overflow-hidden">
                <!-- Image Area -->
                <div class="relative w-full aspect-[4/3] bg-[#0a0a0a] overflow-hidden">
                    <img src="{{ $config->image_url }}" alt="{{ $config->name }}" class="w-full h-full object-cover mix-blend-lighten opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                    
                    <!-- Number -->
                    <div class="absolute top-4 left-4 text-primary font-mono text-sm tracking-widest">/0{{ $index + 1 }}</div>
                    
                    <!-- Optional Badges based on index -->
                    @if($index === 1)
                    <div class="absolute top-4 right-4 bg-primary text-white text-[10px] font-black tracking-widest uppercase px-3 py-1">Best Seller</div>
                    @elseif($index === 3)
                    <div class="absolute top-4 right-4 bg-primary text-white text-[10px] font-black tracking-widest uppercase px-3 py-1">Flagship</div>
                    @endif
                </div>
                
                <!-- Title Area -->
                <div class="p-5 border-b border-white/5 flex-grow flex flex-col relative z-10">
                    <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-2">
                        @if($index === 0) Entry-level dominance
                        @elseif($index === 1) Mid-tower supremacy
                        @elseif($index === 2) No compromises
                        @else The absolute pinnacle
                        @endif
                    </div>
                    <h3 class="text-white text-xl sm:text-2xl font-black tracking-wide uppercase mb-8 group-hover:text-primary transition-colors">{{ $config->name }}</h3>
                    
                    <!-- Specs Table -->
                    <div class="space-y-4 mt-auto mb-8 flex-grow">
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">CPU</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">Intel & AMD Options</span>
                        </div>
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">GPU</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $config->gpu->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">RAM</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $config->intelRam->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">Storage</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $config->storage->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">PSU</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $config->powerSupply->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="pt-6 border-t border-white/10 flex items-end justify-between mt-auto">
                        <div>
                            <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-1">Starting At</div>
                            <div class="text-primary text-2xl font-black">₱{{ number_format($config->price, 0) }}</div>
                        </div>
                        <a href="{{ route('ecommerce.configurator-overview', $config->id) }}" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2">
                            Configure &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    @if($storefrontListings->isNotEmpty())
    <section class="max-w-7xl mx-auto px-6 lg:px-8 mb-24 relative z-10 pt-10">
        <div class="flex items-end justify-between mb-8"><div><p class="text-primary text-xs font-black tracking-[0.3em] uppercase mb-2">Available now</p><h2 class="text-3xl md:text-4xl font-black">CLIENT STORE PICKS</h2></div><span class="text-xs text-gray-400">Live inventory availability</span></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">@foreach($storefrontListings as $listing)<a href="{{ route('ecommerce.listings.show', $listing) }}" class="rounded-2xl p-5 bg-white/5 border border-white/10 hover:border-primary/70 transition"><div class="h-36 rounded-xl bg-black/40 flex items-center justify-center overflow-hidden">@if($listing->image_url)<img class="max-h-full object-contain" src="{{ asset('storage/'.$listing->image_url) }}" alt="{{ $listing->name }}">@endif</div><h3 class="font-bold mt-4">{{ $listing->name }}</h3><p class="text-primary font-black text-xl mt-2">₱{{ number_format((float) $listing->price, 2) }}</p><p class="text-xs text-emerald-400 mt-2">{{ $listing->available_quantity }} available</p></a>@endforeach</div>
    </section>
    @endif

    <!-- Featured Products (Prebuilts) -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10 pt-10">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-16 border-b border-white/10 pb-8">
            <div>
                <h2 class="text-5xl sm:text-6xl font-black text-white uppercase tracking-tight leading-none">Pre-Built<br>Systems</h2>
            </div>
            <div class="mt-6 lg:mt-0 flex flex-col items-end gap-4">
                <p class="text-gray-400 text-sm font-medium max-w-sm text-right">Ready to ship. Professionally assembled and stress-tested for out-of-the-box performance.</p>
                <div class="hidden sm:flex items-center gap-2">
                    <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('prebuilt-carousel', -1)">
                        <i class="ph-bold ph-caret-left"></i>
                    </button>
                    <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel('prebuilt-carousel', 1)">
                        <i class="ph-bold ph-caret-right"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div id="prebuilt-carousel" class="flex gap-6 overflow-x-hidden pb-8 pt-4 -mx-6 px-6 lg:-mx-8 lg:px-8 scroll-px-6 lg:scroll-px-8 snap-x snap-mandatory hide-scroll-bar scroll-smooth" style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);">
            @foreach($prebuiltPcs as $index => $pc)
            <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl border border-white/5 flex flex-col group hover:border-primary/50 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] transition-all duration-500 relative overflow-hidden shrink-0 snap-start w-[85vw] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)]">
                <!-- Image Area -->
                <div class="relative w-full aspect-[4/3] bg-[#0a0a0a] overflow-hidden">
                    <img src="{{ $pc->image_url }}" alt="{{ $pc->name }}" class="w-full h-full object-cover mix-blend-lighten opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                    
                    <!-- Number -->
                    <div class="absolute top-4 left-4 text-primary font-mono text-sm tracking-widest">/0{{ $index + 1 }}</div>
                    
                    <!-- Optional Badges based on index -->
                    @if($index === 0)
                    <div class="absolute top-4 right-4 bg-primary text-white text-[10px] font-black tracking-widest uppercase px-3 py-1">Popular Choice</div>
                    @endif
                </div>
                
                <!-- Title Area -->
                <div class="p-5 border-b border-white/5 flex-grow flex flex-col relative z-10">
                    <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-2">
                        Prebuilt Gaming PC
                    </div>
                    <h3 class="text-white text-xl sm:text-2xl font-black tracking-wide uppercase mb-8 group-hover:text-primary transition-colors line-clamp-2">{{ $pc->name }}</h3>
                    
                    <!-- Specs Table -->
                    <div class="space-y-4 mt-auto mb-8 flex-grow">
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">CPU</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $pc->cpu->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">GPU</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $pc->gpu->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">RAM</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $pc->ram->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm border-b border-white/5 pb-2">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">Storage</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $pc->storage->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start text-sm">
                            <span class="text-primary w-16 shrink-0 uppercase text-[10px] font-black tracking-widest pt-1">PSU</span>
                            <span class="text-gray-300 font-medium line-clamp-2 text-[11px] sm:text-xs leading-relaxed">{{ $pc->powerSupply->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="pt-6 border-t border-white/10 flex items-end justify-between mt-auto">
                        <div>
                            <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-1">Starting At</div>
                            <div class="text-primary text-2xl font-black">₱{{ number_format($pc->price, 0) }}</div>
                        </div>
                        <a href="{{ route('ecommerce.prebuilt-overview', $pc->id) }}" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2">
                            Shop Now &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Browse By Category (Bento Grid) -->
    <section id="categories" class="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10 pt-10">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-16 border-b border-white/10 pb-8">
            <div>
                <h2 class="text-5xl sm:text-6xl font-black text-white uppercase tracking-tight leading-none">Explore<br>Categories</h2>
            </div>
            <div class="mt-6 lg:mt-0 max-w-sm text-right">
                <p class="text-gray-400 text-sm font-medium">Find exactly what you need. From ready-to-ship systems to fully custom workstations.</p>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <!-- Top Row: 2 Items -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Prebuilt Gaming PCs -->
                <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl relative overflow-hidden group h-[350px] lg:h-[400px] border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] flex flex-col justify-end">
                    <img src="https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Prebuilt Gaming PCs" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-30 mix-blend-lighten" style="mask-image: linear-gradient(to top, transparent, black 80%); -webkit-mask-image: linear-gradient(to top, transparent, black 80%);">
                    
                    <div class="relative z-10 p-8 border-t border-white/5 bg-black/60 backdrop-blur-md">
                        <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-2">Ready to ship</div>
                        <h3 class="text-white text-3xl font-black tracking-wide uppercase mb-4 group-hover:text-primary transition-colors">Prebuilt PCs</h3>
                        <p class="text-sm text-gray-400 mb-8 max-w-md">Browse through our full range of ready-to-ship prebuilt gaming PCs to find your perfect computer.</p>
                        <a href="{{ url('/prebuilt-pcs') }}" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-6 py-3 transition-all flex items-center gap-2 w-max">
                            Browse &rarr;
                        </a>
                    </div>
                </div>

                <!-- Custom PC Builder -->
                <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl relative overflow-hidden group h-[350px] lg:h-[400px] border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] flex flex-col justify-end">
                    <img src="https://images.unsplash.com/photo-1618339220157-daa2cd9ade56?q=80&w=1935&auto=format&fit=crop" alt="Custom PC Builder" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-30 mix-blend-lighten" style="mask-image: linear-gradient(to top, transparent, black 80%); -webkit-mask-image: linear-gradient(to top, transparent, black 80%);">
                    
                    <div class="relative z-10 p-8 border-t border-white/5 bg-black/60 backdrop-blur-md">
                        <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-2">Built for you</div>
                        <h3 class="text-white text-3xl font-black tracking-wide uppercase mb-4 group-hover:text-primary transition-colors">Custom Gaming PCs</h3>
                        <p class="text-sm text-gray-400 mb-8 max-w-md">Customize your PC with top brands like Intel, AMD, and ASUS, with no compatibility worries.</p>
                        <a href="{{ route('ecommerce.pc-configurator') }}" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-6 py-3 transition-all flex items-center gap-2 w-max">
                            Start Building &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: 3 Items -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- PC Forge -->
                <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl relative overflow-hidden group h-[320px] border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] flex flex-col justify-end">
                    <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?q=80&w=800&auto=format&fit=crop" alt="PC Forge" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-20 mix-blend-lighten">
                    
                    <div class="relative z-10 p-6 border-t border-white/5 bg-black/60 backdrop-blur-md">
                        <h3 class="text-white text-xl font-black tracking-wide uppercase mb-2 group-hover:text-primary transition-colors">PC Forge</h3>
                        <p class="text-[11px] text-gray-400 mb-6 line-clamp-2">Customize your ideal PC from scratch.</p>
                        <a href="{{ route('ecommerce.build-pc') }}" class="border border-primary/30 hover:border-primary text-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2 w-max">
                            Build Now &rarr;
                        </a>
                    </div>
                </div>

                <!-- Gaming Laptops -->
                <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl relative overflow-hidden group h-[320px] border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] flex flex-col justify-end">
                    <img src="https://images.unsplash.com/photo-1603302576837-37561b2e2302?q=80&w=800&auto=format&fit=crop" alt="Gaming Laptops" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-20 mix-blend-lighten">
                    
                    <div class="relative z-10 p-6 border-t border-white/5 bg-black/60 backdrop-blur-md">
                        <h3 class="text-white text-xl font-black tracking-wide uppercase mb-2 group-hover:text-primary transition-colors">Gaming Laptops</h3>
                        <p class="text-[11px] text-gray-400 mb-6 line-clamp-2">Immerse yourself on the go.</p>
                        <a href="{{ route('ecommerce.gaming-laptops') }}" class="border border-primary/30 hover:border-primary text-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2 w-max">
                            Browse &rarr;
                        </a>
                    </div>
                </div>

                <!-- Parts & Accessories -->
                <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl relative overflow-hidden group h-[320px] border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] flex flex-col justify-end">
                    <img src="https://images.unsplash.com/photo-1595225476474-87563907a212?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Parts" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-20 mix-blend-lighten">
                    
                    <div class="relative z-10 p-6 border-t border-white/5 bg-black/60 backdrop-blur-md">
                        <h3 class="text-white text-xl font-black tracking-wide uppercase mb-2 group-hover:text-primary transition-colors">Forge Store</h3>
                        <p class="text-[11px] text-gray-400 mb-6 line-clamp-2">Gear up with your favorite parts.</p>
                        <a href="{{ route('ecommerce.forge-store') }}" class="border border-primary/30 hover:border-primary text-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2 w-max">
                            Gear Up &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <!-- CTA Banner -->
    <section id="cta-section" class="relative w-full py-32 lg:py-40 flex items-center justify-center overflow-hidden border-t border-white/5 mt-10 transition-colors duration-1000">
        <!-- Background elements -->
        <div id="cta-bg-layer" class="absolute inset-0 liquid-glass bg-black/60 backdrop-blur-2xl opacity-0 transition-opacity duration-1000 z-0 pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#000] via-transparent to-transparent z-0 pointer-events-none"></div>
        
        <!-- Diagonal subtle lines (from inspiration) -->
        <div class="absolute left-[15%] md:left-[25%] top-[-50%] w-px h-[200%] bg-gradient-to-b from-transparent via-primary/30 to-transparent rotate-[12deg] z-0 opacity-50"></div>
        <div class="absolute right-[15%] md:right-[25%] top-[-50%] w-px h-[200%] bg-gradient-to-b from-transparent via-primary/30 to-transparent rotate-[12deg] z-0 opacity-50"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
        
        <div class="relative z-10 max-w-5xl mx-auto px-6 text-center flex flex-col items-center">
            <div class="text-primary text-[10px] sm:text-[11px] font-black tracking-[0.4em] sm:tracking-[0.6em] uppercase mb-10 flex items-center gap-4">
                <span class="w-10 h-px bg-primary/50"></span>
                R E A D Y _ T O _ B U I L D
                <span class="w-10 h-px bg-primary/50"></span>
            </div>
            
            <h2 class="text-5xl sm:text-7xl md:text-[5.5rem] font-black uppercase tracking-tight leading-[0.95] mb-10">
                <span class="text-white block mb-2 sm:mb-4">Stop Settling.</span>
                <span class="text-primary block drop-shadow-[0_0_30px_rgba(255,107,0,0.2)]">Start Winning.</span>
            </h2>
            
            <p class="text-gray-400 text-sm sm:text-base max-w-2xl mx-auto mb-12 font-medium leading-relaxed">
                Free shipping. Free setup support. 30-day no-questions return policy. Your next machine is three clicks away.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 w-full max-w-md mx-auto sm:max-w-none">
                <a href="{{ url('/configurator') }}" class="bg-primary hover:bg-[#ff8533] text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] px-10 py-5 transition-all flex items-center justify-center w-full sm:w-auto shadow-[0_0_30px_rgba(255,107,0,0.3)] hover:shadow-[0_0_50px_rgba(255,107,0,0.5)] transform hover:-translate-y-1">
                    Build Yours Now &rarr;
                </a>
                <a href="{{ url('/contact') }}" class="border border-white/20 hover:border-white text-gray-300 hover:text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] px-10 py-5 transition-all flex items-center justify-center w-full sm:w-auto bg-black/20 backdrop-blur-sm">
                    Talk To An Expert
                </a>
            </div>
        </div>
    </section>
    
    <x-footer />
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctaSection = document.getElementById('cta-section');
            const bgLayer = document.getElementById('cta-bg-layer');
            
            if (ctaSection && bgLayer) {
                // Remove CSS transition since we are controlling it frame-by-frame via JS
                bgLayer.classList.remove('transition-opacity', 'duration-1000');
                
                const updateOpacity = () => {
                    const rect = ctaSection.getBoundingClientRect();
                    const windowHeight = window.innerHeight;
                    
                    // Element is below viewport
                    if (rect.top > windowHeight) {
                        bgLayer.style.opacity = 0;
                        return;
                    }
                    
                    // Element is above viewport
                    if (rect.bottom < 0) {
                        bgLayer.style.opacity = 1;
                        return;
                    }
                    
                    // Start fading in as soon as the top hits the bottom of the screen (rect.top == windowHeight)
                    // Reach full opacity when the top reaches 50% of the screen height
                    const startFade = windowHeight;
                    const endFade = windowHeight * 0.5;
                    
                    let progress = (startFade - rect.top) / (startFade - endFade);
                    progress = Math.max(0, Math.min(1, progress));
                    
                    bgLayer.style.opacity = progress;
                };
                
                window.addEventListener('scroll', updateOpacity, { passive: true });
                window.addEventListener('resize', updateOpacity, { passive: true });
                updateOpacity(); // Initial check
            }
        });
    </script>
    


    

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])

    <!-- Load our compiled JavaScript (You can remove LiquidGlass initialization from inside this file) -->
    @vite('Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js')

    <script>
        function scrollCarousel(id, direction) {
            const el = document.getElementById(id);
            if (!el) return;
            // Get the first child to measure its width
            const card = el.children[0];
            if (!card) return;
            
            const amount = (card.offsetWidth + 24) * direction; // 24 is the gap (gap-6 = 1.5rem = 24px)
            const start = el.scrollLeft;
            const duration = 400;
            let startTime = null;

            el.style.scrollSnapType = 'none';

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                
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
