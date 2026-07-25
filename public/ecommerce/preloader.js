(() => {
    const dismiss = () => {
        const preloader = document.getElementById('preloader');

        if (! preloader || preloader.dataset.dismissed === 'true') {
            return;
        }

        preloader.dataset.dismissed = 'true';
        preloader.style.opacity = '0';
        preloader.style.pointerEvents = 'none';

        window.setTimeout(() => preloader.remove(), 500);
    };

    // Leave the original animation visible briefly, then always release the
    // page even when a third-party asset is slow or unavailable.
    window.addEventListener('load', () => window.setTimeout(dismiss, 850), { once: true });
    window.setTimeout(dismiss, 4000);
})();
