<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechForge | Custom PC Configurator</title>
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #ffffff; }
        .liquid-glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col items-center justify-center py-20 px-4">

    <!-- Ambient background -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[50vw] h-[50vw] bg-primary/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[40vw] h-[40vw] bg-red-600/10 blur-[100px] rounded-full"></div>
    </div>

    <div class="max-w-4xl w-full liquid-glass rounded-3xl p-8 shadow-2xl border-white/10">
        <h1 class="text-4xl font-black text-white mb-2 text-center">Build Your Ultimate PC</h1>
        <p class="text-gray-400 text-center mb-10 text-sm">Our intelligent configurator ensures 100% compatibility across all components.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- CPU -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2"><i class="ph-bold ph-cpu text-primary"></i> Processor (CPU)</label>
                <select id="cpu-select" class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl p-4 text-white font-medium focus:border-primary focus:outline-none transition-colors appearance-none">
                    <option value="">Select CPU...</option>
                </select>
                <div class="text-[10px] text-gray-500" id="cpu-info"></div>
            </div>

            <!-- Motherboard -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2"><i class="ph-bold ph-circuitry text-primary"></i> Motherboard</label>
                <select id="mobo-select" disabled class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl p-4 text-white font-medium focus:border-primary focus:outline-none transition-colors appearance-none disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">Select CPU First...</option>
                </select>
                <div class="text-[10px] text-gray-500" id="mobo-info"></div>
            </div>

            <!-- RAM -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2"><i class="ph-bold ph-memory text-primary"></i> Memory (RAM)</label>
                <select id="ram-select" disabled class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl p-4 text-white font-medium focus:border-primary focus:outline-none transition-colors appearance-none disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">Select Motherboard First...</option>
                </select>
                <div class="text-[10px] text-gray-500" id="ram-info"></div>
            </div>

            <!-- GPU -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2"><i class="ph-bold ph-graphics-card text-primary"></i> Graphics Card (GPU)</label>
                <select id="gpu-select" class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl p-4 text-white font-medium focus:border-primary focus:outline-none transition-colors appearance-none">
                    <option value="">Select GPU...</option>
                </select>
                <div class="text-[10px] text-gray-500" id="gpu-info"></div>
            </div>

            <!-- Power Supply -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2"><i class="ph-bold ph-plug text-primary"></i> Power Supply (PSU)</label>
                <select id="psu-select" disabled class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl p-4 text-white font-medium focus:border-primary focus:outline-none transition-colors appearance-none disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">Select CPU and GPU First...</option>
                </select>
                <div class="text-[10px] text-gray-500" id="psu-info"></div>
            </div>

            <!-- Case -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2"><i class="ph-bold ph-computer-tower text-primary"></i> Case</label>
                <select id="case-select" disabled class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl p-4 text-white font-medium focus:border-primary focus:outline-none transition-colors appearance-none disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">Select Motherboard First...</option>
                </select>
                <div class="text-[10px] text-gray-500" id="case-info"></div>
            </div>

        </div>

        <div class="mt-12 border-t border-white/10 pt-8 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">Total Price</p>
                <p class="text-4xl font-black text-white" id="total-price">P0</p>
            </div>
            <button class="bg-primary hover:bg-primary-hover text-white px-8 py-4 rounded-xl font-bold uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(255,107,0,0.3)] hover:shadow-[0_0_30px_rgba(255,107,0,0.5)]">
                Checkout Build
            </button>
        </div>
    </div>

    <script>
        window.PageConfig = {
            cpus: @json($cpus),
            motherboards: @json($motherboards),
            rams: @json($rams),
            gpus: @json($gpus),
            powerSupplies: @json($powerSupplies),
            cases: @json($cases)
        };
    </script>
    @vite('Modules/E-Commerce/Techforge/resources/js/Pages/Configurator/Configurator.js')
</body>
</html>
