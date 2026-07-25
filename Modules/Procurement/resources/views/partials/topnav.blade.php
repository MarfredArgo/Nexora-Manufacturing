@php
    $navUserName = session('employee_name', auth()->user()->name ?? 'Employee');
    $navUserEmail = auth()->user()->email ?? session('employee_email', '');
    $navUserRole = ucfirst(str_replace('_', ' ', auth()->user()->role ?? session('employee_role', 'Employee')));
    $navParts = preg_split('/\s+/', trim($navUserName)) ?: [];
    $navInitials = strtoupper(substr($navParts[0] ?? 'E', 0, 1) . (count($navParts) > 1 ? substr(end($navParts), 0, 1) : ''));
@endphp
<nav class="topnav">
    <div class="logo">
        <img src="{{ asset('images/procurement-banner.png') }}" alt="Nexora ERP">
    </div>
    <div class="divider-v"></div>
    <strong style="color:#fff;">Procurement</strong>

    <div class="nav-right">
        <div class="profile-menu">
            <button type="button" class="avatar-btn" onclick="toggleProfileMenu(event)" aria-label="Open profile menu">
                <span class="avatar-initials">{{ $navInitials }}</span>
            </button>
            <div class="profile-dropdown" id="profile-dropdown">
                <div class="profile-header">
                    <span class="avatar-initials lg">{{ $navInitials }}</span>
                    <div class="profile-id">
                        <strong>{{ $navUserName }}</strong>
                        @if($navUserEmail)<div class="profile-email">{{ $navUserEmail }}</div>@endif
                        <span class="profile-role">{{ $navUserRole }}</span>
                    </div>
                </div>

                <button type="button" class="theme-switch-row" onclick="toggleTheme()">
                    <span class="tsr-label">
                        <svg class="theme-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4.2" stroke="currentColor" stroke-width="2"/><path d="M12 2v2.5M12 19.5V22M4.2 4.2l1.8 1.8M18 18l1.8 1.8M2 12h2.5M19.5 12H22M4.2 19.8l1.8-1.8M18 6l1.8-1.8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <svg class="theme-icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.6 6.6 0 0 0 9.8 9.8z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                        Dark Mode
                    </span>
                    <span class="theme-switch"><span class="theme-switch-knob"></span></span>
                </button>

                <form method="POST" action="{{ route('procurement.logout') }}">
                    @csrf
                    <button type="submit" class="profile-logout">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
