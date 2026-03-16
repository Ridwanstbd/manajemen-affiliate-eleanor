@extends('layouts.auth')
@section('title', 'Sign Up - LedgerFlow')

@section('content')
    <x-organisms.auth-card 
        title="Create an account" 
        subtitle="Start managing your finances today."
    >
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <x-atoms.label value="Full Name" />
                <x-atoms.input type="text" name="name" placeholder="John Doe" required />
            </div>
            
            <div class="form-group">
                <x-atoms.label value="Email Address" />
                <x-atoms.input type="email" name="email" placeholder="name@company.com" required />
            </div>
            
            <div class="form-group">
                <x-atoms.label value="Password" />
                <x-atoms.input type="password" name="password" placeholder="Create a password" required />
            </div>

            <x-atoms.button variant="primary" type="submit" class="btn-block" style="margin-top: 8px;">
                Create Account
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Already have an account? <a href="{{ route('login') }}" class="text-link">Sign in</a>
        </x-slot>
    </x-organisms.auth-card>
@endsection