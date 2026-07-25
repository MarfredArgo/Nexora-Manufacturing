<script src="https://unpkg.com/@studio-freight/lenis@1.0.39/dist/lenis.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.lenis) {
            window.lenis = new Lenis({
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                direction: 'vertical',
                gestureDirection: 'vertical',
                smooth: true,
                mouseMultiplier: 1,
                smoothTouch: false,
                touchMultiplier: 2,
                infinite: false,
            });

            function raf(time) {
                if (window.lenis) window.lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);
        }
    });
</script>
