{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MLBB Analytics')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0B0E14; }
        .neon-text { color: #00f2ff; text-shadow: 0 0 10px rgba(0, 242, 255, 0.3); }
        .card-custom { background: #151921; border: 1px solid #1F2937; border-radius: 16px; }
    </style>
</head>
<body class="text-gray-100 min-h-screen">
    
    <x-navbar />
    
    <main class="max-w-7xl mx-auto px-8 py-10">
        @yield('content')
    </main>

</body>
</html>