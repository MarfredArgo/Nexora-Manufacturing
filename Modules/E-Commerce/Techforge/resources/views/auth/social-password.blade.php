<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <title>{{ config('app.name', 'TechForge') }} | Set Password</title>
    
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
            min-height: 100vh;
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
    </style>

    @vite('Modules/E-Commerce/Techforge/resources/css/liquidglass.css')
</head>
<body class="relative antialiased selection:bg-primary selection:text-white flex items-center justify-center">

    <!-- Background Ambient Effects -->
    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>

    <div class="w-full max-w-md px-6 relative z-10 py-12">
        <a href="{{ route('ecommerce.login') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors mb-8 group text-sm font-medium">
            <i class="ph ph-arrow-left group-hover:-translate-x-1 transition-transform"></i> Back to Login
        </a>

        <!-- Form Container -->
        <div class="liquid-glass rounded-[2rem] p-8 border border-white/10 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent opacity-50 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-col items-center mb-8">
                    <div class="bg-gradient-to-br from-primary to-orange-400 w-12 h-12 rounded-xl flex items-center justify-center shadow-[0_0_20px_rgba(255,107,0,0.4)] mb-4">
                        <i class="ph-fill ph-lock-key text-2xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1">Set Password</h2>
                    <p class="text-sm text-gray-400 font-light text-center">You're almost done! Please set a password for <strong class="text-white">{{ $socialUser['email'] }}</strong></p>
                </div>
                
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-xs rounded-xl p-3 mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('ecommerce.social.process-registration') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="password" class="block text-xs font-medium text-gray-300 mb-1.5 ml-1">Password</label>
                        <div class="relative group">
                            <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors text-lg"></i>
                            <input type="password" name="password" id="password" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-11 pr-11 text-white placeholder-gray-500 focus:outline-none focus:border-primary/50 focus:bg-white/10 transition-all text-sm" placeholder="••••••••" required>
                            <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors">
                                <i class="ph ph-eye text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <label for="remember" class="flex items-center gap-2 mt-2 ml-1 cursor-pointer group w-fit">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="remember" id="remember" class="peer appearance-none w-4 h-4 rounded border border-white/20 bg-white/5 checked:bg-primary checked:border-primary transition-all cursor-pointer shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i class="ph-bold ph-check absolute text-white text-[10px] opacity-0 peer-checked:opacity-100 pointer-events-none transition-all scale-50 peer-checked:scale-100 duration-200"></i>
                        </div>
                        <span class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors select-none">Remember me for 30 days</span>
                    </label>

                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-[#ff8c33] hover:from-[#ff8c33] hover:to-primary text-white py-3.5 rounded-xl font-bold transition-all duration-300 shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,107,0,0.5)] hover:-translate-y-0.5 mt-4 flex items-center justify-center gap-2">
                        Complete Sign Up <i class="ph-bold ph-check"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ph-eye');
                    icon.classList.add('ph-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ph-eye-slash');
                    icon.classList.add('ph-eye');
                }
            });
        });
    </script>
</body>
</html>
