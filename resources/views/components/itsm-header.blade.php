@props([
    'homeRoute' => '#',
    'navItems' => [],
    'active' => null,
])

<header class="flex h-32 items-center justify-between bg-[#0B1E3D] pl-4 pr-12 shadow-lg">
    <a href="{{ $homeRoute }}" class="block h-24 transition hover:scale-[1.02]">
        <img src="{{ asset('images/Banner Transparent.png') }}" alt="Nexora Logo" class="h-full object-contain">
    </a>

    <div class="flex items-center gap-16">
        <nav class="flex items-center gap-8 text-base font-medium">
            @foreach ($navItems as $item)
                @php
                    $isActive = $active === $item['key']
                        || ($item['key'] === 'employees' && request()->routeIs('client.itsm.employees'));
                @endphp
                <a href="{{ $item['route'] }}" @if ($isActive) aria-current="page" @endif class="{{ $isActive ? 'font-bold text-[#60A5FA]' : 'text-white/70 transition hover:text-white' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="relative" data-user-menu>
            <button type="button" class="flex items-center transition hover:scale-105" data-user-menu-button aria-label="Open user menu">
                <img src="{{ asset('images/icon.png') }}" alt="User" class="h-9 w-9 object-contain">
            </button>

            <div class="invisible absolute right-0 top-12 z-50 w-[200px] translate-y-[-10px] overflow-hidden rounded-lg bg-white opacity-0 shadow-2xl transition data-[open=true]:visible data-[open=true]:translate-y-0 data-[open=true]:opacity-100" data-user-menu-dropdown>
                <a href="#" class="block border-b border-slate-200 px-5 py-4 text-sm font-semibold text-[#0B1E3D] transition hover:bg-slate-100 hover:text-[#1B6FC8]">My Profile</a>
                <a href="#" class="block border-b border-slate-200 px-5 py-4 text-sm font-semibold text-[#0B1E3D] transition hover:bg-slate-100 hover:text-[#1B6FC8]">System Settings</a>
                <a href="{{ route('login') }}" class="block px-5 py-4 text-sm font-semibold text-[#DC2626] transition hover:bg-slate-100">Log Out</a>
            </div>
        </div>
    </div>
</header>

<script>
    document.querySelectorAll('[data-user-menu]').forEach((menu) => {
        const button = menu.querySelector('[data-user-menu-button]');
        const dropdown = menu.querySelector('[data-user-menu-dropdown]');

        button?.addEventListener('click', (event) => {
            event.stopPropagation();
            dropdown.dataset.open = dropdown.dataset.open === 'true' ? 'false' : 'true';
        });

        window.addEventListener('click', () => {
            dropdown.dataset.open = 'false';
        });
    });
</script>
