@extends('layouts.app')
@section('title', 'Kelola Affiliator')

@section('content')
    <div class="user-wrapper">
        <x-molecules.glass-tabs>
            <x-molecules.glass-tab-item :active="$currentTab === 'request-access'" href="?tab=request-access">Menunggu Persetujuan</x-molecules.glass-tab-item>
            <x-molecules.glass-tab-item :active="$currentTab === 'active'" href="?tab=active">Affiliator Aktif</x-molecules.glass-tab-item>
            <x-molecules.glass-tab-item :active="$currentTab === 'blacklist'" href="?tab=blacklist">Daftar Hitam</x-molecules.glass-tab-item>
            <x-molecules.glass-tab-item :active="$currentTab === 'kol-contract'" href="?tab=kol-contract">Kontrak KOL</x-molecules.glass-tab-item>
        </x-molecules.glass-tabs>

        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @if($currentTab === 'request-access')
                @include('pages.admin.users.request-access.index', get_defined_vars())
            @elseif($currentTab === 'active')
                @include('pages.admin.users.active.index', get_defined_vars())
            @elseif($currentTab === 'blacklist')
                @include('pages.admin.users.blacklist.index', get_defined_vars())
            @elseif($currentTab === 'kol-contract')
                @include('pages.admin.users.kol-contract.index', get_defined_vars())
            @endif
        </div>
    </div>
@endsection