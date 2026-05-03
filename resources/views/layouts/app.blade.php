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
        <nav class="bottom-nav">
            <a href="#" class="nav-item-bottom active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Beranda</span>
            </a>
            <a href="#" class="nav-item-bottom">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                <span>Link</span>
            </a>
            <a href="#" class="nav-item-bottom">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Komisi</span>
            </a>
            <a href="#" class="nav-item-bottom">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Tim</span>
            </a>
            <a href="#" class="nav-item-bottom">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Profil</span>
            </a>
        </nav>
        @endif
    </div>
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.3.7/r-3.0.8/datatables.min.js" integrity="sha384-boYeFHa9k/55HjzoiYWQCNzji5OI87gFtcxTbVleqB8IfcRSFAYu0NHM6XtV/5ej" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.0.1/dist/js/shepherd.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/scripts.js') }}"></script>
</body>
</html>