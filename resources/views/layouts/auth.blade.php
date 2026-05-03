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
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }
        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-2xl);
            box-shadow: var(--glass-shadow);
            padding: 40px 32px;
            animation: fadeInUp 0.55s var(--ease-smooth) both;
        }
        .auth-header { text-align: center; margin-bottom: 32px; }
        .auth-brand { display: inline-flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .auth-title { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; letter-spacing: -0.5px; }
        .auth-subtitle { font-size: 14px; color: var(--text-secondary); }
        .form-options { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; font-size: 13.5px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; color: var(--text-secondary); cursor: pointer; font-weight: 500; }
        .text-link { color: var(--primary-blue); text-decoration: none; font-weight: 600; transition: color 0.2s; cursor: pointer; }
        .text-link:hover { color: #2563eb; text-decoration: underline; }
        .auth-footer { margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-secondary); }
        .btn-block { width: 100%; justify-content: center; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        @yield('content')
    </div>
    @yield('scripts')
    <script src="{{ asset('js/scripts.js') }}"></script>
    <x-molecules.alert />
</body>
</html>