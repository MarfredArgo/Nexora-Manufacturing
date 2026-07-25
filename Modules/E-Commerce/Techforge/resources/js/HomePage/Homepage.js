import Lenis from 'lenis'

//Lenis Smooth Scrolling
const lenis = new Lenis();
window.lenis = lenis; // Expose globally for Navbar.js

function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// Carousel Logic
const carouselData = [
    {
        img: 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        title: 'AMD RYZEN 9 5950X w/ RTX 5090',
        desc: 'Extreme 4K Gaming Performance',
        price: 'P56,000'
    },
    {
        img: 'https://images.unsplash.com/photo-1591488320449-011701bb6704?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        title: 'INTEL CORE i9 14900K w/ RTX 4080',
        desc: 'Ultimate Content Creation Machine',
        price: 'P62,500'
    },
    {
        img: 'https://images.unsplash.com/photo-1547082299-de196ea013d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        title: 'CUSTOM WATERCOOLED PC',
        desc: 'Premium custom loop cooling',
        price: 'P120,000'
    }
];

let currentIndex = 0;
const imgEl = document.getElementById('carousel-img');
const titleEl = document.getElementById('carousel-title');
const descEl = document.getElementById('carousel-desc');
const priceEl = document.getElementById('carousel-price');
const dots = document.querySelectorAll('#carousel-dots button');

if (imgEl) {
    function updateCarousel(index) {
        currentIndex = index;

        // Fade out with slight slide down
        [imgEl, titleEl, descEl, priceEl].forEach(el => {
            el.classList.add('opacity-0', 'translate-y-2');
        });

        setTimeout(() => {
            // Update data
            imgEl.src = carouselData[index].img;
            titleEl.textContent = carouselData[index].title;
            descEl.textContent = carouselData[index].desc;
            priceEl.textContent = carouselData[index].price;
            
            const btnEl = document.getElementById('carousel-add-btn');
            if(btnEl) {
                btnEl.dataset.name = carouselData[index].title;
                btnEl.dataset.price = carouselData[index].price;
            }

            // Update dots
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-primary shadow-[0_0_12px_rgba(255,107,0,0.8)] transition-all duration-300 focus:outline-none';
                } else {
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-white/20 transition-all duration-300 hover:bg-white/50 focus:outline-none';
                }
            });

            // Fade in with slight slide up
            [imgEl, titleEl, descEl, priceEl].forEach(el => {
                el.classList.remove('opacity-0', 'translate-y-2');
            });
        }, 500); // Wait for fade out to complete (matching CSS transition duration)
    }

    // Auto rotate every 3 seconds
    let interval = setInterval(() => {
        let nextIndex = (currentIndex + 1) % carouselData.length;
        updateCarousel(nextIndex);
    }, 3000);

    // Click dots to change manually
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            clearInterval(interval); // Reset timer on manual click
            updateCarousel(index);
            interval = setInterval(() => {
                let nextIndex = (currentIndex + 1) % carouselData.length;
                updateCarousel(nextIndex);
            }, 3000);
        });
    });
}
