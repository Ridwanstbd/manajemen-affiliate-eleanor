@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="profile-header-mobile">
    <div class="profile-avatar-mobile">
        <x-atoms.icon name="profile" />
    </div>
    <div class="profile-name-mobile">{{ $user['username'] }}</div>
    <div class="profile-contact-mobile">
        {{ $user['email'] }}<br>
        {{ $user['phone'] }}
    </div>
    <div class="profile-badge-mobile">
        {{ $user['status'] }}
    </div>
</div>

<div class="menu-list-mobile">
    @foreach($menus as $menu)
        <a href="{{ $menu['route'] }}" class="menu-item-mobile">
            <div class="menu-icon-box-mobile">
                <x-atoms.icon name="{{ $menu['icon'] }}" />
            </div>
            <div class="menu-content-mobile">
                <div class="menu-title-mobile">{{ $menu['title'] }}</div>
                <div class="menu-subtitle-mobile">{{ $menu['subtitle'] }}</div>
            </div>
            <div class="menu-chevron-mobile">
                <x-atoms.icon name="chevron-right" />
            </div>
        </a>
    @endforeach
</div>

<form method="POST" action="{{ route('logout') }}" id="logout">
    @csrf
    <x-atoms.button variant="primary" type="submit" form="logout" class="logout-btn-mobile">
        Keluar dari Akun 
    </x-atoms.button>
</form>
@endsection

@push('scripts')
@endpush