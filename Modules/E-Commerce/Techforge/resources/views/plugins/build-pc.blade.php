<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>{{ config('app.name', 'TechForge') }} | PC Configurator</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            background: radial-gradient(circle, rgba(255, 107, 0, 0.25) 0%, rgba(255, 107, 0, 0) 65%);
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
            background: radial-gradient(circle, rgba(153, 0, 0, 0.3) 0%, rgba(153, 0, 0, 0) 65%);
            z-index: -1;
            pointer-events: none;
            animation: floatPulse2 25s ease-in-out infinite;
        }

        @keyframes floatPulse1 {
            0% { opacity: 0.3; transform: translate(0, 0) scale(0.8); }
            33% { opacity: 0.8; transform: translate(25vw, 15vh) scale(1.2); }
            66% { opacity: 0.4; transform: translate(-10vw, 30vh) scale(0.9); }
            100% { opacity: 0.3; transform: translate(0, 0) scale(0.8); }
        }

        @keyframes floatPulse2 {
            0% { opacity: 0.8; transform: translate(0, 0) scale(1.1); }
            33% { opacity: 0.3; transform: translate(-25vw, -15vh) scale(0.8); }
            66% { opacity: 0.7; transform: translate(15vw, -25vh) scale(1.3); }
            100% { opacity: 0.8; transform: translate(0, 0) scale(1.1); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #ff6b00; }

        /* PC Builder Styles */
        .component-slot {
            transition: all 0.3s ease;
        }
        .component-slot:hover {
            border-color: rgba(255, 107, 0, 0.4);
            background-color: rgba(255, 255, 255, 0.03);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.1);
        }

        .step-dot.active {
            background-color: #ff6b00;
            border-color: #ff6b00;
            box-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
            color: white;
        }
        .step-dot.completed {
            background-color: rgba(255, 107, 0, 0.2);
            border-color: #ff6b00;
            color: #ff6b00;
        }
        
        .visualizer-slot {
            transition: all 0.5s ease;
            opacity: 0.2;
            fill: #333;
            stroke: #555;
        }
        .visualizer-slot.active {
            opacity: 1;
            fill: rgba(255, 107, 0, 0.2);
            stroke: #ff6b00;
            filter: drop-shadow(0 0 8px rgba(255, 107, 0, 0.6));
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


    <!-- Main Builder Section -->
    <main class="relative pt-36 pb-10 lg:pt-48 lg:pb-16 overflow-hidden min-h-screen z-10">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header & Steps -->
            <div class="mb-6 liquid-glass-heavy rounded-3xl px-6 py-4 border border-white/10 shadow-xl">
                <!-- Budget -->
                <div class="w-full mb-8 max-w-4xl mx-auto relative">
                    <div class="flex justify-between items-end text-xs text-gray-400 mb-2 font-bold uppercase tracking-widest">
                        <div class="flex items-center gap-2">
                            <span>Budget</span>
                            <button onclick="openBudgetModal()" class="text-gray-500 hover:text-white transition-colors" title="Edit Budget">
                                <i class="ph ph-pencil-simple text-sm"></i>
                            </button>
                        </div>
                        <span><span id="budget-current" class="text-primary font-black">P0</span> / <span id="budget-max">P200,000</span></span>
                    </div>
                    <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden relative">
                        <div id="budget-bar" class="h-full bg-primary transition-all duration-500 w-0"></div>
                    </div>
                    <div id="budget-warning" class="hidden absolute top-full left-0 mt-1 text-red-500 text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                        <i class="ph-fill ph-warning-circle"></i> Warning: Build exceeds budget
                    </div>
                </div>

                <!-- Steps -->
                <div class="flex items-center justify-between max-w-4xl mx-auto overflow-x-auto custom-scrollbar pb-2 relative">
                    <div class="absolute top-4 left-0 w-full h-[1px] bg-white/10 -z-10"></div>
                    
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-1">
                        <div class="w-8 h-8 rounded-full border-2 border-primary bg-primary text-white flex items-center justify-center text-xs font-bold step-dot active">1</div>
                        <span class="text-[10px] text-white font-bold uppercase tracking-wider step-text">Core</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-2">
                        <div class="w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot">2</div>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text">Memory</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-3">
                        <div class="w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot">3</div>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text">Storage</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-4">
                        <div class="w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot">4</div>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text">Graphics</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-5">
                        <div class="w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot">5</div>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text">Power</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-6">
                        <div class="w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot">6</div>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text">Case</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group z-10" id="step-7">
                        <div class="w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot">7</div>
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text">Cooling</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
                
                <!-- Left Column: Components List -->
                <div class="xl:col-span-1 space-y-4" id="components-list">
                    <!-- JavaScript will inject component groups here -->
                </div>

                <!-- Center Column: Visualizer -->
                <div class="xl:col-span-2 relative z-20 flex flex-col gap-6">
                    <div class="liquid-glass-heavy rounded-3xl p-4 lg:p-6 border border-white/10 shadow-2xl flex items-center justify-center min-h-[450px] lg:min-h-[500px] relative overflow-hidden bg-black/40">
                        <!-- Abstract Glassmorphic PC Case Visualizer -->
                        <svg class="w-full max-w-lg h-auto font-sans" viewBox="0 0 400 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Case -->
                            <rect id="vis-case" class="visualizer-slot" x="10" y="10" width="380" height="480" rx="20"/>
                            <text id="vis-case-text" class="visualizer-text" x="200" y="475" fill="rgba(255,255,255,0.2)" text-anchor="middle" font-size="12" font-weight="bold" letter-spacing="2">CASE</text>
                            
                            <!-- Motherboard Area -->
                            <rect id="vis-motherboard" class="visualizer-slot" x="30" y="30" width="280" height="320" rx="10"/>
                            <text id="vis-motherboard-text" class="visualizer-text" x="170" y="55" fill="rgba(255,255,255,0.2)" text-anchor="middle" font-size="14" font-weight="bold" letter-spacing="2">MOTHERBOARD</text>
                            
                            <!-- CPU -->
                            <rect id="vis-cpu" class="visualizer-slot" x="135" y="90" width="70" height="70" rx="5"/>
                            <text id="vis-cpu-text" class="visualizer-text" x="170" y="130" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="12" font-weight="bold">CPU</text>
                            
                            <!-- RAM -->
                            <text id="vis-memory-text" class="visualizer-text" x="227" y="75" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="12" font-weight="bold">RAM</text>
                            <rect id="vis-memory" class="visualizer-slot" x="210" y="85" width="15" height="80" rx="4"/>
                            <rect id="vis-memory-2" class="visualizer-slot" x="230" y="85" width="15" height="80" rx="4"/>
                            
                            <!-- GPU -->
                            <rect id="vis-gpu" class="visualizer-slot" x="45" y="215" width="250" height="50" rx="5"/>
                            <text id="vis-gpu-text" class="visualizer-text" x="170" y="245" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="14" font-weight="bold" letter-spacing="1">GRAPHICS CARD</text>
                            
                            <!-- Storage NVMe -->
                            <rect id="vis-ssd" class="visualizer-slot" x="135" y="280" width="70" height="15" rx="4"/>
                            <text id="vis-ssd-text" class="visualizer-text" x="170" y="315" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="12" font-weight="bold">SSD</text>
                            
                            <!-- PSU -->
                            <rect id="vis-psu" class="visualizer-slot" x="30" y="370" width="120" height="100" rx="10"/>
                            <text id="vis-psu-text" class="visualizer-text" x="90" y="425" fill="rgba(255,255,255,0.4)" text-anchor="middle" font-size="14" font-weight="bold" letter-spacing="1">POWER</text>
                            
                            <!-- Cooler -->
                            <rect id="vis-cooler" class="visualizer-slot" x="330" y="45" width="45" height="255" rx="5"/>
                            <text id="vis-cooler-text" class="visualizer-text" x="352" y="172" fill="rgba(255,255,255,0.2)" text-anchor="middle" font-size="12" font-weight="bold" letter-spacing="2" transform="rotate(90 352 172)">COOLER</text>
                        </svg>

                        <!-- Floating Component Info Overlay -->
                        <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-0 transition-opacity duration-300" id="visualizer-overlay">
                            <div class="bg-[#050505]/80 backdrop-blur-md px-6 py-3 rounded-2xl border border-primary/30 shadow-[0_0_20px_rgba(255,107,0,0.3)] text-center">
                                <span class="text-xs text-primary font-bold tracking-widest uppercase block mb-1">Installed</span>
                                <h3 class="text-white font-bold" id="visualizer-label">Component</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Progress / Power Draw -->
                    <div class="liquid-glass rounded-2xl p-4 lg:p-5 border border-white/10 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="w-full">
                            <div class="flex justify-between text-xs text-gray-400 mb-2 font-bold uppercase tracking-widest">
                                <span>Components</span>
                                <span class="text-white"><span id="comp-count">0</span> / 8</span>
                            </div>
                            <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden">
                                <div id="comp-bar" class="h-full bg-white transition-all duration-500 w-0"></div>
                            </div>
                        </div>
                        <div class="w-px h-10 bg-white/10 hidden sm:block"></div>
                        <div class="w-full">
                            <div class="flex justify-between text-xs text-gray-400 mb-2 font-bold uppercase tracking-widest">
                                <span class="text-primary flex items-center gap-1"><i class="ph-fill ph-lightning"></i> Est. Power</span>
                                <span class="text-white">~<span id="power-draw">0</span>W</span>
                            </div>
                            <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden relative">
                                <div id="power-bar" class="h-full bg-gradient-to-r from-primary to-red-500 transition-all duration-500 w-0"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Analytics & Summary -->
                <div class="xl:col-span-1 space-y-4">
                    
                    <!-- Rating & Balance -->
                    <div class="liquid-glass rounded-3xl p-4 lg:p-5 border border-white/10 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-primary/5 group-hover:bg-primary/10 transition-colors"></div>
                        <div class="relative z-10 flex items-center gap-4">
                            <!-- Left: Build Score -->
                            <div class="w-[45%] flex flex-col items-center justify-center">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 text-center leading-tight">Build Rating</h4>
                                <div class="flex items-end justify-center gap-1 mb-3">
                                    <span class="text-5xl font-black text-white leading-none tracking-tighter" id="build-score">0</span>
                                </div>
                                <div id="build-badge" class="text-[10px] tracking-widest font-bold uppercase px-3 py-1 rounded-full hidden text-center">
                                </div>
                            </div>
                            
                            <!-- Divider -->
                            <div class="w-px h-32 bg-white/10"></div>
                            
                            <!-- Right: Radar Chart -->
                            <div class="w-[55%] flex flex-col items-center justify-center">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center leading-tight">Performance</h4>
                                <div class="relative w-full h-32">
                                    <canvas id="radarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Build Summary -->
                    <div class="liquid-glass rounded-3xl p-4 lg:p-5 border border-white/10">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Build Summary</h4>
                        
                        <div id="summary-parts-list" class="space-y-2 mb-4 max-h-40 overflow-y-auto custom-scrollbar pr-2">
                            <!-- JS injected parts -->
                        </div>

                        <div class="border-t border-white/10 pt-4 mb-6">
                            <div class="flex justify-between items-end">
                                <span class="text-gray-400 text-sm">Total Price</span>
                                <span class="text-3xl font-black text-white" id="total-price">P0</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button id="add-to-cart-btn" class="w-full bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white py-3.5 rounded-xl font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,107,0,0.5)] hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                Checkout Build <i class="ph-fill ph-check-circle text-lg"></i>
                            </button>
                            <div class="grid grid-cols-3 gap-2">
                                <button id="save-build-btn" class="w-full bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white py-2.5 rounded-xl font-semibold transition-all border border-white/10 text-xs flex items-center justify-center gap-1">
                                    <i class="ph ph-floppy-disk text-sm"></i> Save
                                </button>
                                <button id="load-build-btn" class="w-full bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white py-2.5 rounded-xl font-semibold transition-all border border-white/10 text-xs flex items-center justify-center gap-1">
                                    <i class="ph ph-upload-simple text-sm"></i> Load
                                </button>
                                <button id="reset-build" class="w-full bg-white/5 hover:bg-red-500/20 text-gray-300 hover:text-red-400 py-2.5 rounded-xl font-semibold transition-all border border-white/10 hover:border-red-500/50 text-xs flex items-center justify-center gap-1">
                                    <i class="ph ph-trash text-sm"></i> Clear
                                </button>
                            </div>
                        </div>
                        <input type="file" id="load-build-input" class="hidden" accept=".json">
                    </div>

                    <!-- Compare Builds -->
                    <div class="liquid-glass rounded-3xl p-4 lg:p-5 border border-white/10">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 text-center">Compare</h4>
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="text-center w-1/2 p-3 bg-white/5 rounded-xl border border-primary/30">
                                <div class="text-[10px] text-gray-400 mb-1">Current</div>
                                <div class="text-lg font-bold text-primary" id="compare-current">0</div>
                            </div>
                            <div class="text-center w-1/2 p-3 bg-white/5 rounded-xl border border-white/10">
                                <div class="text-[10px] text-gray-400 mb-1">Uploaded</div>
                                <div class="text-lg font-bold text-white transition-colors duration-300" id="compare-uploaded">--</div>
                            </div>
                        </div>
                        <button id="compare-upload-btn" class="w-full bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white py-2.5 rounded-xl font-semibold transition-all border border-white/10 text-xs flex items-center justify-center gap-1">
                            <i class="ph ph-upload-simple text-sm"></i> Upload JSON to Compare
                        </button>
                        <input type="file" id="compare-upload-input" class="hidden" accept=".json">
                    </div>

                </div>

            </div>
        </div>
    </main>

    <x-footer />

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
        }
        .visualizer-slot.active {
            opacity: 1;
            fill: rgba(255, 107, 0, 0.2);
            stroke: #ff6b00;
            filter: drop-shadow(0 0 8px rgba(255, 107, 0, 0.6));
        }
        .visualizer-text {
            transition: all 0.5s ease;
        }
        .visualizer-text.active-text {
            fill: #ff6b00 !important;
            opacity: 1 !important;
            filter: drop-shadow(0 0 5px rgba(255, 107, 0, 0.8));
        }
        @keyframes shineSweep {
            0% { transform: translateX(-100%) skewX(-20deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(200%) skewX(-20deg); opacity: 0; }
        }
        .animate-component-shine {
            animation: shineSweep 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
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

    <!-- Budget Target Modal -->
    <x-build-pc.budget-modal />
    
    <!-- Notification Toast -->
    <x-notification />
    <x-confirmation />

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[70] hidden items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-[#0f0f0f] border border-white/10 p-8 rounded-2xl shadow-[0_0_50px_rgba(255,107,0,0.2)] max-w-sm w-full text-center transform scale-95 transition-transform duration-300" id="success-modal-content">
            <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ph-fill ph-check-circle text-5xl text-primary drop-shadow-[0_0_15px_rgba(255,107,0,0.8)]"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-wider mb-2 uppercase">Build Complete!</h2>
            <p class="text-gray-400 mb-8 text-sm">Your custom TechForge PC has been successfully added to your cart.</p>
            <div class="flex gap-4">
                <button id="success-continue-btn" class="flex-1 bg-white/10 hover:bg-white/20 text-white py-3 rounded-xl font-bold transition-all border border-white/10 text-sm">
                    Continue Browsing
                </button>
                <button id="success-cart-btn" class="flex-1 bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white py-3 rounded-xl font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] text-sm">
                    View Cart
                </button>
            </div>
        </div>
    </div>

    <!-- PC Builder JavaScript -->
    <script src="{{ asset('js/configurator-engine.js') }}"></script>
    <script>
        window.PageConfig = {
            allComponents: @json($allComponents ?? []),
            cartAddRoute: '{{ route("ecommerce.cart.add") }}',
            csrfToken: document.querySelector('meta[name="csrf-token"]').content
        };
    </script>

    

    @vite(['Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js', 'Modules/E-Commerce/Techforge/resources/js/Pages/BuildPc/BuildPc.js'])
</body>
</html>
