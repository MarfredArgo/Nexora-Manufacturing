window.addEventListener('load', () => {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        const visitKey = preloader.dataset.visitKey || 'techforge_visited';
        if (!sessionStorage.getItem(visitKey)) {
            sessionStorage.setItem(visitKey, 'true');
            setTimeout(() => {
                preloader.classList.add('opacity-0');
                setTimeout(() => preloader.style.display = 'none', 1000); 
            }, 1800);
        } else {
            preloader.classList.add('opacity-0');
            setTimeout(() => preloader.style.display = 'none', 1000);
        }
    }
});
