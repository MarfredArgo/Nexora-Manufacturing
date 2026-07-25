function bindMobileFilter() {
    const mobileFilterBtn = document.getElementById('mobile-filter-btn');
    const filterSidebar = document.getElementById('filter-sidebar');
    const closeFilterBtn = document.getElementById('close-filter-btn');
    if (mobileFilterBtn && filterSidebar && closeFilterBtn) {
        mobileFilterBtn.addEventListener('click', () => {
            filterSidebar.classList.remove('translate-x-full');
            filterSidebar.classList.add('translate-x-0');
        });
        closeFilterBtn.addEventListener('click', () => {
            filterSidebar.classList.remove('translate-x-0');
            filterSidebar.classList.add('translate-x-full');
        });
    }
}
bindMobileFilter();

// Client-side filtering logic
let configs = window.initialConfigs || [];
const appUrl = window.appUrl || '';
let searchTab = new URLSearchParams(window.location.search).get('tab') || 'prebuilt';
let currentPage = 1;
const itemsPerPage = 6;

let filterForm = document.getElementById('filter-form');
let productGrid = document.getElementById('product-grid');
let paginationContainer = document.getElementById('pagination-container');
let productCountEl = document.getElementById('product-count');

function augmentProcName(name) {
    if (!name) return '';
    if (!name.startsWith('AMD') && name.includes('Ryzen')) return 'AMD ' + name;
    if (!name.startsWith('Intel') && name.includes('Core')) return 'Intel ' + name;
    return name;
}

function augmentGpuName(name) {
    if (!name) return '';
    if (!name.startsWith('NVIDIA') && (name.includes('RTX') || name.includes('GTX'))) return 'NVIDIA ' + name;
    if (!name.startsWith('AMD') && name.includes('RX')) return 'AMD ' + name;
    return name;
}

