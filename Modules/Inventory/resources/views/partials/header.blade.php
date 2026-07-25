<header style="background:linear-gradient(180deg,#10264a 0%,#0b1e3d 100%);height:70px;display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:10;box-shadow:0 4px 18px rgba(0,0,0,0.35);border-bottom:1px solid rgba(255,255,255,0.06);">
    <div style="display:flex;align-items:center;flex-shrink:0;">
        <button type="button" onclick="toggleNav()" style="cursor:pointer;background:none;border:none;padding:0;"><img src="{{ asset('images/nexora-banner.png') }}" alt="Nexora logo" style="height:55px; width:auto;"></button>
    </div>
    <nav style="display:flex;align-items:center;gap:16px;margin-left:auto;">
        <button type="button" onclick="toggleProfileDropdown()" id="profileTrigger" style="width:34px;height:34px;border-radius:50%;background:rgba(74,158,232,.15);overflow:hidden;display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;border:none;padding:0;">
            <img src="{{ asset('images/icon.png') }}" alt="User avatar" style="width:100%;height:100%;object-fit:cover;display:block;">
        </button>
    </nav>
    <!-- Profile Dropdown -->
    <div id="profileDropdown" class="profile-dropdown">
        <button type="button" class="profile-dropdown-close" onclick="toggleProfileDropdown()">&times;</button>
        <div class="profile-dropdown-email">{{ session('employee_email', '') }}</div>
        <div class="profile-dropdown-avatar-wrap">
            <div class="profile-dropdown-avatar">
                <img src="{{ asset('images/icon.png') }}" alt="User avatar">
            </div>
        </div>
        <div class="profile-dropdown-greeting">Hi, {{ session('employee_name', 'User') }}!</div>
        <ul class="profile-dropdown-menu">
            <li>
                <a href="#">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </li>
            <li>
                <a href="#">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Help
                </a>
            </li>
            <li class="logout">
                <form method="POST" action="{{ route('inventory.logout') }}" style="margin:0;padding:0;">
                    @csrf
                    <button type="submit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
