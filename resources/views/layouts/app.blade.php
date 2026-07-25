<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXORA ERP | Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#0B1E3D] font-inter text-white min-h-screen flex">

    <aside class="w-64 bg-[#132B52] border-r border-[#E2E8F0]/10 p-6 flex flex-col">
        <h2 class="text-[#4A9EE8] font-bold text-xl mb-8 tracking-wider">NEXORA</h2>
        
        <nav class="flex-1 space-y-2">
            <a href="/dashboard" class="block px-4 py-3 rounded bg-[#1B6FC8] text-white font-semibold text-[14px]">
                Dashboard Overview
            </a>
        </nav>

        <div class="mt-auto border-t border-[#E2E8F0]/10 pt-4">
            <p class="text-[#5B7A9D] text-[11px] uppercase font-semibold tracking-wider">Logged in as</p>
            <p class="text-white font-semibold text-[14px] truncate">{{ Auth::user()->username ?? 'User' }}</p>
        </div>
    </aside>

    <main class="flex-1 p-10">
        <h1 class="text-[28px] font-bold tracking-tight mb-8">Dashboard Overview</h1>
        
        <div class="content">
            @yield('content')
        </div>
    </main>

</body>
</html>