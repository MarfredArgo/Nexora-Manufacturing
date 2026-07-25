<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ config('app.name', 'TechForge') }} | Cart & Returns</title>
    
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

        /* Orange Gradient Text */
        .text-gradient {
            background: linear-gradient(to right, #ffffff, #ffaa66);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #050505; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #ff6b00; }

        /* Preloader Animations */
        @keyframes spinFastOnce { 0% { transform: rotate(0deg); } 100% { transform: rotate(720deg); } }
        .animate-spin-fast { animation: spinFastOnce 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        @keyframes slideTextOut { 0% { max-width: 0; opacity: 0; padding-left: 0; } 100% { max-width: 400px; opacity: 1; padding-left: 1.5rem; } }
        .animate-slide-text { animation: slideTextOut 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; animation-delay: 0.8s; overflow: hidden; white-space: nowrap; opacity: 0; max-width: 0; }

        .hide-scroll-bar::-webkit-scrollbar { display: none; }
        .hide-scroll-bar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cart-item-enter { animation: fadeSlideUp 0.3s ease-out forwards; }

        @keyframes fadeSlideOut {
            from { opacity: 1; transform: translateX(0); max-height: 200px; margin-bottom: 1.5rem; }
            to { opacity: 0; transform: translateX(-30px); max-height: 0; margin-bottom: 0; overflow: hidden; }
        }
        .cart-item-exit { animation: fadeSlideOut 0.4s ease-in forwards; }
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

    <!-- Cart & Returns Section -->
    <main class="relative pt-40 pb-20 lg:pt-48 lg:pb-28 overflow-hidden z-10 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-14">
            
            <!-- Page Header -->
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-white/10 pb-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">Returns & Cart</h1>
                    <p class="text-sm text-gray-400">Review your items and our return policies.</p>
                </div>
                <a href="{{ url('/') }}" class="text-sm font-bold text-gray-400 hover:text-white transition-colors flex items-center gap-2 group">
                    <i class="ph-bold ph-arrow-left group-hover:-translate-x-1 transition-transform"></i> Continue Shopping
                </a>
            </div>

            <!-- Free Shipping Progress Bar -->
            @if(count($cart) > 0)
            @php
                $shippingProgress = min(($subtotal / $freeShippingThreshold) * 100, 100);
                $remaining = max($freeShippingThreshold - $subtotal, 0);
            @endphp
            <div class="mb-8 liquid-glass rounded-2xl p-5 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <i class="ph-fill ph-truck text-lg {{ $remaining <= 0 ? 'text-green-400' : 'text-primary' }}"></i>
                        @if($remaining <= 0)
                            <span class="text-sm font-bold text-green-400">You've unlocked FREE shipping! 🎉</span>
                        @else
                            <span class="text-sm font-bold text-white">Spend <span class="text-primary">₱{{ number_format($remaining) }}</span> more for <span class="text-green-400">FREE shipping!</span></span>
                        @endif
                    </div>
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">₱{{ number_format($subtotal) }} / ₱{{ number_format($freeShippingThreshold) }}</span>
                </div>
                <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full rounded-full transition-all duration-700 ease-out {{ $remaining <= 0 ? 'bg-gradient-to-r from-green-500 to-emerald-400 shadow-[0_0_12px_rgba(34,197,94,0.6)]' : 'bg-gradient-to-r from-primary to-orange-400 shadow-[0_0_12px_rgba(255,107,0,0.5)]' }}" style="width: {{ $shippingProgress }}%"></div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Cart Items (Left) -->
                <div class="lg:col-span-8 space-y-6" id="cart-items-container">
                    
                    <h3 class="text-xl font-bold text-white mb-4">Your Items</h3>

                    @if(count($cart) > 0)
                        @foreach($cart as $index => $item)
                        <div class="cart-item-row liquid-glass rounded-2xl p-5 border border-white/10 flex flex-col sm:flex-row items-start sm:items-center gap-5 cart-item-enter hover:border-white/20 transition-all" data-product-id="{{ $item['id'] }}" style="animation-delay: {{ $index * 0.05 }}s">
                            @php
                                $productType = $item['product_type'] ?? 'generic';
                                $detailUrl = match($productType) {
                                    'prebuilt' => url('/prebuilt-overview/' . $item['id']),
                                    'laptop' => url('/laptop-overview/' . $item['id']),
                                    'configurator' => url('/custompc-overview/' . $item['id']),
                                    'custom' => url('/custompc-overview/' . $item['id']),
                                    default => null,
                                };
                            @endphp

                            <!-- Clickable Product Image + Name -->
                            @if($detailUrl)
                            <a href="{{ $detailUrl }}" class="flex items-center gap-5 flex-1 min-w-0 group/link">
                            @else
                            <div class="flex items-center gap-5 flex-1 min-w-0">
                            @endif
                                <!-- Product Image -->
                                <div class="w-24 h-24 bg-[#0a0a0a] rounded-xl flex-shrink-0 border border-white/5 flex items-center justify-center overflow-hidden p-2 {{ $detailUrl ? 'group-hover/link:border-primary/40 transition-colors' : '' }}">
                                    @if(isset($item['image_url']) && !empty($item['image_url']))
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain {{ $detailUrl ? 'group-hover/link:scale-110 transition-transform duration-300' : '' }}">
                                    @else
                                        <i class="ph-light ph-desktop text-3xl text-gray-600"></i>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-white font-bold truncate text-lg {{ $detailUrl ? 'group-hover/link:text-primary transition-colors' : '' }}">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest">Unit Price: ₱{{ number_format($item['price'], 2) }}</p>
                                    @if($detailUrl)
                                        <p class="text-[10px] text-gray-600 mt-1 flex items-center gap-1 opacity-0 group-hover/link:opacity-100 transition-opacity"><i class="ph ph-arrow-square-out"></i> View product details</p>
                                    @endif
                                </div>
                            @if($detailUrl)
                            </a>
                            @else
                            </div>
                            @endif

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-1 shrink-0">
                                <button onclick="updateQty('{{ $item['id'] }}', {{ $item['quantity'] - 1 }})" class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-primary hover:border-primary transition-all {{ $item['quantity'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                                    <i class="ph-bold ph-minus text-xs"></i>
                                </button>
                                <div class="w-12 h-9 rounded-lg bg-black/40 border border-white/10 flex items-center justify-center">
                                    <span class="text-sm font-bold text-white" id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                                </div>
                                <button onclick="updateQty('{{ $item['id'] }}', {{ $item['quantity'] + 1 }})" class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-primary hover:border-primary transition-all">
                                    <i class="ph-bold ph-plus text-xs"></i>
                                </button>
                            </div>

                            <!-- Line Total -->
                            <div class="text-right shrink-0 min-w-[100px]">
                                <p class="text-lg font-black text-primary" id="line-total-{{ $item['id'] }}">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                            </div>

                            <!-- Delete Button -->
                            <button onclick="removeItem('{{ $item['id'] }}')" class="text-gray-600 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-500/10 shrink-0 group">
                                <i class="ph ph-trash text-xl group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <!-- Empty Cart State -->
                        <div id="empty-cart-state" class="liquid-glass rounded-3xl p-10 border border-white/10 flex flex-col items-center justify-center text-center min-h-[300px]">
                            <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4 border border-white/10">
                                <i class="ph-light ph-shopping-cart text-4xl text-gray-400"></i>
                            </div>
                            
                            @auth('ecommerce')
                                <h3 class="text-xl font-bold text-white mb-2">Your cart is currently empty.</h3>
                                <p class="text-sm text-gray-400 mb-6">Looks like you haven't added anything yet. Discover our premium components and gear to elevate your setup.</p>
                            @else
                                <h3 class="text-xl font-bold text-white mb-2">Sign in to view your cart</h3>
                                <p class="text-sm text-gray-400 mb-6">You may have items saved in your account. Sign in to see them, or continue shopping to add items as a guest.</p>
                            @endauth
                            
                            <div class="flex items-center gap-4">
                                @guest('ecommerce')
                                <a href="{{ route('ecommerce.login') }}" class="bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)]">
                                    Sign In
                                </a>
                                @endguest
                                <a href="{{ url('/') }}" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all border border-white/10 hover:border-white/20">
                                    Browse Products
                                </a>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Order Summary & Returns (Right) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Summary Card -->
                    <div class="liquid-glass-heavy rounded-3xl p-6 border border-white/10 shadow-[0_10px_30px_rgba(0,0,0,0.8)] lg:sticky lg:top-36">
                        <h3 class="text-lg font-bold text-white mb-6">Order Summary</h3>
                        
                        <div class="space-y-4 text-sm mb-6">
                            <div class="flex justify-between text-gray-400">
                                <span>Subtotal (<span id="summary-item-count">{{ collect($cart)->sum('quantity') }}</span> items)</span>
                                <span class="text-white font-medium" id="summary-subtotal">₱{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>Shipping</span>
                                <span class="font-medium {{ $shipping == 0 && count($cart) > 0 ? 'text-green-400' : 'text-white' }}" id="summary-shipping">
                                    @if($shipping == 0 && count($cart) > 0)
                                        FREE
                                    @else
                                        ₱{{ number_format($shipping, 2) }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>Discount</span>
                                <span class="text-white font-medium" id="summary-discount">₱{{ number_format($discount, 2) }}</span>
                            </div>
                        </div>

                        <!-- Promo Code Input -->
                        <div class="mb-6">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <i class="ph ph-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                                    <input type="text" id="promo-input" placeholder="Enter promo code" class="w-full bg-black/40 border border-white/10 rounded-xl py-2.5 pl-9 pr-3 text-white text-sm placeholder:text-gray-600 focus:outline-none focus:border-primary transition-colors">
                                </div>
                                <button onclick="applyPromo()" class="px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white hover:bg-primary hover:border-primary transition-all shrink-0">
                                    Apply
                                </button>
                            </div>
                            <p id="promo-message" class="text-xs mt-2 hidden"></p>
                        </div>
                        
                        <div class="border-t border-white/10 pt-4 mb-6">
                            <div class="flex justify-between items-end">
                                <span class="text-base text-gray-300">Total</span>
                                <span class="text-3xl font-black text-white" id="summary-total">₱{{ number_format($total, 2) }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1 text-right">Including all applicable taxes</p>
                        </div>
                        
                        @if(count($cart) > 0)
                            <a href="{{ \Illuminate\Support\Facades\Auth::guard('ecommerce')->check() ? route('ecommerce.checkout.index') : route('ecommerce.cart.checkout.redirect') }}" id="checkout-btn" class="w-full bg-gradient-to-r from-primary to-orange-400 hover:from-primary-hover hover:to-primary text-white py-4 rounded-xl font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,107,0,0.5)] hover:-translate-y-1 flex items-center justify-center gap-2 text-lg group">
                                Proceed to Checkout <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @else
                            <button disabled id="checkout-btn" class="w-full bg-white/5 text-gray-500 py-4 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2 text-lg">
                                Proceed to Checkout <i class="ph-bold ph-arrow-right"></i>
                            </button>
                        @endif
                        
                        <div class="mt-5 flex items-center justify-center gap-2 text-[10px] text-gray-500 uppercase tracking-widest">
                            <i class="ph-fill ph-shield-check text-sm text-primary"></i> 256-bit Secure Checkout
                        </div>
                    </div>

                    <!-- Returns Policy Card -->
                    <div class="liquid-glass rounded-3xl p-6 border border-white/10 relative overflow-hidden group hover:border-primary/30 transition-colors">
                        <div class="absolute -right-6 -top-6 text-white/5 group-hover:text-primary/10 transition-colors">
                            <i class="ph-fill ph-arrow-counter-clockwise text-9xl"></i>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-base font-bold text-white mb-2 flex items-center gap-2">
                                <i class="ph-bold ph-arrow-counter-clockwise text-primary"></i> Hassle-Free Returns
                            </h3>
                            <p class="text-xs text-gray-400 leading-relaxed mb-4">
                                Not satisfied with your purchase? You can return most items in their original condition within <span class="text-white font-bold">30 days</span> of delivery for a full refund.
                            </p>
                            <a href="#" class="text-xs font-bold text-primary hover:text-white transition-colors flex items-center gap-1 group/link">
                                View Full Return Policy <i class="ph-bold ph-arrow-right group-hover/link:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

            <!-- You May Also Like Section -->
            @if(count($recommendations) > 0)
            <section class="mt-20">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-white">You May Also Like</h2>
                        <p class="text-sm text-gray-500 mt-1">Complete your setup with these popular picks.</p>
                    </div>
                </div>
                <div class="flex gap-5 overflow-x-auto pb-6 -mx-6 px-6 scroll-px-6 snap-x snap-mandatory hide-scroll-bar scroll-smooth" style="mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 32px, black calc(100% - 32px), transparent);">
                    @foreach($recommendations as $rec)
                    <div class="w-[260px] shrink-0 snap-start liquid-glass rounded-2xl p-4 border border-white/10 hover:border-primary/40 transition-all group">
                        <div class="aspect-square w-full rounded-xl bg-black/40 mb-4 flex items-center justify-center p-4 border border-white/5 overflow-hidden">
                            <img src="{{ $rec->image_url }}" alt="{{ $rec->name }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <h4 class="text-sm font-bold text-white truncate mb-1" title="{{ $rec->name }}">{{ $rec->name }}</h4>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-black text-white">₱{{ number_format($rec->price) }}</span>
                            <button onclick="window.addToCart('{{ $rec->id }}', '{{ addslashes($rec->name) }}', {{ $rec->price }}, '{{ $rec->image_url }}')" class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all">
                                <i class="ph-bold ph-plus text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
        </div>
    </main>

    <x-footer />

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const FREE_SHIPPING_THRESHOLD = {{ $freeShippingThreshold }};

        function updateQty(productId, newQty) {
            if (newQty < 1) return;

            fetch('/cart/update-quantity', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, quantity: newQty })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function removeItem(productId) {
            const row = document.querySelector(`[data-product-id="${productId}"]`);
            if (row) row.classList.add('cart-item-exit');

            fetch('/cart/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => location.reload(), 400);
                }
            });
        }

        function applyPromo() {
            const input = document.getElementById('promo-input');
            const msg = document.getElementById('promo-message');
            const code = input.value.trim().toUpperCase();

            if (!code) {
                msg.textContent = 'Please enter a promo code.';
                msg.className = 'text-xs mt-2 text-red-400';
                return;
            }

            // Demo codes
            const validCodes = {
                'TECHFORGE10': { discount: 10, type: 'percent' },
                'WELCOME500': { discount: 500, type: 'flat' },
            };

            if (validCodes[code]) {
                const promo = validCodes[code];
                msg.textContent = `Code "${code}" applied! ${promo.type === 'percent' ? promo.discount + '% off' : '₱' + promo.discount + ' off'} your order.`;
                msg.className = 'text-xs mt-2 text-green-400';
                input.classList.add('border-green-500/50');
                input.classList.remove('border-white/10');
            } else {
                msg.textContent = 'Invalid promo code. Please try again.';
                msg.className = 'text-xs mt-2 text-red-400';
                input.classList.add('border-red-500/50');
                input.classList.remove('border-white/10');
            }
        }
    </script>

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])

    <!-- Load our compiled JavaScript -->
    @vite('Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js')
</body>
</html>
