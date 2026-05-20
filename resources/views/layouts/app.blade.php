<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <x-molecules.alert />
    <x-atoms.lightbox />
    @stack('scripts')
    <script src="{{ asset('js/scripts.js') }}"></script>

    @auth
    <script>
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        if ('serviceWorker' in navigator && 'PushManager' in window) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function (registration) {
                    Notification.requestPermission().then(function (permission) {
                        if (permission === 'granted') {
                            registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array("{{ env('VAPID_PUBLIC_KEY') }}")
                            }).then(function (subscription) {
                                fetch("{{ route('push.subscribe') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify(subscription)
                                }).catch(function(error) {
                                    console.error('Gagal mengirim langganan ke server:', error);
                                });
                            }).catch(function(error) {
                                console.error('Gagal melakukan subscribe:', error);
                            });
                        }
                    });
                }).catch(function(error) {
                    console.error('Pendaftaran Service Worker gagal:', error);
                });
            });
        }
    </script>
    @endauth
</body>
</html>