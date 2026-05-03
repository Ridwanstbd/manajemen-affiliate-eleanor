@extends('layouts.auth')
@section('title', 'Klaim Akun')

@section('content')
    <x-organisms.auth-card 
        title="Klaim Akun" 
        subtitle="Konfirmasi Akun Anda di Eleanor"
    >
        <form action="{{ route('account.send-claim') }}" method="POST">
            @csrf            
            <div class="form-group">
                <x-atoms.label value="Email" />
                <x-atoms.input type="email" name="email" placeholder="name@company.com" required />
            </div>
            
            <div class="form-group">
                <x-atoms.label value="Nomor Telepon WA aktif" />
                <x-atoms.input type="text" name="phone_number" placeholder="08123456789" required />
            </div>

            <div class="form-group">
                <x-atoms.label value="Kata Sandi" />
                <x-atoms.input type="password" name="password" placeholder="••••••••" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Klaim
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Pastikan data benar-benar milik anda
        </x-slot>
    </x-organisms.auth-card>
@endsection