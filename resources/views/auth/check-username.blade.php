@extends('layouts.auth')
@section('title', 'Cek Akun')

@section('content')
    <x-organisms.auth-card 
        title="Selamat Datang," 
        subtitle="Masukkan Username TikTok"
    >
        <form action="{{ route('login.verify-username') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Username Tiktok" />
                <x-atoms.input type="text" name="username" placeholder="mazzprifarm" required />
            </div>
            
            <x-atoms.button variant="primary" type="submit" class="btn-block">
                Lanjutkan
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Langkah untuk konfirmasi akun terdaftar
        </x-slot>
    </x-organisms.auth-card>
@endsection