function formatNumber(num) {
    return parseInt(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderProducts(products) {
    if (!productGrid) return;
    
    // Update count
    if (productCountEl) {
        productCountEl.textContent = products.length;
    }

    // Pagination calculations
    const totalPages = Math.ceil(products.length / itemsPerPage);
    if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentProducts = products.slice(startIndex, endIndex);

    // Render HTML
    if (currentProducts.length === 0) {
        productGrid.innerHTML = `
            <div class="col-span-1 sm:col-span-2 xl:col-span-3 py-20 flex flex-col items-center justify-center text-center bg-black/20 rounded-[2rem] border border-white/5">
                <i class="ph ph-magnifying-glass text-6xl text-gray-600 mb-6"></i>
                <h3 class="text-2xl font-bold text-white mb-2">No items found</h3>
                <p class="text-gray-400">Try adjusting your search or filters.</p>
            </div>
        `;
        paginationContainer.innerHTML = '';
        return;
    }

    let html = '';
    currentProducts.forEach(config => {
        let imageUrl = config.image_url ? 
            (config.image_url.startsWith('http') ? config.image_url : `${appUrl}/${config.image_url}`) 
            : 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=800&q=80';
        
        let detailsHtml = '';
        let actionBtnHtml = '';

        if (config.is_part) {
            detailsHtml = `
                <div class="space-y-1.5 mb-4 text-xs text-gray-400">
                    <p>Standalone component.</p>
                </div>
            `;
            actionBtnHtml = `
                <button type="button" 
                    class="add-to-cart-btn w-full py-2 rounded-full border border-primary text-primary hover:bg-primary hover:text-white font-bold transition-all duration-300 text-center flex items-center justify-center gap-2 text-sm"
                    data-product-id="${config.id}"
                    data-name="${config.name}"
                    data-price="${config.price}"
                    data-image="${imageUrl}">
                    <i class="ph-bold ph-shopping-cart"></i> Add to Cart
                </button>
            `;
        } else {
            const cpuName = config.cpu?.name || 'N/A';
            const moboName = config.motherboard?.name || 'N/A';
            const gpuName = config.gpu?.name || 'N/A';
            const ramName = config.ram?.name || 'N/A';
            const storageName = config.storage?.name || 'N/A';
            const psuName = config.powerSupply?.name || 'N/A';

            detailsHtml = `
                <div class="space-y-1.5 mb-4 text-xs">
                    <div class="flex items-center gap-2 text-gray-400"><i class="ph ph-cpu text-gray-500 text-sm shrink-0"></i> <span class="text-gray-300 truncate">${cpuName}</span></div>
                    <div class="flex items-center gap-2 text-gray-400"><i class="ph ph-circuitry text-gray-500 text-sm shrink-0"></i> <span class="text-gray-300 truncate">${moboName}</span></div>
                    <div class="flex items-center gap-2 text-gray-400"><i class="ph ph-graphics-card text-gray-500 text-sm shrink-0"></i> <span class="text-gray-300 truncate">${gpuName}</span></div>
                    <div class="flex items-center gap-2 text-gray-400"><i class="ph ph-memory text-gray-500 text-sm shrink-0"></i> <span class="text-gray-300 truncate">${ramName}</span></div>
                    <div class="flex items-center gap-2 text-gray-400"><i class="ph ph-hard-drives text-gray-500 text-sm shrink-0"></i> <span class="text-gray-300 truncate">${storageName}</span></div>
                    <div class="flex items-center gap-2 text-gray-400"><i class="ph ph-plug text-gray-500 text-sm shrink-0"></i> <span class="text-gray-300 truncate">${psuName}</span></div>
                </div>
            `;

            if (searchTab === 'prebuilt') {
                actionBtnHtml = `
                    <button type="button" 
                        class="add-to-cart-btn w-full py-2 rounded-full border border-primary text-primary hover:bg-primary hover:text-white font-bold transition-all duration-300 text-center flex items-center justify-center gap-2 text-sm"
                        data-product-id="${config.id}"
                        data-name="${config.name}"
                        data-price="${config.price}"
                        data-image="${imageUrl}">
                        <i class="ph-bold ph-shopping-cart"></i> Add to Cart
                    </button>
                `;
            } else {
                actionBtnHtml = `
                    <a href="${appUrl}/configurator-overview/${config.id}" class="w-full py-2 rounded-full border border-primary text-primary hover:bg-primary hover:text-white font-bold transition-all duration-300 text-center flex items-center justify-center gap-2 text-sm">
                        <i class="ph-bold ph-wrench"></i> Customize This Build
                    </a>
                `;
            }
        }

        html += `
            <div class="bg-gradient-to-b from-[#2a110a] to-[#140502] border border-[#3a1810] rounded-[2rem] p-4 relative overflow-hidden group hover:border-primary/50 transition-all duration-500 hover:shadow-[0_10px_30px_rgba(255,107,0,0.2)] flex flex-col h-full">
                <div class="relative rounded-2xl overflow-hidden aspect-[4/3] mb-5 bg-[#0a0a0a]">
                    <img src="${imageUrl}" alt="${config.name}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 opacity-90 group-hover:opacity-100">
                </div>
                <div class="flex flex-col flex-1">
                    <h3 class="text-lg font-bold text-white group-hover:text-primary transition-colors line-clamp-1 mb-3">${config.name}</h3>
                    ${detailsHtml}
                    <hr class="border-white/10 my-4">
                    <div class="mt-auto pt-2">
                        <div class="flex flex-col mb-4">
                            <span class="text-xl font-black text-white">P${formatNumber(config.price)}</span>
                        </div>
                        ${actionBtnHtml}
                    </div>
                </div>
            </div>
        `;
    });

    productGrid.innerHTML = html;

    // Render Pagination
    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml += `
            <button ${currentPage === 1 ? 'disabled' : ''} class="px-4 py-2 bg-[#2a110a] hover:bg-[#3a1810] disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-white font-bold transition-colors" data-page="${currentPage - 1}">Prev</button>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                paginationHtml += `
                    <button class="px-4 py-2 ${currentPage === i ? 'bg-primary' : 'bg-[#2a110a] hover:bg-[#3a1810]'} rounded-lg text-white font-bold transition-colors" data-page="${i}">${i}</button>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                paginationHtml += `<span class="px-4 py-2 text-gray-500">...</span>`;
            }
        }

        paginationHtml += `
            <button ${currentPage === totalPages ? 'disabled' : ''} class="px-4 py-2 bg-[#2a110a] hover:bg-[#3a1810] disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-white font-bold transition-colors" data-page="${currentPage + 1}">Next</button>
        `;
    }
    paginationContainer.innerHTML = paginationHtml;

    // Add event listeners to new pagination buttons
    const pageBtns = paginationContainer.querySelectorAll('button[data-page]');
    pageBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = parseInt(btn.dataset.page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            applyFilters(); // Re-render with new page
        });
    });
}

