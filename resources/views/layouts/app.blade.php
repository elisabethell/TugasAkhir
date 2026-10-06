<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MetaScout: Land of Dawn - Portal Analisis MLBB')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #FAF8F8;
            color: #18181B;
            -webkit-font-smoothing: antialiased;
        }
        .text-maroon { color: #6B0F1A; }
        .bg-maroon { background-color: #700B1A; }
        .bg-maroon-hover:hover { background-color: #550713; }
        .border-maroon { border-color: #700B1A; }
        .bg-maroon-subtle { background-color: #FCECEE; }
        .border-subtle { border-color: #F3E8E8; }
        .card-custom {
            background-color: #FFFFFF;
            border: 1px solid #F3E8E8;
            border-radius: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 4px 12px rgba(112, 11, 26, 0.02);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen py-6 px-3 sm:px-6">

    <div class="max-w-[1140px] mx-auto space-y-5">
        @if(!View::hasSection('hide_navbar'))
            <x-navbar />
        @endif

        @yield('content')

        @if(!View::hasSection('hide_footer'))
            <x-footer />
        @endif
    </div>

    @stack('scripts')
</body>
</html>