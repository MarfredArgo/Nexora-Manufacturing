// schedule.js
// Handles the Schedule sub-page: priority filter + search.

let scheduleFilter = 'all';

function filterSchedule(filter) {
    scheduleFilter = filter;

    document.querySelectorAll('.filter-sched').forEach(btn => {
        btn.classList.remove('bg-nexora-corporate', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });

    // Match on data-filter — buttons contain a count <span> inside them,
    // so clicking the number itself would miss if we used evt.target.
    const activeBtn = document.querySelector(`.filter-sched[data-filter="${filter}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
        activeBtn.classList.add('bg-nexora-corporate', 'text-white');
    }

    applyScheduleFilters();
}

function filterScheduleSearch() {
    applyScheduleFilters();
}

function applyScheduleFilters() {
    const searchEl = document.getElementById('scheduleSearch');
    const search   = searchEl ? searchEl.value.toLowerCase() : '';
    let visibleCount = 0;

    document.querySelectorAll('.schedule-row').forEach(row => {
        const matchesPriority = scheduleFilter === 'all' || row.dataset.priority === scheduleFilter;
        const matchesSearch   = row.dataset.name.toLowerCase().includes(search);
        const show            = matchesPriority && matchesSearch;
        row.style.display     = show ? '' : 'none';
        if (show) visibleCount++;
    });

    const noResults = document.getElementById('schedule-no-results');
    if (noResults) noResults.classList.toggle('hidden', visibleCount !== 0);
}
