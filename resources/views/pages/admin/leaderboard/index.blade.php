@extends('layouts.app')
@section('title', 'Papan Peringkat')

@section('content')
    @php
        $currentTab = request('tab', 'monthly');
    @endphp

    <div class="leaderboard-wrapper">
        <x-molecules.glass-tabs>
            <x-molecules.glass-tab-item :active="$currentTab === 'monthly'" href="?tab=monthly">Bulanan</x-molecules.glass-tab-item>
            <x-molecules.glass-tab-item :active="$currentTab === 'challenge'" href="?tab=challenge">Tantangan</x-molecules.glass-tab-item>
        </x-molecules.glass-tabs>

        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @if($currentTab === 'monthly')
                @include('pages.admin.leaderboard.monthly')
            @elseif($currentTab === 'challenge')
                @include('pages.admin.leaderboard.challenge')
            @endif
        </div>
    </div>
@endsection