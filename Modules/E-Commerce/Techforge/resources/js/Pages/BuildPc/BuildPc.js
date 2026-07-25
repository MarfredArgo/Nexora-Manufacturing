// We assume window.PageConfig is set in the view before this script is loaded.
document.addEventListener('DOMContentLoaded', () => {
    let radarChartInstance = null;
    
    const allComponents = window.PageConfig.allComponents || [];
    window.engine = new ConfiguratorEngine(allComponents);

    const componentGroups = [
        {
            id: 'core',
            name: 'Core Components',
            items: [
                { id: 'Processor', name: 'Processor', icon: 'ph-cpu', essential: true, visId: 'vis-cpu', dbType: 'Processor' },
                { id: 'Motherboard', name: 'Motherboard', icon: 'ph-circuitry', essential: true, visId: 'vis-motherboard', dbType: 'Motherboard' }
            ]
        },
        {
            id: 'memory_storage',
            name: 'Memory & Storage',
            items: [
                { id: 'Memory', name: 'Memory (RAM)', icon: 'ph-memory', essential: true, visId: 'vis-memory', dbType: 'Memory' },
                { id: 'Primary Storage', name: 'Primary Storage (SSD)', icon: 'ph-hard-drives', essential: true, visId: 'vis-ssd', dbType: 'Storage', storageFilter: 'primary' },
                { id: 'Secondary Storage', name: 'Secondary Storage', icon: 'ph-hard-drive', essential: false, visId: '', dbType: 'Storage', storageFilter: 'secondary' }
            ]
        },
        {
            id: 'graphics_power',
            name: 'Graphics & Power',
            items: [
                { id: 'Video Card', name: 'Graphics Card', icon: 'ph-graphics-card', essential: true, visId: 'vis-gpu', dbType: 'Video Card' },
                { id: 'Power Supply', name: 'Power Supply', icon: 'ph-plug', essential: true, visId: 'vis-psu', dbType: 'Power Supply' }
            ]
        },
        {
            id: 'chassis_cooling',
            name: 'Chassis & Cooling',
            items: [
                { id: 'Case', name: 'Case', icon: 'ph-desktop-tower', essential: true, visId: 'vis-case', dbType: 'Case' },
                { id: 'CPU Cooler', name: 'CPU Cooler', icon: 'ph-fan', essential: true, visId: 'vis-cooler', dbType: 'Cooling' },
                { id: 'Case Fan', name: 'Case Fan', icon: 'ph-wind', essential: false, visId: '', dbType: 'Case Fan' }
            ]
        }
    ];

    const totalEssential = 7;
    let maxBudget = localStorage.getItem('pc_build_budget') ? parseInt(localStorage.getItem('pc_build_budget')) : 0;
    let currentSelectingCategory = null;
    let currentSelectingDbType = null;
    let availableComponents = [];
    let lastSelectedCategory = null;

    // --- DOM Elements ---
    const componentsListEl = document.getElementById('components-list');
    const totalPriceEl = document.getElementById('total-price');
    const powerDrawEl = document.getElementById('power-draw');
    const compCountEl = document.getElementById('comp-count');
    const budgetCurrentEl = document.getElementById('budget-current');
    
    const budgetBar = document.getElementById('budget-bar');
    const compBar = document.getElementById('comp-bar');
    const powerBar = document.getElementById('power-bar');

    const modalEl = document.getElementById('product-modal');
    const modalTitleEl = document.getElementById('modal-title');
    const modalProductsEl = document.getElementById('modal-products');

    // Filters DOM
    const modalSearchEl = document.getElementById('modal-search');
    const modalSortEl = document.getElementById('modal-sort');
    const modalFilterBrandEl = document.getElementById('modal-filter-brand');
    const modalFilterSocketEl = document.getElementById('modal-filter-socket');
    const modalFilterPlatformEl = document.getElementById('modal-filter-platform');
    const modalPriceMinEl = document.getElementById('modal-price-min');
    const modalPriceMaxEl = document.getElementById('modal-price-max');
    const modalResetFiltersEl = document.getElementById('modal-reset-filters');

    // --- Format Currency ---
    const formatPHP = (amount) => {
        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
    };

    // --- Budget Functions ---
    window.openBudgetModal = () => {
        const budgetModal = document.getElementById('budget-modal');
        if (budgetModal) {
            const input = document.getElementById('budget-input');
            if (input) input.value = maxBudget > 0 ? maxBudget : '';
            budgetModal.classList.remove('opacity-0', 'pointer-events-none');
            const box = budgetModal.querySelector('.liquid-glass-heavy');
            if (box) box.classList.remove('scale-95');
        }
    };

    window.closeBudgetModal = () => {
        const budgetModal = document.getElementById('budget-modal');
        if (budgetModal) {
            budgetModal.classList.add('opacity-0', 'pointer-events-none');
            const box = budgetModal.querySelector('.liquid-glass-heavy');
            if (box) box.classList.add('scale-95');
            
            // If they close without setting a budget and it's 0, just leave it as 0
            if (maxBudget === 0) {
                document.getElementById('budget-max').textContent = 'Not Set';
            }
        }
    };

    window.saveBudget = () => {
        const input = document.getElementById('budget-input');
        if (input && input.value) {
            maxBudget = parseInt(input.value) || 0;
            localStorage.setItem('pc_build_budget', maxBudget);
            document.getElementById('budget-max').textContent = formatPHP(maxBudget);
            updateSummary();
            window.closeBudgetModal();
        }
    };

    // --- Init ---
    if (maxBudget === 0) {
        document.getElementById('budget-max').textContent = 'Not Set';
        // Open modal on first load if budget isn't set
        setTimeout(window.openBudgetModal, 500);
    } else {
        document.getElementById('budget-max').textContent = formatPHP(maxBudget);
    }

    // --- Update UI from Engine ---
    window.engine.subscribe((build) => {
        renderComponents();
        updateSummary();
        updateVisualizer();
        updateSteps();
    });

    // --- Render Components List ---
    const renderComponents = () => {
        componentsListEl.innerHTML = '';
        let html = '';

        componentGroups.forEach(group => {
            html += `<div class="mb-4">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 pl-2">${group.name}</h3>
                        <div class="space-y-1.5">`;
            
            group.items.forEach(cat => {
                const selectedProduct = window.engine.getComponent(cat.id);
                let compStatus = { compatible: true, reason: '' };
                if (selectedProduct) {
                    compStatus = window.engine.checkCompatibility(selectedProduct, cat.id);
                }

                const borderClasses = selectedProduct 
                    ? (!compStatus.compatible ? 'border-red-500/50 bg-red-500/5' : 'border-primary/30 bg-primary/5') 
                    : 'border-white/5';

                html += `
                    <div class="liquid-glass rounded-xl p-2.5 flex flex-col gap-2 component-slot border ${borderClasses} relative overflow-hidden group">
                        <div class="flex items-center justify-between gap-3 relative z-10">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border overflow-hidden ${selectedProduct && selectedProduct.image_url ? 'bg-transparent border-transparent' : (selectedProduct ? (!compStatus.compatible ? 'bg-red-500/10 border-red-500/30' : 'bg-primary/10 border-primary/30') : 'bg-white/5 border-white/10')}">
                                    ${selectedProduct && selectedProduct.image_url
                                        ? `<img src="${selectedProduct.image_url}" alt="${selectedProduct.name}" class="w-full h-full object-cover">`
                                        : `<i class="ph ${cat.icon} text-lg ${selectedProduct ? (!compStatus.compatible ? 'text-red-500' : 'text-primary') : 'text-gray-500'}"></i>`
                                    }
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none mb-1">${cat.name} ${cat.essential ? '<span class="text-primary">*</span>' : ''}</h4>
                                    ${selectedProduct 
                                        ? `<h3 class="text-sm font-bold ${!compStatus.compatible ? 'text-red-400' : 'text-white'} leading-tight truncate">${selectedProduct.name}</h3>`
                                        : `<h3 class="text-xs font-medium text-gray-500 italic">Select component</h3>`
                                    }
                                </div>
                            </div>
                            <div class="flex flex-col items-end shrink-0 gap-1">
                                ${selectedProduct 
                                    ? `<span class="text-sm font-black ${!compStatus.compatible ? 'text-red-400' : 'text-white'}">${formatPHP(selectedProduct.price)}</span>
                                       <div class="flex gap-1 mt-1">
                                           <button onclick="openModal('${cat.id}', '${cat.dbType}')" class="text-[10px] px-2 py-1 rounded bg-white/10 hover:bg-white/20 text-white font-semibold transition-colors">Change</button>
                                           <button onclick="window.engine.removeComponent('${cat.id}')" class="text-[10px] px-2 py-1 rounded bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white transition-colors"><i class="ph ph-x"></i></button>
                                       </div>`
                                    : `<button onclick="openModal('${cat.id}', '${cat.dbType}')" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-primary text-gray-300 hover:text-white text-xs font-bold transition-all border border-white/10 hover:border-transparent">
                                            Choose
                                       </button>`
                                }
                            </div>
                        </div>
                        ${!compStatus.compatible ? `
                        <div class="w-full bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] px-2.5 py-1.5 rounded-lg flex items-center gap-1.5 font-bold uppercase tracking-wider animate-pulse relative z-10">
                            <i class="ph ph-warning-circle text-sm"></i> Incompatible: ${compStatus.reason}
                        </div>
                        ` : ''}
                        
                        ${selectedProduct && cat.id === lastSelectedCategory ? `
                        <div class="absolute top-0 bottom-0 left-0 w-[50%] bg-gradient-to-r from-transparent via-primary/40 to-transparent animate-component-shine pointer-events-none z-20 mix-blend-overlay"></div>
                        ` : ''}
                    </div>
                `;
            });
            html += `</div></div>`;
        });

        componentsListEl.innerHTML = html;
        lastSelectedCategory = null;
    };

    const updateVisualizer = () => {
        document.querySelectorAll('.visualizer-slot').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.visualizer-text').forEach(el => el.classList.remove('active-text'));
        
        componentGroups.forEach(group => {
            group.items.forEach(cat => {
                if(window.engine.getComponent(cat.id) && cat.visId) {
                    const visEl = document.getElementById(cat.visId);
                    const textEl = document.getElementById(cat.visId + '-text');
                    
                    if(visEl) visEl.classList.add('active');
                    if(textEl) textEl.classList.add('active-text');
                    
                    if(cat.id === 'Memory') {
                        document.getElementById('vis-memory-2')?.classList.add('active');
                    }
                }
            });
        });
    };

    const updateSummary = () => {
        const total = window.engine.calculateTotal();
        const wattage = window.engine.getRequiredWattage();
        
        let count = 0; // Essential components count
        let totalCount = 0; // All selected components count
        let partsHtml = '';

        let catNames = {};
        let essentialCats = [];
        componentGroups.forEach(g => g.items.forEach(i => {
            catNames[i.id] = i.name;
            if (i.essential) essentialCats.push(i.id);
        }));

        Object.entries(window.engine.currentBuild).forEach(([catId, prod]) => {
            if (prod) {
                totalCount++;
                if (essentialCats.includes(catId)) count++;
                
                const catName = catNames[catId] || catId;
                partsHtml += `<div class="flex justify-between items-center text-xs py-1 border-b border-white/5 last:border-0"><span class="text-gray-400 truncate pr-2 flex-1">${catName}</span><span class="text-white font-bold">${formatPHP(prod.price)}</span></div>`;
            }
        });
        const summaryPartsList = document.getElementById('summary-parts-list');
        if (summaryPartsList) summaryPartsList.innerHTML = partsHtml;

        if (totalPriceEl) totalPriceEl.textContent = formatPHP(total);
        if (powerDrawEl) powerDrawEl.textContent = Math.ceil(wattage);
        if (compCountEl) compCountEl.textContent = count;
        if (budgetCurrentEl) budgetCurrentEl.textContent = formatPHP(total);

        if (budgetBar) {
            if (maxBudget > 0) {
                budgetBar.style.width = Math.min((total / maxBudget) * 100, 100) + '%';
                const warningEl = document.getElementById('budget-warning');
                if (total > maxBudget) {
                    budgetBar.classList.remove('bg-primary');
                    budgetBar.classList.add('bg-red-500');
                    if (warningEl) warningEl.classList.remove('hidden');
                } else {
                    budgetBar.classList.remove('bg-red-500');
                    budgetBar.classList.add('bg-primary');
                    if (warningEl) warningEl.classList.add('hidden');
                }
            } else {
                budgetBar.style.width = '0%';
                budgetBar.classList.remove('bg-red-500');
                budgetBar.classList.add('bg-primary');
                const warningEl = document.getElementById('budget-warning');
                if (warningEl) warningEl.classList.add('hidden');
            }
        }

        if (compBar) compBar.style.width = Math.min((count / 8) * 100, 100) + '%';
        if (powerBar) powerBar.style.width = Math.min((wattage / 1200) * 100, 100) + '%';

        const scoreEl = document.getElementById('build-score');
        const badgeEl = document.getElementById('build-badge');
        if (scoreEl && badgeEl) {
            let score = 0;
            if (count > 0) {
                score += Math.min(30, (count / 8) * 30);
                score += Math.min(70, (total / 250000) * 70);
            }
            const targetScore = Math.round(score);
            const currentScore = parseInt(scoreEl.textContent) || 0;
            
            if (currentScore !== targetScore) {
                if (scoreEl.animationId) cancelAnimationFrame(scoreEl.animationId);
                const duration = 800; // ms
                const startTime = performance.now();
                
                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    // easeOutExpo
                    const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    
                    scoreEl.textContent = Math.round(currentScore + (targetScore - currentScore) * easeProgress);
                    
                    if (progress < 1) {
                        scoreEl.animationId = requestAnimationFrame(animate);
                    } else {
                        scoreEl.textContent = targetScore;
                    }
                };
                scoreEl.animationId = requestAnimationFrame(animate);
            }

            badgeEl.classList.remove('hidden', 'bg-gray-500/20', 'text-gray-300', 'bg-blue-500/20', 'text-blue-400', 'bg-purple-500/20', 'text-purple-400', 'bg-primary/20', 'text-primary');
            
            if (score === 0) {
                badgeEl.classList.add('hidden');
            } else if (score < 40) {
                badgeEl.textContent = 'Core';
                badgeEl.classList.add('bg-gray-500/20', 'text-gray-300');
            } else if (score < 70) {
                badgeEl.textContent = 'Advanced';
                badgeEl.classList.add('bg-blue-500/20', 'text-blue-400');
            } else if (score < 90) {
                badgeEl.textContent = 'Extreme';
                badgeEl.classList.add('bg-purple-500/20', 'text-purple-400');
            } else {
                badgeEl.textContent = 'Apex';
                badgeEl.classList.add('bg-primary/20', 'text-primary');
            }
            
            const compareCurrentEl = document.getElementById('compare-current');
            if (compareCurrentEl) {
                compareCurrentEl.textContent = targetScore;
            }
        }

        const checkoutBtn = document.getElementById('add-to-cart-btn');
        if (checkoutBtn) {
            checkoutBtn.disabled = false;
            checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'grayscale', 'pointer-events-none');
            
            if (count >= essentialCats.length) {
                checkoutBtn.innerHTML = 'Checkout Build <i class="ph-fill ph-check-circle text-lg"></i>';
            } else {
                checkoutBtn.innerHTML = '<i class="ph ph-magic-wand text-lg"></i> Fill Missing Components';
            }
        }
        
        // Update Chart
        const ctx = document.getElementById('radarChart');
        if (ctx) {
            const cpu = window.engine.currentBuild['Processor'] ? Math.min(100, 40 + (parseFloat(window.engine.currentBuild['Processor'].price) / 1000)) : 0;
            const ram = window.engine.currentBuild['Memory'] ? Math.min(100, 40 + (parseFloat(window.engine.currentBuild['Memory'].price) / 500)) : 0;
            const gpu = window.engine.currentBuild['Video Card'] ? Math.min(100, 40 + (parseFloat(window.engine.currentBuild['Video Card'].price) / 2000)) : 0;
            const ssd = window.engine.currentBuild['Primary Storage'] ? Math.min(100, 40 + (parseFloat(window.engine.currentBuild['Primary Storage'].price) / 300)) : 0;
            const psu = window.engine.currentBuild['Power Supply'] ? Math.min(100, 40 + (parseFloat(window.engine.currentBuild['Power Supply'].price) / 200)) : 0;

            const chartData = [cpu, ram, gpu, ssd, psu];

            if (radarChartInstance) {
                radarChartInstance.data.datasets[0].data = chartData;
                radarChartInstance.update();
            } else {
                radarChartInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: ['CPU', 'RAM', 'GPU', 'SSD', 'PSU'],
                        datasets: [{
                            label: 'Performance',
                            data: chartData,
                            backgroundColor: 'rgba(255, 107, 0, 0.4)',
                            borderColor: '#ff6b00',
                            pointBackgroundColor: '#ff6b00',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#ff6b00'
                        }]
                    },
                    options: {
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { display: false, maxTicksLimit: 5 },
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                pointLabels: { color: '#9ca3af', font: { size: 10, weight: 'bold' } },
                                angleLines: { color: 'rgba(255, 255, 255, 0.1)' }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        },
                        maintainAspectRatio: false
                    }
                });
            }
        }
    };

    const updateSteps = () => {
        const checkStep = (stepId, conditions) => {
            const stepEl = document.getElementById(stepId);
            if (!stepEl) return;
            const dot = stepEl.querySelector('.step-dot');
            const text = stepEl.querySelector('.step-text');
            const isActive = conditions.some(c => !!window.engine.getComponent(c));
            if (isActive) {
                if (dot) dot.className = 'w-8 h-8 rounded-full border-2 border-primary bg-primary text-white flex items-center justify-center text-xs font-bold step-dot active';
                if (text) text.className = 'text-[10px] text-white font-bold uppercase tracking-wider step-text';
            } else {
                if (dot) dot.className = 'w-8 h-8 rounded-full border-2 border-white/20 bg-black text-gray-400 flex items-center justify-center text-xs font-bold step-dot';
                if (text) text.className = 'text-[10px] text-gray-500 font-bold uppercase tracking-wider step-text';
            }
        };

        checkStep('step-1', ['Processor', 'Motherboard']);
        checkStep('step-2', ['Memory']);
        checkStep('step-3', ['Primary Storage']);
        checkStep('step-4', ['Video Card']);
        checkStep('step-5', ['Power Supply']);
        checkStep('step-6', ['Case']);
        checkStep('step-7', ['CPU Cooler']);
    };

    // --- Modal Functions ---
    window.openModal = (categoryId, dbType) => {
        currentSelectingCategory = categoryId;
        currentSelectingDbType = dbType;
        
        let catName = categoryId;
        let catIcon = '';
        let storageFilter = null;
        
        componentGroups.forEach(g => g.items.forEach(i => {
            if(i.id === categoryId) { 
                catName = i.name; 
                catIcon = i.icon; 
                if (i.storageFilter) storageFilter = i.storageFilter;
            }
        }));
        
        if (modalTitleEl) modalTitleEl.innerHTML = `<i class="ph ${catIcon} text-primary"></i> Select ${catName}`;
        
        if(modalSearchEl) modalSearchEl.value = '';
        if(modalSortEl) modalSortEl.value = 'name_asc';
        if(modalPriceMinEl) modalPriceMinEl.value = '';
        if(modalPriceMaxEl) modalPriceMaxEl.value = '';

        availableComponents = allComponents.filter(c => {
            if (c.type !== dbType) return false;
            if (dbType === 'Storage' && storageFilter && c.storage_type) {
                const typeStr = c.storage_type.toLowerCase();
                const isSataOrHdd = typeStr.includes('sata') || typeStr.includes('hdd');
                if (storageFilter === 'primary' && isSataOrHdd) return false;
                if (storageFilter === 'secondary' && !isSataOrHdd) return false;
            }
            return true;
        });

        renderModalProducts();
        
        if (modalEl) {
            modalEl.classList.remove('opacity-0', 'pointer-events-none');
            const box = modalEl.querySelector('.liquid-glass-heavy');
            if (box) box.classList.remove('scale-95');
        }
    };

    window.closeModal = () => {
        if (modalEl) {
            modalEl.classList.add('opacity-0', 'pointer-events-none');
            const box = modalEl.querySelector('.liquid-glass-heavy');
            if (box) box.classList.add('scale-95');
        }
    };

    const renderModalProducts = () => {
        if (!modalProductsEl) return;
        
        const search = modalSearchEl ? modalSearchEl.value.toLowerCase() : '';
        const sort = modalSortEl ? modalSortEl.value : 'name_asc';
        const pMin = modalPriceMinEl ? parseFloat(modalPriceMinEl.value) || 0 : 0;
        const pMax = modalPriceMaxEl ? parseFloat(modalPriceMaxEl.value) || Infinity : Infinity;
        
        const showIncompatibleEl = document.getElementById('show-incompatible');
        const showIncompatible = showIncompatibleEl ? showIncompatibleEl.checked : false;
        
        let filtered = availableComponents.filter(c => {
            const matchName = c.name.toLowerCase().includes(search);
            const matchPrice = c.price >= pMin && c.price <= pMax;
            return matchName && matchPrice;
        });
        
        if (sort === 'name_asc') filtered.sort((a,b) => a.name.localeCompare(b.name));
        if (sort === 'price_asc') filtered.sort((a,b) => a.price - b.price);
        if (sort === 'price_desc') filtered.sort((a,b) => b.price - a.price);

        const processed = filtered.map(c => {
            const compatibility = window.engine.checkCompatibility(c, currentSelectingCategory);
            return { ...c, compatible: compatibility.compatible, reason: compatibility.reason };
        });

        const finalDisplay = processed.filter(c => showIncompatible || c.compatible);

        const buildCard = (c) => {
            const currentComp = window.engine.getComponent(currentSelectingCategory);
            const isSelected = currentComp && currentComp.id === c.id;
            const onClick = isSelected ? '' : `onclick="selectComponent(${c.id})"`;
            
            const opacityClass = !c.compatible ? 'opacity-60 grayscale' : '';
            
            let borderClass = 'border-white/5 hover:border-white/20 cursor-pointer';
            if (isSelected) borderClass = 'border-primary shadow-[0_0_15px_rgba(255,107,0,0.3)]';
            if (!c.compatible) borderClass = 'border-red-500/40 bg-red-500/5 cursor-pointer hover:border-red-500/60';

            let iconClass = 'ph-cube';
            if (c.type === 'Processor') iconClass = 'ph-cpu';
            else if (c.type === 'Video Card') iconClass = 'ph-graphics-card';
            else if (c.type === 'Memory') iconClass = 'ph-memory';
            else if (c.type === 'Storage') iconClass = 'ph-hard-drives';
            else if (c.type === 'Motherboard') iconClass = 'ph-circuitry';
            else if (c.type === 'Power Supply') iconClass = 'ph-plug';
            else if (c.type === 'Case') iconClass = 'ph-computer-tower';
            else if (c.type === 'Cooling') iconClass = 'ph-fan';
            else if (c.type === 'Case Fan') iconClass = 'ph-wind';

            const imageHtml = c.image_url 
                ? `<img src="${c.image_url}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-300 relative z-0">`
                : `<i class="ph-light ${iconClass} text-6xl text-gray-500 group-hover:text-primary group-hover:scale-110 transition-all duration-300 relative z-0"></i>`;

            let badgesHtml = '';
            const renderBadge = (label, value) => {
                if (value === null || value === undefined || value === '') return '';
                return `<div class="flex items-center gap-1 bg-white/5 border border-white/10 px-2 py-0.5 rounded text-[9px] text-gray-300 uppercase tracking-wider"><span class="text-gray-500 font-medium">${label}:</span> <span class="font-bold">${value}</span></div>`;
            };

            let badgesContent = '';
            if (c.type === 'Processor') {
                badgesContent += renderBadge('Cores', c.core_count);
                badgesContent += renderBadge('PCC', c.core_clock);
                badgesContent += renderBadge('PCBC', c.boost_clock);
                badgesContent += renderBadge('Arch', c.microarchitecture);
                badgesContent += renderBadge('TDP', c.tdp ? c.tdp + 'W' : null);
                badgesContent += renderBadge('IG', c.integrated_graphics);
            } else if (c.type === 'Video Card') {
                badgesContent += renderBadge('Chipset', c.chipset);
                badgesContent += renderBadge('Memory', c.memory ? c.memory + 'GB' : null);
                badgesContent += renderBadge('Boost', c.boost_clock);
                badgesContent += renderBadge('Color', c.color);
                badgesContent += renderBadge('Length', c.length_mm ? c.length_mm + 'mm' : null);
            } else if (c.type === 'Memory') {
                badgesContent += renderBadge('Speed', c.speed ? c.speed + ' MT/s' : null);
                badgesContent += renderBadge('Modules', c.modules);
            } else if (c.type === 'Storage') {
                badgesContent += renderBadge('Capacity', c.capacity ? c.capacity + 'GB' : null);
                badgesContent += renderBadge('Type', c.storage_type || c.type);
                badgesContent += renderBadge('Cache', c.cache);
                badgesContent += renderBadge('Form', c.form_factor);
                badgesContent += renderBadge('Interface', c.interface);
            } else if (c.type === 'Power Supply') {
                badgesContent += renderBadge('Type', c.type); 
                badgesContent += renderBadge('Efficiency', c.efficiency);
                badgesContent += renderBadge('Wattage', c.wattage ? c.wattage + 'W' : null);
                badgesContent += renderBadge('Modular', c.modular);
                badgesContent += renderBadge('Color', c.color);
            } else if (c.type === 'Motherboard') {
                badgesContent += renderBadge('Socket', c.socket);
                const formFactorMap = {1: 'E-ATX', 2: 'ATX', 3: 'Micro-ATX', 4: 'Mini-ITX'};
                badgesContent += renderBadge('Form', formFactorMap[c.form_factor] || c.form_factor);
                badgesContent += renderBadge('Max RAM', c.memory_max);
                badgesContent += renderBadge('Slots', c.memory_slots);
                badgesContent += renderBadge('Color', c.color);
            } else if (c.type === 'Case') {
                badgesContent += renderBadge('Type', c.type);
                badgesContent += renderBadge('Color', c.color);
                badgesContent += renderBadge('Panel', c.side_panel);
            } else if (c.type === 'Cooling') {
                badgesContent += renderBadge('RPM', c.fan_rpm);
                badgesContent += renderBadge('Noise', c.noise_level);
                badgesContent += renderBadge('Color', c.color);
                badgesContent += renderBadge('Radiator', c.radiator_size);
            } else if (c.type === 'Case Fan') {
                badgesContent += renderBadge('Size', c.size);
                badgesContent += renderBadge('RPM', c.rpm);
                badgesContent += renderBadge('Airflow', c.airflow);
                badgesContent += renderBadge('Noise', c.noise_level);
                badgesContent += renderBadge('Color', c.color);
                badgesContent += renderBadge('RGB', c.rgb ? 'Yes' : 'No');
            }

            if (badgesContent) {
                badgesHtml = `<div class="flex flex-wrap gap-1.5 mb-2 mt-1">${badgesContent}</div>`;
            }

            return `
                <div class="liquid-glass p-4 rounded-2xl border ${borderClass} ${opacityClass} flex flex-col transition-all group relative cursor-pointer" ${onClick}>
                    <div class="w-full h-32 mb-4 bg-white/5 rounded-xl flex items-center justify-center p-2 relative overflow-hidden" style="isolation: isolate;">
                        ${imageHtml}
                        ${isSelected ? `
                        <div class="absolute left-0 right-0 top-0 bottom-0 m-auto h-10 bg-primary group-hover:bg-white transition-colors duration-300 border-y border-primary/50 group-hover:border-white shadow-xl shadow-primary/20 z-20 flex items-center justify-center cursor-default pointer-events-none">
                            <span class="text-white group-hover:text-primary transition-colors duration-300 text-[12px] font-black uppercase tracking-widest">Selected</span>
                        </div>
                        ` : ''}
                        ${!c.compatible ? `
                        <div class="absolute left-0 right-0 top-0 bottom-0 m-auto min-h-[40px] py-1 bg-red-600/90 backdrop-blur-md border-y border-red-500 shadow-xl shadow-red-600/20 z-20 flex items-center justify-center cursor-default pointer-events-none px-3 text-center">
                            <span class="text-white text-[10px] font-black uppercase tracking-widest leading-tight drop-shadow-md">${c.reason}</span>
                        </div>
                        ` : ''}
                    </div>
                    <h4 class="font-bold text-white text-sm leading-tight ${badgesHtml ? 'mb-1' : 'mb-2'}">${c.name}</h4>
                    ${badgesHtml}
                    <div class="mt-auto flex justify-between items-end">
                        <p class="text-primary font-black">P${parseFloat(c.price).toLocaleString()}</p>
                    </div>
                </div>
            `;
        };

        modalProductsEl.innerHTML = '';
        if (finalDisplay.length === 0) {
            modalProductsEl.innerHTML = '<div class="text-center py-12"><i class="ph ph-magnifying-glass text-4xl text-gray-600 mb-2"></i><p class="text-gray-500">No components found.</p></div>';
        } else {
            modalProductsEl.innerHTML = `
                <div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        ${finalDisplay.map(buildCard).join('')}
                    </div>
                </div>
            `;
        }
    };

    window.selectComponent = (id) => {
        const component = availableComponents.find(c => c.id === id);
        lastSelectedCategory = currentSelectingCategory;
        window.engine.setComponent(currentSelectingCategory, component);
        window.closeModal();
    };

    // Event Listeners for modal filters
    if(modalSearchEl) modalSearchEl.addEventListener('input', renderModalProducts);
    if(modalSortEl) modalSortEl.addEventListener('change', renderModalProducts);
    if(modalPriceMinEl) modalPriceMinEl.addEventListener('input', renderModalProducts);
    if(modalPriceMaxEl) modalPriceMaxEl.addEventListener('input', renderModalProducts);
    
    const showIncompatibleEl = document.getElementById('show-incompatible');
    if(showIncompatibleEl) showIncompatibleEl.addEventListener('change', renderModalProducts);

    if(modalResetFiltersEl) {
        modalResetFiltersEl.addEventListener('click', () => {
            if(modalSearchEl) modalSearchEl.value = '';
            if(modalSortEl) modalSortEl.value = 'name_asc';
            if(modalPriceMinEl) modalPriceMinEl.value = '';
            if(modalPriceMaxEl) modalPriceMaxEl.value = '';
            if(showIncompatibleEl) showIncompatibleEl.checked = false;
            renderModalProducts();
        });
    }
    const closeModalBtn = document.getElementById('close-modal');
    if(closeModalBtn) closeModalBtn.addEventListener('click', window.closeModal);

    // Add to Cart Logic
    const checkoutBtn = document.getElementById('add-to-cart-btn');
    if(checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            const essentialOrder = ['Processor', 'Motherboard', 'Memory', 'Primary Storage', 'Video Card', 'Power Supply', 'Case', 'CPU Cooler'];
            const firstMissing = essentialOrder.find(id => !window.engine.getComponent(id));

            if (firstMissing) {
                let catToOpen = null;
                componentGroups.forEach(g => {
                    const found = g.items.find(i => i.id === firstMissing);
                    if (found) catToOpen = found;
                });
                if (catToOpen) {
                    window.openModal(catToOpen.id, catToOpen.dbType);
                }
                return;
            }

            const originalText = checkoutBtn.innerHTML;
            checkoutBtn.innerHTML = '<i class="ph ph-spinner animate-spin"></i> Processing...';
            checkoutBtn.disabled = true;

            // Front-end only simulation
            setTimeout(() => {
                checkoutBtn.innerHTML = '<i class="ph-bold ph-check-circle"></i> Added to Cart';
                
                const successModal = document.getElementById('success-modal');
                const successContent = document.getElementById('success-modal-content');
                if (successModal && successContent) {
                    successModal.classList.remove('hidden');
                    successModal.classList.add('flex');
                    setTimeout(() => {
                        successModal.classList.remove('opacity-0');
                        successContent.classList.remove('scale-95');
                        successContent.classList.add('scale-100');
                    }, 10);
                }

                setTimeout(() => {
                    checkoutBtn.innerHTML = originalText;
                    checkoutBtn.disabled = false;
                    const badge = document.querySelector('#cart-btn span');
                    if (badge) {
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                        badge.innerText = (parseInt(badge.innerText || '0') + 1);
                    }
                }, 1000);
            }, 800);
        });
    }

    // Success Modal Events
    const successModal = document.getElementById('success-modal');
    const successContent = document.getElementById('success-modal-content');
    const closeSuccessModal = () => {
        if(successModal && successContent) {
            successModal.classList.add('opacity-0');
            successContent.classList.remove('scale-100');
            successContent.classList.add('scale-95');
            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }, 300);
        }
    };
    
    const continueBtn = document.getElementById('success-continue-btn');
    if (continueBtn) continueBtn.addEventListener('click', closeSuccessModal);
    
    const viewCartBtn = document.getElementById('success-cart-btn');
    if (viewCartBtn) {
        viewCartBtn.addEventListener('click', () => {
            closeSuccessModal();
            window.location.href = '/cart';
        });
    }

    // Save, Load, Clear Logic
    const saveBuildBtn = document.getElementById('save-build-btn');
    if (saveBuildBtn) {
        saveBuildBtn.addEventListener('click', () => {
            const payload = window.engine.getCartPayload();
            // Don't save empty builds
            if (Object.values(window.engine.currentBuild).every(v => v === null)) {
                window.showNotification("Empty Build", "Your build is empty! Please add some components before saving.", "alert");
                return;
            }
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(payload);
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "techforge-build.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
            
            window.showNotification("Build Saved", "Your PC build has been saved successfully.", "alert");
        });
    }

    const loadBuildBtn = document.getElementById('load-build-btn');
    const loadBuildInput = document.getElementById('load-build-input');
    if (loadBuildBtn && loadBuildInput) {
        loadBuildBtn.addEventListener('click', () => {
            loadBuildInput.click();
        });
        
        loadBuildInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const loadedBuild = JSON.parse(e.target.result);
                        Object.keys(window.engine.currentBuild).forEach(cat => {
                            window.engine.removeComponent(cat);
                        });
                        Object.entries(loadedBuild).forEach(([cat, comp]) => {
                            if (comp) {
                                window.engine.setComponent(cat, comp);
                            }
                        });
                        // Reset file input so same file can be loaded again if needed
                        loadBuildInput.value = '';
                        window.showNotification("Build Loaded", "Your PC build has been loaded successfully.", "alert");
                    } catch (err) {
                        window.showNotification("Invalid File", "Please make sure it's a valid TechForge JSON build file.", "alert");
                    }
                };
                reader.readAsText(file);
            }
        });
    }

    const clearBuildBtn = document.getElementById('reset-build');
    if (clearBuildBtn) {
        clearBuildBtn.addEventListener('click', () => {
            window.showConfirmation("Clear Build", "Are you sure you want to completely clear your current build?", () => {
                Object.keys(window.engine.currentBuild).forEach(cat => {
                    window.engine.removeComponent(cat);
                });
                window.showNotification("Build Cleared", "Your PC build has been completely reset.", "alert");
            });
        });
    }

    const compareUploadBtn = document.getElementById('compare-upload-btn');
    const compareUploadInput = document.getElementById('compare-upload-input');
    const compareUploadedScoreEl = document.getElementById('compare-uploaded');

    if (compareUploadBtn && compareUploadInput) {
        compareUploadBtn.addEventListener('click', () => {
            compareUploadInput.click();
        });

        compareUploadInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const loadedBuild = JSON.parse(e.target.result);
                        
                        let count = 0;
                        let total = 0;
                        
                        Object.values(loadedBuild).forEach(comp => {
                            if (comp) {
                                count++;
                                if (comp.price) total += parseFloat(comp.price);
                            }
                        });
                        
                        let uploadedScore = 0;
                        if (count > 0) {
                            uploadedScore += Math.min(30, (count / 8) * 30);
                            uploadedScore += Math.min(70, (total / 250000) * 70);
                        }
                        uploadedScore = Math.round(uploadedScore);
                        
                        if (compareUploadedScoreEl) {
                            compareUploadedScoreEl.textContent = uploadedScore;
                            compareUploadedScoreEl.classList.remove('text-white');
                            compareUploadedScoreEl.classList.add('text-[#ff8c33]');
                        }
                        
                        compareUploadInput.value = '';
                        window.showNotification("Compare Success", "Uploaded build scored " + uploadedScore + "/100.", "alert");
                        
                    } catch (err) {
                        window.showNotification("Invalid File", "Please make sure it's a valid TechForge JSON build file.", "alert");
                    }
                };
                reader.readAsText(file);
            }
        });
    }

    // Init call
    renderComponents();
    updateSummary();
    
    // Preloader
    const preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.style.opacity = '0';
            setTimeout(() => preloader.style.display = 'none', 1000);
        }, 500);
    }
});
