<!-- Budget Target Modal -->
<div id="budget-modal" data-lenis-prevent class="fixed inset-0 bg-black/80 backdrop-blur-md z-[100] opacity-0 pointer-events-none transition-all duration-300 flex items-center justify-center p-4">
    <div class="liquid-glass-heavy w-full max-w-md rounded-[2rem] border border-white/10 shadow-2xl flex flex-col transform scale-95 transition-transform duration-300 relative overflow-hidden bg-[#050505]">
        
        <!-- Header -->
        <div class="px-8 py-6 border-b border-white/10 flex justify-between items-center bg-[#050505]/50">
            <div>
                <h3 class="text-xl font-black text-white" id="budget-modal-title">Set Your Budget</h3>
                <p class="text-xs text-gray-400 mt-1">We'll help you stay on track while building.</p>
            </div>
            <button onclick="closeBudgetModal()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-primary flex items-center justify-center text-white transition-all shadow-lg hover:shadow-[0_0_15px_rgba(255,107,0,0.5)]">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-8 flex flex-col gap-6 bg-[#050505]/30">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-[55%] text-gray-500 text-xl font-bold">₱</span>
                <input type="number" id="budget-input" placeholder="e.g. 150000" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-10 pr-4 text-white text-lg font-bold focus:outline-none focus:border-primary transition-colors h-14" min="0" step="1000">
            </div>

            <div class="flex gap-4">
                <button onclick="closeBudgetModal()" class="flex-1 py-3 rounded-xl border border-white/10 text-white font-bold hover:bg-white/5 transition-colors">
                    Cancel
                </button>
                <button onclick="saveBudget()" class="flex-1 bg-primary hover:bg-primary-dark text-white py-3 rounded-xl font-bold uppercase tracking-widest transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(255,107,0,0.4)]">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
