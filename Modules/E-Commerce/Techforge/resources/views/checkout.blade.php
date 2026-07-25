<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <title>{{ config('app.name', 'TechForge') }} | Secure Checkout</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505;
            color: #ffffff;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, .tech-font {
            font-family: 'Chakra Petch', sans-serif;
        }

        .code-font {
            font-family: 'JetBrains Mono', monospace;
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

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #05050A; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #ff6b00; }
        
        .form-input {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.25rem;
            padding: 0.75rem 1rem;
            color: white;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #ff6b00;
            background-color: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 10px rgba(255, 107, 0, 0.2);
        }
        
        /* Payment Radio Button styling */
        .payment-option input[type="radio"]:checked + div {
            border-color: #ff6b00;
            background-color: rgba(255, 107, 0, 0.05);
        }
        .payment-option input[type="radio"]:checked + div .radio-dot {
            background-color: #ff6b00;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #fff;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 16px;
            background-color: #ff6b00;
        }
        .section-title::after {
            content: '';
            display: block;
            flex-grow: 1;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin-left: 0.5rem;
        }
        
        .step-indicator {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #666;
            transition: all 0.3s;
        }
        .step-indicator .step-num {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.875rem;
            font-family: 'JetBrains Mono', monospace;
            transition: all 0.3s;
        }
        .step-indicator.active {
            color: #fff;
        }
        .step-indicator.active .step-num {
            background-color: #ff6b00;
            color: #000;
            border-color: #ff6b00;
            box-shadow: 0 0 15px rgba(255, 107, 0, 0.4);
        }
        .step-indicator.completed .step-num {
            background-color: transparent;
            color: #ff6b00;
            border-color: #ff6b00;
        }
    </style>
</head>
<body class="relative antialiased selection:bg-primary selection:text-white pb-20">

    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>

    <!-- Minimal Header -->
    <header class="py-6 mb-4">
        <div class="container mx-auto px-4 max-w-6xl">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-br from-primary to-orange-400 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(255,107,0,0.4)] group-hover:shadow-[0_0_25px_rgba(255,107,0,0.6)] transition-all">
                    <img src="{{ Vite::asset('Modules/E-Commerce/Techforge/resources/img/Techforge_Logo.png') }}" alt="TechForge Logo" class="h-6 w-auto object-contain">
                </div>
                <span class="text-xl font-bold tracking-wide text-white tech-font group-hover:text-primary transition-colors">TECHFORGE</span>
            </a>
        </div>
    </header>

    <main class="container mx-auto px-4 max-w-6xl relative z-10">
        
        <!-- Step Progress Bar -->
        <div class="flex items-center gap-4 mb-10 text-sm font-bold uppercase tracking-widest tech-font">
            <div id="step-indicator-1" class="step-indicator active">
                <span class="step-num">1</span>
                <span>Shipping</span>
            </div>
            <div class="flex-1 h-px bg-white/10"></div>
            <div id="step-indicator-2" class="step-indicator">
                <span class="step-num">2</span>
                <span>Payment</span>
            </div>
            <div class="flex-1 h-px bg-white/10"></div>
            <div id="step-indicator-3" class="step-indicator">
                <span class="step-num">3</span>
                <span>Review</span>
            </div>
        </div>

        <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
            
            <!-- Left Column: Forms -->
            <div class="lg:col-span-7 bg-[#0a0a0a] border border-white/5 rounded-xl p-6 lg:p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent pointer-events-none"></div>

                <!-- STEP 1: SHIPPING -->
                <div id="step-1-content" class="space-y-8 relative z-10 animate-fade-in">
                    
                    <!-- Dispatch Banner -->
                    <div class="bg-[#1a1a00] border border-[#ffaa00]/30 rounded-md p-4 flex items-center gap-3">
                        <i class="ph ph-clock text-[#ffaa00] text-xl"></i>
                        <span class="text-sm text-gray-300">Order within <span class="text-[#ffaa00] font-bold code-font bg-[#ffaa00]/10 px-2 py-0.5 rounded">02:46:40</span> for same-day dispatch</span>
                    </div>

                    <!-- Contact Info -->
                    <section>
                        <div class="section-title">Contact Information</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 code-font">First Name</label>
                                <input type="text" id="firstName" name="firstName" required class="form-input" value="{{ explode(' ', \Illuminate\Support\Facades\Auth::guard('ecommerce')->user()->name)[0] ?? '' }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Last Name</label>
                                <input type="text" id="lastName" name="lastName" required class="form-input" value="{{ explode(' ', \Illuminate\Support\Facades\Auth::guard('ecommerce')->user()->name)[1] ?? '' }}">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" required class="form-input" value="{{ \Illuminate\Support\Facades\Auth::guard('ecommerce')->user()->email }}" readonly>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required class="form-input" placeholder="+63 912 345 6789">
                            </div>
                        </div>
                    </section>

                    <!-- Delivery Address -->
                    <section>
                        <div class="section-title">Delivery Address</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Street Address</label>
                                <input type="text" id="address" name="address" required class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Apartment / Suite</label>
                                <input type="text" name="apartment" class="form-input" placeholder="Optional">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">City</label>
                                <input type="text" id="city" name="city" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">State / Province</label>
                                <input type="text" id="province" name="province" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Zip Code</label>
                                <input type="text" id="zip" name="zip" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Country</label>
                                <input type="text" name="country" required class="form-input" value="Philippines" readonly>
                            </div>
                        </div>
                    </section>

                    <!-- Shipping Method -->
                    <section>
                        <div class="section-title">Shipping Method</div>
                        <div class="space-y-3">
                            <label class="payment-option block cursor-pointer">
                                <input type="radio" name="shippingMethod" value="standard" class="sr-only" checked onchange="updateTotals()">
                                <div class="border border-white/10 rounded p-4 flex items-center justify-between transition-colors bg-white/5 hover:bg-white/10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-4 h-4 rounded-full border border-gray-500 flex items-center justify-center">
                                            <div class="radio-dot w-2 h-2 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white text-sm">Standard Delivery</h4>
                                            <p class="text-xs text-gray-400">3-5 Business Days</p>
                                        </div>
                                    </div>
                                    <span class="font-bold text-primary text-sm code-font">₱150.00</span>
                                </div>
                            </label>
                            <label class="payment-option block cursor-pointer">
                                <input type="radio" name="shippingMethod" value="express" class="sr-only" onchange="updateTotals()">
                                <div class="border border-white/10 rounded p-4 flex items-center justify-between transition-colors bg-white/5 hover:bg-white/10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-4 h-4 rounded-full border border-gray-500 flex items-center justify-center">
                                            <div class="radio-dot w-2 h-2 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white text-sm">Express Delivery</h4>
                                            <p class="text-xs text-gray-400">1-2 Business Days</p>
                                        </div>
                                    </div>
                                    <span class="font-bold text-primary text-sm code-font">₱300.00</span>
                                </div>
                            </label>
                        </div>
                    </section>

                    <div class="pt-6">
                        <button type="button" onclick="goToStep(2)" class="w-full bg-primary hover:bg-white hover:text-black text-white py-4 rounded font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(255,107,0,0.2)] hover:shadow-[0_0_25px_rgba(255,255,255,0.5)] tech-font">
                            Continue to Payment
                        </button>
                    </div>
                </div>

                <!-- STEP 2: PAYMENT -->
                <div id="step-2-content" class="space-y-8 hidden relative z-10 animate-fade-in">
                    
                    <section>
                        <div class="section-title">Payment Method</div>
                        <p class="text-sm text-gray-400 mb-4">All transactions are secure and encrypted.</p>
                        <div class="space-y-3">
                            <label class="payment-option block cursor-pointer">
                                <input type="radio" name="paymentMethod" value="credit_card" class="sr-only" checked>
                                <div class="border border-white/10 rounded p-4 flex items-center justify-between transition-colors bg-white/5 hover:bg-white/10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-4 h-4 rounded-full border border-gray-500 flex items-center justify-center">
                                            <div class="radio-dot w-2 h-2 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <span class="font-bold text-white text-sm">Credit / Debit Card</span>
                                    </div>
                                    <div class="flex gap-2 text-xl text-gray-400">
                                        <i class="ph-fill ph-credit-card"></i>
                                    </div>
                                </div>
                            </label>
                            <label class="payment-option block cursor-pointer">
                                <input type="radio" name="paymentMethod" value="gcash" class="sr-only">
                                <div class="border border-white/10 rounded p-4 flex items-center justify-between transition-colors bg-white/5 hover:bg-white/10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-4 h-4 rounded-full border border-gray-500 flex items-center justify-center">
                                            <div class="radio-dot w-2 h-2 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <span class="font-bold text-white text-sm">GCash / Maya</span>
                                    </div>
                                    <div class="flex gap-2 text-xl text-blue-500">
                                        <i class="ph-fill ph-wallet"></i>
                                    </div>
                                </div>
                            </label>
                            <label class="payment-option block cursor-pointer">
                                <input type="radio" name="paymentMethod" value="paypal" class="sr-only">
                                <div class="border border-white/10 rounded p-4 flex items-center justify-between transition-colors bg-white/5 hover:bg-white/10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-4 h-4 rounded-full border border-gray-500 flex items-center justify-center">
                                            <div class="radio-dot w-2 h-2 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <span class="font-bold text-white text-sm">PayPal</span>
                                    </div>
                                    <div class="flex gap-2 text-xl text-blue-400">
                                        <i class="ph-fill ph-paypal-logo"></i>
                                    </div>
                                </div>
                            </label>
                            <label class="payment-option block cursor-pointer">
                                <input type="radio" name="paymentMethod" value="cod" class="sr-only">
                                <div class="border border-white/10 rounded p-4 flex items-center justify-between transition-colors bg-white/5 hover:bg-white/10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-4 h-4 rounded-full border border-gray-500 flex items-center justify-center">
                                            <div class="radio-dot w-2 h-2 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <span class="font-bold text-white text-sm">Cash on Delivery</span>
                                    </div>
                                    <div class="flex gap-2 text-xl text-green-500">
                                        <i class="ph-fill ph-money"></i>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>

                    <div class="pt-6 flex gap-4">
                        <button type="button" onclick="goToStep(1)" class="w-1/3 bg-transparent border border-white/20 hover:bg-white/5 text-white py-4 rounded font-bold uppercase tracking-widest transition-all tech-font text-sm">
                            Back
                        </button>
                        <button type="button" onclick="goToStep(3)" class="w-2/3 bg-primary hover:bg-white hover:text-black text-white py-4 rounded font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(255,107,0,0.2)] hover:shadow-[0_0_25px_rgba(255,255,255,0.5)] tech-font text-sm">
                            Review Order
                        </button>
                    </div>
                </div>

                <!-- STEP 3: REVIEW -->
                <div id="step-3-content" class="space-y-8 hidden relative z-10 animate-fade-in">
                    
                    <section>
                        <div class="section-title">Review Details</div>
                        <div class="space-y-4">
                            <div class="bg-white/5 border border-white/10 rounded p-4">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest code-font">Ship To</span>
                                    <button type="button" onclick="goToStep(1)" class="text-xs text-primary hover:underline code-font">Edit</button>
                                </div>
                                <p class="text-sm text-white code-font" id="review-address-text">Waiting for input...</p>
                            </div>
                            <div class="bg-white/5 border border-white/10 rounded p-4">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest code-font">Pay With</span>
                                    <button type="button" onclick="goToStep(2)" class="text-xs text-primary hover:underline code-font">Edit</button>
                                </div>
                                <p class="text-sm text-white flex items-center gap-2 code-font" id="review-payment-text">
                                    Waiting for input...
                                </p>
                            </div>
                        </div>
                    </section>

                    <div class="pt-6 flex gap-4">
                        <button type="button" onclick="goToStep(2)" class="w-1/3 bg-transparent border border-white/20 hover:bg-white/5 text-white py-4 rounded font-bold uppercase tracking-widest transition-all tech-font text-sm">
                            Back
                        </button>
                        <button type="submit" id="place-order-btn" class="w-2/3 bg-primary hover:bg-white hover:text-black text-white py-4 rounded font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(255,107,0,0.4)] hover:shadow-[0_0_25px_rgba(255,255,255,0.8)] tech-font text-sm flex items-center justify-center gap-2">
                            Place Order
                        </button>
                    </div>
                    
                    <p class="text-xs text-gray-500 text-center mt-4">By placing your order, you agree to TechForge's privacy notice and conditions of use.</p>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-5">
                <div class="border border-white/10 rounded-xl p-6 lg:sticky lg:top-10 bg-[#05050A]">
                    <div class="flex justify-between items-end mb-6">
                        <h3 class="text-sm font-bold text-white tracking-widest uppercase tech-font">Order Summary</h3>
                        <span class="text-xs text-gray-400">{{ collect($cartItems)->sum('quantity') }} items</span>
                    </div>
                    
                    <!-- Items -->
                    <div class="space-y-4 max-h-[40vh] overflow-y-auto pr-2 mb-6" id="summary-items">
                        @foreach($cartItems as $item)
                        <div class="flex gap-4 pb-4 border-b border-white/5 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-[#000] border border-white/10 rounded flex items-center justify-center overflow-hidden p-1 shrink-0">
                                @if(isset($item['image_url']) && !empty($item['image_url']))
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <i class="ph-light ph-desktop text-2xl text-gray-500"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <h4 class="text-[13px] text-white font-bold leading-tight">{{ $item['name'] }}</h4>
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex items-center border border-white/10 rounded bg-[#000] h-6">
                                        <span class="px-2 text-xs text-gray-400">-</span>
                                        <span class="text-xs text-white font-bold w-4 text-center">{{ $item['quantity'] }}</span>
                                        <span class="px-2 text-xs text-gray-400">+</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-white code-font">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Promo Code -->
                    <div class="flex gap-2 mb-8">
                        <input type="text" placeholder="PROMO CODE" class="form-input text-sm !bg-[#000] flex-1 code-font">
                        <button type="button" class="px-4 border border-white/20 rounded text-xs font-bold text-white hover:bg-white/10 transition-colors uppercase tracking-widest">Apply</button>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-3 text-[13px] mb-6 code-font">
                        <div class="flex justify-between text-gray-400">
                            <span>Subtotal</span>
                            <span class="text-white">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Shipping (<span id="shipping-label">Standard</span>)</span>
                            <span class="text-primary font-bold" id="display-shipping">₱{{ number_format($shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Est. Tax (8.75%)</span>
                            <span class="text-white">₱0.00</span>
                        </div>
                    </div>

                    <div class="border-t border-white/10 pt-6 mb-8 flex justify-between items-end">
                        <span class="text-sm font-bold tracking-widest uppercase tech-font">Total</span>
                        <span class="text-3xl font-black text-primary code-font" id="display-total">₱{{ number_format($total, 2) }}</span>
                    </div>
                    
                    <!-- Badges -->
                    <div class="flex justify-between pt-6 border-t border-white/5">
                        <div class="flex flex-col items-center gap-1">
                            <i class="ph ph-shield-check text-primary"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-widest">2-Year Warranty</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <i class="ph ph-arrow-counter-clockwise text-white"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-widest">30-Day Returns</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <i class="ph-fill ph-lightning text-orange-500"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-widest">Same-Day Build</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </main>

    <!-- Mock Payment Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] hidden items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-[#050505] border border-primary/30 rounded-xl p-8 max-w-md w-full shadow-[0_0_50px_rgba(255,107,0,0.1)] text-center relative overflow-hidden">
            
            <div id="payment-processing" class="space-y-6 relative z-10">
                <div class="w-16 h-16 border-2 border-white/10 border-t-primary rounded-full animate-spin mx-auto"></div>
                <h3 class="text-xl font-bold text-white tech-font tracking-widest uppercase">Authorizing</h3>
                <p class="text-gray-400 text-xs code-font">Establishing secure connection to payment gateway...</p>
            </div>
            
            <div id="payment-success" class="space-y-6 hidden relative z-10">
                <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto border border-primary/50">
                    <i class="ph-bold ph-check text-3xl text-primary"></i>
                </div>
                <h3 class="text-xl font-bold text-white tech-font tracking-widest uppercase">Success</h3>
                <p class="text-gray-400 text-xs code-font">Payment authorized. Finalizing order...</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@studio-freight/lenis@1.0.39/dist/lenis.min.js"></script>
    <script>
        // Smooth Scrolling with Lenis
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
        })
        function raf(time) {
            lenis.raf(time)
            requestAnimationFrame(raf)
        }
        requestAnimationFrame(raf)

        const subtotal = {{ $subtotal }};
        let currentStep = 1;
        
        function goToStep(step) {
            // Basic validation before leaving step 1
            if (step === 2 && currentStep === 1) {
                const requiredFields = ['firstName', 'lastName', 'phone', 'address', 'city', 'province', 'zip'];
                let isValid = true;
                requiredFields.forEach(id => {
                    const el = document.getElementById(id);
                    if (!el.value) {
                        el.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        el.classList.remove('border-red-500');
                    }
                });
                
                if (!isValid) {
                    alert('Please fill in all required fields.');
                    return;
                }
            }
            
            // Setup review data if going to step 3
            if (step === 3) {
                const address = `${document.getElementById('address').value}, ${document.getElementById('city').value}, ${document.getElementById('province').value} ${document.getElementById('zip').value}`;
                document.getElementById('review-address-text').textContent = address;
                
                const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
                const paymentNames = {
                    'credit_card': '<i class="ph-fill ph-credit-card"></i> Credit / Debit Card',
                    'gcash': '<i class="ph-fill ph-wallet text-blue-500"></i> GCash / Maya',
                    'paypal': '<i class="ph-fill ph-paypal-logo text-blue-400"></i> PayPal',
                    'cod': '<i class="ph-fill ph-money text-green-500"></i> Cash on Delivery'
                };
                document.getElementById('review-payment-text').innerHTML = paymentNames[paymentMethod];
            }

            // Hide all steps
            document.getElementById('step-1-content').classList.add('hidden');
            document.getElementById('step-2-content').classList.add('hidden');
            document.getElementById('step-3-content').classList.add('hidden');
            
            // Update indicators
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                if (i < step) {
                    indicator.classList.remove('active');
                    indicator.classList.add('completed');
                    indicator.querySelector('.step-num').innerHTML = '<i class="ph-bold ph-check"></i>';
                } else if (i === step) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                    indicator.querySelector('.step-num').innerHTML = i;
                } else {
                    indicator.classList.remove('active', 'completed');
                    indicator.querySelector('.step-num').innerHTML = i;
                }
            }
            
            // Show new step
            document.getElementById(`step-${step}-content`).classList.remove('hidden');
            currentStep = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateTotals() {
            const shippingMethod = document.querySelector('input[name="shippingMethod"]:checked').value;
            const shippingCost = shippingMethod === 'express' ? 300 : 150;
            const total = subtotal + shippingCost;
            
            document.getElementById('shipping-label').textContent = shippingMethod === 'express' ? 'Express' : 'Standard';
            document.getElementById('display-shipping').textContent = shippingCost === 0 ? 'FREE' : '₱' + shippingCost.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('display-total').textContent = '₱' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('place-order-btn');
            const originalBtnHtml = btn.innerHTML;
            btn.innerHTML = '<i class="ph ph-spinner animate-spin"></i> Processing';
            btn.disabled = true;

            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const isDigitalPayment = paymentMethod !== 'cod';

            // Function to actually submit order to backend
            const submitOrder = () => {
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                fetch('{{ route('ecommerce.checkout.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert(response.message || 'An error occurred during checkout.');
                        resetBtn();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error. Please try again.');
                    resetBtn();
                });
            };

            const resetBtn = () => {
                btn.innerHTML = originalBtnHtml;
                btn.disabled = false;
                hideModal();
            };

            const showModal = () => {
                const modal = document.getElementById('payment-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modal.classList.remove('opacity-0'), 10);
            };

            const hideModal = () => {
                const modal = document.getElementById('payment-modal');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            };

            if (isDigitalPayment) {
                // Show mock payment gateway
                showModal();
                
                setTimeout(() => {
                    document.getElementById('payment-processing').classList.add('hidden');
                    document.getElementById('payment-success').classList.remove('hidden');
                    
                    setTimeout(() => {
                        submitOrder();
                    }, 1500);
                }, 2500);
            } else {
                // Cash on delivery, process immediately
                submitOrder();
            }
        });
    </script>
</body>
</html>
