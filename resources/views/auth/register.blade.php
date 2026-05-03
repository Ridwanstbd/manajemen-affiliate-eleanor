@extends('layouts.auth')
@section('title', 'Daftar')

@section('content')
    <x-organisms.auth-card 
        title="Daftar Affiliator" 
        subtitle="Pendaftaran Mitra Eleanor"
    >
        <form action="{{ route('access.send-request') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Username TikTok" />
                <x-atoms.input type="text" name="username_tiktok" placeholder="mazzprifarm" required />
            </div>
            
            <div class="form-group">
                <x-atoms.label value="Email" />
                <x-atoms.input type="email" name="email" placeholder="name@company.com" required />
            </div>
            
            <div class="form-group">
                <x-atoms.label value="Nomor Telepon WA aktif" />
                <x-atoms.input type="text" name="phone_number" placeholder="08123456789" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Daftar
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Pastikan data benar-benar milik anda
        </x-slot>
    </x-organisms.auth-card>
@endsection