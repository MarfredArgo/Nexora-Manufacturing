{{-- ROOT APP.BLADE --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora - BI Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('bi/css/dashboard.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('bi/images/Nexora_Logo_Transparent.png') }}">
    <style>
        /* This layout doesn't load Tailwind's preflight, so reset the native
           <button> chrome the collapse toggle would otherwise inherit. */
        .sidebar-toggle-btn {
            width: 100%;
            background: transparent;
            border: none;
            font-family: inherit;
            text-align: left;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="{{ route('bi.dashboard') }}" class="nexora-logo" id="headerLogoBtn">
            <img src="{{ asset('bi/images/Banner Transparent.png') }}" alt="Nexora Logo">
        </a>
        <div class="header-right">
            <div class="header-profile-wrap" id="headerProfileWrap">
                <button class="header-profile-btn" id="headerProfileBtn">
                    <i data-lucide="user" class="profile-icon"></i>
                    <span class="notification-badge" id="notificationBadge">0</span>
                </button>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-dropdown-header">
                        <h3>Notifications</h3>
                        <button class="notification-mark-read" onclick="markAllRead()">Mark all as read</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <p style="text-align:center;color:var(--slate-500);padding:2rem;font-size:11px;">Loading
                            notifications…</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="app-body">
        <aside id="sidebar">
            <div class="nav-menu" id="navMenu">
                <button id="sidebarToggle" class="nav-item sidebar-toggle-btn" title="Toggle Sidebar">
                    <div class="nav-item-title">
                        <i data-lucide="panel-left-close" id="sidebarToggleIcon"></i>
                        <span class="nav-item-text">Collapse</span>
                    </div>
                </button>
                <a href="{{ route('bi.dashboard', request()->only('client_id')) }}"
                    class="nav-item {{ request()->routeIs('bi.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                    <div class="nav-item-title">
                        <i data-lucide="layout-dashboard" class="nav-icon"></i>
                        <span class="nav-item-text">Dashboard</span>
                    </div>
                    <div class="nav-item-sub">Executive Overview</div>
                </a>
                <a href="{{ route('bi.ai-insights', request()->only('client_id')) }}"
                    class="nav-item {{ request()->routeIs('bi.ai-insights') ? 'active' : '' }}" data-tooltip="AI Insights">
                    <div class="nav-item-title">
                        <i data-lucide="brain" class="nav-icon"></i>
                        <span class="nav-item-text">AI Insights</span>
                    </div>
                    <div class="nav-item-sub">Recommendations</div>
                </a>
                <a href="{{ route('bi.department-analytics', request()->only('client_id')) }}"
                    class="nav-item {{ request()->routeIs('bi.department-analytics') ? 'active' : '' }}"
                    data-tooltip="Department Analytics">
                    <div class="nav-item-title">
                        <i data-lucide="building-2" class="nav-icon"></i>
                        <span class="nav-item-text">Department Analytics</span>
                    </div>
                    <div class="nav-item-sub">KPI Deep Dive</div>
                </a>
                <a href="{{ route('bi.live-monitor', request()->only('client_id')) }}"
                    class="nav-item {{ request()->routeIs('bi.live-monitor') ? 'active' : '' }}"
                    data-tooltip="Live Monitor">
                    <div class="nav-item-title">
                        <i data-lucide="activity" class="nav-icon"></i>
                        <span class="nav-item-text">Live Monitor</span>
                    </div>
                    <div class="nav-item-sub">Real‑time Feed</div>
                </a>
            </div>

            {{-- Sidebar footer – classic divider + version + toggle --}}
            <div class="sidebar-footer">
                <i data-lucide="info" class="footer-icon"></i>
                <span>NEXORA BI v1.0.0</span>

                <div class="theme-switch-wrapper">
                    <i data-lucide="sun" class="theme-switch-icon"></i>
                    <label class="theme-switch">
                        <input type="checkbox" id="themeSwitchCheckbox">
                        <span class="theme-switch-slider"></span>
                    </label>
                    <i data-lucide="moon" class="theme-switch-icon"></i>
                </div>
            </div>
        </aside>
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Floating AI Chat Bot -->
    <div class="ai-chat-bot" id="aiChatBot">
        <button class="ai-chat-toggle" id="aiChatToggle" title="NEXORA AI Business Analyst">
            <img src="{{ asset('bi/images/Nexora_Logo_Transparent.png') }}" class="chat-toggle-logo" alt="Nexora">
        </button>
        <div class="ai-chat-window" id="aiChatWindow">
            <div class="ai-chat-header">
                <div class="ai-chat-header-left">
                    <img src="{{ asset('bi/images/Nexora_Logo_Transparent.png') }}" class="chat-header-logo" alt="Nexora">
                    <div>
                        <h4>NEXORA AI Business Analyst</h4>
                        <p>Ask me anything about your business</p>
                    </div>
                </div>
                <button class="ai-chat-close" id="aiChatClose"><i data-lucide="x" class="chat-close-icon"></i></button>
            </div>
            <div class="ai-chat-messages" id="aiChatMessages">
                <div class="ai-message ai-message-bot">
                    <div class="ai-message-avatar"><img src="{{ asset('bi/images/Nexora_Logo_Transparent.png') }}"
                            class="msg-avatar-logo" alt="Nexora"></div>
                    <div class="ai-message-content">
                        <p>Hello! I'm your NEXORA AI Business Analyst. Since NEXORA BI gathers data across enterprise
                            modules, I can help you transform data into actionable insights. What would you like to
                            know?</p>
                    </div>
                </div>
            </div>
            <div class="ai-chat-input-container">
                <div class="ai-suggestion-chips" id="aiSuggestionChips">
                    <button class="ai-chip"
                        onclick="sendAiMessage('Give me a summary of overall business performance.')">Business
                        summary</button>
                    <button class="ai-chip"
                        onclick="sendAiMessage('Explain insights from this week\'s activity.')">Weekly insights</button>
                    <button class="ai-chip" onclick="sendAiMessage('What are the top risks I should be aware of?')">Risk
                        alerts</button>
                    <button class="ai-chip" onclick="sendAiMessage('Show me revenue trends and forecast.')">Revenue
                        forecast</button>
                </div>
                <div class="ai-chat-input-row">
                    <input type="text" class="ai-chat-input" id="aiChatInput" placeholder="Type your question here..."
                        onkeypress="handleAiChatKeypress(event)">
                    <button class="ai-chat-send" id="aiChatSend" onclick="sendAiMessage()"><i data-lucide="send"
                            class="send-icon"></i></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Load read state from localStorage
        let readAlertIds = JSON.parse(localStorage.getItem('nexora_read_alerts') || '[]');
        let currentAlertCount = 0;

        // Client-scoped module endpoints.
        const biClientScope = @json(request()->integer('client_id') ?: null);
        const biLiveFeedUrl = @json(route('bi.live-feed'));
        const biChatUrl = @json(route('bi.ai.chat'));
        const biScopedUrl = (url) => url + (biClientScope ? (url.includes('?') ? '&' : '?') + 'client_id=' + biClientScope : '');

        document.addEventListener('DOMContentLoaded', () => {

            const chatToggle = document.getElementById('aiChatToggle');
            const chatClose = document.getElementById('aiChatClose');
            const chatBot = document.getElementById('aiChatBot');

            chatToggle.addEventListener('click', () => {
                chatBot.classList.toggle('ai-chat-open');
            });

            chatClose.addEventListener('click', () => {
                chatBot.classList.remove('ai-chat-open');
            });

            const profileBtn = document.getElementById('headerProfileBtn');
            const profileWrap = document.getElementById('headerProfileWrap');
            const dropdown = document.getElementById('notificationDropdown');

            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!profileWrap.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });

            // Handle clicking individual notifications
            dropdown.addEventListener('click', (e) => {
                const notifItem = e.target.closest('.notification-item');
                if (!notifItem) return;

                const alertId = notifItem.getAttribute('data-alert-id');
                if (alertId && !readAlertIds.includes(alertId)) {
                    readAlertIds.push(alertId);
                    localStorage.setItem('nexora_read_alerts', JSON.stringify(readAlertIds));
                    notifItem.classList.remove('unread');
                    currentAlertCount = document.querySelectorAll('#notificationList .notification-item.unread').length;
                    updateNotificationBadge(currentAlertCount);
                }
            });

            // ============================================================
            // COLLAPSIBLE SIDEBAR
            // ============================================================
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarToggleIcon = document.getElementById('sidebarToggleIcon');

            if (sidebar && sidebarToggle && sidebarToggleIcon) {
                // Restore saved state
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                    sidebarToggleIcon.setAttribute('data-lucide', 'panel-left-open');
                    lucide.createIcons();
                }

                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                    sidebarToggleIcon.setAttribute('data-lucide', isCollapsed ? 'panel-left-open' : 'panel-left-close');
                    lucide.createIcons();
                });
            }

            // Start dynamic notifications
            fetchNotifications();
            setInterval(fetchNotifications, 30000);

        });

        function updateBadgeCount() {
            updateNotificationBadge(currentAlertCount);
        }

        // ============================================================
        // DYNAMIC NOTIFICATIONS (from live-feed API)
        // ============================================================
        async function fetchNotifications() {
            try {
                const res = await fetch(biScopedUrl(biLiveFeedUrl));
                const data = await res.json();
                renderNotifications(data.alerts || []);
            } catch (e) {
                console.error('Notification fetch error:', e);
            }
        }

        function renderNotifications(alerts) {
            const container = document.getElementById('notificationList');
            if (!container) return;

            if (alerts.length === 0) {
                container.innerHTML = '<p style="text-align:center;color:var(--slate-500);padding:2rem;font-size:11px;">All clear — no alerts</p>';
                currentAlertCount = 0;
                updateNotificationBadge(0);
                return;
            }

            const iconMap = {
                'alert-triangle': 'bg-icon-red',
                'alert-circle': 'bg-icon-orange',
                'clock-alert': 'bg-icon-red',
                'cpu': 'bg-icon-orange',
                'file-text': 'bg-icon-blue',
                'truck': 'bg-icon-orange',
                'box': 'bg-icon-orange',
                'shield': 'bg-icon-red',
                'ticket': 'bg-icon-blue',
                'dollar-sign': 'bg-icon-orange',
            };

            const timeAgo = (timestamp) => {
                const seconds = Math.floor((Date.now() - new Date(timestamp).getTime()) / 1000);
                if (seconds < 10) return 'Just now';
                if (seconds < 60) return seconds + 's ago';
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return minutes + 'm ago';
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return hours + 'h ago';
                return Math.floor(hours / 24) + 'd ago';
            };

            container.innerHTML = alerts.slice(0, 5).map(a => {
                const alertId = a.id ? String(a.id) : a.title.replace(/\s+/g, '-').toLowerCase();
                const isRead = readAlertIds.includes(alertId);
                return `
                <div class="notification-item ${isRead ? '' : 'unread'}" data-alert-id="${alertId}">
                    <div class="notification-dot"></div>
                    <div class="notification-icon ${iconMap[a.icon] || 'bg-icon-blue'}">
                        <i data-lucide="${a.icon}" class="notif-icon-sm"></i>
                    </div>
                    <div class="notification-content">
                        <p class="notification-title">${a.title}</p>
                        <p class="notification-desc">${a.description}</p>
                        <span class="notification-time">${timeAgo(a.timestamp)}</span>
                    </div>
                </div>
            `}).join('');

            // Count unread
            currentAlertCount = document.querySelectorAll('#notificationList .notification-item.unread').length;
            updateNotificationBadge(currentAlertCount);
            lucide.createIcons();
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                badge.textContent = count || '';
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        }

        function markAllRead() {
            document.querySelectorAll('#notificationList .notification-item[data-alert-id]').forEach(item => {
                const alertId = item.getAttribute('data-alert-id');
                if (alertId && !readAlertIds.includes(alertId)) {
                    readAlertIds.push(alertId);
                }
            });
            localStorage.setItem('nexora_read_alerts', JSON.stringify(readAlertIds));
            document.querySelectorAll('#notificationList .notification-item.unread').forEach(item => item.classList.remove('unread'));
            currentAlertCount = 0;
            updateNotificationBadge(0);
        }

        function handleAiChatKeypress(event) {
            if (event.key === 'Enter') {
                sendAiMessage();
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async function sendAiMessage(presetMessage) {
            const input = document.getElementById('aiChatInput');
            const message = presetMessage || input.value.trim();

            if (!message) return;

            const messagesContainer = document.getElementById('aiChatMessages');

            // User message
            const userMsg = document.createElement('div');
            userMsg.className = 'ai-message ai-message-user';
            userMsg.innerHTML = `
                <div class="ai-message-content">
                    <p>${escapeHtml(message)}</p>
                </div>
            `;
            messagesContainer.appendChild(userMsg);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            if (!presetMessage) {
                input.value = '';
            }

            // Thinking indicator
            const thinkingMsg = document.createElement('div');
            thinkingMsg.className = 'ai-message ai-message-bot';
            thinkingMsg.innerHTML = `
                <div class="ai-message-content">
                    <p><em>Thinking...</em></p>
                </div>
            `;
            messagesContainer.appendChild(thinkingMsg);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            const sendBtn = document.getElementById('aiChatSend');
            sendBtn.disabled = true;

            try {
                const response = await fetch(biScopedUrl(biChatUrl), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const body = await response.text();
                console.log("Status:", response.status);
                console.log("Response:", body);

                if (!response.ok) {
                    throw new Error(`Server error ${response.status}: ${body}`);
                }

                const data = JSON.parse(body);

                thinkingMsg.remove();

                const botMsg = document.createElement('div');
                botMsg.className = 'ai-message ai-message-bot';
                botMsg.innerHTML = `
                    <div class="ai-message-avatar">
                        <img src="{{ asset('bi/images/Nexora_Logo_Transparent.png') }}" class="msg-avatar-logo" alt="Nexora">
                    </div>
                    <div class="ai-message-content">
                        <p>${escapeHtml(data.message)}</p>
                    </div>
                `;

                messagesContainer.appendChild(botMsg);

            } catch (e) {
                console.error("AI Error:", e);
                thinkingMsg.remove();

                const errMsg = document.createElement('div');
                errMsg.className = 'ai-message ai-message-bot';
                errMsg.innerHTML = `
                    <div class="ai-message-content">
                        <p>${escapeHtml(e.message)}</p>
                    </div>
                `;

                messagesContainer.appendChild(errMsg);
            } finally {
                sendBtn.disabled = false;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

        // ============================================================
        // SIDEBAR THEME SWITCH (replaces old header toggle)
        // ============================================================
        const themeCheckbox = document.getElementById('themeSwitchCheckbox');

        // Apply saved theme
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            themeCheckbox.checked = true;
        }

        themeCheckbox.addEventListener('change', () => {
            if (themeCheckbox.checked) {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
            }
            lucide.createIcons();
            window.dispatchEvent(new Event('themechange'));

            // Update dashboard chart if it exists
            if (window.salesTrendChart) {
                const isDarkNow = document.documentElement.getAttribute('data-theme') === 'dark';
                const gridColor = isDarkNow ? '#64748B' : '#E2E8F0';
                salesTrendChart.options.scales.y.grid.color = gridColor;
                salesTrendChart.options.scales.y.border.color = gridColor;
                salesTrendChart.options.scales.x.border.color = gridColor;
                salesTrendChart.options.scales.y.ticks.color = isDarkNow ? '#94A3B8' : '#5B7A9D';
                salesTrendChart.options.scales.x.ticks.color = isDarkNow ? '#94A3B8' : '#5B7A9D';
                salesTrendChart.update();
            }
        });
    </script>
    @yield('scripts')
</body>

</html>