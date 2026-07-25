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

    <!-- Category Hero -->
    <main class="relative pt-32 pb-16 lg:pt-40 lg:pb-20 overflow-hidden w-full">
        <div class="w-full relative z-10 group" style="mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);">
            <div class="absolute inset-0 w-full h-full">
                <img src="https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Custom PCs" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-[#050505] via-transparent to-[#050505] pointer-events-none"></div>
            </div>
            
            <div class="max-w-[1500px] mx-auto px-6 lg:px-8 relative z-10 py-16 md:py-24">
                <div class="w-full md:w-2/3">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-wide mb-4">PC Configurator</h1>
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed max-w-lg">Discover our meticulously curated tiers of custom-built performance machines, engineered specifically for your needs.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Category Content -->
    <main class="max-w-[1500px] mx-auto px-4 lg:px-8 pb-32 relative z-10">
        


        <div id="configs-container" class="grid grid-cols-1 lg:grid-cols-4 gap-6 xl:gap-8">
            @foreach($tiers as $tier)
            <div class="tier-group">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="h-px bg-white/10 flex-1"></div>
                    <h2 class="text-xl font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="ph {{ $tier == 'Apex' ? 'ph-rocket text-primary' : ($tier == 'Extreme' ? 'ph-star text-primary' : ($tier == 'Advanced' ? 'ph-lightning text-primary' : 'ph-game-controller text-primary')) }} text-2xl"></i> 
                        {{ $tier }}
                    </h2>
                    <div class="h-px bg-white/10 flex-1"></div>
                </div>

                <div class="flex flex-col gap-4">
                    @foreach($configs->where('tier', $tier) as $config)
                    <x-product-card :config="$config" type="custom" />
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <script>
        function switchPlatform(platform) {
            // Update tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.innerText.trim() === platform) {
                    btn.classList.add('bg-primary', 'text-white', 'shadow-[0_0_15px_rgba(255,107,0,0.4)]');
                    btn.classList.remove('text-gray-400');
                } else {
                    btn.classList.remove('bg-primary', 'text-white', 'shadow-[0_0_15px_rgba(255,107,0,0.4)]');
                    btn.classList.add('text-gray-400');
                }
            });

            // Update cards
            document.querySelectorAll('.config-card').forEach(card => {
                if (platform === 'All' || card.dataset.platform === platform) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

    <x-footer />


    

    <!-- Custom Slider JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function initSliders() {
                const minInput = document.getElementById('price-min');
                const maxInput = document.getElementById('price-max');
                const rangeMin = document.getElementById('range-min');
                const rangeMax = document.getElementById('range-max');
                const track = document.getElementById('slider-track');

                if(!minInput || !maxInput || !rangeMin || !rangeMax || !track) return;

                function updateTrack() {
                    const min = parseInt(rangeMin.value);
                    const max = parseInt(rangeMax.value);
                    const maxAllowed = parseInt(rangeMin.max);
                    
                    const leftPercent = (min / maxAllowed) * 100;
                    const rightPercent = 100 - ((max / maxAllowed) * 100);
                    
                    track.style.left = leftPercent + '%';
                    track.style.right = rightPercent + '%';
                }

                // Initial setup
                updateTrack();

                rangeMin.addEventListener('input', function() {
                    const val = parseInt(this.value);
                    const maxVal = parseInt(rangeMax.value);
                    if (val >= maxVal) {
                        this.value = maxVal - 1000;
                    }
                    minInput.value = this.value;
                    updateTrack();
                });

                rangeMax.addEventListener('input', function() {
                    const val = parseInt(this.value);
                    const minVal = parseInt(rangeMin.value);
                    if (val <= minVal) {
                        this.value = minVal + 1000;
                    }
                    maxInput.value = this.value;
                    updateTrack();
                });

                minInput.addEventListener('change', function() {
                    let val = parseInt(this.value);
                    if(isNaN(val)) val = 0;
                    const maxVal = parseInt(rangeMax.value);
                    if(val > maxVal) val = maxVal - 1000;
                    rangeMin.value = val;
                    this.value = val;
                    updateTrack();
                });

                maxInput.addEventListener('change', function() {
                    let val = parseInt(this.value);
                    if(isNaN(val)) val = parseInt(rangeMax.max);
                    const minVal = parseInt(rangeMin.value);
                    if(val < minVal) val = minVal + 1000;
                    rangeMax.value = val;
                    this.value = val;
                    updateTrack();
                });

                // Dispatch event when sliding finishes so AJAX reloads
                rangeMin.addEventListener('change', () => minInput.dispatchEvent(new Event('change', { bubbles: true })));
                rangeMax.addEventListener('change', () => maxInput.dispatchEvent(new Event('change', { bubbles: true })));
            }

            initSliders();

            const form = document.getElementById('filter-form');
            if (form) {
                form.addEventListener('change', function(e) {
                    if (e.target.id && (e.target.id.endsWith('-accordion') || e.target.id.endsWith('-toggle'))) {
                        return;
                    }

                    // Save state of open accordions
                    const openAccordions = Array.from(document.querySelectorAll('input[type="checkbox"][id$="-accordion"]:checked')).map(el => el.id);

                    const formData = new FormData(this);
                    const params = new URLSearchParams(formData);
                    const url = this.action + '?' + params.toString();
                    
                    const gridArea = document.getElementById('product-grid-area');
                    if (gridArea) gridArea.style.opacity = '0.5';

                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            const newSidebar = doc.getElementById('filter-sidebar');
                            const newGrid = doc.getElementById('product-grid-area');
                            
                            if (newSidebar) {
                                document.getElementById('filter-sidebar').innerHTML = newSidebar.innerHTML;
                                
                                // Restore accordion states
                                openAccordions.forEach(id => {
                                    const el = document.getElementById(id);
                                    if (el) el.checked = true;
                                });
                                
                                initSliders();
                            }
                            if (newGrid) {
                                gridArea.innerHTML = newGrid.innerHTML;
                                gridArea.style.opacity = '1';
                            }
                            
                            window.history.pushState({}, '', url);
                        })
                        .catch(err => {
                            console.error('Filtering failed:', err);
                            if (gridArea) gridArea.style.opacity = '1';
                        });
                });
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                });
            }
        });
    </script>

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])

    <!-- Load our compiled JavaScript (You can remove LiquidGlass initialization from inside this file) -->
    @vite(['Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js', 'Modules/E-Commerce/Techforge/resources/js/Category/Category.js'])
</body>
</html>
