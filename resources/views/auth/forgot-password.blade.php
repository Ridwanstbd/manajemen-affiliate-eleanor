@extends('layouts.auth')
@section('title', 'Forgot Password - LedgerFlow')

@section('content')
    <x-organisms.auth-card 
        title="Forgot Password" 
        subtitle="No worries, we'll send you reset instructions."
        :useIcon="true"
    >
        <x-slot name="iconSvg">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </x-slot>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Email Address" />
                <x-atoms.input type="email" name="email" placeholder="name@company.com" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Send Reset Link
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            <a href="{{ route('login') }}" class="text-link" style="display: inline-flex; align-items: center; gap: 4px;">
                ← Back to log in
            </a>
        </x-slot>
    </x-organisms.auth-card>
@endsection