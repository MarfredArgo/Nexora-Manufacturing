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

    <!-- Account Section -->
    <main class="relative pt-40 pb-20 lg:pt-48 lg:pb-28 overflow-hidden z-10 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-14">
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
                
                <!-- Sidebar -->
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        
                        <!-- Account Details Category -->
                        <div class="flex flex-col">
                            <div class="flex items-center gap-3 text-gray-400 font-bold text-base mb-2">
                                <i class="ph ph-user text-xl"></i>
                                Account Details
                            </div>
                            <!-- Subcategories -->
                            <div class="flex flex-col ml-8 gap-3 border-l border-white/10 pl-4 py-1">
                                <a href="{{ route('ecommerce.account.profile') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Profile</a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Bank & Cards</a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Addresses</a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Change Password</a>
                            </div>
                        </div>

                        <!-- Other Categories -->
                        <a href="{{ route('ecommerce.account.purchases') }}" class="flex items-center gap-3 text-white font-bold text-base mt-4">
                            <i class="ph ph-receipt text-xl text-primary"></i>
                            Purchases
                        </a>
                        <a href="#" class="flex items-center gap-3 text-gray-400 hover:text-white transition-colors font-bold text-base mt-2">
                            <i class="ph ph-ticket text-xl"></i>
                            Vouchers
                        </a>
                        <a href="#" class="flex items-center gap-3 text-gray-400 hover:text-white transition-colors font-bold text-base mt-2">
                            <i class="ph ph-coins text-xl"></i>
                            Forge Points
                        </a>
                    </div>
                </div>

                <!-- Content -->
                <div class="lg:col-span-3 liquid-glass rounded-3xl p-6 sm:p-10 border border-white/10 shadow-2xl relative overflow-hidden">
                    <!-- Glassmorphism subtle glow -->
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/20 blur-3xl rounded-full pointer-events-none"></div>

                    <div class="border-b border-white/10 pb-4 mb-6 relative z-10">
                        <h2 class="text-2xl font-black text-white">Purchases</h2>
                        <p class="text-sm text-gray-400 mt-1">View and track your orders</p>
                    </div>

                    <div class="relative z-10 flex flex-col gap-6">
                        <!-- Tabs -->
                        <div class="flex overflow-x-auto gap-2 sm:gap-6 border-b border-white/10 pb-2 scrollbar-hide">
                            <a href="#" class="text-primary font-bold border-b-2 border-primary pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors">All</a>
                            <a href="#" class="text-gray-400 hover:text-white font-medium pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors border-b-2 border-transparent hover:border-white/30">To Pay</a>
                            <a href="#" class="text-gray-400 hover:text-white font-medium pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors border-b-2 border-transparent hover:border-white/30">To Ship</a>
                            <a href="#" class="text-gray-400 hover:text-white font-medium pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors border-b-2 border-transparent hover:border-white/30">To Receive</a>
                            <a href="#" class="text-gray-400 hover:text-white font-medium pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors border-b-2 border-transparent hover:border-white/30">Completed</a>
                            <a href="#" class="text-gray-400 hover:text-white font-medium pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors border-b-2 border-transparent hover:border-white/30">Cancelled</a>
                            <a href="#" class="text-gray-400 hover:text-white font-medium pb-2 px-1 sm:px-2 whitespace-nowrap text-sm transition-colors border-b-2 border-transparent hover:border-white/30">Return / Refund</a>
                        </div>
                        
                        <!-- Empty State -->
                        <div class="py-20 flex flex-col items-center justify-center text-center">
                            <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                <i class="ph ph-receipt text-5xl text-gray-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">No orders yet</h3>
                            <p class="text-gray-400 text-sm max-w-xs sm:max-w-sm">When you place an order, it will show up here. Browse our store to find your next PC!</p>
                            <a href="{{ route('ecommerce.prebuilt-pcs') }}" class="mt-8 bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white px-8 py-3 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,107,0,0.5)] transform hover:-translate-y-0.5">
                                Start Shopping
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    

    @vite(['Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js', 'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js'])

    <!-- Load our compiled JavaScript -->
    @vite('Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js')
</body>
</html>
