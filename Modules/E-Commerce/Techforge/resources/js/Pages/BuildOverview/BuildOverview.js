// We assume window.PageConfig is set in the view before this script is loaded.
document.addEventListener('DOMContentLoaded', function() {
    const allComponents = window.PageConfig.allComponents;
    const initialBuild = window.PageConfig.initialBuild;

    // ConfiguratorEngine should be available globally via asset inclusion, or we can assume it exists
    if (typeof ConfiguratorEngine !== 'undefined') {
        window.engine = new ConfiguratorEngine(allComponents, initialBuild);
        setTimeout(() => updateVisualizer(initialBuild), 100);
        
        let currentCategory = '';
        
        const typeMapping = {
            'Processor': 'Processor',
            'Video Card': 'Video Card',
            'Memory': 'Memory',
            'Primary Storage': 'Storage',
            'Motherboard': 'Motherboard',
            'Power Supply': 'Power Supply',
            'Case': 'Case'
        };

        let availableComponents = [];

        // UI Updates
        const updateVisualizer = (build) => {
            document.querySelectorAll('.visualizer-slot').forEach(el => el.classList.remove('active'));
            
            const mapping = {
                'Processor': ['vis-cpu', 'vis-cooler'],
                'Motherboard': ['vis-motherboard'],
                'Memory': ['vis-memory', 'vis-memory-2'],
                'Video Card': ['vis-gpu'],
                'Primary Storage': ['vis-ssd'],
                'Power Supply': ['vis-psu'],
                'Case': ['vis-case']
            };

            Object.keys(build).forEach(cat => {
                if(build[cat] && mapping[cat]) {
                    mapping[cat].forEach(id => {
                        const el = document.getElementById(id);
                        if(el) el.classList.add('active');
                    });
                }
            });
        };

        window.engine.subscribe((build) => {
            updateVisualizer(build);
            updatePriceUI();
            
            // Sync all labels
            Object.keys(build).forEach(category => {
                const component = build[category];
                updateUIText(category, component ? component.name : 'Select ' + category);
            });
        });

        function renderModalProducts() {
            const list = document.getElementById('modal-products');
            const search = document.getElementById('modal-search').value.toLowerCase();
            const sort = document.getElementById('modal-sort').value;
            const pMin = parseFloat(document.getElementById('modal-price-min').value) || 0;
            const pMax = parseFloat(document.getElementById('modal-price-max').value) || Infinity;
            const showIncompatible = document.getElementById('show-incompatible').checked;
            
            let filtered = availableComponents.filter(c => {
                const matchName = c.name.toLowerCase().includes(search);
                const matchPrice = c.price >= pMin && c.price <= pMax;
                return matchName && matchPrice;
            });
            
            if (sort === 'name_asc') filtered.sort((a,b) => a.name.localeCompare(b.name));
            if (sort === 'price_asc') filtered.sort((a,b) => a.price - b.price);
            if (sort === 'price_desc') filtered.sort((a,b) => b.price - a.price);

            const processed = filtered.map(c => {
                const compatibility = window.engine.checkCompatibility(c, currentCategory);
                return { ...c, compatible: compatibility.compatible, reason: compatibility.reason };
            });

            const finalDisplay = processed.filter(c => showIncompatible || c.compatible);

            list.innerHTML = '';
            if (finalDisplay.length === 0) {
                list.innerHTML = '<div class="col-span-full text-center py-12"><i class="ph ph-magnifying-glass text-4xl text-gray-600 mb-2"></i><p class="text-gray-500">No components found.</p></div>';
            } else {
                finalDisplay.forEach(c => {
                    const currentComp = window.engine.getComponent(currentCategory);
                    const isSelected = currentComp && currentComp.id === c.id;
                    const onClick = isSelected ? '' : `onclick="selectComponent(${c.id})"`;
                    
                    const getComponentIcon = (type) => {
                        const icons = {
                            'Processor': 'ph-cpu',
                            'Video Card': 'ph-graphics-card',
                            'Memory': 'ph-memory',
                            'Storage': 'ph-hard-drives',
                            'Motherboard': 'ph-circuitry',
                            'Power Supply': 'ph-plug',
                            'Case': 'ph-computer-tower'
                        };
                        return icons[type] || 'ph-question';
                    };

                    const imageOrIcon = c.image_url 
                        ? `<img src="${c.image_url}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-300">`
                        : `<i class="ph ${getComponentIcon(c.type)} text-6xl text-gray-500 group-hover:text-primary group-hover:scale-110 transition-all duration-300"></i>`;
                        
                    const opacityClass = !c.compatible ? 'opacity-50 grayscale pointer-events-none' : '';
                    const borderClass = isSelected ? 'border-primary shadow-[0_0_15px_rgba(255,107,0,0.3)]' : 'border-white/5 hover:border-white/20 cursor-pointer';

                    list.innerHTML += `
                        <div class="liquid-glass p-4 rounded-2xl border ${borderClass} ${opacityClass} flex flex-col transition-all group relative" ${c.compatible ? onClick : ''}>
                            <div class="w-full h-32 mb-4 bg-white/5 rounded-xl flex items-center justify-center p-2">
                                ${imageOrIcon}
                            </div>
                            <h4 class="font-bold text-white text-sm mb-2 leading-tight">${c.name}</h4>
                            <div class="mt-auto flex justify-between items-end">
                                <p class="text-primary font-black">P${parseFloat(c.price).toLocaleString()}</p>
                                ${isSelected ? '<div class="bg-primary text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-widest">Selected</div>' : ''}
                            </div>
                            ${!c.compatible ? `<div class="absolute top-2 right-2 bg-red-500/90 text-white text-[9px] font-black uppercase tracking-wider px-2 py-1 rounded shadow-lg backdrop-blur-sm z-10">${c.reason}</div>` : ''}
                        </div>
                    `;
                });
            }
        }

        window.openModal = function(label) {
            const dbType = typeMapping[label];
            const modal = document.getElementById('product-modal');
            const box = modal.querySelector('.liquid-glass-heavy');
            const title = document.getElementById('modal-title');
            
            if(!dbType) {
                alert('No alternative parts available for ' + label);
                return;
            }
            
            currentCategory = label;
            title.innerText = 'Select ' + label;
            
            availableComponents = allComponents.filter(c => c.type === dbType);
            
            document.getElementById('modal-search').value = '';
            document.getElementById('modal-sort').value = 'name_asc';
            document.getElementById('modal-price-min').value = '';
            document.getElementById('modal-price-max').value = '';

            renderModalProducts();
            
            modal.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            if (window.lenis) window.lenis.stop();
        };

        window.closeModal = function() {
            const modal = document.getElementById('product-modal');
            const box = modal.querySelector('.liquid-glass-heavy');
            modal.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            if (window.lenis) window.lenis.start();
        };

        const modalSearch = document.getElementById('modal-search');
        if (modalSearch) modalSearch.addEventListener('input', renderModalProducts);
        
        const modalSort = document.getElementById('modal-sort');
        if (modalSort) modalSort.addEventListener('change', renderModalProducts);
        
        const modalPriceMin = document.getElementById('modal-price-min');
        if (modalPriceMin) modalPriceMin.addEventListener('input', renderModalProducts);
        
        const modalPriceMax = document.getElementById('modal-price-max');
        if (modalPriceMax) modalPriceMax.addEventListener('input', renderModalProducts);
        
        const showIncompatible = document.getElementById('show-incompatible');
        if (showIncompatible) showIncompatible.addEventListener('change', renderModalProducts);
        
        const modalResetFilters = document.getElementById('modal-reset-filters');
        if (modalResetFilters) {
            modalResetFilters.addEventListener('click', () => {
                document.getElementById('modal-search').value = '';
                document.getElementById('modal-sort').value = 'name_asc';
                document.getElementById('modal-price-min').value = '';
                document.getElementById('modal-price-max').value = '';
                document.getElementById('show-incompatible').checked = false;
                renderModalProducts();
            });
        }

        function updatePriceUI() {
            const total = window.engine.calculateTotal();
            const priceEl = document.querySelector('.text-3xl.font-black.text-white');
            if (priceEl) priceEl.innerText = 'P' + total.toLocaleString();
        }

        function updateUIText(category, text) {
            const specsList = document.getElementById('specs-list');
            if (!specsList) return;
            const rows = specsList.querySelectorAll('.group');
            rows.forEach(row => {
                const labelEl = row.querySelector('.text-xs.font-bold.text-gray-400');
                if (labelEl && labelEl.innerText.toLowerCase() === category.toLowerCase()) {
                    const valueEl = row.querySelector('.text-sm.font-bold.text-gray-200');
                    if (valueEl) valueEl.innerText = text;
                }
            });
        }

        window.addToCart = function() {
            const currentBuild = window.engine.currentBuild;
            const missing = Object.entries(currentBuild).filter(([k,v]) => v === null);
            if (missing.length > 0) {
                alert("Please select components for: " + missing.map(m => m[0]).join(', '));
                return;
            }

            const btn = document.querySelector('button[onclick="addToCart()"]');
            if (!btn) return;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ph ph-spinner animate-spin"></i> Adding...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('product_id', 'custom_' + Date.now());
            formData.append('name', 'Custom ' + window.PageConfig.productName);
            formData.append('price', window.engine.calculateTotal());
            formData.append('image_url', window.PageConfig.productImageUrl);
            formData.append('quantity', 1);
            formData.append('configuration', window.engine.getCartPayload());

            fetch(window.PageConfig.cartAddRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.PageConfig.csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    btn.innerHTML = '<i class="ph-bold ph-check"></i> Added';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        const badge = document.querySelector('#cart-btn span');
                        if (badge) {
                            badge.classList.remove('hidden');
                            badge.classList.add('flex');
                            badge.innerText = data.cart_count;
                        }
                    }, 2000);
                }
            });
        };

        window.selectComponent = function(id) {
            const component = availableComponents.find(c => c.id === id);
            
            const conflicts = window.engine.getConflictsIfSelected(currentCategory, component);
            
            if (conflicts.length > 0) {
                let msg = "Changing this component will require changing your " + conflicts.join(', ') + ". Proceed?";
                if(!confirm(msg)) {
                    return;
                }
                conflicts.forEach(cat => window.engine.removeComponent(cat));
            }
            
            window.engine.setComponent(currentCategory, component);
            window.closeModal();
        };
    }
});
