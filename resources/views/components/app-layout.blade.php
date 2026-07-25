<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Nexora') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased text-white bg-[#0B1E3D] overflow-x-hidden">
    
    <main>
        {{-- The {{ $slot }} variable automatically injects whatever is inside the <x-app-layout> tags from your other files --}}
        {{ $slot }}
    </main>

</body>
</html>