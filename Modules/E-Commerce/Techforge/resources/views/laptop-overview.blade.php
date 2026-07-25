<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ config('app.name', 'TechForge') }} | {{ $product->name }}</title>
    
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
            background: radial-gradient(circle, rgba(255, 107, 0, 0.15) 0%, rgba(255, 107, 0, 0) 65%);
            z-index: -1;
            pointer-events: none;
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
        
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
</head>
<body class="relative antialiased selection:bg-primary selection:text-white">

    @vite('Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js')

    <!-- Background Ambient Effects -->
    <div class="ambient-light-1"></div>

    <x-navbar />

    <main class="flex-grow container mx-auto px-4 pt-32 pb-16 lg:pt-40 lg:pb-20 relative z-10 max-w-7xl">
        
        @php
            $isOnSale = rand(0, 1) == 1; // 50% chance to be on sale
            $originalPrice = $product->price + (floor(rand(5000, 15000) / 1000) * 1000);
            $rating = rand(40, 50) / 10;
            $reviewCount = rand(10, 250);
            $saveAmount = $originalPrice - $product->price;
            
            // Dummy image thumbnails
            $mainImg = $product->image_url ?? 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=800&q=80';
            $thumbnails = [
                $mainImg,
                'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1555680202-c86f0e12f086?auto=format&fit=crop&w=800&q=80'
            ];
            
            $shortSpecs = [
                ['label' => 'OS', 'value' => 'Windows 11 Home'],
                ['label' => 'Display', 'value' => $product->display ?? '15.6" FHD 144Hz'],
                ['label' => 'Processor', 'value' => $product->processor ?? 'N/A'],
                ['label' => 'Video Card', 'value' => $product->gpu ?? 'N/A'],
                ['label' => 'Memory', 'value' => $product->ram ?? 'N/A'],
                ['label' => 'Storage', 'value' => $product->storage ?? 'N/A'],
                ['label' => 'Warranty', 'value' => '1 Year Limited Warranty'],
            ];
            
            $detailedSpecs = [
                ['label' => 'Operating System', 'value' => 'Windows 11 Home 64-bit'],
                ['label' => 'Display', 'value' => $product->display ?? '15.6" FHD (1920x1080) 144Hz IPS'],
                ['label' => 'Processor', 'value' => $product->processor ?? 'N/A'],
                ['label' => 'Video Card', 'value' => $product->gpu ?? 'N/A'],
                ['label' => 'Memory', 'value' => $product->ram ?? 'N/A'],
                ['label' => 'Storage', 'value' => $product->storage ?? 'N/A'],
                ['label' => 'Camera', 'value' => '720p HD Camera with Privacy Shutter'],
                ['label' => 'Audio', 'value' => 'Stereo Speakers, Nahimic Audio'],
                ['label' => 'Networking', 'value' => 'Wi-Fi 6 (802.11ax) + Bluetooth 5.2'],
                ['label' => 'Keyboard', 'value' => '4-Zone RGB Backlit Keyboard'],
                ['label' => 'Battery', 'value' => '4-Cell 80Whr Polymer Battery'],
                ['label' => 'Warranty', 'value' => '1 Year Standard Warranty (Labor + Parts)'],
            ];
        @endphp

        <div class="flex flex-col lg:flex-row gap-12 mb-20">
            <!-- Left Column: Hero Images -->
            <div class="w-full lg:w-1/2 flex flex-col gap-4">
                <!-- Main Image -->
                <div data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2" class="liquid-glass rounded-3xl p-8 border border-white/10 aspect-square flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-black/40 to-black/80" style="transform-style: preserve-3d;">
                    <img id="main-product-image" src="{{ $mainImg }}" alt="{{ $product->name }}" class="w-full h-full object-contain drop-shadow-2xl transition-transform duration-500" style="transform: translateZ(20px);">
                </div>
                <!-- Thumbnails -->
                <div class="flex gap-4 overflow-x-auto hide-scrollbar pb-2">
                    @foreach($thumbnails as $idx => $thumb)
                        <div onclick="updateMainImage(this, '{{ $thumb }}')" class="thumbnail-btn cursor-pointer w-24 h-24 shrink-0 rounded-2xl p-2 border {{ $idx === 0 ? 'border-primary bg-primary/10' : 'border-white/10 bg-black/40 hover:border-white/30' }} transition-all">
                            <img src="{{ $thumb }}" class="w-full h-full object-cover rounded-xl" alt="Thumbnail {{ $idx }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Column: Product Details -->
            <div class="w-full lg:w-1/2 flex flex-col">
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight mb-6">{{ $product->name }}</h1>
                
                <hr class="border-white/10 mb-6">
                
                <div class="flex flex-col gap-2 mb-6">
                    @if($isOnSale)
                        <div class="bg-primary/20 border border-primary/30 text-primary px-3 py-1 rounded-md w-max text-xs font-black uppercase tracking-widest">
                            Save ₱{{ number_format($saveAmount) }}
                        </div>
                    @endif
                    
                    <div class="flex items-end gap-4">
                        <span class="text-4xl font-black text-white">₱{{ number_format($product->price) }}</span>
                        @if($isOnSale)
                            <span class="text-xl text-gray-500 line-through mb-1">₱{{ number_format($originalPrice) }}</span>
                        @endif
                    </div>
                </div>

                <hr class="border-white/10 mb-6">

                <div class="flex items-center gap-2 mb-6">
                    <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-green-500 font-bold tracking-wider uppercase text-sm">In Stock & Ready to Ship</span>
                </div>
                
                <div class="bg-black/30 border border-white/5 rounded-xl p-4 mb-6 flex items-center gap-4 shadow-lg group hover:border-primary/30 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center shrink-0 border border-blue-500/30 group-hover:bg-primary/10 group-hover:border-primary/30 group-hover:text-primary transition-colors">
                        <i class="ph-bold ph-truck text-blue-500 text-xl group-hover:text-primary transition-colors"></i>
                    </div>
                    <div>
                        <div class="text-gray-200 text-sm">Order within <span class="text-white font-bold">2 hrs 14 mins</span></div>
                        <div class="text-xs text-gray-500">For expected delivery by <span class="text-white font-bold">{{ now()->addDays(3)->format('l, F j') }}</span></div>
                    </div>
                </div>

                <hr class="border-white/10 mb-6">

                <!-- Short Specs -->
                <div class="space-y-3 mb-8">
                    @foreach($shortSpecs as $spec)
                    <div class="flex gap-4 text-sm">
                        <span class="text-gray-500 font-bold w-24 shrink-0">{{ $spec['label'] }}</span>
                        <span class="text-gray-200">{{ $spec['value'] }}</span>
                    </div>
                    @endforeach
                </div>

                <hr class="border-white/10 mb-6">

                <!-- Performance Widget -->
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="ph-bold ph-game-controller text-primary"></i> Est. 1440p Performance
                    </h3>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-[#050505]/50 border border-white/5 rounded-xl p-3 text-center backdrop-blur-sm group hover:border-primary/30 transition-colors cursor-default">
                            <div class="text-primary font-black text-xl mb-1">
                                {{ str_contains(strtoupper($product->gpu ?? ''), '4090') ? '160+' : (str_contains(strtoupper($product->gpu ?? ''), '4080') ? '140+' : '110+') }}
                            </div>
                            <div class="text-[9px] text-gray-400 font-bold uppercase">Cyberpunk</div>
                        </div>
                        <div class="bg-[#050505]/50 border border-white/5 rounded-xl p-3 text-center backdrop-blur-sm group hover:border-primary/30 transition-colors cursor-default">
                            <div class="text-primary font-black text-xl mb-1">
                                {{ str_contains(strtoupper($product->gpu ?? ''), '4090') ? '350+' : (str_contains(strtoupper($product->gpu ?? ''), '4080') ? '300+' : '220+') }}
                            </div>
                            <div class="text-[9px] text-gray-400 font-bold uppercase">Valorant</div>
                        </div>
                        <div class="bg-[#050505]/50 border border-white/5 rounded-xl p-3 text-center backdrop-blur-sm group hover:border-primary/30 transition-colors cursor-default">
                            <div class="text-primary font-black text-xl mb-1">
                                {{ str_contains(strtoupper($product->gpu ?? ''), '4090') ? '180+' : (str_contains(strtoupper($product->gpu ?? ''), '4080') ? '150+' : '120+') }}
                            </div>
                            <div class="text-[9px] text-gray-400 font-bold uppercase">Warzone</div>
                        </div>
                    </div>
                </div>

                <hr class="border-white/10 mb-6">

                <!-- Actions -->
                <div class="flex flex-col gap-4 mb-8">
                    <div class="flex items-center gap-4">
                        <!-- Quantity -->
                        <div class="flex items-center border border-white/10 rounded-xl bg-black/40 h-14 w-32">
                            <button onclick="decrementQty()" class="w-10 h-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 rounded-l-xl transition-colors"><i class="ph-bold ph-minus"></i></button>
                            <input type="number" id="qty" value="1" min="1" max="10" class="flex-1 bg-transparent text-center text-white font-bold outline-none border-none pointer-events-none appearance-none">
                            <button onclick="incrementQty()" class="w-10 h-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 rounded-r-xl transition-colors"><i class="ph-bold ph-plus"></i></button>
                        </div>
                        
                        <button type="button" id="main-add-to-cart" onclick="window.addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image_url ?? $product->image ?? '' }}', parseInt(document.getElementById('qty').value) || 1, 'laptop', null, event.currentTarget)" class="flex-1 h-14 bg-primary hover:bg-white hover:text-black text-white rounded-xl font-black uppercase tracking-widest flex items-center justify-center gap-3 transition-all duration-300 shadow-[0_0_20px_rgba(255,107,0,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] group">
                            <i class="ph-bold ph-shopping-cart text-xl group-hover:scale-110 transition-transform"></i> Add To Cart
                        </button>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="flex items-center gap-4">
                    <div class="flex text-primary text-lg">
                        @for($i = 1; $i <= 5; $i++)
                            @if($rating >= $i)
                                <i class="ph-fill ph-star"></i>
                            @elseif($rating >= $i - 0.5)
                                <i class="ph-fill ph-star-half"></i>
                            @else
                                <i class="ph-fill ph-star text-gray-600"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-white font-bold">{{ number_format($rating, 1) }}</span>
                    <span class="text-gray-500 text-sm">({{ $reviewCount }} Reviews)</span>
                </div>
            </div>
        </div>

        <hr class="border-white/10 mb-12">

        <!-- What's in the Box -->
        <div class="max-w-4xl mx-auto mb-16">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-widest flex items-center gap-3">
                <i class="ph-bold ph-package text-primary"></i> What's in the Box
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-[#050505]/50 border border-white/5 rounded-2xl p-6 text-center backdrop-blur-md transition-all hover:bg-white/5 hover:border-primary/30 group">
                    <i class="ph-bold ph-laptop text-3xl text-primary mb-3 block drop-shadow-[0_0_10px_rgba(255,107,0,0.5)] group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Gaming Laptop</span>
                </div>
                <div class="bg-[#050505]/50 border border-white/5 rounded-2xl p-6 text-center backdrop-blur-md transition-all hover:bg-white/5 hover:border-primary/30 group">
                    <i class="ph-bold ph-battery-charging text-3xl text-primary mb-3 block drop-shadow-[0_0_10px_rgba(255,107,0,0.5)] group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Power Adapter</span>
                </div>
                <div class="bg-[#050505]/50 border border-white/5 rounded-2xl p-6 text-center backdrop-blur-md transition-all hover:bg-white/5 hover:border-primary/30 group">
                    <i class="ph-bold ph-book-open text-3xl text-primary mb-3 block drop-shadow-[0_0_10px_rgba(255,107,0,0.5)] group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">User Manual</span>
                </div>
                <div class="bg-[#050505]/50 border border-white/5 rounded-2xl p-6 text-center backdrop-blur-md transition-all hover:bg-white/5 hover:border-primary/30 group">
                    <i class="ph-bold ph-shield-check text-3xl text-primary mb-3 block drop-shadow-[0_0_10px_rgba(255,107,0,0.5)] group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Warranty Card</span>
                </div>
            </div>
        </div>

        <!-- Detailed Specifications -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-black text-white mb-8 uppercase tracking-widest flex items-center gap-3">
                <i class="ph-bold ph-list-dashes text-primary"></i> Detailed Specifications
            </h2>
            
            <div class="liquid-glass rounded-3xl border border-white/10 overflow-hidden">
                <div class="flex flex-col">
                    @foreach($detailedSpecs as $index => $spec)
                    <div class="flex flex-col sm:flex-row border-b border-white/5 last:border-b-0 {{ $index % 2 == 0 ? 'bg-black/20' : 'bg-transparent' }}">
                        <div class="sm:w-1/3 p-4 sm:p-6 text-sm font-bold text-gray-400 uppercase tracking-wider border-r border-white/5">
                            {{ $spec['label'] }}
                        </div>
                        <div class="sm:w-2/3 p-4 sm:p-6 text-base text-gray-200">
                            {{ $spec['value'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>


        <!-- Sticky Action Bar -->
        <div id="sticky-bar" class="fixed bottom-0 left-0 right-0 z-50 transform translate-y-full transition-transform duration-500">
            <div class="bg-[#0f0f0f]/90 backdrop-blur-xl border-t border-white/10 px-4 py-4 flex items-center justify-between shadow-[0_-10px_30px_rgba(0,0,0,0.5)]">
                <div class="flex items-center gap-4 max-w-7xl mx-auto w-full justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ $mainImg }}" class="w-12 h-12 rounded-lg object-cover hidden sm:block border border-white/10">
                        <div>
                            <h3 class="text-white font-bold">{{ $product->name }}</h3>
                            <div class="flex items-center gap-2">
                                <div class="text-primary font-black text-sm">₱{{ number_format($product->price) }}</div>
                                <div class="text-[10px] text-green-500 uppercase tracking-widest font-bold hidden sm:block">In Stock</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="bg-primary hover:bg-white hover:text-black text-white px-8 py-3 rounded-xl font-black uppercase tracking-widest transition-all duration-300 shadow-[0_0_15px_rgba(255,107,0,0.4)]">
                        Add To Cart
                    </button>
                </div>
            </div>
        </div>
    </main>

    <x-footer />

    <script>
        let isScrolling = false;
        window.addEventListener('scroll', () => {
            if (!isScrolling) {
                window.requestAnimationFrame(() => {
                    const stickyBar = document.getElementById('sticky-bar');
                    const footer = document.querySelector('footer');
                    const mainBtn = document.getElementById('main-add-to-cart');
                    
                    let isFooterVisible = false;
                    if (footer) {
                        const rect = footer.getBoundingClientRect();
                        // Check if the top of the footer is visible in the viewport
                        isFooterVisible = rect.top < window.innerHeight;
                    }

                    let isMainBtnPast = false;
                    if (mainBtn) {
                        const btnRect = mainBtn.getBoundingClientRect();
                        // We only show the sticky bar if the main button has scrolled completely ABOVE the viewport
                        // meaning the user can no longer see the main add to cart button
                        isMainBtnPast = btnRect.bottom < 0;
                    } else {
                        isMainBtnPast = window.scrollY > 600;
                    }

                    if (isMainBtnPast && !isFooterVisible) {
                        stickyBar.classList.remove('translate-y-full');
                    } else {
                        stickyBar.classList.add('translate-y-full');
                    }
                    isScrolling = false;
                });
                isScrolling = true;
            }
        }, { passive: true });

        function updateMainImage(btn, src) {
            document.getElementById('main-product-image').src = src;
            
            document.querySelectorAll('.thumbnail-btn').forEach(el => {
                el.classList.remove('border-primary', 'bg-primary/10');
                el.classList.add('border-white/10', 'bg-black/40');
            });
            
            btn.classList.remove('border-white/10', 'bg-black/40');
            btn.classList.add('border-primary', 'bg-primary/10');
        }

        function incrementQty() {
            let input = document.getElementById('qty');
            if (parseInt(input.value) < 10) input.value = parseInt(input.value) + 1;
        }

        function decrementQty() {
            let input = document.getElementById('qty');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }
    </script>
    
    <!-- Lenis Smooth Scroll -->
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.39/dist/lenis.min.js"></script>
    <script>
        const lenis = new Lenis({
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
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    </script>
</body>
</html>
