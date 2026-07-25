<div id="global-confirmation" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[300] opacity-0 pointer-events-none transition-all duration-300 flex items-center justify-center p-4">
    <div id="confirmation-box" class="liquid-glass-heavy w-full max-w-sm p-6 rounded-2xl border border-white/10 shadow-2xl flex flex-col transform scale-95 transition-transform duration-300 bg-[#050505]">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-full bg-primary/20 text-primary flex items-center justify-center mb-4 border border-primary/30">
                <i class="ph-fill ph-warning-circle text-3xl"></i>
            </div>
            <h3 id="confirmation-title" class="text-xl font-bold text-white mb-2">Confirm</h3>
            <p id="confirmation-message" class="text-gray-400 text-sm mb-6 leading-relaxed">
                Are you sure?
            </p>
            <div class="flex w-full gap-3" id="confirmation-actions">
                <button id="confirmation-btn-cancel" class="flex-1 px-4 py-2.5 rounded-xl font-bold text-sm bg-white/5 text-white hover:bg-white/10 transition-colors border border-white/10">Cancel</button>
                <button id="confirmation-btn-confirm" class="flex-1 px-4 py-2.5 rounded-xl font-bold text-sm bg-primary text-white hover:bg-[#ff8c33] transition-colors shadow-[0_0_15px_rgba(255,107,0,0.3)] hover:shadow-[0_0_25px_rgba(255,107,0,0.5)]">Proceed</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.showConfirmation = function(title, message, onConfirm) {
        const modal = document.getElementById('global-confirmation');
        const box = document.getElementById('confirmation-box');
        
        document.getElementById('confirmation-title').innerText = title;
        document.getElementById('confirmation-message').innerText = message;
        
        const close = () => {
            modal.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
        };
        
        const btnCancel = document.getElementById('confirmation-btn-cancel');
        const btnConfirm = document.getElementById('confirmation-btn-confirm');
        
        btnCancel.onclick = close;
        
        btnConfirm.onclick = () => {
            close();
            if(onConfirm) onConfirm();
        };
        
        modal.classList.remove('opacity-0', 'pointer-events-none');
        box.classList.remove('scale-95');
    };
</script>
