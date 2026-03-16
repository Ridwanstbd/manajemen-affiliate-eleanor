<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LedgerFlow - Modern Accounting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="app">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <x-organisms.sidebar />

        <main class="main">
            <x-organisms.header />
            
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
    @yield('scripts')
    <script src="{{ asset('js/scripts.js') }}"></script>
</body>
</html>