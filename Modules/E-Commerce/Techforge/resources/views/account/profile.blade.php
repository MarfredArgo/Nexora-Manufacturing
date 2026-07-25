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
                            <div class="flex items-center gap-3 text-white font-bold text-base mb-2">
                                <i class="ph ph-user text-xl text-primary"></i>
                                Account Details
                            </div>
                            <!-- Subcategories -->
                            <div class="flex flex-col ml-8 gap-3 border-l border-white/10 pl-4 py-1">
                                <a href="{{ route('ecommerce.account.profile') }}" class="text-primary font-bold text-sm hover:text-primary transition-colors">Profile</a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Bank & Cards</a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Addresses</a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Change Password</a>
                            </div>
                        </div>

                        <!-- Other Categories -->
                        <a href="{{ route('ecommerce.account.purchases') }}" class="flex items-center gap-3 text-gray-400 hover:text-white transition-colors font-bold text-base mt-4">
                            <i class="ph ph-receipt text-xl"></i>
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

                    <div class="border-b border-white/10 pb-4 mb-8 relative z-10">
                        <h2 class="text-2xl font-black text-white">Profile</h2>
                        <p class="text-sm text-gray-400 mt-1">Manage your account</p>
                    </div>

                    <div class="flex flex-col-reverse md:flex-row gap-12 relative z-10">
                        <!-- Form (Left side) -->
                        <div class="flex-1 space-y-6">
                            
                            <!-- Username -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                                <label class="w-32 text-sm text-gray-400 font-medium shrink-0">Username</label>
                                <div class="flex-1">
                                    <input type="text" value="{{ Auth::guard('ecommerce')->check() ? Auth::guard('ecommerce')->user()->name : 'user123' }}" disabled class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed focus:outline-none shadow-inner">
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                                <label class="w-32 text-sm text-gray-400 font-medium shrink-0">Name</label>
                                <div class="flex-1">
                                    <input type="text" value="{{ Auth::guard('ecommerce')->check() ? Auth::guard('ecommerce')->user()->name : 'John Doe' }}" class="w-full bg-black/40 border border-white/10 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-black/60 rounded-xl px-4 py-2.5 text-sm text-white transition-all outline-none placeholder-gray-600">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                                <label class="w-32 text-sm text-gray-400 font-medium shrink-0">Email</label>
                                <div class="flex-1">
                                    <input type="email" value="{{ Auth::guard('ecommerce')->check() ? Auth::guard('ecommerce')->user()->email : 'johndoe@example.com' }}" class="w-full bg-black/40 border border-white/10 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-black/60 rounded-xl px-4 py-2.5 text-sm text-white transition-all outline-none placeholder-gray-600">
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                                <label class="w-32 text-sm text-gray-400 font-medium shrink-0">Phone Number</label>
                                <div class="flex-1">
                                    <input type="tel" placeholder="Enter your phone number" class="w-full bg-black/40 border border-white/10 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-black/60 rounded-xl px-4 py-2.5 text-sm text-white transition-all outline-none placeholder-gray-600">
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-6">
                                <label class="w-32 text-sm text-gray-400 font-medium shrink-0 pt-2">Gender</label>
                                <div class="flex-1 flex items-center gap-8 pt-2">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <div class="relative flex items-center justify-center">
                                            <input type="radio" name="gender" value="male" class="peer sr-only">
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-600 peer-checked:border-primary transition-colors"></div>
                                            <div class="w-2.5 h-2.5 bg-primary rounded-full absolute scale-0 peer-checked:scale-100 transition-transform shadow-[0_0_8px_rgba(255,107,0,0.8)]"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-400 group-hover:text-white transition-colors">Male</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <div class="relative flex items-center justify-center">
                                            <input type="radio" name="gender" value="female" class="peer sr-only">
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-600 peer-checked:border-primary transition-colors"></div>
                                            <div class="w-2.5 h-2.5 bg-primary rounded-full absolute scale-0 peer-checked:scale-100 transition-transform shadow-[0_0_8px_rgba(255,107,0,0.8)]"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-400 group-hover:text-white transition-colors">Female</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <div class="relative flex items-center justify-center">
                                            <input type="radio" name="gender" value="other" class="peer sr-only">
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-600 peer-checked:border-primary transition-colors"></div>
                                            <div class="w-2.5 h-2.5 bg-primary rounded-full absolute scale-0 peer-checked:scale-100 transition-transform shadow-[0_0_8px_rgba(255,107,0,0.8)]"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-400 group-hover:text-white transition-colors">Other</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Date of Birth -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                                <label class="w-32 text-sm text-gray-400 font-medium shrink-0">Date of Birth</label>
                                <div class="flex-1 grid grid-cols-3 gap-3">
                                    <div class="relative">
                                        <select class="w-full bg-black/40 border border-white/10 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-black/60 rounded-xl pl-4 pr-10 py-2.5 text-sm text-white transition-all outline-none appearance-none cursor-pointer">
                                            <option value="" disabled selected>DD</option>
                                            @for($i=1; $i<=31; $i++)
                                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                                    </div>
                                    <div class="relative">
                                        <select class="w-full bg-black/40 border border-white/10 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-black/60 rounded-xl pl-4 pr-10 py-2.5 text-sm text-white transition-all outline-none appearance-none cursor-pointer">
                                            <option value="" disabled selected>MM</option>
                                            @for($i=1; $i<=12; $i++)
                                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                                    </div>
                                    <div class="relative">
                                        <select class="w-full bg-black/40 border border-white/10 focus:border-primary focus:ring-1 focus:ring-primary focus:bg-black/60 rounded-xl pl-4 pr-10 py-2.5 text-sm text-white transition-all outline-none appearance-none cursor-pointer">
                                            <option value="" disabled selected>YYYY</option>
                                            @for($i=date('Y'); $i>=1900; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 flex justify-start">
                                <button class="bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white px-8 py-3 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,107,0,0.5)] transform hover:-translate-y-0.5">
                                    Save Changes
                                </button>
                            </div>
                        </div>

                        <!-- Right Side (Profile Picture) -->
                        <div class="md:w-64 flex flex-col items-center justify-center border-b md:border-b-0 md:border-l border-white/10 pb-8 md:pb-0 md:pl-10">
                            <div class="w-32 h-32 rounded-full border-2 border-white/10 overflow-hidden mb-6 bg-black/40 flex items-center justify-center relative group cursor-pointer shadow-xl transition-all hover:border-primary/50">
                                <!-- Fallback user icon if no image -->
                                <i class="ph ph-user text-5xl text-gray-500 group-hover:opacity-0 transition-opacity duration-300"></i>
                                
                                <!-- Hover overlay -->
                                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center backdrop-blur-sm">
                                    <i class="ph ph-camera text-2xl text-white mb-1"></i>
                                    <span class="text-[10px] text-white font-bold uppercase tracking-wider">Change</span>
                                </div>
                            </div>
                            
                            <button class="bg-white/5 border border-white/10 hover:border-white/30 hover:bg-white/10 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all w-full text-center">
                                Select Image
                            </button>
                            
                            <p class="text-[11px] text-gray-500 mt-4 text-center leading-relaxed">
                                File size: max. 1 MB<br>
                                File extension: .JPEG, .PNG
                            </p>
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
