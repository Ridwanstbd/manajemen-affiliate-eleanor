@extends('layouts.auth')
@section('title', 'Reset Password - LedgerFlow')

@section('content')
    <x-organisms.auth-card 
        title="Set new password" 
        subtitle="Your new password must be different from previous used passwords."
        :useIcon="true"
    >
        <x-slot name="iconSvg">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </x-slot>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            {{-- Token reset password dari Laravel --}}
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            
            <div class="form-group">
                <x-atoms.label value="Email Address" />
                <x-atoms.input type="email" name="email" value="{{ old('email', request()->email) }}" required readonly />
            </div>

            <div class="form-group">
                <x-atoms.label value="Password" />
                <x-atoms.input type="password" name="password" placeholder="••••••••" required />
            </div>

            <div class="form-group">
                <x-atoms.label value="Confirm Password" />
                <x-atoms.input type="password" name="password_confirmation" placeholder="••••••••" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Reset Password
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            <a href="{{ route('login') }}" class="text-link" style="display: inline-flex; align-items: center; gap: 4px;">
                ← Back to log in
            </a>
        </x-slot>
    </x-organisms.auth-card>
@endsection