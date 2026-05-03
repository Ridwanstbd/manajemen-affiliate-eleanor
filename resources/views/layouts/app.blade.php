<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(View::hasSection('title'))
            @yield('title') - Affiliate Eleanor
        @else
            Affiliate Eleanor
        @endif
    </title>
    <link rel="icon" type="image/png" href="{{ asset('img\logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@11.0.1/dist/css/shepherd.css"/>
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.3.7/r-3.0.8/datatables.min.css" rel="stylesheet" integrity="sha384-tHETVcKgnXe5WTGN+FzIWpZT+tNkGAKkIdV+9ZIdnusy711pbrakwn7FoP0ORIPb" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="{{ auth()->check() && auth()->user()->role === 'AFFILIATOR' ? 'role-affiliator' : 'role-admin' }}">
    <div class="app">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <x-organisms.sidebar />

        <main class="main {{ auth()->check() && auth()->user()->role === 'AFFILIATOR' ? 'affiliator-main' : '' }}">
            <x-organisms.header />
            <div class="content">
                @yield('content')
            </div>
        </main>
        @if(auth()->check() && auth()->user()->role === 'AFFILIATOR' && !View::hasSection('is_subpage'))
        <x-organisms.bottom-nav />
        @endif
    </div>
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.3.7/r-3.0.8/datatables.min.js" integrity="sha384-boYeFHa9k/55HjzoiYWQCNzji5OI87gFtcxTbVleqB8IfcRSFAYu0NHM6XtV/5ej" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.0.1/dist/js/shepherd.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/scripts.js') }}"></script>
    <x-molecules.alert />
</body>
</html>