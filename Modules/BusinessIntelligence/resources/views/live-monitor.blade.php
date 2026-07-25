@extends('bi::layouts.app')

@section('content')
    <div class="tab-content active-tab" style="display:block;">
        <div class="subheader-bar">
            <div class="subheader-title">
                <h3>Live Monitor</h3>
                <p>Real‑time alerts and activity across all departments. Updates every 60 seconds.</p>
            </div>
            <div class="subheader-controls">
                <span id="liveStatus"
                    style="font-size:11px; color: var(--success); display:flex; align-items:center; gap:4px; white-space:nowrap;">
                    <span class="live-dot"></span> Live
                </span>
                <button class="control-btn" onclick="fetchLiveFeed()" title="Refresh Now">
                    <i data-lucide="refresh-cw" class="control-icon"></i>
                </button>
            </div>
        </div>
        <div class="content-container">
            <div id="liveFeedContainer">
                <p style="color: var(--slate-500); text-align:center; padding:2rem;">Loading activity feed…</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const clientScope = @json(request()->integer('client_id') ?: null);
        const liveFeedUrl = @json(route('bi.live-feed'));
        const scopedUrl = (url) => url + (clientScope ? (url.includes('?') ? '&' : '?') + 'client_id=' + clientScope : '');

        function timeAgo(timestamp) {
            const seconds = Math.floor((Date.now() - new Date(timestamp).getTime()) / 1000);
            if (seconds < 10) return 'Just now';
            if (seconds < 60) return seconds + 's ago';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + 'm ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + 'h ago';
            return Math.floor(hours / 24) + 'd ago';
        }

        async function fetchLiveFeed() {
            const status = document.getElementById('liveStatus');
            status.innerHTML = '<span class="live-dot" style="background:var(--warning);"></span> Updating…';
            try {
                const res = await fetch(scopedUrl(liveFeedUrl));
                const data = await res.json();
                renderFeed(data);
                status.innerHTML = '<span class="live-dot"></span> Live';
            } catch (e) {
                status.innerHTML = '<span class="live-dot" style="background:var(--danger);"></span> Offline';
            }
        }

        function renderFeed(data) {
            const container = document.getElementById('liveFeedContainer');
            if (!data.alerts || data.alerts.length === 0) {
                container.innerHTML = '<p style="color: var(--slate-500); text-align:center; padding:2rem;">All systems operational — no alerts at this time.</p>';
                return;
            }

            let html = `
                <div class="live-summary-bar">
                    <div class="live-summary-item"><span class="live-summary-count">${data.alerts.length}</span><span class="live-summary-label">Active Alerts</span></div>
                    <div class="live-summary-item live-summary-critical"><span class="live-summary-count">${data.summary.critical}</span><span class="live-summary-label">Critical</span></div>
                    <div class="live-summary-item live-summary-warning"><span class="live-summary-count">${data.summary.warning}</span><span class="live-summary-label">Warnings</span></div>
                    <div class="live-summary-item live-summary-info"><span class="live-summary-count">${data.summary.info}</span><span class="live-summary-label">Info</span></div>
                </div>`;

            html += '<div class="live-alerts-grid">';
            data.alerts.forEach(a => {
                html += `
                    <div class="live-alert-card live-alert-${a.severity}">
                        <div class="live-alert-header">
                            <div class="live-alert-icon-wrap"><i data-lucide="${a.icon}" class="live-alert-icon"></i></div>
                            <div class="live-alert-meta">
                                <span class="live-alert-dept">${a.department}</span>
                                <span class="live-alert-time" data-timestamp="${a.timestamp}">${timeAgo(a.timestamp)}</span>
                            </div>
                        </div>
                        <h4 class="live-alert-title">${a.title}</h4>
                        <p class="live-alert-desc">${a.description}</p>
                        ${a.metrics ? `<div class="live-alert-metrics">${a.metrics.map(m => `<div class="live-alert-metric"><span class="live-metric-val">${m.value}</span><span class="live-metric-label">${m.label}</span></div>`).join('')}</div>` : ''}
                    </div>`;
            });
            html += '</div>';

            container.innerHTML = html;
            lucide.createIcons();
        }

        // Live ticking timestamps every 10 seconds
        setInterval(() => {
            document.querySelectorAll('.live-alert-time').forEach(el => {
                const ts = el.getAttribute('data-timestamp');
                if (ts) el.textContent = timeAgo(ts);
            });
        }, 10000);

        document.addEventListener('DOMContentLoaded', () => {
            fetchLiveFeed();
            setInterval(fetchLiveFeed, 60000);
        });
    </script>
@endsection
