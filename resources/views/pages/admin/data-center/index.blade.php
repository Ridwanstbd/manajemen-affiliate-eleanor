@extends('layouts.app')
@section('title', 'Data Center & Analitik')

@section('content')
    @php
        $currentTab = request('tab', 'analytics');
    @endphp

    <div class="data-center-wrapper">
        <x-molecules.glass-tabs>
            <x-molecules.glass-tab-item :active="$currentTab === 'analytics'" href="?tab=analytics">Analisa & ROI</x-molecules.glass-tab-item>
            <x-molecules.glass-tab-item :active="$currentTab === 'summary'" href="?tab=summary">Ringkasan</x-molecules.glass-tab-item>
            <x-molecules.glass-tab-item :active="$currentTab === 'detail'" href="?tab=detail">Detail ROI</x-molecules.glass-tab-item>

            <x-slot name="actions">
                <x-molecules.dropdown>
                    <x-slot:trigger>
                        <x-atoms.button variant="secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); font-weight: 500;">
                            <x-atoms.icon name="journal" style="width: 16px; height: 16px; margin-right: 6px; display: inline-block; vertical-align: middle;" />
                            {{ $selectedMonthLabel }} 
                        </x-atoms.button>
                    </x-slot:trigger>

                    @forelse($availableMonths as $m)
                        <x-atoms.dropdown-item href="?tab={{ $currentTab }}&selected_month={{ $m['value'] }}">
                            {{ $m['label'] }}
                        </x-atoms.dropdown-item>
                    @empty
                        <x-atoms.dropdown-item>Belum ada data</x-atoms.dropdown-item>
                    @endforelse
                </x-molecules.dropdown>
            </x-slot>
        </x-molecules.glass-tabs>

        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @if($currentTab === 'analytics')
                @include('pages.admin.data-center.analytics-roi')
            @elseif($currentTab === 'summary')
                @include('pages.admin.data-center.summary')
            @elseif($currentTab === 'detail')
                @include('pages.admin.data-center.detail-roi')
            @endif
        </div>
    </div>
@endsection