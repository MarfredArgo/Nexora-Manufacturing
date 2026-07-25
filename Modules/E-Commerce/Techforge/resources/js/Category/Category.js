import Lenis from 'lenis'

// 1. Mobile Filter Toggle
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

const filterForm = document.getElementById('filter-form');

// 2. Filter Checkbox Interactions (Event Delegation)
function updateParentCheckboxes() {
    const parents = document.querySelectorAll('.brand-checkbox[data-parent]');
    parents.forEach(parent => {
        const parentId = parent.getAttribute('data-parent');
        const children = document.querySelectorAll(`input[data-child-of="${parentId}"]`);
        if (children.length === 0) return;
        
        const checkedCount = Array.from(children).filter(c => c.checked).length;
        if (checkedCount === 0) {
            parent.checked = false;
            parent.classList.remove('is-indeterminate');
        } else if (checkedCount === children.length) {
            parent.checked = true;
            parent.classList.remove('is-indeterminate');
        } else {
            parent.checked = false;
            parent.classList.add('is-indeterminate');
        }
    });
}

// Call once on load to ensure sync
updateParentCheckboxes();

document.addEventListener('change', (e) => {
    if (e.target.matches('.brand-checkbox[data-parent]')) {
        const parentId = e.target.getAttribute('data-parent');
        const children = document.querySelectorAll(`input[data-child-of="${parentId}"]`);
        children.forEach(child => child.checked = e.target.checked);
        
        e.target.classList.remove('is-indeterminate');
        
        const form = document.getElementById('filter-form');
        if (form) { if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit(); }
    } else if (e.target.matches('#filter-form input[type="checkbox"]')) {
        if (!e.target.id || !e.target.id.includes('accordion')) {
            updateParentCheckboxes();
            
            const form = document.getElementById('filter-form');
            if (form) { if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit(); }
        }
    }
});

// 3. Price Range Input Interaction
document.addEventListener('input', (e) => {
    if (e.target.matches('#range-min') || e.target.matches('#range-max') || e.target.matches('#price-min') || e.target.matches('#price-max')) {
        const rangeMin = document.getElementById('range-min');
        const rangeMax = document.getElementById('range-max');
        const priceMin = document.getElementById('price-min');
        const priceMax = document.getElementById('price-max');
        const track = document.getElementById('slider-track');
        
        if (!rangeMin || !rangeMax || !priceMin || !priceMax || !track) return;
        
        const minGap = 1000;
        const absoluteMin = parseInt(rangeMin.min);
        const absoluteMax = parseInt(rangeMax.max);
        
        if (e.target === rangeMin || e.target === rangeMax) {
            let minVal = parseInt(rangeMin.value);
            let maxVal = parseInt(rangeMax.value);
            
            if (maxVal - minVal < minGap) {
                if (e.target === rangeMin) {
                    rangeMin.value = maxVal - minGap;
                    minVal = parseInt(rangeMin.value);
                } else {
                    rangeMax.value = minVal + minGap;
                    maxVal = parseInt(rangeMax.value);
                }
            }
            
            priceMin.value = minVal;
            priceMax.value = maxVal;
        } else if (e.target === priceMin || e.target === priceMax) {
            let minVal = parseInt(priceMin.value) || absoluteMin;
            let maxVal = parseInt(priceMax.value) || absoluteMax;
            
            if (minVal < absoluteMin) minVal = absoluteMin;
            if (maxVal > absoluteMax) maxVal = absoluteMax;
            
            rangeMin.value = minVal;
            rangeMax.value = maxVal;
        }

        const currentMin = parseInt(rangeMin.value);
        const currentMax = parseInt(rangeMax.value);
        
        const range = Math.max(absoluteMax - absoluteMin, 1);
        const leftPercent = ((currentMin - absoluteMin) / range) * 100;
        const rightPercent = 100 - (((currentMax - absoluteMin) / range) * 100);
        
        track.style.left = leftPercent + '%';
        track.style.right = rightPercent + '%';
    }
});

document.addEventListener('change', (e) => {
    if (e.target.matches('#range-min') || e.target.matches('#range-max') || e.target.matches('#price-min') || e.target.matches('#price-max')) {
        const form = document.getElementById('filter-form');
        if (form) { if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit(); }
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && (e.target.matches('#price-min') || e.target.matches('#price-max'))) {
        e.preventDefault();
        const form = document.getElementById('filter-form');
        if (form) { if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit(); }
    }
});

// 4. Sort Dropdown Interaction
document.addEventListener('change', (e) => {
    if (e.target.matches('select[name="sort"]')) {
        const form = document.getElementById('filter-form');
        if (form) { if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit(); }
    }
});

// 5. Reset All Button Interaction
document.addEventListener('click', (e) => {
    if (e.target.matches('#reset-all-btn')) {
        e.preventDefault();
        
        const form = document.getElementById('filter-form');
        if (!form) return;

        // Reset checkboxes
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            cb.classList.remove('is-indeterminate');
        });

        // Reset price inputs and sliders
        const rangeMin = document.getElementById('range-min');
        const rangeMax = document.getElementById('range-max');
        const priceMin = document.getElementById('price-min');
        const priceMax = document.getElementById('price-max');
        const track = document.getElementById('slider-track');

        if (rangeMin && rangeMax && priceMin && priceMax && track) {
            const absoluteMin = rangeMin.min;
            const absoluteMax = rangeMax.max;
            
            rangeMin.value = absoluteMin;
            priceMin.value = absoluteMin;
            
            rangeMax.value = absoluteMax;
            priceMax.value = absoluteMax;
            
            track.style.left = '0%';
            track.style.right = '0%';
        }

        // Trigger form submission (which applyFilters will catch)
        if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit();
    }
});
