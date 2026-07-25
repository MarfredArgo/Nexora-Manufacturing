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
            .visualizer-slot {
            transition: all 0.5s ease;
            opacity: 0.2;
            fill: #333;
            stroke: #555;
            cursor: pointer;
        }
        .visualizer-slot:hover {
            stroke: #ff6b00;
            fill: rgba(255, 107, 0, 0.2);
            opacity: 0.8;
        }
        .visualizer-slot.active {
            opacity: 1;
            fill: rgba(255, 107, 0, 0.2);
            stroke: #ff6b00;
            filter: drop-shadow(0 0 8px rgba(255, 107, 0, 0.6));
        }

        @keyframes containerGlow {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 0, 0); border-color: transparent; }
            30% { box-shadow: 0 0 15px 2px rgba(255, 107, 0, 0.4); border-color: rgba(255, 107, 0, 0.5); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 0, 0); border-color: transparent; }
        }
        @keyframes shineSlide {
            0% { left: -100%; }
            100% { left: 150%; }
        }
        .animate-shine {
            animation: containerGlow 1.2s ease-out forwards;
        }
        .animate-shine::after {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 107, 0, 0.5), transparent);
            transform: skewX(-20deg);
            animation: shineSlide 1.2s ease-out forwards;
            pointer-events: none;
        }
        @keyframes shakeRow {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .animate-shake {
            animation: shakeRow 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        .visualizer-slot.active.error {
            animation: pulseError 1.5s infinite;
        }
        @keyframes pulseError {
            0%, 100% {
                fill: rgba(239, 68, 68, 0.1);
                stroke: rgba(239, 68, 68, 0.6);
                filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.3));
            }
            50% {
                fill: rgba(239, 68, 68, 0.5);
                stroke: rgba(239, 68, 68, 1);
                filter: drop-shadow(0 0 12px rgba(239, 68, 68, 0.8));
            }
        }
        .visualizer-text.error {
            animation: pulseTextError 1.5s infinite;
        }
        @keyframes pulseTextError {
            0%, 100% {
                fill: rgba(239, 68, 68, 0.5);
                opacity: 0.6;
            }
            50% {
                fill: rgba(239, 68, 68, 1);
                opacity: 1;
            }
        }
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
</head>
<body class="relative antialiased selection:bg-primary selection:text-white">

    @vite('Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js')


    <!-- Background Ambient Effects -->
    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>
    @vite('Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js')



    <x-navbar />
    <!-- Product Selection Modal -->
    <div id="product-modal" data-lenis-prevent class="fixed inset-0 bg-black/80 backdrop-blur-md z-[100] opacity-0 pointer-events-none transition-all duration-300 flex items-center justify-center p-4">
        <div class="liquid-glass-heavy w-full max-w-5xl max-h-[90vh] h-[800px] rounded-[2rem] border border-white/10 shadow-2xl flex flex-col transform scale-95 transition-transform duration-300 relative overflow-hidden bg-[#050505]">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-white/10 flex justify-between items-center bg-[#050505]/50 shrink-0">
                <div>
                    <h3 class="text-2xl font-black text-white" id="modal-title">Select Component</h3>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-primary flex items-center justify-center text-white transition-all shadow-lg hover:shadow-[0_0_15px_rgba(255,107,0,0.5)]">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Filters -->
            <div class="px-8 py-4 border-b border-white/10 bg-[#050505]/40 shrink-0 flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="relative flex-1 w-full">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" id="modal-search" placeholder="Search components..." class="w-full bg-white/5 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <select id="modal-sort" class="bg-[#050505] border border-white/10 rounded-xl py-2 px-3 text-white text-sm focus:outline-none focus:border-primary">
                            <option value="name_asc">Name: A-Z</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4 items-end" id="modal-dynamic-filters">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Price Range</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="modal-price-min" placeholder="Min" class="w-20 bg-white/5 border border-white/10 rounded-lg py-1.5 px-2 text-white text-xs focus:outline-none focus:border-primary">
                            <span class="text-gray-500">-</span>
                            <input type="number" id="modal-price-max" placeholder="Max" class="w-20 bg-white/5 border border-white/10 rounded-lg py-1.5 px-2 text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <div class="ml-auto flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="show-incompatible" class="sr-only">
                                <div class="block bg-white/10 border border-white/20 w-10 h-6 rounded-full group-hover:bg-white/20 transition-colors"></div>
                                <div class="dot absolute left-1 top-1 bg-gray-400 w-4 h-4 rounded-full transition-transform"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-400 group-hover:text-white transition-colors">Show Incompatible</span>
                            <style>
                                #show-incompatible:checked ~ .dot { transform: translateX(100%); background-color: #ff6b00; }
                                    .visualizer-slot {
            transition: all 0.5s ease;
            opacity: 0.2;
            fill: #333;
            stroke: #555;
            cursor: pointer;
        }
        .visualizer-slot:hover {
            stroke: #ff6b00;
            fill: rgba(255, 107, 0, 0.2);
            opacity: 0.8;
        }
        .visualizer-slot.active {
            opacity: 1;
            fill: rgba(255, 107, 0, 0.2);
            stroke: #ff6b00;
            filter: drop-shadow(0 0 8px rgba(255, 107, 0, 0.6));
        }
    </style>
                        </label>
                        <button id="modal-reset-filters" class="text-xs text-gray-400 hover:text-white transition-colors flex items-center gap-1 py-1.5 px-2">
                            <i class="ph ph-arrow-counter-clockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Content (Products List) -->
            <div id="modal-scroll-wrapper" class="p-8 overflow-y-auto custom-scrollbar flex-1 bg-[#050505]/30">
                <div id="modal-scroll-content">
                    <div class="flex flex-col gap-8" id="modal-products">
                        <!-- JavaScript will populate this -->
                    </div>
                </div>
            </div>
        </div>
    </div>



    <main class="flex-grow container mx-auto px-4 pt-32 pb-16 lg:pt-40 lg:pb-20 relative z-10">
        <div class="flex flex-col lg:flex-row gap-12 max-w-6xl mx-auto">
            
            <!-- Left Column: Visuals -->
            <div class="w-full lg:w-5/12 flex flex-col items-center">
                <!-- PC Image Container -->
                <div class="relative w-full flex items-center justify-center mb-8">
                    <svg class="w-full max-w-lg h-auto font-sans" viewBox="0 0 400 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Case Outline -->
                        <g onclick="openModal('Case')" class="cursor-pointer group">
                            <rect id="vis-case" class="visualizer-slot" x="20" y="20" width="360" height="460" rx="20" stroke="rgba(255,255,255,0.1)" stroke-width="4" fill="rgba(255,255,255,0.02)"/>
                            <rect x="30" y="30" width="340" height="440" rx="10" stroke="rgba(255,255,255,0.05)" stroke-width="2"/>
                            <text x="200" y="470" fill="rgba(255,255,255,0.2)" text-anchor="middle" font-size="10" font-weight="bold" class="group-hover:fill-primary transition-colors">CASE</text>
                        </g>
                        
                        <!-- Motherboard Area -->
                        <g onclick="openModal('Motherboard')" class="cursor-pointer group">
                            <rect id="vis-motherboard" class="visualizer-slot" x="40" y="40" width="260" height="300" rx="4"/>
                            <text id="text-Motherboard" x="170" y="65" fill="rgba(255,255,255,0.2)" text-anchor="middle" font-size="12" font-weight="bold" letter-spacing="2" class="visualizer-text group-hover:fill-primary transition-colors pointer-events-none">MOTHERBOARD</text>
                        </g>
                        
                        <!-- CPU Area -->
                        <g onclick="openModal('Processor')" class="cursor-pointer group">
                            <rect id="vis-cpu" class="visualizer-slot" x="140" y="100" width="60" height="60" rx="4"/>
                            <text id="text-Processor" x="170" y="134" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="10" font-weight="bold" class="visualizer-text group-hover:fill-primary transition-colors pointer-events-none">CPU</text>
                        </g>

                        <!-- Cooler Area (Front Radiator/Fans) -->
                        <g onclick="openModal('Cooling')" class="cursor-pointer group">
                            <rect id="vis-cooler" class="visualizer-slot" x="315" y="60" width="35" height="240" rx="4"/>
                            <text id="text-Cooling" x="332.5" y="180" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="10" font-weight="bold" transform="rotate(90, 332.5, 180)" letter-spacing="2" class="visualizer-text group-hover:fill-primary transition-colors pointer-events-none">COOLER</text>
                        </g>
                        
                        <!-- RAM -->
                        <g onclick="openModal('Memory')" class="cursor-pointer group">
                            <rect id="vis-memory" class="visualizer-slot" x="220" y="90" width="10" height="80" rx="2"/>
                            <rect id="vis-memory-2" class="visualizer-slot" x="240" y="90" width="10" height="80" rx="2"/>
                            <text id="text-Memory" x="235" y="80" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="10" font-weight="bold" class="visualizer-text group-hover:fill-primary transition-colors">RAM</text>
                        </g>
                        
                        <!-- GPU -->
                        <g onclick="openModal('Video Card')" class="cursor-pointer group">
                            <rect id="vis-gpu" class="visualizer-slot" x="40" y="220" width="240" height="50" rx="4"/>
                            <text id="text-Video Card" x="160" y="249" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="12" font-weight="bold" letter-spacing="1" class="visualizer-text group-hover:fill-primary transition-colors pointer-events-none">GRAPHICS CARD</text>
                        </g>
                        
                        <!-- Storage NVMe -->
                        <g onclick="openModal('Primary Storage')" class="cursor-pointer group">
                            <rect id="vis-ssd" class="visualizer-slot" x="140" y="280" width="60" height="15" rx="2"/>
                            <text id="text-Primary Storage" x="170" y="310" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="10" font-weight="bold" class="visualizer-text group-hover:fill-primary transition-colors">SSD</text>
                        </g>
                        
                        <!-- PSU -->
                        <g onclick="openModal('Power Supply')" class="cursor-pointer group">
                            <rect id="vis-psu" class="visualizer-slot" x="40" y="370" width="120" height="90" rx="4"/>
                            <text id="text-Power Supply" x="100" y="419" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="12" font-weight="bold" letter-spacing="1" class="visualizer-text group-hover:fill-primary transition-colors pointer-events-none">POWER</text>
                        </g>
                    </svg>
                </div>

                <!-- Configurator Instructions -->
                <div class="mt-4 bg-[#050505]/50 border border-primary/20 rounded-2xl p-6 text-center max-w-md backdrop-blur-md shadow-[0_0_30px_rgba(255,107,0,0.05)] relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/0 via-primary/5 to-primary/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 border border-primary/30">
                        <i class="ph-bold ph-hand-pointing text-2xl text-primary animate-pulse"></i>
                    </div>
                    <h3 class="text-white font-bold uppercase tracking-widest mb-2 text-sm">Interactive Builder</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">
                        Click on any highlighted component slot in the blueprint diagram above to swap parts, customize your rig, and check hardware compatibility in real-time.
                    </p>
                </div>
                <!-- Power & Performance Widgets -->
                <div class="mt-4 grid grid-cols-2 gap-4 w-full max-w-md">
                    <!-- Power Draw -->
                    <div class="bg-[#050505]/50 border border-white/5 rounded-2xl p-4 backdrop-blur-md shadow-lg flex flex-col justify-center transition-colors duration-500" id="power-widget-box">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1"><i class="ph-bold ph-lightning text-primary"></i> Power Draw</span>
                            <span id="power-draw-text" class="text-[10px] font-bold text-white">0W / 0W</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1.5 mb-1 overflow-hidden relative">
                            <div id="power-draw-bar" class="bg-primary h-1.5 rounded-full transition-all duration-500 w-0 relative z-10"></div>
                        </div>
                        <div id="power-draw-warning" class="text-[9px] text-red-500 font-black uppercase tracking-wide opacity-0 transition-opacity mt-1">PSU Upgrade Required!</div>
                    </div>
                    
                    <!-- Performance -->
                    <div class="bg-[#050505]/50 border border-white/5 rounded-2xl p-4 backdrop-blur-md shadow-lg flex flex-col justify-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1 mb-1"><i class="ph-bold ph-crosshair text-primary"></i> Est. Gaming FPS</span>
                        <div class="flex items-end gap-1">
                            <span id="fps-value" class="text-2xl font-black text-white leading-none">--</span>
                            <span class="text-[9px] font-bold text-gray-500 uppercase pb-0.5">@ 1440p High</span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Right Column: Details & Specs -->
            <div class="w-full lg:w-7/12 flex flex-col">
                <!-- Header -->
                <div class="mb-8 border-b border-white/10 pb-6">
                    <h1 class="text-4xl lg:text-5xl font-black text-white mb-4">{{ $product->name }}</h1>

                    <!-- Platform Toggle -->
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-gray-400 text-sm font-bold uppercase tracking-wider">Platform:</span>
                        <div class="inline-flex bg-[#050505] rounded-full p-1 border border-white/10 relative">
                            <!-- Toggle Indicator -->
                            <div id="platform-indicator" class="absolute top-1 bottom-1 left-1 w-[calc(50%-4px)] bg-primary rounded-full transition-all duration-300 ease-out shadow-[0_0_15px_rgba(255,107,0,0.4)]"></div>
                            
                            <button id="btn-intel" onclick="switchPlatform('Intel')" class="relative z-10 px-6 py-2 rounded-full text-sm font-bold transition-all text-white">Intel</button>
                            <button id="btn-amd" onclick="switchPlatform('AMD')" class="relative z-10 px-6 py-2 rounded-full text-sm font-bold transition-all text-gray-500 hover:text-white">AMD</button>
                        </div>
                    </div>

                    
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <!-- Rating -->
                        <div class="flex items-center gap-1 text-primary">
                            @for($i = 0; $i < 5; $i++)
                                <i class="ph-fill ph-star {{ $i < floor($product->rating) ? '' : 'text-gray-600' }}"></i>
                            @endfor
                            <span class="text-white font-bold ml-2">{{ $product->rating }}</span>
                        </div>
                        
                        <!-- Price & CTA -->
                        <div class="w-full mt-4 pt-4 border-t border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <!-- Breakdown Bar -->
                            <div class="flex-1 min-w-[200px]">
                                <div class="flex justify-between text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">
                                    <span>Budget Distribution</span>
                                </div>
                                <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden flex mb-2" id="price-breakdown-bar">
                                    <!-- JS populated -->
                                </div>
                                <div class="flex gap-4" id="price-breakdown-legend">
                                    <!-- JS populated -->
                                </div>
                            </div>

                            <div class="flex items-center gap-6 shrink-0">
                                <div class="text-right">
                                    @if($product->original_price && $product->original_price > $product->price)
                                        <div class="text-sm text-gray-500 line-through">P{{ number_format($product->original_price) }}</div>
                                    @endif
                                    <div class="text-3xl font-black text-white">P{{ number_format($product->price) }}</div>
                                </div>
                                <button onclick="addBuildToCart()" type="button" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-bold uppercase tracking-widest transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(255,107,0,0.4)] flex items-center gap-2">
                                    <i class="ph-bold ph-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specs List -->
                <div class="flex-grow">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-white uppercase tracking-widest flex items-center gap-2">
                            <i class="ph-bold ph-list-dashes text-primary"></i> Core Components
                        </h2>
                        <div class="flex items-center gap-2">
                            <button onclick="shareBuild()" class="text-[10px] font-bold uppercase tracking-widest text-primary border border-primary/20 hover:bg-primary/10 transition-all flex items-center gap-2 px-3 py-1.5 rounded-lg">
                                <i class="ph-bold ph-share-network"></i> Share
                            </button>
                            <button onclick="resetToDefault()" class="text-[10px] font-bold uppercase tracking-widest text-gray-500 border border-white/5 hover:text-white hover:bg-white/10 transition-all flex items-center gap-2 px-3 py-1.5 rounded-lg">
                                <i class="ph-bold ph-arrow-counter-clockwise"></i> Reset
                            </button>
                        </div>
                    </div>

                    @php
                        // Map specs to nice icons and labels
                        $specs = [
                            ['label' => 'Operating System', 'value' => $product->os ?? 'Windows 11 Home', 'icon' => 'ph-windows-logo', 'price' => 0],
                            ['label' => 'Case', 'value' => $product->pcCase->name ?? 'TechForge Standard Case', 'icon' => 'ph-computer-tower', 'price' => $product->pcCase->price ?? 0],
                            ['label' => 'Processor', 'value' => $product->intelCpu->name ?? 'N/A', 'icon' => 'ph-cpu', 'price' => $product->intelCpu->price ?? 0],
                            ['label' => 'Video Card', 'value' => $product->gpu->name ?? 'N/A', 'icon' => 'ph-graphics-card', 'price' => $product->gpu->price ?? 0],
                            ['label' => 'Memory', 'value' => $product->intelRam->name ?? 'N/A', 'icon' => 'ph-memory', 'price' => $product->intelRam->price ?? 0],
                            ['label' => 'Primary Storage', 'value' => $product->storage->name ?? 'N/A', 'icon' => 'ph-hard-drives', 'price' => $product->storage->price ?? 0],
                            ['label' => 'Power Supply', 'value' => $product->powerSupply->name ?? 'N/A', 'icon' => 'ph-plug', 'price' => $product->powerSupply->price ?? 0],
                            ['label' => 'Motherboard', 'value' => $product->intelMotherboard->name ?? 'N/A', 'icon' => 'ph-circuitry', 'price' => $product->intelMotherboard->price ?? 0],
                            ['label' => 'Cooling', 'value' => $product->cooler->name ?? 'Standard Air Cooler', 'icon' => 'ph-fan', 'price' => $product->cooler->price ?? 0],
                            
                        ];
                        
                        $editUrl = route('ecommerce.build-pc', [
                            'cpu' => $product->cpu->name ?? null, 
                            'gpu' => $product->gpu->name ?? null, 
                            'ram' => $product->ram->name ?? null, 
                            'storage' => $product->storage->name ?? null, 
                            'motherboard' => $product->motherboard->name ?? null, 
                            'psu' => $product->powerSupply->name ?? null, 
                            'case' => $product->pcCase->name ?? null, 
                            'cooler' => $product->cooler ?? null
                        ]);
                    @endphp

                    <div class="liquid-glass backdrop-blur-2xl bg-[#050505]/60 rounded-2xl border border-white/5 divide-y divide-white/5" id="specs-list">
                        @foreach($specs as $spec)
                        <div class="group relative overflow-hidden flex items-center justify-between p-4 sm:p-5 transition-colors hover:bg-white/5">
                            <div class="flex items-center gap-4 sm:gap-6 flex-1 min-w-0">
                                <div class="w-28 sm:w-32 shrink-0 flex items-center gap-2">
                                    <i class="{{ $spec['icon'] }} text-gray-500 text-lg transition-colors"></i>
                                    <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase">{{ $spec['label'] }}</span>
                                </div>
                                <div class="text-xs sm:text-sm font-bold text-gray-200 truncate transition-colors flex-1 component-value">
                                    {{ $spec['value'] }}
                                </div>
                            </div>
                            <div class="flex items-center gap-4 shrink-0 ml-4">
                                <div class="text-[10px] sm:text-xs font-bold text-primary text-right component-price min-w-[4rem]">
                                    @if($spec['price'] > 0)
                                        +P{{ number_format($spec['price']) }}
                                    @endif
                                </div>
                                <div class="action-slot flex justify-end min-w-[2rem]">
                                    <!-- Warning/Info badges will inject here via JS -->
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        
    </main>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[70] hidden items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-[#0f0f0f] border border-white/10 p-8 rounded-2xl shadow-[0_0_50px_rgba(255,107,0,0.2)] max-w-sm w-full text-center transform scale-95 transition-transform duration-300" id="success-modal-content">
            <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ph-fill ph-check-circle text-5xl text-primary drop-shadow-[0_0_15px_rgba(255,107,0,0.8)]"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-wider mb-2 uppercase">Build Complete!</h2>
            <p class="text-gray-400 mb-8 text-sm">Your custom TechForge PC has been successfully added to your cart.</p>
            <div class="flex gap-4">
                <button onclick="document.getElementById('success-modal').classList.add('opacity-0'); document.getElementById('success-modal-content').classList.add('scale-95'); setTimeout(() => { document.getElementById('success-modal').classList.add('hidden'); document.getElementById('success-modal').classList.remove('flex'); }, 300)" class="flex-1 bg-white/10 hover:bg-white/20 text-white py-3 rounded-xl font-bold transition-all border border-white/10 text-sm">
                    Continue Browsing
                </button>
                <button class="flex-1 bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white py-3 rounded-xl font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] text-sm">
                    View Cart
                </button>
            </div>
        </div>
    </div>

    <x-footer />

        <script src="{{ asset('js/configurator-engine.js') }}"></script>
    <script>
        const allComponents = @json($allComponents);
        
        const intelDefaults = {
            'Processor': @json($product->intelCpu),
            'Motherboard': @json($product->intelMotherboard),
            'Memory': @json($product->intelRam)
        };
        const amdDefaults = {
            'Processor': @json($product->amdCpu),
            'Motherboard': @json($product->amdMotherboard),
            'Memory': @json($product->amdRam)
        };

        const initialBuild = {
            'Processor': intelDefaults['Processor'],
            'Video Card': @json($product->gpu),
            'Memory': intelDefaults['Memory'],
            'Primary Storage': @json($product->storage),
            'Motherboard': intelDefaults['Motherboard'],
            'Power Supply': @json($product->powerSupply),
            'Case': @json($product->pcCase),
            'Cooling': @json($product->cooler)
        };

        const engine = new ConfiguratorEngine(allComponents, initialBuild);

        
        window.shareBuild = function() {
            if (typeof showNotification === 'function') {
                showNotification('Link Copied!', 'The link to your custom build has been copied to your clipboard.', 'success');
            }
        };

        function updateWidgets() {
            // Power Draw
            const psu = engine.getComponent('Power Supply');
            const psuWattage = psu ? parseInt(psu.wattage || 0) : 0;
            const requiredWattage = engine.getRequiredWattage();
            
            const powerText = document.getElementById('power-draw-text');
            const powerBar = document.getElementById('power-draw-bar');
            const powerWarning = document.getElementById('power-draw-warning');
            const powerBox = document.getElementById('power-widget-box');
            
            if (powerText && powerBar) {
                powerText.innerText = Math.ceil(requiredWattage) + 'W / ' + psuWattage + 'W';
                const percentage = psuWattage > 0 ? Math.min((requiredWattage / psuWattage) * 100, 100) : 0;
                powerBar.style.width = percentage + '%';
                
                powerBar.className = 'h-1.5 rounded-full transition-all duration-500 w-0 relative z-10'; // reset
                if (percentage > 90) {
                    powerBar.classList.add('bg-red-500', 'shadow-[0_0_10px_rgba(239,68,68,0.8)]');
                    powerWarning.style.opacity = '1';
                    powerBox.classList.add('border-red-500/30', 'bg-red-500/5');
                } else if (percentage > 75) {
                    powerBar.classList.add('bg-yellow-500', 'shadow-[0_0_10px_rgba(234,179,8,0.8)]');
                    powerWarning.style.opacity = '0';
                    powerBox.classList.remove('border-red-500/30', 'bg-red-500/5');
                } else {
                    powerBar.classList.add('bg-primary', 'shadow-[0_0_10px_rgba(255,107,0,0.8)]');
                    powerWarning.style.opacity = '0';
                    powerBox.classList.remove('border-red-500/30', 'bg-red-500/5');
                }
                
                setTimeout(() => { powerBar.style.width = percentage + '%'; }, 50);
            }
            
            // FPS Estimation
            const gpu = engine.getComponent('Video Card');
            const fpsValue = document.getElementById('fps-value');
            if (fpsValue) {
                if (gpu) {
                    let baseFps = 60;
                    const gpuName = gpu.name.toUpperCase();
                    if (gpuName.includes('4090') || gpuName.includes('5090') || gpuName.includes('7900 XTX')) baseFps = 165;
                    else if (gpuName.includes('4080') || gpuName.includes('5080') || gpuName.includes('7900')) baseFps = 144;
                    else if (gpuName.includes('4070') || gpuName.includes('7800')) baseFps = 110;
                    else if (gpuName.includes('4060') || gpuName.includes('7600')) baseFps = 85;
                    else if (gpuName.includes('3080') || gpuName.includes('3090')) baseFps = 100;
                    else baseFps = 75;
                    
                    fpsValue.innerText = baseFps;
                } else {
                    fpsValue.innerText = '--';
                }
            }
            
            // Price Breakdown
            const total = engine.calculateTotal();
            const cpu = engine.getComponent('Processor');
            
            if (total > 0 && gpu && cpu) {
                const gpuPrice = parseFloat(gpu.price);
                const cpuPrice = parseFloat(cpu.price);
                const otherPrice = total - gpuPrice - cpuPrice;
                
                const gpuPct = (gpuPrice / total) * 100;
                const cpuPct = (cpuPrice / total) * 100;
                const otherPct = (otherPrice / total) * 100;
                
                const bar = document.getElementById('price-breakdown-bar');
                const legend = document.getElementById('price-breakdown-legend');
                
                if (bar && legend) {
                    bar.innerHTML = `
                        <div style="width: ${gpuPct}%" class="bg-primary h-full transition-all duration-700 hover:brightness-125 cursor-help" title="GPU: P${gpuPrice.toLocaleString()}"></div>
                        <div style="width: ${cpuPct}%" class="bg-blue-500 h-full transition-all duration-700 hover:brightness-125 cursor-help" title="CPU: P${cpuPrice.toLocaleString()}"></div>
                        <div style="width: ${otherPct}%" class="bg-gray-600 h-full transition-all duration-700 hover:brightness-125 cursor-help" title="Other: P${otherPrice.toLocaleString()}"></div>
                    `;
                    
                    legend.innerHTML = `
                        <div class="flex items-center gap-1.5 text-[9px] font-bold text-gray-400 uppercase"><div class="w-1.5 h-1.5 rounded-full bg-primary shadow-[0_0_5px_rgba(255,107,0,0.8)]"></div> GPU (${Math.round(gpuPct)}%)</div>
                        <div class="flex items-center gap-1.5 text-[9px] font-bold text-gray-400 uppercase"><div class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_5px_rgba(59,130,246,0.8)]"></div> CPU (${Math.round(cpuPct)}%)</div>
                        <div class="flex items-center gap-1.5 text-[9px] font-bold text-gray-400 uppercase"><div class="w-1.5 h-1.5 rounded-full bg-gray-600"></div> Other (${Math.round(otherPct)}%)</div>
                    `;
                }
            }
        }

        window.resetToDefault = function() {
            if (currentPlatform === 'Intel') {
                engine.setComponent('Processor', intelDefaults['Processor']);
                engine.setComponent('Motherboard', intelDefaults['Motherboard']);
                engine.setComponent('Memory', intelDefaults['Memory']);
            } else {
                engine.setComponent('Processor', amdDefaults['Processor']);
                engine.setComponent('Motherboard', amdDefaults['Motherboard']);
                engine.setComponent('Memory', amdDefaults['Memory']);
            }
            
            engine.setComponent('Video Card', initialBuild['Video Card']);
            engine.setComponent('Primary Storage', initialBuild['Primary Storage']);
            engine.setComponent('Power Supply', initialBuild['Power Supply']);
            engine.setComponent('Case', initialBuild['Case']);
            engine.setComponent('Cooling', initialBuild['Cooling']);
            
            if (typeof showNotification === 'function') {
                showNotification('Reset Successful', 'Components have been restored to their defaults.', 'info');
            }
        };


        let currentPlatform = 'Intel';
        window.switchPlatform = function(platform) {
            if (currentPlatform === platform) return;
            currentPlatform = platform;

            const btnIntel = document.getElementById('btn-intel');
            const btnAmd = document.getElementById('btn-amd');
            const indicator = document.getElementById('platform-indicator');

            if (platform === 'Intel') {
                btnIntel.classList.replace('text-gray-500', 'text-white');
                btnAmd.classList.replace('text-white', 'text-gray-500');
                indicator.style.transform = 'translateX(0)';
                
                engine.setComponent('Processor', intelDefaults['Processor']);
                engine.setComponent('Motherboard', intelDefaults['Motherboard']);
                engine.setComponent('Memory', intelDefaults['Memory']);
                
                if (typeof playShineAnimation === 'function') {
                    playShineAnimation('Processor', true);
                    playShineAnimation('Motherboard', true);
                    playShineAnimation('Memory', true);
                }
                if (typeof showNotification === 'function') {
                    showNotification('Platform Updated', 'Switched to Intel architecture.', 'success');
                }
            } else {
                btnAmd.classList.replace('text-gray-500', 'text-white');
                btnIntel.classList.replace('text-white', 'text-gray-500');
                indicator.style.transform = 'translateX(100%)';
                
                engine.setComponent('Processor', amdDefaults['Processor']);
                engine.setComponent('Motherboard', amdDefaults['Motherboard']);
                engine.setComponent('Memory', amdDefaults['Memory']);
                
                if (typeof playShineAnimation === 'function') {
                    playShineAnimation('Processor', true);
                    playShineAnimation('Motherboard', true);
                    playShineAnimation('Memory', true);
                }
                if (typeof showNotification === 'function') {
                    showNotification('Platform Updated', 'Switched to AMD architecture.', 'success');
                }
            }
        };

        setTimeout(() => updateVisualizer(initialBuild), 100);
        
        let currentCategory = '';
        
        const typeMapping = {
            'Processor': 'Processor',
            'Video Card': 'Video Card',
            'Memory': 'Memory',
            'Primary Storage': 'Storage',
            'Motherboard': 'Motherboard',
            'Power Supply': 'Power Supply',
            'Case': 'Case',
            'Cooling': 'Cooling'
        };

        let availableComponents = [];

        const updateVisualizer = (build) => {
            document.querySelectorAll('.visualizer-slot').forEach(el => el.classList.remove('active', 'error'));
            document.querySelectorAll('.visualizer-text').forEach(el => el.classList.remove('error'));
            
            const mapping = {
                'Processor': ['vis-cpu'],
                'Motherboard': ['vis-motherboard'],
                'Memory': ['vis-memory', 'vis-memory-2'],
                'Video Card': ['vis-gpu'],
                'Primary Storage': ['vis-ssd'],
                'Power Supply': ['vis-psu'],
                'Case': ['vis-case'],
                'Cooling': ['vis-cooler']
            };

            Object.keys(build).forEach(cat => {
                if(build[cat] && mapping[cat]) {
                    const compatibility = engine.checkCompatibility(build[cat], cat);
                    const isError = !compatibility.compatible;
                    
                    mapping[cat].forEach(id => {
                        const el = document.getElementById(id);
                        if(el) {
                            el.classList.add('active');
                            if (isError) el.classList.add('error');
                        }
                    });
                    
                    const textEl = document.getElementById('text-' + cat);
                    if (textEl && isError) {
                        textEl.classList.add('error');
                    }
                }
            });
        };

        engine.subscribe((build) => {
            updateVisualizer(build);
            updatePriceUI();
            if(typeof updateWidgets === 'function') updateWidgets();
            
            // Sync all labels
            Object.keys(build).forEach(category => {
                const component = build[category];
                let conflictReason = null;
                if (component) {
                    const compatibility = engine.checkCompatibility(component, category);
                    if (!compatibility.compatible) {
                        conflictReason = compatibility.reason;
                    }
                }
                updateUIText(category, component, !component, conflictReason);
            });
        });

        function renderModalProducts() {
            const list = document.getElementById('modal-products');
            const search = document.getElementById('modal-search').value.toLowerCase();
            const sort = document.getElementById('modal-sort').value;
            const pMin = parseFloat(document.getElementById('modal-price-min').value) || 0;
            const pMax = parseFloat(document.getElementById('modal-price-max').value) || Infinity;
            const showIncompatible = document.getElementById('show-incompatible').checked;
            
            let filtered = availableComponents.filter(c => {
                const matchName = c.name.toLowerCase().includes(search);
                const matchPrice = c.price >= pMin && c.price <= pMax;
                return matchName && matchPrice;
            });
            
            if (sort === 'name_asc') filtered.sort((a,b) => a.name.localeCompare(b.name));
            if (sort === 'price_asc') filtered.sort((a,b) => a.price - b.price);
            if (sort === 'price_desc') filtered.sort((a,b) => b.price - a.price);

            const processed = filtered.map(c => {
                const compatibility = engine.checkCompatibility(c, currentCategory);
                return { ...c, compatible: compatibility.compatible, reason: compatibility.reason };
            });

            const finalDisplay = processed.filter(c => showIncompatible || c.compatible);

            const defaultComponentId = initialBuild[currentCategory] ? initialBuild[currentCategory].id : null;
            const defaultComps = finalDisplay.filter(c => c.id === defaultComponentId);
            const otherComps = finalDisplay.filter(c => c.id !== defaultComponentId);

            const buildCard = (c) => {
                const currentComp = engine.getComponent(currentCategory);
                const isSelected = currentComp && currentComp.id === c.id;
                const onClick = isSelected ? '' : `onclick="selectComponent(${c.id})"`;
                
                const opacityClass = !c.compatible ? 'opacity-60 grayscale' : '';
                
                let borderClass = 'border-white/5 hover:border-white/20 cursor-pointer';
                if (isSelected) borderClass = 'border-primary shadow-[0_0_15px_rgba(255,107,0,0.3)]';
                if (!c.compatible) borderClass = 'border-red-500/40 bg-red-500/5 cursor-pointer hover:border-red-500/60';

                let iconClass = 'ph-cube';
                if (c.component_category === 'Processor') iconClass = 'ph-cpu';
                else if (c.component_category === 'Video Card') iconClass = 'ph-graphics-card';
                else if (c.component_category === 'Memory') iconClass = 'ph-memory';
                else if (c.component_category === 'Storage') iconClass = 'ph-hard-drives';
                else if (c.component_category === 'Motherboard') iconClass = 'ph-circuitry';
                else if (c.component_category === 'Power Supply') iconClass = 'ph-plug';
                else if (c.component_category === 'Case') iconClass = 'ph-computer-tower';

                const imageHtml = c.image_url 
                    ? `<img src="${c.image_url}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-300 relative z-0">`
                    : `<i class="ph-light ${iconClass} text-6xl text-gray-500 group-hover:text-primary group-hover:scale-110 transition-all duration-300 relative z-0"></i>`;

                let badgesHtml = '';
                const renderBadge = (label, value) => {
                    if (value === null || value === undefined || value === '') return '';
                    return `<div class="flex items-center gap-1 bg-white/5 border border-white/10 px-2 py-0.5 rounded text-[9px] text-gray-300 uppercase tracking-wider"><span class="text-gray-500 font-medium">${label}:</span> <span class="font-bold">${value}</span></div>`;
                };

                let badgesContent = '';
                if (c.component_category === 'Processor') {
                    badgesContent += renderBadge('Cores', c.core_count);
                    badgesContent += renderBadge('PCC', c.core_clock);
                    badgesContent += renderBadge('PCBC', c.boost_clock);
                    badgesContent += renderBadge('Arch', c.microarchitecture);
                    badgesContent += renderBadge('TDP', c.tdp ? c.tdp + 'W' : null);
                    badgesContent += renderBadge('IG', c.integrated_graphics);
                } else if (c.component_category === 'Video Card') {
                    badgesContent += renderBadge('Chipset', c.chipset);
                    badgesContent += renderBadge('Memory', c.memory ? c.memory + 'GB' : null);
                    badgesContent += renderBadge('Boost', c.boost_clock);
                    badgesContent += renderBadge('Color', c.color);
                    badgesContent += renderBadge('Length', c.length_mm ? c.length_mm + 'mm' : null);
                } else if (c.component_category === 'Memory') {
                    badgesContent += renderBadge('Speed', c.speed ? c.speed + ' MT/s' : null);
                    badgesContent += renderBadge('Modules', c.modules);
                } else if (c.component_category === 'Storage') {
                    badgesContent += renderBadge('Capacity', c.capacity ? c.capacity + 'GB' : null);
                    badgesContent += renderBadge('Type', c.type);
                    badgesContent += renderBadge('Cache', c.cache);
                    badgesContent += renderBadge('Form', c.form_factor);
                    badgesContent += renderBadge('Interface', c.interface);
                } else if (c.component_category === 'Power Supply') {
                    badgesContent += renderBadge('Type', c.type); 
                    badgesContent += renderBadge('Efficiency', c.efficiency);
                    badgesContent += renderBadge('Wattage', c.wattage ? c.wattage + 'W' : null);
                    badgesContent += renderBadge('Modular', c.modular);
                    badgesContent += renderBadge('Color', c.color);
                } else if (c.component_category === 'Motherboard') {
                    badgesContent += renderBadge('Socket', c.socket);
                    const formFactorMap = {1: 'E-ATX', 2: 'ATX', 3: 'Micro-ATX', 4: 'Mini-ITX'};
                    badgesContent += renderBadge('Form', formFactorMap[c.form_factor] || c.form_factor);
                    badgesContent += renderBadge('Max RAM', c.memory_max);
                    badgesContent += renderBadge('Slots', c.memory_slots);
                    badgesContent += renderBadge('Color', c.color);
                } else if (c.component_category === 'Case') {
                    badgesContent += renderBadge('Type', c.type);
                    badgesContent += renderBadge('Color', c.color);
                    badgesContent += renderBadge('Panel', c.side_panel);
                } else if (c.component_category === 'Cooling') {
                    badgesContent += renderBadge('RPM', c.fan_rpm);
                    badgesContent += renderBadge('Noise', c.noise_level);
                    badgesContent += renderBadge('Color', c.color);
                    badgesContent += renderBadge('Radiator', c.radiator_size);
                }

                if (badgesContent) {
                    badgesHtml = `<div class="flex flex-wrap gap-1.5 mb-2 mt-1">${badgesContent}</div>`;
                }

                return `
                    <div class="liquid-glass p-4 rounded-2xl border ${borderClass} ${opacityClass} flex flex-col transition-all group relative" ${onClick}>
                        <div class="w-full h-32 mb-4 bg-white/5 rounded-xl flex items-center justify-center p-2 relative overflow-hidden" style="isolation: isolate;">
                            ${imageHtml}
                            ${isSelected ? `
                            <div class="absolute left-0 right-0 top-0 bottom-0 m-auto h-10 bg-primary group-hover:bg-white transition-colors duration-300 border-y border-primary/50 group-hover:border-white shadow-xl shadow-primary/20 z-20 flex items-center justify-center cursor-default pointer-events-none">
                                <span class="text-white group-hover:text-primary transition-colors duration-300 text-[12px] font-black uppercase tracking-widest">Selected</span>
                            </div>
                            ` : ''}
                            ${!c.compatible ? `
                            <div class="absolute left-0 right-0 top-0 bottom-0 m-auto min-h-[40px] py-1 bg-red-600/90 backdrop-blur-md border-y border-red-500 shadow-xl shadow-red-600/20 z-20 flex items-center justify-center cursor-default pointer-events-none px-3 text-center">
                                <span class="text-white text-[10px] font-black uppercase tracking-widest leading-tight drop-shadow-md">${c.reason}</span>
                            </div>
                            ` : ''}
                        </div>
                        <h4 class="font-bold text-white text-sm leading-tight ${badgesHtml ? 'mb-1' : 'mb-2'}">${c.name}</h4>
                        ${badgesHtml}
                        <div class="mt-auto flex justify-between items-end">
                            <p class="text-primary font-black">P${parseFloat(c.price).toLocaleString()}</p>
                        </div>
                    </div>
                `;
            };

            list.innerHTML = '';
            
            if (finalDisplay.length === 0) {
                list.innerHTML = '<div class="text-center py-12"><i class="ph ph-magnifying-glass text-4xl text-gray-600 mb-2"></i><p class="text-gray-500">No components found.</p></div>';
            } else {
                if (defaultComps.length > 0) {
                    list.innerHTML += `
                        <div class="mb-2">
                            <h4 class="text-white font-bold mb-4 uppercase tracking-widest text-sm flex items-center gap-2"><i class="ph-fill ph-star text-primary"></i> Default Component</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                ${defaultComps.map(buildCard).join('')}
                            </div>
                        </div>
                    `;
                }
                
                if (otherComps.length > 0) {
                    list.innerHTML += `
                        <div>
                            <h4 class="text-white font-bold mb-4 uppercase tracking-widest text-sm flex items-center gap-2"><i class="ph-fill ph-squares-four text-primary"></i> Available Options</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                ${otherComps.map(buildCard).join('')}
                            </div>
                        </div>
                    `;
                }
            }
        }

        function openModal(label) {
            const dbType = typeMapping[label];
            const modal = document.getElementById('product-modal');
            const box = modal.querySelector('.liquid-glass-heavy');
            const title = document.getElementById('modal-title');
            
            if(!dbType) {
                showNotification('Unavailable', 'No alternative parts available for ' + label, 'alert');
                return;
            }
            
            currentCategory = label;
            title.innerText = 'Select ' + label;
            
            availableComponents = allComponents.filter(c => c.component_category === dbType);
            
            document.getElementById('modal-search').value = '';
            document.getElementById('modal-sort').value = 'name_asc';
            document.getElementById('modal-price-min').value = '';
            document.getElementById('modal-price-max').value = '';

            renderModalProducts();
            
            modal.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            if (window.lenis) window.lenis.stop();
            if (window.startModalLenis) setTimeout(window.startModalLenis, 300); // wait for modal transition
        }

        function closeModal() {
            const modal = document.getElementById('product-modal');
            const box = modal.querySelector('.liquid-glass-heavy');
            modal.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            if (window.lenis) window.lenis.start();
            if (window.stopModalLenis) window.stopModalLenis();
        }

        document.getElementById('modal-search').addEventListener('input', renderModalProducts);
        document.getElementById('modal-sort').addEventListener('change', renderModalProducts);
        document.getElementById('modal-price-min').addEventListener('input', renderModalProducts);
        document.getElementById('modal-price-max').addEventListener('input', renderModalProducts);
        document.getElementById('show-incompatible').addEventListener('change', renderModalProducts);
        document.getElementById('modal-reset-filters').addEventListener('click', () => {
            document.getElementById('modal-search').value = '';
            document.getElementById('modal-sort').value = 'name_asc';
            document.getElementById('modal-price-min').value = '';
            document.getElementById('modal-price-max').value = '';
            document.getElementById('show-incompatible').checked = false;
            renderModalProducts();
        });

        function updatePriceUI() {
            const total = engine.calculateTotal();
            document.querySelector('.text-3xl.font-black.text-white').innerText = 'P' + total.toLocaleString();
        }

        function updateUIText(category, component, isMissing = false, conflictReason = null) {
            const specsList = document.getElementById('specs-list');
            const rows = specsList.querySelectorAll('.group');
            const text = component ? component.name : 'Select ' + category;
            const price = (component && component.price) ? component.price : 0;
            
            rows.forEach(row => {
                const labelEl = row.querySelector('.text-gray-400.uppercase');
                if (labelEl && labelEl.innerText.toLowerCase() === category.toLowerCase()) {
                    const valueEl = row.querySelector('.component-value');
                    const priceEl = row.querySelector('.component-price');
                    const actionSlot = row.querySelector('.action-slot');

                    if (priceEl && price > 0) {
                        priceEl.innerText = '+P' + parseFloat(price).toLocaleString();
                    } else if (priceEl) {
                        priceEl.innerText = '';
                    }

                    if (conflictReason) {
                        row.classList.add('bg-red-500/10');
                        valueEl.classList.add('text-red-400');
                        valueEl.classList.remove('text-gray-200');
                        valueEl.innerText = text;
                        
                        if (actionSlot) {
                            actionSlot.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/20 border border-red-500/30 text-[10px] uppercase tracking-widest font-black rounded-lg text-red-500 animate-pulse shadow-[0_0_15px_rgba(239,68,68,0.15)] whitespace-nowrap">
                                    <i class="ph-fill ph-warning"></i> ${conflictReason}
                                </span>
                            `;
                        }
                    } else if (isMissing) {
                        row.classList.add('bg-red-500/10');
                        valueEl.classList.add('text-red-400');
                        valueEl.classList.remove('text-gray-200');
                        valueEl.innerText = text;
                        if (actionSlot && !labelEl.innerText.toLowerCase().includes('warranty')) {
                            actionSlot.innerHTML = '';
                        }
                    } else {
                        row.classList.remove('bg-red-500/10');
                        valueEl.classList.remove('text-red-400');
                        valueEl.classList.add('text-gray-200');
                        valueEl.innerText = text;
                        if (actionSlot && !labelEl.innerText.toLowerCase().includes('warranty')) {
                            actionSlot.innerHTML = '';
                        }
                    }
                }
            });
        }

        function scrollToRowIfHidden(row, forceScroll = false) {
            setTimeout(() => {
                const rect = row.getBoundingClientRect();
                const isVisible = (rect.top >= 100) && (rect.bottom <= window.innerHeight - 100);
                if (!isVisible || forceScroll) {
                    if (window.lenis) {
                        window.lenis.scrollTo(row, { offset: -200, duration: 1.2, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
                    } else {
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }, 300); // Give modal time to close and Lenis to restart
        }

        function playShineAnimation(category, preventScroll = false) {
            const specsList = document.getElementById('specs-list');
            const rows = specsList.querySelectorAll('.group');
            rows.forEach(row => {
                const labelEl = row.querySelector('.text-gray-400.uppercase');
                if (labelEl && labelEl.innerText.toLowerCase() === category.toLowerCase()) {
                    row.classList.remove('animate-shine');
                    void row.offsetWidth; // Trigger reflow
                    row.classList.add('animate-shine');
                    setTimeout(() => row.classList.remove('animate-shine'), 1500);
                    if (!preventScroll) scrollToRowIfHidden(row, false);
                }
            });
        }

        function playShakeAnimation(categories) {
            const specsList = document.getElementById('specs-list');
            const rows = specsList.querySelectorAll('.group');
            let firstRow = null;
            
            rows.forEach(row => {
                const labelEl = row.querySelector('.text-gray-400.uppercase');
                if (labelEl && categories.map(c => c.toLowerCase()).includes(labelEl.innerText.toLowerCase())) {
                    row.classList.remove('animate-shake');
                    void row.offsetWidth; // Trigger reflow
                    row.classList.add('animate-shake');
                    setTimeout(() => row.classList.remove('animate-shake'), 500);
                    
                    if (!firstRow) firstRow = row;
                }
            });
            
            if (firstRow) {
                scrollToRowIfHidden(firstRow, true); // Force scroll to draw attention to error
            }
        }

        function addBuildToCart() {
            const currentBuild = engine.currentBuild;
            const essentialCats = ['Processor', 'Motherboard', 'Memory', 'Primary Storage', 'Video Card', 'Power Supply', 'Case', 'Cooling'];
            const missing = Object.entries(currentBuild).filter(([k,v]) => v === null && essentialCats.includes(k));
            if (missing.length > 0) {
                if (typeof showNotification === 'function') {
                    showNotification('Missing Components', 'Please select components for: ' + missing.map(m => m[0]).join(', '), 'alert');
                }
                return;
            }

            const incompatible = [];
            Object.keys(currentBuild).forEach(category => {
                if (currentBuild[category] && !engine.checkCompatibility(currentBuild[category], category).compatible) {
                    incompatible.push(category);
                }
            });
            if (incompatible.length > 0) {
                showNotification('Incompatible Build', 'Please resolve hardware conflicts for: ' + incompatible.join(', '), 'alert');
                return;
            }

            const btn = document.querySelector('button[onclick="addBuildToCart()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ph ph-spinner animate-spin"></i> Adding...';
            btn.disabled = true;

            const total = engine.calculateTotal();
            const caseComponent = engine.getComponent('Case');
            const imageUrl = caseComponent ? (caseComponent.image_url || caseComponent.image || '') : '';
            const customId = 'custom-pc-' + Date.now();

            window.addToCart(customId, 'Custom PC Build', total, imageUrl, 1, 'custom', engine.getCartPayload());

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 800);
        }

        function selectComponent(id) {
            const component = availableComponents.find(c => c.id === id);
            
            engine.setComponent(currentCategory, component);
            closeModal();
            
            const conflicts = [];
            Object.keys(engine.currentBuild).forEach(cat => {
                if (engine.currentBuild[cat]) {
                    const compat = engine.checkCompatibility(engine.currentBuild[cat], cat);
                    if (!compat.compatible) {
                        conflicts.push(cat);
                    }
                }
            });
            
            if (conflicts.length > 0) {
                playShakeAnimation(conflicts);
                if (typeof showNotification === 'function') {
                    showNotification('Compatibility Warning', 'The selected component has compatibility issues with your current build.', 'alert');
                }
            } else {
                playShineAnimation(currentCategory);
                if (typeof showNotification === 'function') {
                    showNotification('Component Updated', component.name + ' has been successfully added to your build.', 'success');
                }
            }
        }

        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 1000);
            }
        });
    </script>
    @vite('Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js')
    
    <!-- Lenis Smooth Scroll -->
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.39/dist/lenis.min.js"></script>
    <script>
        window.lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            direction: 'vertical',
            gestureDirection: 'vertical',
            smooth: true,
            mouseMultiplier: 1,
            smoothTouch: false,
            touchMultiplier: 2,
            infinite: false,
        });

        window.modalLenis = null;

        window.startModalLenis = function() {
            if (window.modalLenis) return;
            const wrapper = document.getElementById('modal-scroll-wrapper');
            const content = document.getElementById('modal-scroll-content');
            if (wrapper && content) {
                window.modalLenis = new Lenis({
                    wrapper: wrapper,
                    content: content,
                    duration: 1.2,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    direction: 'vertical',
                    smooth: true,
                });
            }
        };

        window.stopModalLenis = function() {
            if (window.modalLenis) {
                window.modalLenis.destroy();
                window.modalLenis = null;
            }
        };

        function raf(time) {
            if (window.lenis) window.lenis.raf(time);
            if (window.modalLenis) window.modalLenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    </script>
    <x-notification />
</body>
</html>
