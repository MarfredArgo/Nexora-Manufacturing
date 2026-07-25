<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <title>{{ config('app.name', 'TechForge') }} | Order Successful</title>
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
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505;
            color: #ffffff;
            overflow-x: hidden;
        }
        .ambient-light {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80vw;
            height: 80vw;
            background: radial-gradient(circle, rgba(255, 107, 0, 0.15) 0%, rgba(255, 107, 0, 0) 65%);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="relative antialiased selection:bg-primary selection:text-white min-h-screen flex flex-col items-center justify-center py-12">

    <div class="ambient-light"></div>

    <div class="container mx-auto px-4 max-w-2xl relative z-10 text-center">
        <!-- Success Icon -->
        <div class="w-32 h-32 bg-green-500/10 rounded-full border border-green-500/30 flex items-center justify-center mx-auto mb-8 relative animate-[bounce_2s_infinite]">
            <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping"></div>
            <i class="ph-bold ph-check text-6xl text-green-500 relative z-10"></i>
        </div>

        <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Order Confirmed!</h1>
        <p class="text-gray-400 text-lg mb-8">Thank you for your purchase. We've received your order and are currently processing it.</p>

        <!-- Order Details Card -->
        <div class="bg-black/40 border border-white/10 rounded-3xl p-8 mb-8 text-left shadow-2xl backdrop-blur-md">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-white/10 pb-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mb-1">Order Number</p>
                    <p class="text-xl font-black text-white">{{ $order->tracking_number }}</p>
                </div>
                <div class="mt-4 sm:mt-0 sm:text-right">
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mb-1">Date</p>
                    <p class="text-white font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            <div class="space-y-4 mb-6 max-h-[30vh] overflow-y-auto pr-2">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center text-xs font-bold text-gray-400">
                            {{ $item->quantity }}x
                        </div>
                        <p class="text-sm text-white font-medium line-clamp-1">{{ $item->name }}</p>
                    </div>
                    <p class="text-sm text-white font-bold shrink-0">₱{{ number_format($item->price * $item->quantity, 2) }}</p>
                </div>
                @endforeach
            </div>

            <div class="border-t border-white/10 pt-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Subtotal</span>
                    <span>₱{{ number_format($order->total - $order->shipping_fee, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Shipping</span>
                    <span>₱{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="flex justify-between items-end mt-4 pt-4 border-t border-white/5">
                    <span class="text-base text-white font-bold">Total Paid</span>
                    <span class="text-2xl font-black text-primary">₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/') }}" class="bg-primary hover:bg-white hover:text-black text-white px-8 py-4 rounded-xl font-black uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,255,255,0.5)]">
                Continue Shopping
            </a>
            <a href="#" class="bg-white/5 border border-white/10 hover:bg-white/10 text-white px-8 py-4 rounded-xl font-bold transition-all">
                View My Account
            </a>
        </div>
    </div>

</body>
</html>
