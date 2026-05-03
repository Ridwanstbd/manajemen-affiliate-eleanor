@extends('layouts.auth')
@section('title', 'Cek Kata Sandi ')

@section('content')
    <x-organisms.auth-card 
        title="Anda terdaftar" 
        subtitle="Masukan Kata Sandi"
    >
        <form action="{{ route('login.verify-password') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Password" />
                <x-atoms.input type="password" name="password" placeholder="••••••••" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block">
                Masuk
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Lupa Kata Sandi? <a href="{{ route('password.request') }}" class="text-link">Klik disini.</a>
        </x-slot>
    </x-organisms.auth-card>
@endsection