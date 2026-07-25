<div id="global-notification-container" class="fixed bottom-6 right-6 z-[200] flex flex-col gap-3 pointer-events-none items-end justify-end">
    <!-- Toasts injected here -->
</div>

<script>
    window.showNotification = function(title, message, type = 'alert', onConfirm = null) {
        const container = document.getElementById('global-notification-container');
        
        // Ensure max 3 notifications
        while (container.children.length >= 3) {
            container.removeChild(container.firstChild);
        }
        
        const toast = document.createElement('div');
        toast.className = "pointer-events-auto liquid-glass-heavy w-[380px] p-5 rounded-2xl border border-white/10 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.8)] flex flex-col transform translate-y-8 opacity-0 transition-all duration-300 bg-[#050505]/95 backdrop-blur-xl";
        
        let iconHtml = '<i class="ph-fill ph-warning-circle text-xl"></i>';
        
        const close = () => {
            toast.classList.add('translate-x-8', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 300);
        };
        
        toast.innerHTML = `
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-primary/20 text-primary flex items-center justify-center shrink-0">
                    ${iconHtml}
                </div>
                <div class="flex-1 pt-0.5">
                    <h3 class="text-base font-bold text-white leading-none mb-1.5">${title}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-1">${message}</p>
                </div>
                <button class="notification-close-btn text-gray-500 hover:text-white pointer-events-auto h-fit -mt-1 -mr-1">
                    <i class="ph ph-x"></i>
                </button>
            </div>
        `;
        
        // Attach event listeners
        const closeBtn = toast.querySelector('.notification-close-btn');
        closeBtn.onclick = close;
        
        container.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-8', 'opacity-0');
            });
        });
        
        // Auto hide after 5s
        setTimeout(close, 5000);
    };
</script>
