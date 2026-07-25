// Search logic
const searchContainer = document.getElementById('search-container');
const searchInput = document.getElementById('search-input');
const searchDropdown = document.getElementById('search-dropdown');
const cartDropdown = document.getElementById('cart-dropdown');
const searchClear = document.getElementById('search-clear');
const searchOverlay = document.getElementById('search-overlay');

if (searchInput && searchDropdown) {
    const ul = searchDropdown.querySelector('ul');
    const defaultSearchHtml = `
        <div class="px-5 mb-2">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Popular Searches</span>
        </div>
        <ul class="text-sm text-gray-300 flex flex-col">
            <li><a href="/search?q=RTX%204090" class="flex items-center gap-3 px-5 py-2.5 hover:bg-white/5 hover:text-primary transition-colors"><i class="ph ph-magnifying-glass text-gray-500"></i> RTX 4090</a></li>
            <li><a href="/search?q=Ryzen%207%207800X3D" class="flex items-center gap-3 px-5 py-2.5 hover:bg-white/5 hover:text-primary transition-colors"><i class="ph ph-magnifying-glass text-gray-500"></i> Ryzen 7 7800X3D</a></li>
            <li><a href="/search?q=Prebuilt%20Gaming%20PC" class="flex items-center gap-3 px-5 py-2.5 hover:bg-white/5 hover:text-primary transition-colors"><i class="ph ph-magnifying-glass text-gray-500"></i> Prebuilt Gaming PC</a></li>
            <li><a href="/search?q=32GB%20DDR5%20RAM" class="flex items-center gap-3 px-5 py-2.5 hover:bg-white/5 hover:text-primary transition-colors"><i class="ph ph-magnifying-glass text-gray-500"></i> 32GB DDR5 RAM</a></li>
        </ul>
    `;

    searchInput.addEventListener('focus', () => {
        if (window.lenis) window.lenis.stop();
        
        if (cartDropdown && !cartDropdown.classList.contains('opacity-0')) {
            cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        }

        // Show search dropdown when focused, even if empty
        searchDropdown.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-2');
        searchDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');

        if (searchInput.value.trim().length === 0) {
            searchDropdown.innerHTML = defaultSearchHtml;
        }

        if (searchOverlay) {
            searchOverlay.classList.remove('opacity-0', 'pointer-events-none');
            searchOverlay.classList.add('opacity-100', 'pointer-events-auto');
        }
    });

    let debounceTimer;
    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();
        
        if (query.length > 0) {
            searchClear.classList.remove('opacity-0', 'pointer-events-none');
            searchClear.classList.add('opacity-100', 'pointer-events-auto');
            
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`);
                    const results = await response.json();
                    
                    if (results.length > 0) {
                        searchDropdown.innerHTML = `
                            <ul class="text-sm text-gray-300 flex flex-col">
                                ` + results.map(item => `
                                    <li>
                                        <a href="/search?q=${encodeURIComponent(item.name)}" class="flex items-center justify-between px-4 py-2 hover:bg-white/5 transition-colors group">
                                            <div class="flex items-center gap-3">
                                                <i class="ph ph-magnifying-glass text-primary text-lg group-hover:scale-110 transition-transform"></i>
                                                <div class="flex flex-col">
                                                    <span class="text-gray-200 font-medium text-sm">${item.name}</span>
                                                    <span class="text-gray-500 font-light text-[10px] uppercase">${item.type}</span>
                                                </div>
                                            </div>
                                            <span class="text-primary font-bold text-xs">${item.price}</span>
                                        </a>
                                    </li>
                                `).join('') + `
                            </ul>
                        `;
                    } else {
                        searchDropdown.innerHTML = `
                            <ul class="text-sm text-gray-300 flex flex-col">
                                <li class="px-4 py-4 text-gray-500 text-sm text-center">No products found for "${query}"</li>
                            </ul>
                        `;
                    }
                } catch (error) {
                    console.error('Error fetching search suggestions:', error);
                }
            }, 300);
            
        } else {
            searchClear.classList.remove('opacity-100', 'pointer-events-auto');
            searchClear.classList.add('opacity-0', 'pointer-events-none');
            searchDropdown.innerHTML = defaultSearchHtml;
        }
    });

    if (searchClear) {
        searchClear.addEventListener('click', (e) => {
            e.preventDefault();
            searchInput.value = '';
            searchClear.classList.remove('opacity-100', 'pointer-events-auto');
            searchClear.classList.add('opacity-0', 'pointer-events-none');
            searchDropdown.innerHTML = defaultSearchHtml;
            searchInput.focus();
        });
    }

    document.addEventListener('click', (e) => {
        if (!searchContainer.contains(e.target)) {
            // Hide search dropdown
            searchDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            searchDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');

            // Hide overlay
            if (searchOverlay) {
                searchOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                searchOverlay.classList.add('opacity-0', 'pointer-events-none');
            }

            // Enable scroll
            if (window.lenis) window.lenis.start();
        }
    });
}

// Cart Dropdown Logic
const cartContainer = document.getElementById('cart-container');
const cartBtn = document.getElementById('cart-btn');

if (cartContainer && cartBtn && cartDropdown) {
    cartBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        // Toggle dropdown
        const isClosed = cartDropdown.classList.contains('opacity-0');
        
        if (isClosed) {
            // Close Search if open
            if (searchDropdown && !searchDropdown.classList.contains('opacity-0')) {
                searchDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                searchDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                
                if (searchOverlay) {
                    searchOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                    searchOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }

            // Open
            cartDropdown.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-2');
            cartDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
        } else {
            // Close
            cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        }
    });

    document.addEventListener('click', (e) => {
        if (!cartContainer.contains(e.target)) {
            // Close when clicking outside
            cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        }
    });
}

// Gaming PCs Dropdown Logic
const gamingPcsContainer = document.getElementById('gaming-pcs-container');
const gamingPcsBtn = document.getElementById('gaming-pcs-btn');
const gamingPcsDropdown = document.getElementById('gaming-pcs-dropdown');
const gamingPcsIcon = document.getElementById('gaming-pcs-icon');
const navOverlay = document.getElementById('nav-overlay');

if (gamingPcsBtn && gamingPcsDropdown && navOverlay) {
    gamingPcsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const isOpen = !gamingPcsDropdown.classList.contains('opacity-0');

        if (isOpen) {
            // Close
            gamingPcsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            gamingPcsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
            navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
            navOverlay.classList.add('opacity-0', 'pointer-events-none');
            gamingPcsIcon.classList.remove('rotate-180', 'text-primary');
            gamingPcsBtn.classList.remove('text-primary');
            
            if (!searchDropdown || searchDropdown.classList.contains('opacity-0')) {
                if (window.lenis) window.lenis.start();
            }
        } else {
            // Close search dropdown if open
            if (searchDropdown && !searchDropdown.classList.contains('opacity-0')) {
                searchDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                searchDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                if (searchOverlay) {
                    searchOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                    searchOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }
            // Close cart if open
            if (cartDropdown && !cartDropdown.classList.contains('opacity-0')) {
                cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            }
            // Close gaming laptops if open
            if (gamingLaptopsDropdown && !gamingLaptopsDropdown.classList.contains('opacity-0')) {
                gamingLaptopsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingLaptopsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (gamingLaptopsIcon) gamingLaptopsIcon.classList.remove('rotate-180', 'text-primary');
                if (gamingLaptopsBtn) gamingLaptopsBtn.classList.remove('text-primary');
            }
            // Close parts if open
            if (typeof partsDropdown !== 'undefined' && partsDropdown && !partsDropdown.classList.contains('opacity-0')) {
                partsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                partsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (partsIcon) partsIcon.classList.remove('rotate-180', 'text-primary');
                if (partsBtn) partsBtn.classList.remove('text-primary');
            }
            // Close forge store if open
            if (typeof forgeStoreDropdown !== 'undefined' && forgeStoreDropdown && !forgeStoreDropdown.classList.contains('opacity-0')) {
                forgeStoreDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                forgeStoreDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (typeof forgeStoreIcon !== 'undefined' && forgeStoreIcon) forgeStoreIcon.classList.remove('rotate-180', 'text-primary');
                if (typeof forgeStoreBtn !== 'undefined' && forgeStoreBtn) forgeStoreBtn.classList.remove('text-primary');
            }

            // Open
            gamingPcsDropdown.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            gamingPcsDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            navOverlay.classList.remove('opacity-0', 'pointer-events-none');
            navOverlay.classList.add('opacity-100', 'pointer-events-auto');
            gamingPcsIcon.classList.add('rotate-180', 'text-primary');
            gamingPcsBtn.classList.add('text-primary');
            if (window.lenis) window.lenis.stop();
        }
    });

    document.addEventListener('click', (e) => {
        if (!gamingPcsContainer.contains(e.target)) {
            // Close if clicking outside
            if (!gamingPcsDropdown.classList.contains('opacity-0')) {
                gamingPcsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingPcsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                navOverlay.classList.add('opacity-0', 'pointer-events-none');
                gamingPcsIcon.classList.remove('rotate-180', 'text-primary');
                gamingPcsBtn.classList.remove('text-primary');
                
                // Only start lenis if search is not open
                if (!searchOverlay || searchOverlay.classList.contains('opacity-0')) {
                    if (window.lenis) window.lenis.start();
                }
            }
        }
    });
}

// Gaming Laptops Dropdown Logic
const gamingLaptopsContainer = document.getElementById('gaming-laptops-container');
const gamingLaptopsBtn = document.getElementById('gaming-laptops-btn');
const gamingLaptopsDropdown = document.getElementById('gaming-laptops-dropdown');
const gamingLaptopsIcon = document.getElementById('gaming-laptops-icon');

if (gamingLaptopsBtn && gamingLaptopsDropdown && navOverlay) {
    gamingLaptopsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const isOpen = !gamingLaptopsDropdown.classList.contains('opacity-0');

        if (isOpen) {
            // Close
            gamingLaptopsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            gamingLaptopsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
            navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
            navOverlay.classList.add('opacity-0', 'pointer-events-none');
            gamingLaptopsIcon.classList.remove('rotate-180', 'text-primary');
            gamingLaptopsBtn.classList.remove('text-primary');
            
            if (!searchDropdown || searchDropdown.classList.contains('opacity-0')) {
                if (window.lenis) window.lenis.start();
            }
        } else {
            // Close search dropdown if open
            if (searchDropdown && !searchDropdown.classList.contains('opacity-0')) {
                searchDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                searchDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                if (searchOverlay) {
                    searchOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                    searchOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }
            // Close cart if open
            if (cartDropdown && !cartDropdown.classList.contains('opacity-0')) {
                cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            }
            // Close gaming PCs if open
            if (gamingPcsDropdown && !gamingPcsDropdown.classList.contains('opacity-0')) {
                gamingPcsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingPcsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (gamingPcsIcon) gamingPcsIcon.classList.remove('rotate-180', 'text-primary');
                if (gamingPcsBtn) gamingPcsBtn.classList.remove('text-primary');
            }
            // Close parts if open
            if (typeof partsDropdown !== 'undefined' && partsDropdown && !partsDropdown.classList.contains('opacity-0')) {
                partsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                partsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (partsIcon) partsIcon.classList.remove('rotate-180', 'text-primary');
                if (partsBtn) partsBtn.classList.remove('text-primary');
            }
            // Close forge store if open
            if (typeof forgeStoreDropdown !== 'undefined' && forgeStoreDropdown && !forgeStoreDropdown.classList.contains('opacity-0')) {
                forgeStoreDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                forgeStoreDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (typeof forgeStoreIcon !== 'undefined' && forgeStoreIcon) forgeStoreIcon.classList.remove('rotate-180', 'text-primary');
                if (typeof forgeStoreBtn !== 'undefined' && forgeStoreBtn) forgeStoreBtn.classList.remove('text-primary');
            }

            // Open
            gamingLaptopsDropdown.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            gamingLaptopsDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            navOverlay.classList.remove('opacity-0', 'pointer-events-none');
            navOverlay.classList.add('opacity-100', 'pointer-events-auto');
            gamingLaptopsIcon.classList.add('rotate-180', 'text-primary');
            gamingLaptopsBtn.classList.add('text-primary');
            if (window.lenis) window.lenis.stop();
        }
    });

    document.addEventListener('click', (e) => {
        if (!gamingLaptopsContainer.contains(e.target)) {
            // Close if clicking outside
            if (!gamingLaptopsDropdown.classList.contains('opacity-0')) {
                gamingLaptopsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingLaptopsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                navOverlay.classList.add('opacity-0', 'pointer-events-none');
                gamingLaptopsIcon.classList.remove('rotate-180', 'text-primary');
                gamingLaptopsBtn.classList.remove('text-primary');
                
                // Only start lenis if search is not open
                if (!searchOverlay || searchOverlay.classList.contains('opacity-0')) {
                    if (window.lenis) window.lenis.start();
                }
            }
        }
    });
}

// Parts & Accessories Dropdown Logic
const partsContainer = document.getElementById('parts-container');
const partsBtn = document.getElementById('parts-btn');
const partsDropdown = document.getElementById('parts-dropdown');
const partsIcon = document.getElementById('parts-icon');

if (partsBtn && partsDropdown && navOverlay) {
    partsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const isOpen = !partsDropdown.classList.contains('opacity-0');

        if (isOpen) {
            // Close
            partsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            partsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
            navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
            navOverlay.classList.add('opacity-0', 'pointer-events-none');
            partsIcon.classList.remove('rotate-180', 'text-primary');
            partsBtn.classList.remove('text-primary');
            
            if (!searchDropdown || searchDropdown.classList.contains('opacity-0')) {
                if (window.lenis) window.lenis.start();
            }
        } else {
            // Close search dropdown if open
            if (searchDropdown && !searchDropdown.classList.contains('opacity-0')) {
                searchDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                searchDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                if (searchOverlay) {
                    searchOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                    searchOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }
            // Close cart if open
            if (cartDropdown && !cartDropdown.classList.contains('opacity-0')) {
                cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            }
            // Close gaming PCs if open
            if (gamingPcsDropdown && !gamingPcsDropdown.classList.contains('opacity-0')) {
                gamingPcsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingPcsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (gamingPcsIcon) gamingPcsIcon.classList.remove('rotate-180', 'text-primary');
                if (gamingPcsBtn) gamingPcsBtn.classList.remove('text-primary');
            }
            // Close gaming laptops if open
            if (gamingLaptopsDropdown && !gamingLaptopsDropdown.classList.contains('opacity-0')) {
                gamingLaptopsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingLaptopsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (gamingLaptopsIcon) gamingLaptopsIcon.classList.remove('rotate-180', 'text-primary');
                if (gamingLaptopsBtn) gamingLaptopsBtn.classList.remove('text-primary');
            }

            // Open
            partsDropdown.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            partsDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            navOverlay.classList.remove('opacity-0', 'pointer-events-none');
            navOverlay.classList.add('opacity-100', 'pointer-events-auto');
            partsIcon.classList.add('rotate-180', 'text-primary');
            partsBtn.classList.add('text-primary');
            if (window.lenis) window.lenis.stop();
        }
    });

    document.addEventListener('click', (e) => {
        if (!partsContainer.contains(e.target)) {
            // Close if clicking outside
            if (!partsDropdown.classList.contains('opacity-0')) {
                partsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                partsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                navOverlay.classList.add('opacity-0', 'pointer-events-none');
                partsIcon.classList.remove('rotate-180', 'text-primary');
                partsBtn.classList.remove('text-primary');
                
                // Only start lenis if search is not open
                if (!searchOverlay || searchOverlay.classList.contains('opacity-0')) {
                    if (window.lenis) window.lenis.start();
                }
            }
        }
    });
}

// Forge Store Dropdown Logic
const forgeStoreContainer = document.getElementById('forge-store-container');
const forgeStoreBtn = document.getElementById('forge-store-btn');
const forgeStoreDropdown = document.getElementById('forge-store-dropdown');
const forgeStoreIcon = document.getElementById('forge-store-icon');

if (forgeStoreBtn && forgeStoreDropdown && navOverlay) {
    forgeStoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const isOpen = !forgeStoreDropdown.classList.contains('opacity-0');

        if (isOpen) {
            // Close
            forgeStoreDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            forgeStoreDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
            navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
            navOverlay.classList.add('opacity-0', 'pointer-events-none');
            forgeStoreIcon.classList.remove('rotate-180', 'text-primary');
            forgeStoreBtn.classList.remove('text-primary');
            
            if (!searchDropdown || searchDropdown.classList.contains('opacity-0')) {
                if (window.lenis) window.lenis.start();
            }
        } else {
            // Close search dropdown if open
            if (searchDropdown && !searchDropdown.classList.contains('opacity-0')) {
                searchDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                searchDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                if (searchOverlay) {
                    searchOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                    searchOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }
            // Close cart if open
            if (cartDropdown && !cartDropdown.classList.contains('opacity-0')) {
                cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            }
            // Close gaming PCs if open
            if (typeof gamingPcsDropdown !== 'undefined' && gamingPcsDropdown && !gamingPcsDropdown.classList.contains('opacity-0')) {
                gamingPcsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingPcsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (gamingPcsIcon) gamingPcsIcon.classList.remove('rotate-180', 'text-primary');
                if (gamingPcsBtn) gamingPcsBtn.classList.remove('text-primary');
            }
            // Close gaming laptops if open
            if (typeof gamingLaptopsDropdown !== 'undefined' && gamingLaptopsDropdown && !gamingLaptopsDropdown.classList.contains('opacity-0')) {
                gamingLaptopsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                gamingLaptopsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (gamingLaptopsIcon) gamingLaptopsIcon.classList.remove('rotate-180', 'text-primary');
                if (gamingLaptopsBtn) gamingLaptopsBtn.classList.remove('text-primary');
            }
            // Close forge store if open
            if (typeof forgeStoreDropdown !== 'undefined' && forgeStoreDropdown && !forgeStoreDropdown.classList.contains('opacity-0')) {
                forgeStoreDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                forgeStoreDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (typeof forgeStoreIcon !== 'undefined' && forgeStoreIcon) forgeStoreIcon.classList.remove('rotate-180', 'text-primary');
                if (typeof forgeStoreBtn !== 'undefined' && forgeStoreBtn) forgeStoreBtn.classList.remove('text-primary');
            }
            // Close parts if open
            if (typeof partsDropdown !== 'undefined' && partsDropdown && !partsDropdown.classList.contains('opacity-0')) {
                partsDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                partsDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                if (partsIcon) partsIcon.classList.remove('rotate-180', 'text-primary');
                if (partsBtn) partsBtn.classList.remove('text-primary');
            }

            // Open
            forgeStoreDropdown.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            forgeStoreDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            navOverlay.classList.remove('opacity-0', 'pointer-events-none');
            navOverlay.classList.add('opacity-100', 'pointer-events-auto');
            forgeStoreIcon.classList.add('rotate-180', 'text-primary');
            forgeStoreBtn.classList.add('text-primary');
            if (window.lenis) window.lenis.stop();
        }
    });

    document.addEventListener('click', (e) => {
        if (!forgeStoreContainer.contains(e.target)) {
            // Close if clicking outside
            if (!forgeStoreDropdown.classList.contains('opacity-0')) {
                forgeStoreDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                forgeStoreDropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                navOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                navOverlay.classList.add('opacity-0', 'pointer-events-none');
                forgeStoreIcon.classList.remove('rotate-180', 'text-primary');
                forgeStoreBtn.classList.remove('text-primary');
                
                // Only start lenis if search is not open
                if (!searchOverlay || searchOverlay.classList.contains('opacity-0')) {
                    if (window.lenis) window.lenis.start();
                }
            }
        }
    });
}

// Cart Add Logic
document.addEventListener('DOMContentLoaded', () => {
    // Inject Modal HTML
    const cartModalHtml = `
    <div id="cart-modal" class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="cart-modal-backdrop"></div>
        <div class="relative bg-[#121212] border border-white/10 rounded-2xl p-6 shadow-2xl max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300" id="cart-modal-content">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ph-fill ph-check-circle text-green-500"></i> Added to Cart
                </h3>
                <button id="close-cart-modal" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            <div class="flex gap-4 mb-6">
                <div class="w-16 h-16 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                    <img id="modal-product-image" src="" alt="Product" class="w-full h-full object-cover hidden">
                    <i id="modal-product-icon" class="ph-light ph-desktop text-2xl text-gray-500"></i>
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-center">
                    <p id="modal-product-name" class="text-sm font-bold text-gray-200 mb-1 line-clamp-2"></p>
                    <p id="modal-product-price" class="text-primary font-black"></p>
                </div>
            </div>
            <div class="flex gap-3">
                <button id="continue-shopping-btn" class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl py-2.5 text-sm font-bold text-white transition-colors">
                    Continue Shopping
                </button>
                <a href="/cart" class="flex-1 bg-gradient-to-r from-primary to-orange-400 hover:from-primary hover:to-orange-500 rounded-xl py-2.5 text-sm font-bold text-white text-center transition-colors shadow-[0_0_15px_rgba(255,107,0,0.3)] flex items-center justify-center">
                    View Cart
                </a>
            </div>
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', cartModalHtml);

    const cartModal = document.getElementById('cart-modal');
    const cartModalContent = document.getElementById('cart-modal-content');
    const closeCartModal = document.getElementById('close-cart-modal');
    const continueShoppingBtn = document.getElementById('continue-shopping-btn');
    const cartModalBackdrop = document.getElementById('cart-modal-backdrop');

    function hideModal() {
        cartModal.classList.remove('opacity-100', 'pointer-events-auto');
        cartModal.classList.add('opacity-0', 'pointer-events-none');
        cartModalContent.classList.remove('scale-100');
        cartModalContent.classList.add('scale-95');
        if (window.lenis) window.lenis.start();
    }

    [closeCartModal, continueShoppingBtn, cartModalBackdrop].forEach(el => {
        if(el) el.addEventListener('click', hideModal);
    });

    // Flying Animation function
    function animateAddToCart(sourceElement) {
        const cartBtn = document.getElementById('cart-btn');
        if (!cartBtn || !sourceElement) return;

        const sourceRect = sourceElement.getBoundingClientRect();
        const targetRect = cartBtn.getBoundingClientRect();

        const flyingEl = document.createElement('div');
        flyingEl.classList.add('fixed', 'bg-primary', 'rounded-full', 'z-[110]', 'pointer-events-none');
        
        flyingEl.style.width = '16px';
        flyingEl.style.height = '16px';
        flyingEl.style.left = (sourceRect.left + sourceRect.width / 2 - 8) + 'px';
        flyingEl.style.top = (sourceRect.top + sourceRect.height / 2 - 8) + 'px';
        flyingEl.style.transition = 'all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1)';
        flyingEl.style.boxShadow = '0 0 15px rgba(255,107,0,1)';
        
        document.body.appendChild(flyingEl);

        // Force reflow
        void flyingEl.offsetWidth;

        flyingEl.style.left = (targetRect.left + targetRect.width / 2 - 8) + 'px';
        flyingEl.style.top = (targetRect.top + targetRect.height / 2 - 8) + 'px';
        flyingEl.style.transform = 'scale(0.3)';
        flyingEl.style.opacity = '0.2';

        setTimeout(() => {
            flyingEl.remove();
        }, 800);
    }

    // Initial cart count fetch
    fetch('/cart/count')
        .then(res => res.json())
        .then(data => {
            updateCartCount(data.cart_count);
        })
        .catch(err => console.error(err));

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.add-to-cart-btn');
        if (!btn) return;
        
        e.preventDefault();
            
            const productId = btn.dataset.productId || 'mock-' + Math.floor(Math.random() * 1000);
            const name = btn.dataset.name || 'Mock Product';
            let priceStr = btn.dataset.price || '0';
            const price = parseFloat(priceStr.replace(/[^0-9.-]+/g,"")); // Remove P, commas
            const imageUrl = btn.dataset.image || '';

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            btn.classList.add('opacity-50', 'cursor-wait');

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    name: name,
                    price: price,
                    quantity: 1,
                    image_url: imageUrl
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.classList.remove('opacity-50', 'cursor-wait');
                if (data.success) {
                    // Trigger flying animation first
                    animateAddToCart(btn);

                    setTimeout(() => {
                        updateCartCount(data.cart_count);
                        
                        // Show modal
                        document.getElementById('modal-product-name').textContent = name;
                        document.getElementById('modal-product-price').textContent = 'P' + price.toLocaleString();
                        
                        const imgEl = document.getElementById('modal-product-image');
                        const iconEl = document.getElementById('modal-product-icon');
                        if (imageUrl) {
                            imgEl.src = imageUrl;
                            imgEl.classList.remove('hidden');
                            iconEl.classList.add('hidden');
                        } else {
                            imgEl.classList.add('hidden');
                            iconEl.classList.remove('hidden');
                        }
                        
                        cartModal.classList.remove('opacity-0', 'pointer-events-none');
                        cartModal.classList.add('opacity-100', 'pointer-events-auto');
                        cartModalContent.classList.remove('scale-95');
                        cartModalContent.classList.add('scale-100');
                        if (window.lenis) window.lenis.stop();
                    }, 400); // delay modal slightly to let user see animation start
                }
            })
            .catch(err => {
                console.error(err);
                btn.classList.remove('opacity-50', 'cursor-wait');
            });
    });

    function updateCartCount(count) {
        // Specifically target only the notification badge span
        const cartBadges = document.querySelectorAll('#cart-btn > div.relative > span.bg-primary');
        cartBadges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
                
                // Add a little pop animation to the badge when updated
                badge.classList.remove('animate-ping');
                void badge.offsetWidth; // trigger reflow
                badge.classList.add('animate-pulse');
                setTimeout(() => badge.classList.remove('animate-pulse'), 1000);
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        });
    }
});
