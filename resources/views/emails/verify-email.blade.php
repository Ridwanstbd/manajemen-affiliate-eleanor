<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Email</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: #333;">Halo, {{ $user->name }}!</h2>
        <p>Terima kasih telah mendaftar di aplikasi kami. Untuk memastikan keamanan akun Anda dan mengaktifkan fitur penuh, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>
        
        <div style="text-align: center;">
            <a href="{{ $url }}" class="btn">Verifikasi Email Saya</a>
        </div>

        <p style="margin-top: 30px;">Jika tombol di atas tidak berfungsi, Anda juga bisa menyalin dan menempelkan tautan berikut ke browser Anda:</p>
        <p style="word-break: break-all; color: #007bff;">{{ $url }}</p>

        <p>Jika Anda tidak merasa mendaftar di aplikasi ini, Anda dapat mengabaikan email ini.</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>