{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MLBB Analytics')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0b1120;
        }
        .dashboard-card {
            background: #161e2d;
            border-radius: 12px;
            border: 1px solid #1e293b;
            padding: 1.5rem;
        }
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
        }
        .stat-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .progress-bar-bg {
            background: #1e293b;
            border-radius: 4px;
            height: 8px;
            overflow: hidden;
        }
        .progress-fill-blue {
            background: #00f2ff;
            height: 100%;
            border-radius: 4px;
        }
        .progress-fill-purple {
            background: #8b5cf6;
            height: 100%;
            border-radius: 4px;
        }
        .table-header {
            background: #1a2234;
            font-weight: 600;
            font-size: 0.8rem;
            color: #94a3b8;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #1e293b;
        }
        .table-cell {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #1e293b;
            font-size: 0.9rem;
            color: #e2e8f0;
        }
    </style>
</head>
<body class="text-gray-100">
    @include('components.navbar')
    
    <main>
        @yield('content')
    </main>
</body>
</html>