@extends('layouts.auth') @section('content')
<div class="card p-4 mx-auto" style="max-width: 500px; margin-top: 50px;">
    <div class="card-body text-center">
        <h3 class="mb-3">Verifikasi Email Anda</h3>
        <p class="text-muted mb-4">
            Terima kasih telah mendaftar! Kami telah mengirimkan tautan verifikasi ke email Anda. 
            Silakan cek kotak masuk (atau folder spam) Anda.
        </p>

        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <p>Belum menerima email?</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
                Kirim Ulang Email Verifikasi
            </button>
        </form>
    </div>
</div>
@endsection