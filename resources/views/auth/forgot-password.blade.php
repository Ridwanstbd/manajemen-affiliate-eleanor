@extends('layouts.auth')
@section('title', 'Lupa Kata Sandi')

@section('content')
    <x-organisms.auth-card 
        title="Lupa Kata Sandi" 
        subtitle="pastikan email dibawah aktif untuk set ulang kata sandi"
    >
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Email Aktif" />
                <x-atoms.input type="email" name="email" placeholder="name@company.com" value="{{ old('email', $email ?? '') }}" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Kirimkan Link Set Ulang
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            <a href="{{ route('login') }}" class="text-link" style="display: inline-flex; align-items: center; gap: 4px;">
                ← Kembali 
            </a>
        </x-slot>
    </x-organisms.auth-card>
@endsection