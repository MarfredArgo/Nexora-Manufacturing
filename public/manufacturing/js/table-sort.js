// table-sort.js
// Makes any table.sortable-table clickable-sortable by column.
function initSortableTables() {
    document.querySelectorAll('table.sortable-table').forEach(table => {
        if (!table._originalOrder) {
            const tbody = table.querySelector('tbody');
            table._originalOrder = Array.from(tbody.children);
        }

        const headers = table.querySelectorAll('th.sortable');
        headers.forEach((th, colIndex) => {
            if (th.dataset.sortBound === '1') return;
            th.dataset.sortBound = '1';
            th.style.cursor = 'pointer';
            th.dataset.sortDir = '';

            const arrow = document.createElement('span');
            arrow.className = 'sort-arrow';
            arrow.style.marginLeft = '4px';
            arrow.style.opacity = '0.4';
            arrow.textContent = '↕';
            th.appendChild(arrow);

            th.addEventListener('click', () => sortTableByColumn(table, th, colIndex));
        });
    });
}

function sortTableByColumn(table, th, colIndex) {
    const type       = th.dataset.sortType || 'text';
    const tbody      = table.querySelector('tbody');
    const allHeaders = table.querySelectorAll('th.sortable');

    const nextDir = th.dataset.sortDir === 'asc' ? 'desc'
                   : th.dataset.sortDir === 'desc' ? ''
                   : 'asc';

    allHeaders.forEach(h => {
        h.dataset.sortDir = '';
        const arrow = h.querySelector('.sort-arrow');
        if (arrow) { arrow.textContent = '↕'; arrow.style.opacity = '0.4'; }
    });

    th.dataset.sortDir = nextDir;
    const arrow = th.querySelector('.sort-arrow');

    if (nextDir === '') {
        if (arrow) { arrow.textContent = '↕'; arrow.style.opacity = '0.4'; }
        restoreOriginalOrder(table);
        return;
    }

    if (arrow) {
        arrow.textContent = nextDir === 'asc' ? '↑' : '↓';
        arrow.style.opacity = '1';
    }

    const realColIndex = getRealColumnIndex(table, colIndex);

    const groupRows = Array.from(tbody.querySelectorAll('tr.no-sort'));

    if (groupRows.length > 0) {
        sortFlatWithGroups(tbody, type, realColIndex, nextDir);
        return;
    }

    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const cellA = a.children[realColIndex];
        const cellB = b.children[realColIndex];
        if (!cellA || !cellB) return 0;

        const valA = getSortValue(cellA, type);
        const valB = getSortValue(cellB, type);

        let result;
        if (type === 'number') {
            result = valA - valB;
        } else {
            result = String(valA).localeCompare(String(valB), undefined, { numeric: true, sensitivity: 'base' });
        }

        return nextDir === 'asc' ? result : -result;
    });

    rows.forEach(row => tbody.appendChild(row));
}

function restoreOriginalOrder(table) {
    const tbody = table.querySelector('tbody');
    if (!table._originalOrder) return;
    table._originalOrder.forEach(row => tbody.appendChild(row));
}

function sortFlatWithGroups(tbody, type, colIndex, dir) {
    const allRows = Array.from(tbody.children);
    const groups  = [];
    let current   = null;

    allRows.forEach(row => {
        if (row.classList.contains('no-sort')) {
            current = { header: row, rows: [] };
            groups.push(current);
        } else if (current) {
            current.rows.push(row);
        } else {
            current = { header: null, rows: [row] };
            groups.push(current);
        }
    });

    groups.forEach(group => {
        group.rows.sort((a, b) => {
            const cellA = a.children[colIndex];
            const cellB = b.children[colIndex];
            if (!cellA || !cellB) return 0;

            const valA = getSortValue(cellA, type);
            const valB = getSortValue(cellB, type);

            let result;
            if (type === 'number') {
                result = valA - valB;
            } else {
                result = String(valA).localeCompare(String(valB), undefined, { numeric: true, sensitivity: 'base' });
            }

            return dir === 'asc' ? result : -result;
        });
    });

    tbody.innerHTML = '';
    groups.forEach(group => {
        if (group.header) tbody.appendChild(group.header);
        group.rows.forEach(row => tbody.appendChild(row));
    });
}

function getRealColumnIndex(table, sortableIndex) {
    const headerRow  = table.querySelector('thead tr');
    const allThs     = Array.from(headerRow.children);
    const sortableTh = table.querySelectorAll('th.sortable')[sortableIndex];
    return allThs.indexOf(sortableTh);
}

function getSortValue(cell, type) {
    const raw = cell.dataset.sortValue !== undefined
        ? cell.dataset.sortValue
        : cell.textContent.trim();

    if (type === 'number') {
        const num = parseFloat(raw.toString().replace(/[^0-9.\-]/g, ''));
        return isNaN(num) ? -Infinity : num;
    }

    return raw.toLowerCase();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSortableTables);
} else {
    initSortableTables();
}
