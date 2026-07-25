@php
    $sections = collect($layout['sections'] ?? [])->keyBy('id');
    $enabledSections = collect($layout['sections'] ?? [])->filter(fn (array $section): bool => (bool) ($section['enabled'] ?? false));
    $hero = $sections->get('hero', []);
    $listingsSection = $sections->get('featured_listings', []);
    $promo = $sections->get('promo', []);
    $benefits = $sections->get('benefits', []);
    $store = $company->ecommerce_slug;
    $storefrontUrl = route('ecommerce.home', ['store' => $store]);
    $logoUrl = !empty($layout['logo_path']) ? (str_starts_with($layout['logo_path'], 'Modules/') ? Vite::asset($layout['logo_path']) : asset('storage/'.$layout['logo_path'])) : ($company->logoUrl() ?: asset('ecommerce/Nexora_Logo.png'));

    $storefrontCompany = request()->attributes->get('ecommerce_company');
    $storefrontName = $storefrontCompany?->company_name ?: ($layout['brand_name'] ?? 'Nexora Store');
    $storefrontVisitKey = 'storefront_visited_'.($storefrontCompany?->ecommerce_slug ?: 'store');

    $primaryHex = $layout['primary_color'] ?? '#ff6b00';
    $primaryR = hexdec(substr($primaryHex, 1, 2));
    $primaryG = hexdec(substr($primaryHex, 3, 2));
    $primaryB = hexdec(substr($primaryHex, 5, 2));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ $layout['brand_name'] ?? $storefrontName }} | {{ $layout['tagline'] ?? 'Nexora Storefront' }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '{{ $layout['primary_color'] ?? '#ff6b00' }}', hover: '{{ $layout['primary_color'] ?? '#ff6b00' }}CC', glow: '{{ $layout['primary_color'] ?? '#ff6b00' }}80' },
                        accent: '{{ $layout['accent_color'] ?? '#f59e0b' }}',
                        dark: { bg: '#050505', surface: '#121212' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    dropShadow: {
                        'glow': '0 0 15px {{ $layout['primary_color'] ?? '#ff6b00' }}80',
                    },
                    boxShadow: {
                        'glow': '0 0 20px {{ $layout['primary_color'] ?? '#ff6b00' }}4D',
                        'glow-lg': '0 0 30px {{ $layout['primary_color'] ?? '#ff6b00' }}26',
                    }
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

        /* Hide Scrollbars */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none;    /* Firefox */
        }

        /* Ambient Radial Light Blurs */
        .ambient-light-1 {
            position: fixed;
            top: -20%;
            left: -20%;
            width: 70vw;
            height: 70vw;
            background: radial-gradient(circle, {{ $layout['primary_color'] ?? '#ff6b00' }}59 0%, transparent 65%);
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
            background: radial-gradient(circle, {{ $layout['accent_color'] ?? '#990000' }}66 0%, transparent 65%);
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

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #050505; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--tw-color-primary); }

        @keyframes spinFastOnce { 0% { transform: rotate(0deg); } 100% { transform: rotate(720deg); } }
        .animate-spin-fast { animation: spinFastOnce 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        @keyframes slideTextOut { 0% { max-width: 0; opacity: 0; padding-left: 0; } 100% { max-width: 400px; opacity: 1; padding-left: 1.5rem; } }
        .animate-slide-text { animation: slideTextOut 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; animation-delay: 0.8s; overflow: hidden; white-space: nowrap; opacity: 0; max-width: 0; }

        @if(request()->routeIs('ecommerce.admin.layout.preview'))
        /* Pause all animations in layout editor preview */
        *, *::before, *::after {
            animation-play-state: paused !important;
        }
        @endif
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
</head>
<body class="relative antialiased selection:bg-primary selection:text-white">
    @if (session('success') || session('error'))
        <div class="fixed top-5 right-5 z-[120] max-w-sm rounded-xl border px-5 py-4 text-sm shadow-2xl {{ session('success') ? 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100' : 'border-red-400/40 bg-red-500/15 text-red-100' }}">
            {{ session('success') ?: session('error') }}
        </div>
    @endif

    @if (request('preview'))
        <div class="sticky top-0 z-[100] bg-amber-500/10 backdrop-blur-md border-b border-amber-500/20 px-5 py-2.5 text-center text-xs tracking-wider font-bold text-amber-400 uppercase shadow-[0_4px_30px_rgba(0,0,0,0.1)]">Preview mode Ã¢â‚¬â€ this draft is not public until you publish it.</div>
    @endif

    @if (!request('preview'))
    <div id="preloader" data-visit-key="{{ $storefrontVisitKey }}" class="fixed inset-0 bg-[#050505] z-[100] flex items-center justify-center transition-opacity duration-1000 ease-in-out">
        <script>
            if (!sessionStorage.getItem(@json($storefrontVisitKey))) {
                document.write(`
                    <div class="relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full animate-pulse"></div>
                        <div class="flex items-center relative z-10">
                            <img src="{{ $logoUrl }}" alt="{{ $storefrontName }} logo" class="h-20 w-auto object-contain animate-spin-fast">
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
    @endif

    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>

    <x-navbar :storefrontName="$storefrontName" :store="$store" :logoUrl="$logoUrl" :layout="$layout" />

    <div class="pt-[140px] lg:pt-[180px]">
    @foreach ($enabledSections as $section)
        @if ($section['id'] === 'hero')
            <main data-preview-section="hero" class="relative pb-0 overflow-hidden flex flex-col items-center justify-start mb-20 transition-all duration-300">
                @if($section['particles_enabled'] ?? false)
                <canvas id="hero-particles" class="absolute inset-0 w-full h-full pointer-events-none z-10 opacity-50"></canvas>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const canvas = document.getElementById('hero-particles');
                        if(!canvas) return;
                        const ctx = canvas.getContext('2d');
                        let w = canvas.width = window.innerWidth;
                        let h = canvas.height = canvas.parentElement.clientHeight;
                        window.addEventListener('resize', () => {
                            w = canvas.width = window.innerWidth;
                            h = canvas.height = canvas.parentElement.clientHeight;
                        });
                        const particlesCount = {{ $section['particles_count'] ?? 40 }};
                        const particlesSpeed = {{ $section['particles_speed'] ?? 1.0 }};

                        const particles = Array.from({length: particlesCount}, () => ({
                            x: Math.random() * w,
                            y: Math.random() * h,
                            r: Math.random() * 2 + 1,
                            dx: (Math.random() - 0.5) * 0.5 * particlesSpeed,
                            dy: ((Math.random() - 0.5) * 1 - 0.5) * particlesSpeed,
                            a: Math.random() * 0.5 + 0.1
                        }));
                        function draw() {
                            ctx.clearRect(0,0,w,h);
                            particles.forEach(p => {
                                ctx.beginPath();
                                ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
                                ctx.fillStyle = `rgba({{ $primaryR }}, {{ $primaryG }}, {{ $primaryB }}, ${p.a})`;
                                ctx.fill();
                                p.x += p.dx;
                                p.y += p.dy;
                                if(p.y < -10) p.y = h+10;
                                if(p.x < -10) p.x = w+10;
                                if(p.x > w+10) p.x = -10;
                            });
                            requestAnimationFrame(draw);
                        }
                        draw();
                    });
                </script>
                @endif
                <div class="relative w-full max-w-7xl mx-auto px-6 z-20 flex flex-col lg:flex-row items-center lg:items-center justify-between gap-12 lg:gap-8 flex-grow mb-12 lg:mb-16 mt-10">
                    <div class="w-full lg:w-1/2 flex flex-col justify-center items-start text-left relative z-30">
                        @php
                            $rawTitle = $section['title'] ?? '';
                            $parsedTitle = preg_replace('/\{(.*?)\}/', '<span class="text-primary drop-shadow-glow">$1</span>', $rawTitle);
                            $parsedTitle = str_replace('&lt;br&gt;', '<br>', $parsedTitle);

                            $titlePreset = $section['title_preset'] ?? 'h1';
                            $presetClasses = match($titlePreset) {
                                'h2' => 'text-4xl sm:text-5xl font-bold uppercase leading-tight tracking-wide',
                                'h3' => 'text-3xl sm:text-4xl font-semibold uppercase leading-snug',
                                'body' => 'text-lg sm:text-xl font-normal leading-relaxed',
                                default => 'text-5xl sm:text-6xl lg:text-7xl font-black uppercase leading-[1.1] tracking-wider'
                            };

                            $titleWidth = $section['title_width'] ?? 'auto';
                            $widthClass = match($titleWidth) {
                                'full' => 'w-full',
                                'narrow' => 'max-w-2xl',
                                default => 'w-auto'
                            };

                            $titleColor = $section['title_color'] ?? '';
                            $colorStyle = $titleColor ? "color: {$titleColor};" : "";
                            $colorClass = $titleColor ? "" : "text-white";
                        @endphp
                        <h1 data-preview-block="panel-hero-heading" data-parent-section="hero" class="{{ $presetClasses }} {{ $widthClass }} {{ $colorClass }} mb-8 relative drop-shadow-xl" style="{{ $colorStyle }}">
                            {!! $parsedTitle !!}
                        </h1>
                        <div data-preview-block="panel-hero-subheading" data-parent-section="hero">
                            <p class="text-gray-400 text-sm sm:text-base max-w-md leading-relaxed mb-10 font-medium">
                                {!! $section['body'] ?? '' !!}
                            </p>
                        </div>
                        <div data-preview-block="panel-hero-button" data-parent-section="hero">
                            @php
                                $buttons = $section['buttons'] ?? (!empty($section['button_label']) ? [['label' => $section['button_label'], 'url' => $section['button_url'] ?? '#products', 'style' => 'primary']] : []);
                                $alignmentMap = ['start' => 'justify-start', 'center' => 'justify-center', 'end' => 'justify-end'];
                                $btnAlign = $alignmentMap[$section['button_alignment'] ?? 'start'] ?? 'justify-start';
                            @endphp
                            @if (count($buttons) > 0)
                            <div class="flex flex-wrap items-center {{ $btnAlign }} gap-4 mb-4 w-full">
                                @foreach($buttons as $btn)
                                    @if(($btn['style'] ?? 'primary') === 'primary')
                                        <a href="{{ $btn['url'] }}" class="bg-primary text-black px-8 py-3.5 font-black hover:bg-white transition-colors uppercase tracking-widest text-xs sm:text-sm shadow-glow hover:shadow-[0_0_30px_rgba(255,255,255,0.5)]">
                                            {{ $btn['label'] }} &rarr;
                                        </a>
                                    @else
                                        <a href="{{ $btn['url'] }}" class="bg-transparent border-2 border-white/30 text-white px-8 py-3.5 font-black hover:border-primary hover:text-primary transition-colors uppercase tracking-widest text-xs sm:text-sm">
                                            {{ $btn['label'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @if(!empty($section['cta_subtext']))
                        <p class="text-gray-500 text-xs font-semibold tracking-wide uppercase mb-12">{{ $section['cta_subtext'] }}</p>
                        @endif

                        <!-- Stats Row -->
                        @if (!empty($section['hero_stats']))
                        <div data-preview-block="panel-hero-stats" data-parent-section="hero" class="grid grid-cols-3 gap-4 sm:gap-8 border-t border-white/10 pt-8 max-w-md w-full">
                            @foreach($section['hero_stats'] as $stat)
                            <div>
                                <div class="text-xl sm:text-2xl font-black text-white mb-1">{!! $stat['value'] !!}</div>
                                <div class="text-gray-500 text-[10px] sm:text-xs uppercase tracking-widest font-bold">{{ $stat['label'] }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <!-- Right Column: Image Frame & Thumbnails -->
                    <div data-preview-block="panel-hero-image" data-parent-section="hero" class="w-full lg:w-1/2 flex justify-center lg:justify-end mt-4 lg:mt-0 relative group z-20">
                        <!-- Diagonal background accent line -->
                        <div class="absolute -inset-20 bg-gradient-to-tr from-transparent via-primary/5 to-transparent transform -skew-x-12 pointer-events-none"></div>

                        @php
                            // Build a normalized display list Ã¢â‚¬â€ real configs or placeholders
                            $heroDisplayItems = [];
                            $placeholderImages = [
                                'https://images.unsplash.com/photo-1593640408182-31c228b2a5f2?w=800&q=80',
                                'https://images.unsplash.com/photo-1587202372616-b43abea06c2a?w=800&q=80',
                                'https://images.unsplash.com/photo-1616763355548-1b606f439f86?w=800&q=80',
                                'https://images.unsplash.com/photo-1547082299-de196ea013d6?w=800&q=80',
                            ];
                            $placeholderLabels = ['PHANTOM', 'TITAN', 'NEXUS', 'APEX'];
                            if (isset($customConfigs) && count($customConfigs) > 0) {
                                foreach ($customConfigs as $i => $cfg) {
                                    $heroDisplayItems[] = [
                                        'name'  => $cfg->name ?? $placeholderLabels[$i] ?? 'BUILD',
                                        'price' => "\u{20B1}" . number_format($cfg->price ?? 0, 0),
                                        'image' => $cfg->image_url ?: $placeholderImages[$i % 4],
                                        'label' => strtoupper(explode(' ', $cfg->name ?? $placeholderLabels[$i] ?? 'BUILD')[0]),
                                    ];
                                }
                            } elseif (!empty($section['image_path'])) {
                                // Single showcase image Ã¢â‚¬â€ create one item from it
                                $heroDisplayItems[] = [
                                    'name'  => $section['badge_text'] ?? 'FEATURED BUILD',
                                    'price' => '',
                                    'image' => asset('storage/' . $section['image_path']),
                                    'label' => 'FEATURED',
                                ];
                                // Pad with placeholders
                                foreach (range(1, 3) as $pi) {
                                    $heroDisplayItems[] = [
                                        'name'  => $placeholderLabels[$pi],
                                        'price' => '',
                                        'image' => $placeholderImages[$pi],
                                        'label' => $placeholderLabels[$pi],
                                    ];
                                }
                            } else {
                                // Fully placeholder mode
                                foreach (range(0, 3) as $pi) {
                                    $heroDisplayItems[] = [
                                        'name'  => $placeholderLabels[$pi],
                                        'price' => '',
                                        'image' => $placeholderImages[$pi],
                                        'label' => $placeholderLabels[$pi],
                                    ];
                                }
                            }
                        @endphp

                        <div class="flex flex-col gap-6 w-full max-w-[500px]">
                            <!-- Outer Frame Wrapper -->
                            <div class="relative w-full aspect-[4/3] lg:aspect-[4/5] xl:aspect-square">
                                <!-- Inner Image Container -->
                                <div class="absolute inset-0 w-full h-full overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] group/card">

                                    <!-- Corner Brackets -->
                                    <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-primary z-20 pointer-events-none"></div>
                                    <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-primary z-20 pointer-events-none"></div>
                                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-primary z-20 pointer-events-none"></div>
                                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-primary z-20 pointer-events-none"></div>

                                    <!-- Main Image -->
                                    <img id="hero-main-img" src="{{ $heroDisplayItems[0]['image'] }}" alt="{{ $heroDisplayItems[0]['name'] }}" class="w-full h-full object-cover transition-opacity duration-700 opacity-90 group-hover/card:opacity-100 mix-blend-lighten">

                                    <!-- Overlay (opacity-controlled) -->
                                    <div class="absolute inset-0 bg-black pointer-events-none transition-opacity duration-500" style="opacity: {{ ($section['overlay_opacity'] ?? 0) / 100 }};"></div>

                                    <!-- Info overlay at bottom -->
                                    <div class="absolute bottom-0 inset-x-0 h-1/2 bg-gradient-to-t from-[#050505] via-[#050505]/60 to-transparent flex flex-col justify-end p-6 sm:p-8 pointer-events-none z-10">
                                        <div class="flex justify-between items-end w-full">
                                            <div>
                                                <div id="hero-badge" class="text-primary text-[10px] font-black uppercase tracking-widest mb-1">{{ $section['badge_text'] ?? 'FEATURED BUILD' }}</div>
                                                <h3 id="hero-title" class="text-white text-2xl sm:text-3xl font-black uppercase tracking-tight">{{ $heroDisplayItems[0]['name'] }}</h3>
                                            </div>
                                            @if ($heroDisplayItems[0]['price'])
                                            <div class="text-right">
                                                <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">FROM</div>
                                                <div id="hero-price" class="text-primary text-xl sm:text-2xl font-black">{{ $heroDisplayItems[0]['price'] }}</div>
                                            </div>
                                            @else
                                            <div id="hero-price" class="hidden"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thumbnails Gallery -->
                            <div class="w-full flex justify-between gap-2 sm:gap-3 z-40 overflow-x-hidden" id="hero-thumbnails-container">
                                @foreach($heroDisplayItems as $index => $item)
                                <button data-config-index="{{ $index }}" class="hero-thumbnail flex-1 h-14 sm:h-20 {{ $index === 0 ? 'border-2 border-primary shadow-[0_0_20px_rgba(255,107,0,0.2)]' : 'border border-white/20 hover:border-primary/50' }} bg-[#050505] relative overflow-hidden group cursor-pointer transition-colors rounded-lg">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover mix-blend-lighten {{ $index === 0 ? 'opacity-90' : 'opacity-40 group-hover:opacity-80 grayscale group-hover:grayscale-0' }} transition-opacity">
                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black via-black/80 to-transparent p-2">
                                        <div class="text-[8px] sm:text-[10px] font-black tracking-widest uppercase text-center {{ $index === 0 ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">{{ $item['label'] }}</div>
                                    </div>
                                </button>
                                @endforeach
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const configsData = {!! json_encode($heroDisplayItems) !!};

                                    const thumbnails = document.querySelectorAll('#hero-thumbnails-container .hero-thumbnail');
                                    const mainImg = document.getElementById('hero-main-img');
                                    const titleEl = document.getElementById('hero-title');
                                    const priceEl = document.getElementById('hero-price');

                                    let autoScrollInterval;

                                    function startAutoScroll() {
                                        const cycleSecs = {{ $section['gallery_cycle'] ?? 5 }};
                                        const cycleMs = (cycleSecs > 0 ? cycleSecs : 5) * 1000;
                                        autoScrollInterval = setInterval(() => {
                                            let activeIndex = -1;
                                            thumbnails.forEach((t, i) => { if (t.classList.contains('border-primary')) activeIndex = i; });
                                            if (activeIndex !== -1) thumbnails[(activeIndex + 1) % thumbnails.length].click();
                                        }, cycleMs);
                                    }

                                    startAutoScroll();

                                    thumbnails.forEach((thumb) => {
                                        thumb.addEventListener('click', function() {
                                            clearInterval(autoScrollInterval);
                                            startAutoScroll();

                                            const idx = parseInt(this.getAttribute('data-config-index'));
                                            const data = configsData[idx];
                                            if (!data) return;

                                            if (titleEl) titleEl.textContent = data.name;
                                            if (priceEl) { priceEl.textContent = data.price; priceEl.style.display = data.price ? '' : 'none'; }

                                            if (mainImg) {
                                                mainImg.style.transition = 'opacity 0.3s ease-in-out';
                                                mainImg.style.opacity = 0;
                                                setTimeout(() => { mainImg.src = data.image; mainImg.style.opacity = 1; }, 300);
                                            }

                                            thumbnails.forEach(t => {
                                                t.className = 'hero-thumbnail flex-1 h-14 sm:h-20 border border-white/20 bg-[#050505] relative overflow-hidden group cursor-pointer hover:border-primary/50 transition-colors rounded-lg';
                                                const img = t.querySelector('img');
                                                if (img) img.className = 'w-full h-full object-cover mix-blend-lighten opacity-40 group-hover:opacity-80 transition-opacity grayscale group-hover:grayscale-0';
                                                const lbl = t.querySelector('div > div');
                                                if (lbl) lbl.className = 'text-[8px] sm:text-[10px] font-black tracking-widest uppercase text-gray-400 group-hover:text-white text-center';
                                            });

                                            this.className = 'hero-thumbnail flex-1 h-14 sm:h-20 border-2 border-primary bg-[#050505] relative overflow-hidden group cursor-pointer shadow-[0_0_20px_rgba(255,107,0,0.2)] rounded-lg';
                                            const aImg = this.querySelector('img');
                                            if (aImg) aImg.className = 'w-full h-full object-cover mix-blend-lighten opacity-90 transition-opacity';
                                            const aLbl = this.querySelector('div > div');
                                            if (aLbl) aLbl.className = 'text-white text-[8px] sm:text-[10px] font-black tracking-widest uppercase text-center';
                                        });
                                    });
                                });
                            </script>
                        </div>
                    </div></div>
                </div>

                <!-- Features Marquee -->
                @if (!empty($section['hero_marquee']))
                <div data-preview-block="panel-hero-marquee" data-parent-section="hero" class="w-full relative z-20 mt-auto overflow-hidden py-3 liquid-glass border-y border-white/5 backdrop-blur-xl">
                    <div class="w-full h-full flex" style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
                        <div class="flex animate-marquee items-center w-max">
                            @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center gap-6 sm:gap-12 px-3 sm:px-6">
                                @foreach($section['hero_marquee'] as $item)
                                <div class="flex items-center gap-6 sm:gap-12">
                                    <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">{{ $item['text'] }}</span>
                                    <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
                                </div>
                                @endforeach
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
                @endif
            </main>

        @elseif ($section['id'] === 'benefits')
            <div data-preview-section="benefits" class="w-full relative z-20 mt-auto overflow-hidden py-3 liquid-glass border-y border-white/5 backdrop-blur-xl mb-24 transition-all duration-300">
                <div class="w-full h-full flex" style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
                    <div class="flex animate-marquee items-center w-max">
                        @for($i=0; $i<4; $i++)
                        <div class="flex items-center gap-6 sm:gap-12 px-3 sm:px-6">
                            @foreach(['benefit_one', 'benefit_two', 'benefit_three'] as $benefitKey)
                            <div class="flex items-center gap-6 sm:gap-12">
                                <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">{{ $section[$benefitKey] ?? 'BENEFIT' }}</span>
                                <div class="w-1.5 h-1.5 bg-primary transform rotate-45 shadow-[0_0_5px_rgba(255,107,0,0.5)]"></div>
                            </div>
                            @endforeach
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

        @elseif ($section['id'] === 'tiers')
            @php
                $blocks = $section['blocks'] ?? [];
                $displayTiers = collect();
                foreach($blocks as $idx => $b) {
                    if (!empty($b['listing_id'])) {
                        $listing = \Modules\Ecommerce\Models\StorefrontListing::find($b['listing_id']);
                        if ($listing) {
                            // Block-level description override
                            if (!empty($b['description'])) {
                                $listing->description = $b['description'];
                            }
                            $displayTiers->push($listing);
                        } else {
                            $displayTiers->push((object)['name' => 'Missing Item', 'image_url' => '', 'description' => 'Item not found.', 'price' => 0]);
                        }
                    } else {
                        $displayTiers->push((object)['name' => 'Select an Item (' . ($idx + 1) . ')', 'image_url' => '', 'description' => 'Select an item in the layout editor.', 'price' => 0]);
                    }
                }

                // If no blocks at all but in preview, show placeholders
                if ($displayTiers->isEmpty() && ($preview ?? false)) {
                    $displayTiers->push((object)['name' => 'Sample Tier 1', 'image_url' => '', 'description' => 'This is a placeholder description for the tier item.', 'price' => 50000]);
                    $displayTiers->push((object)['name' => 'Sample Tier 2', 'image_url' => '', 'description' => 'This is a placeholder description for the tier item.', 'price' => 75000]);
                    $displayTiers->push((object)['name' => 'Sample Tier 3', 'image_url' => '', 'description' => 'This is a placeholder description for the tier item.', 'price' => 100000]);
                    $displayTiers->push((object)['name' => 'Sample Tier 4', 'image_url' => '', 'description' => 'This is a placeholder description for the tier item.', 'price' => 125000]);
                }

                $isCarousel = true;
            @endphp
            @if($displayTiers->isNotEmpty())
            <section data-preview-section="tiers" class="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10 pt-20 transition-all duration-300">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-16 border-b border-white/10 pb-8">
                    <div data-preview-block="panel-tiers-heading" data-parent-section="tiers">
                        @php
                            $parsedTitle = $section['title'] ?? 'Select Your Tier';
                        @endphp
                        <h2 class="text-5xl sm:text-6xl font-black text-white uppercase tracking-tight leading-none">{!! $parsedTitle !!}</h2>
                    </div>
                    <div class="mt-6 lg:mt-0 flex flex-col items-end gap-4" data-preview-block="panel-tiers-subheading" data-parent-section="tiers">
                        <p class="text-gray-400 text-sm font-medium max-w-sm text-right">{!! $section['body'] ?? '' !!}</p>
                        @if($isCarousel)
                        <div class="hidden sm:flex items-center gap-2">
                            <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel(event, 'tiers-carousel', -1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel(event, 'tiers-carousel', 1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="{{ $isCarousel ? 'relative w-full overflow-hidden' : '' }}" {!! $isCarousel ? 'style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);"' : '' !!}>
                    <div class="{{ $isCarousel ? 'flex gap-6 overflow-x-auto pb-8 pt-4 -mx-6 px-6 lg:-mx-8 lg:px-8 scroll-px-6 lg:scroll-px-8 snap-x snap-mandatory scrollbar-hide scroll-smooth' : 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6' }}" {!! $isCarousel ? 'id="tiers-carousel"' : '' !!}>
                        @foreach($displayTiers as $index => $listing)
                        <div data-preview-block="panel-tiers-block-{{ $index }}" data-parent-section="tiers" class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl border border-white/5 flex flex-col group hover:border-primary/50 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] transition-all duration-500 relative overflow-hidden {{ $isCarousel ? 'shrink-0 snap-start w-[85vw] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)]' : '' }}">
                        <!-- Image Area -->
                        <div class="relative w-full aspect-[4/3] bg-[#0a0a0a] overflow-hidden">
                            <img src="{{ $listing->image_url ? asset('storage/'.$listing->image_url) : 'https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" alt="{{ $listing->name }}" class="w-full h-full object-cover mix-blend-lighten opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">

                            <!-- Number -->
                            <div class="absolute top-4 left-4 text-primary font-mono text-sm tracking-widest">/0{{ $index + 1 }}</div>
                        </div>

                        <!-- Title Area -->
                        <div class="p-5 border-b border-white/5 flex-grow flex flex-col relative z-10">
                            <h3 class="text-white text-xl sm:text-2xl font-black tracking-wide uppercase mb-4 group-hover:text-primary transition-colors">{{ $listing->name }}</h3>

                            <!-- Specs/Features generic list -->
                            <div class="space-y-4 mt-auto mb-8 flex-grow">
                                <p class="text-gray-300 font-medium text-[11px] sm:text-xs leading-relaxed line-clamp-4">{{ $listing->description ?? 'No description available for this item. Contact support for more details.' }}</p>
                            </div>

                            <!-- Footer -->
                            <div class="pt-6 border-t border-white/10 flex items-end justify-between mt-auto">
                                <div>
                                    <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-1">Price</div>
                                    <div class="text-primary text-2xl font-black">&#8369;{{ number_format($listing->price, 0) }}</div>
                                </div>
                                @if(isset($listing->id))
                                <a href="{{ route('ecommerce.listings.show', ['store' => $store, 'listing' => $listing]) }}" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2">
                                    Details &rarr;
                                </a>
                                @else
                                <div class="border border-primary/50 text-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 opacity-50 flex items-center gap-2">
                                    Details &rarr;
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

        @elseif ($section['id'] === 'prebuilts')
            @php
                $blocks = $section['blocks'] ?? [];
                $displayPrebuilts = collect();

                if (isset($section['blocks']) && is_array($section['blocks'])) {
                    foreach ($section['blocks'] as $idx => $block) {
                        if (!empty($block['listing_id'])) {
                            $listing = \Modules\Ecommerce\Models\StorefrontListing::find($block['listing_id']);
                            if ($listing) {
                                // Block-level description override
                                if (!empty($block['description'])) {
                                    $listing->description = $block['description'];
                                }
                                $displayPrebuilts->push($listing);
                            } else {
                                $displayPrebuilts->push((object)['name' => 'Missing Item', 'image_url' => '', 'description' => 'Item not found.', 'price' => 0]);
                            }
                        } else {
                            $displayPrebuilts->push((object)['name' => 'Select an Item (' . ($idx + 1) . ')', 'image_url' => '', 'description' => 'Select an item in the layout editor.', 'price' => 0]);
                        }
                    }
                }

                if ($displayPrebuilts->isEmpty() && ($preview ?? false)) {
                    $displayPrebuilts->push((object)['name' => 'Sample Prebuilt 1', 'image_url' => '', 'price' => 60000]);
                    $displayPrebuilts->push((object)['name' => 'Sample Prebuilt 2', 'image_url' => '', 'price' => 85000]);
                    $displayPrebuilts->push((object)['name' => 'Sample Prebuilt 3', 'image_url' => '', 'price' => 110000]);
                    $displayPrebuilts->push((object)['name' => 'Sample Prebuilt 4', 'image_url' => '', 'price' => 135000]);
                    $displayPrebuilts->push((object)['name' => 'Sample Prebuilt 5', 'image_url' => '', 'price' => 160000]);
                }

                $isCarousel = true;
            @endphp
            @if($displayPrebuilts->isNotEmpty())
            <section data-preview-section="prebuilts" class="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10 pt-10 transition-all duration-300">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-16 border-b border-white/10 pb-8">
                    <div data-preview-block="panel-prebuilts-heading" data-parent-section="prebuilts">
                        @php
                            $parsedTitle = $section['title'] ?? 'Pre-Built Systems';
                        @endphp
                        <h2 class="text-5xl sm:text-6xl font-black text-white uppercase tracking-tight leading-none">{!! $parsedTitle !!}</h2>
                    </div>
                    <div class="mt-6 lg:mt-0 flex flex-col items-end gap-4" data-preview-block="panel-prebuilts-subheading" data-parent-section="prebuilts">
                        <p class="text-gray-400 text-sm font-medium max-w-sm text-right">{!! $section['body'] ?? '' !!}</p>
                        @if($isCarousel)
                        <div class="hidden sm:flex items-center gap-2">
                            <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel(event, 'prebuilt-carousel', -1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:border-primary hover:text-primary transition-colors" onclick="scrollCarousel(event, 'prebuilt-carousel', 1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="{{ $isCarousel ? 'relative w-full overflow-hidden' : '' }}" {!! $isCarousel ? 'style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);"' : '' !!}>
                    <div class="{{ $isCarousel ? 'flex gap-6 overflow-x-auto pb-8 pt-4 -mx-6 px-6 lg:-mx-8 lg:px-8 scroll-px-6 lg:scroll-px-8 snap-x snap-mandatory scrollbar-hide scroll-smooth' : 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6' }}" {!! $isCarousel ? 'id="prebuilt-carousel"' : '' !!}>
                        @foreach($displayPrebuilts as $index => $listing)
                        <div data-preview-block="panel-prebuilts-block-{{ $index }}" data-parent-section="prebuilts" class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl border border-white/5 flex flex-col group hover:border-primary/50 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] transition-all duration-500 relative overflow-hidden {{ $isCarousel ? 'shrink-0 snap-start w-[85vw] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)]' : '' }}">
                        <!-- Image Area -->
                        <div class="relative w-full aspect-[4/3] bg-[#0a0a0a] overflow-hidden">
                            <img src="{{ $listing->image_url ? asset('storage/'.$listing->image_url) : 'https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" alt="{{ $listing->name }}" class="w-full h-full object-cover mix-blend-lighten opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                        </div>

                        <!-- Title Area -->
                        <div class="p-5 border-b border-white/5 flex-grow flex flex-col relative z-10">
                            <h3 class="text-white text-xl sm:text-2xl font-black tracking-wide uppercase mb-4 group-hover:text-primary transition-colors line-clamp-2">{{ $listing->name }}</h3>

                            <!-- Description -->
                            <div class="space-y-4 mt-auto mb-8 flex-grow">
                                <p class="text-gray-300 font-medium text-[11px] sm:text-xs leading-relaxed line-clamp-4">{{ $listing->description ?? 'No description available for this item.' }}</p>
                            </div>
                            <div class="pt-6 border-t border-white/10 flex items-end justify-between mt-auto">
                                <div>
                                    <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-1">Price</div>
                                    <div class="text-primary text-2xl font-black">&#8369;{{ number_format($listing->price, 0) }}</div>
                                </div>
                                @if(isset($listing->id))
                                <a href="{{ route('ecommerce.listings.show', ['store' => $store, 'listing' => $listing]) }}" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 transition-all flex items-center gap-2">
                                    Shop Now &rarr;
                                </a>
                                @else
                                <div class="border border-primary/50 text-primary text-[10px] font-black uppercase tracking-widest px-4 py-2 opacity-50 flex items-center gap-2">
                                    Shop Now &rarr;
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

        @elseif ($section['id'] === 'categories')
            <section data-preview-section="categories" id="categories" class="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10 pt-10 transition-all duration-300">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-16 border-b border-white/10 pb-8">
                    <div data-preview-block="panel-categories-heading" data-parent-section="categories">
                        @php
                            $parsedTitle = $section['title'] ?? 'Explore Categories';
                        @endphp
                        <h2 class="text-5xl sm:text-6xl font-black text-white uppercase tracking-tight leading-none">{!! $parsedTitle !!}</h2>
                    </div>
                    <div class="mt-6 lg:mt-0 max-w-sm text-right" data-preview-block="panel-categories-subheading" data-parent-section="categories">
                        <p class="text-gray-400 text-sm font-medium">{!! $section['body'] ?? '' !!}</p>
                    </div>
                </div>

                <div class="flex flex-col gap-6" data-preview-block="panel-categories-grid" data-parent-section="categories">
                    <!-- Top Row: 2 Items -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Prebuilt Gaming PCs -->
                        <div class="liquid-glass backdrop-blur-2xl bg-black/40 rounded-2xl relative overflow-hidden group h-[350px] lg:h-[400px] border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-[0_0_30px_rgba(255,107,0,0.15)] flex flex-col justify-end">
                            <img src="https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Prebuilt PCs" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-30 mix-blend-lighten" style="mask-image: linear-gradient(to top, transparent, black 80%); -webkit-mask-image: linear-gradient(to top, transparent, black 80%);">

                            <div class="relative z-10 p-8 border-t border-white/5 bg-black/60 backdrop-blur-md">
                                <div class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-2">Ready to ship</div>
                                <h3 class="text-white text-3xl font-black tracking-wide uppercase mb-4 group-hover:text-primary transition-colors">Prebuilt PCs</h3>
                                <p class="text-sm text-gray-400 mb-8 max-w-md">Browse through our full range of ready-to-ship prebuilt gaming PCs.</p>
                                <a href="#" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-6 py-3 transition-all flex items-center gap-2 w-max">
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
                                <p class="text-sm text-gray-400 mb-8 max-w-md">Customize your PC with top brands, with no compatibility worries.</p>
                                <a href="#" class="border border-primary/50 hover:border-primary text-primary hover:text-white hover:bg-primary text-[10px] font-black uppercase tracking-widest px-6 py-3 transition-all flex items-center gap-2 w-max">
                                    Start Building &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        @elseif ($section['id'] === 'cta')
            <section data-preview-section="cta" id="cta-section" class="relative w-full py-32 lg:py-40 flex items-center justify-center overflow-hidden border-t border-white/5 mt-10 transition-all duration-1000">
                <!-- Background elements -->
                <div id="cta-bg-layer" class="absolute inset-0 liquid-glass bg-black/60 backdrop-blur-2xl opacity-100 z-0 pointer-events-none"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#000] via-transparent to-transparent z-0 pointer-events-none"></div>

                <!-- Diagonal subtle lines -->
                <div class="absolute left-[15%] md:left-[25%] top-[-50%] w-px h-[200%] bg-gradient-to-b from-transparent via-primary/30 to-transparent rotate-[12deg] z-0 opacity-50"></div>
                <div class="absolute right-[15%] md:right-[25%] top-[-50%] w-px h-[200%] bg-gradient-to-b from-transparent via-primary/30 to-transparent rotate-[12deg] z-0 opacity-50"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

                <div class="relative z-10 max-w-5xl mx-auto px-6 text-center flex flex-col items-center">
                    <div data-preview-block="panel-cta-tag" data-parent-section="cta" class="text-primary text-[10px] sm:text-[11px] font-black tracking-[0.4em] sm:tracking-[0.6em] uppercase mb-10 flex items-center gap-4">
                        <span class="w-10 h-px bg-primary/50"></span>
                        <span id="cta-tag">{{ $section['tag_text'] ?? 'READY_TO_BUILD' }}</span>
                        <span class="w-10 h-px bg-primary/50"></span>
                    </div>

                    <h2 data-preview-block="panel-cta-heading" data-parent-section="cta" class="text-5xl sm:text-7xl md:text-[5.5rem] font-black uppercase tracking-tight leading-[0.95] mb-10">
                        <span class="text-white block mb-2 sm:mb-4">{!! $section['title'] ?? 'Stop Settling.' !!}</span>
                        <span class="text-primary block drop-shadow-[0_0_30px_rgba(255,107,0,0.2)]">{!! $section['subtitle'] ?? 'Start Winning.' !!}</span>
                    </h2>

                    <p data-preview-block="panel-cta-subheading" data-parent-section="cta" class="text-gray-400 text-sm sm:text-base max-w-2xl mx-auto mb-12 font-medium leading-relaxed">
                        {!! $section['body'] ?? '' !!}
                    </p>

                    <div data-preview-block="panel-cta-buttons" data-parent-section="cta" class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 w-full max-w-md mx-auto sm:max-w-none">
                        <a href="{{ $section['primary_button_url'] ?? '#' }}" class="bg-primary hover:bg-[#ff8533] text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] px-10 py-5 transition-all flex items-center justify-center w-full sm:w-auto shadow-[0_0_30px_rgba(255,107,0,0.3)] hover:shadow-[0_0_50px_rgba(255,107,0,0.5)] transform hover:-translate-y-1">
                            {{ $section['primary_button_label'] ?? 'Build Yours Now' }} &rarr;
                        </a>
                        <a href="{{ $section['secondary_button_url'] ?? '#' }}" class="border border-white/20 hover:border-white text-gray-300 hover:text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] px-10 py-5 transition-all flex items-center justify-center w-full sm:w-auto bg-black/20 backdrop-blur-sm">
                            {{ $section['secondary_button_label'] ?? 'Talk To An Expert' }}
                        </a>
                    </div>
                </div>
            </section>
        @endif
    @endforeach
    </div>

    <x-footer :storefrontName="$storefrontName" :store="$store" :logoUrl="$logoUrl" />

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])
    @vite('Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js')

    @if(request()->routeIs('ecommerce.admin.layout.preview'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('[data-preview-section]');
            sections.forEach(section => {
                section.style.cursor = 'pointer';
                section.title = 'Click to edit this section';

                section.addEventListener('mouseover', (e) => {
                    e.stopPropagation();
                    section.style.outline = '3px solid #1B6FC8';
                    section.style.outlineOffset = '4px';
                    section.style.borderRadius = '16px';
                    section.style.boxShadow = '0 0 30px rgba(27,111,200,0.3)';
                    section.style.zIndex = '50';
                });

                section.addEventListener('mouseout', () => {
                    section.style.outline = '';
                    section.style.outlineOffset = '';
                    section.style.borderRadius = '';
                    section.style.boxShadow = '';
                    section.style.zIndex = '';
                });

                section.addEventListener('click', (e) => {
                    if (!e.isTrusted) return; // Prevent programmatic clicks (like gallery auto-scroll) from triggering parent scroll

                    e.preventDefault();
                    e.stopPropagation();
                    window.parent.postMessage({
                        action: 'select_section',
                        section: section.dataset.previewSection
                    }, '*');
                });
            });

            const blocks = document.querySelectorAll('[data-preview-block]');
            blocks.forEach(block => {
                block.style.cursor = 'pointer';
                block.title = 'Click to edit this block';

                block.addEventListener('mouseover', (e) => {
                    e.stopPropagation();
                    block.style.outline = '2px dashed #1B6FC8';
                    block.style.outlineOffset = '2px';
                    block.style.borderRadius = '8px';
                });

                block.addEventListener('mouseout', () => {
                    block.style.outline = '';
                    block.style.outlineOffset = '';
                    block.style.borderRadius = '';
                });

                block.addEventListener('click', (e) => {
                    if (!e.isTrusted) return;
                    e.preventDefault();
                    e.stopPropagation();
                    window.parent.postMessage({
                        action: 'select_block',
                        section: block.dataset.parentSection,
                        block: block.dataset.previewBlock
                    }, '*');
                });
            });
        });
    </script>
    @endif

    <script>
        function scrollCarousel(e, id, direction) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const el = document.getElementById(id);
            if (!el) return;
            const card = el.firstElementChild;
            if (!card) return;

            // Disable native smooth scroll and snapping during animation to prevent conflicts
            const originalScrollBehavior = el.style.scrollBehavior;
            const originalSnapType = el.style.scrollSnapType;
            el.style.scrollBehavior = 'auto';
            el.style.scrollSnapType = 'none';

            const amount = (card.offsetWidth + 24) * direction;
            const start = el.scrollLeft;
            const duration = 400; // ms
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);

                // EaseInOutCubic
                const ease = progress < 0.5
                    ? 4 * progress * progress * progress
                    : 1 - Math.pow(-2 * progress + 2, 3) / 2;

                el.scrollLeft = start + (amount * ease);

                if (timeElapsed < duration) {
                    requestAnimationFrame(animation);
                } else {
                    // Restore native behaviors
                    el.style.scrollBehavior = originalScrollBehavior;
                    el.style.scrollSnapType = originalSnapType;
                }
            }

            requestAnimationFrame(animation);
        }
    </script>
</body>
</html>
