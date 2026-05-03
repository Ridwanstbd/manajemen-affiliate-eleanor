@extends('layouts.auth')
@section('title', 'Set Ulang Kata Sandi')

@section('content')
    <x-organisms.auth-card 
        title="Halaman Aman" 
        subtitle="Kata sandi baru Anda harus berbeda dari kata sandi yang pernah Anda gunakan sebelumnya."
    >

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            {{-- Token reset password dari Laravel --}}
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            
            <div class="form-group">
                <x-atoms.label value="Email" />
                <x-atoms.input type="email" name="email" value="{{ old('email', request()->email) }}" required readonly />
            </div>

            <div class="form-group">
                <x-atoms.label value="Kata Sandi" />
                <x-atoms.input type="password" name="password" placeholder="••••••••" required />
            </div>

            <div class="form-group">
                <x-atoms.label value="Konfirmasi Kata Sandi" />
                <x-atoms.input type="password" name="password_confirmation" placeholder="••••••••" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Set Ulang
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            <a href="{{ route('login') }}" class="text-link" style="display: inline-flex; align-items: center; gap: 4px;">
                ← Kembali ke log in
            </a>
        </x-slot>
    </x-organisms.auth-card>
@endsection