function applyFilters() {
    if (!filterForm) return;

    const formData = new FormData(filterForm);
    const minPrice = parseFloat(formData.get('price_min')) || 0;
    const maxPrice = parseFloat(formData.get('price_max')) || 9999999;
    const processors = formData.getAll('processor[]');
    const gpus = formData.getAll('gpu[]');
    const rams = formData.getAll('ram[]');
    const storages = formData.getAll('storage[]');
    const sort = formData.get('sort') || 'Recommended';
    const queryStr = formData.get('q') ? formData.get('q').toLowerCase() : '';

    let filtered = configs.filter(product => {
        // Tab Category
        if (product.search_category !== searchTab) return false;

        // Price
        if (product.price < minPrice || product.price > maxPrice) return false;

        // Optional: Client-side text query search (if changing tab while searching)
        if (queryStr) {
            const productName = (product.name || '').toLowerCase();
            const cpuNameStr = (product.cpu?.name || '').toLowerCase();
            const gpuNameStr = (product.gpu?.name || '').toLowerCase();
            
            if (!productName.includes(queryStr) && 
                !cpuNameStr.includes(queryStr) && 
                !gpuNameStr.includes(queryStr)) {
                return false;
            }
        }

        if (product.is_part) return true; // parts tab has no component filters

        // Processor
        if (processors.length > 0) {
            const procName = augmentProcName(product.cpu?.name);
            if (!processors.includes(procName)) return false;
        }

        // GPU
        if (gpus.length > 0) {
            const gpuName = augmentGpuName(product.gpu?.name);
            if (!gpus.includes(gpuName)) return false;
        }

        // RAM
        if (rams.length > 0) {
            const ramName = product.ram?.name;
            if (!rams.includes(ramName)) return false;
        }

        // Storage
        if (storages.length > 0) {
            const storageName = product.storage?.name;
            if (storageName) {
                const size = storageName.trim().split(' ')[0]; // e.g. "1TB" from "1TB NVMe"
                if (!storages.includes(size)) return false;
            } else {
                return false;
            }
        }

        return true;
    });

    // Sort
    if (sort === 'Price: Low to High') {
        filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
    } else if (sort === 'Price: High to Low') {
        filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
    } else {
        // Default sort (highest price first, simulating 'recommended' for gaming PCs)
        filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
    }

    renderProducts(filtered);
}

if (filterForm) {
    // Override the native submit method so Category.js calls this instead
    filterForm.submit = function() {
        currentPage = 1;
        applyFilters();
    };

    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        applyFilters();
    });
    
    // Initial render
    applyFilters();
}

function bindAjaxTabs() {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('.tab-link');
        if (!link) return;
        
        e.preventDefault();
        const url = link.href;
        
        searchTab = new URL(url).searchParams.get('tab') || 'prebuilt';
        currentPage = 1;
        
        // Update active styling
        document.querySelectorAll('.tab-link').forEach(t => {
            t.classList.remove('bg-primary', 'text-white', 'font-bold');
            t.classList.add('text-gray-400', 'hover:text-white', 'hover:bg-white/5');
            
            const badge = t.querySelector('span');
            if (badge) {
                badge.classList.remove('bg-black/30', 'text-white');
                badge.classList.add('bg-white/10', 'text-gray-400');
            }
        });
        
        link.classList.remove('text-gray-400', 'hover:text-white', 'hover:bg-white/5');
        link.classList.add('bg-primary', 'text-white', 'font-bold');
        
        const linkBadge = link.querySelector('span');
        if (linkBadge) {
            linkBadge.classList.remove('bg-white/10', 'text-gray-400');
            linkBadge.classList.add('bg-black/30', 'text-white');
        }
        
        window.history.pushState({}, '', url);
        
        // If filter sidebar exists but applies only to PCs, and we switch to Parts, we might want to hide it
        const filterSidebarWrapper = document.getElementById('filter-sidebar-wrapper'); 
        const mobileFilterBtn = document.getElementById('mobile-filter-btn');
        if (searchTab === 'parts' || searchTab === 'laptops') {
            if (filterSidebarWrapper) filterSidebarWrapper.style.display = 'none';
            if (mobileFilterBtn) mobileFilterBtn.style.display = 'none';
        } else {
            if (filterSidebarWrapper) filterSidebarWrapper.style.display = '';
            if (mobileFilterBtn) mobileFilterBtn.style.display = '';
        }
        
        applyFilters();
    });
}
bindAjaxTabs();
