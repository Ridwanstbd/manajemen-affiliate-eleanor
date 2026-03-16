@extends('layouts.auth')
@section('title', 'Sign In - LedgerFlow')

@section('content')
    <x-organisms.auth-card 
        title="Welcome back" 
        subtitle="Please enter your details to sign in."
    >
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Email Address" />
                <x-atoms.input type="email" name="email" placeholder="name@company.com" required />
            </div>
            
            <div class="form-group">
                <x-atoms.label value="Password" />
                <x-atoms.input type="password" name="password" placeholder="••••••••" required />
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" style="accent-color: var(--primary-blue);">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="text-link">Forgot password?</a>
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block">
                Sign In
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Don't have an account? <a href="{{ route('register') }}" class="text-link">Sign up</a>
        </x-slot>
    </x-organisms.auth-card>
@endsection