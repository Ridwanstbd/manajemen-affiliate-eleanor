@extends('layouts.app')

@section('title', 'Papan Peringkat')

@section('content')
<x-organisms.mobile-page-wrapper title="Pengajuan Sampel Saya" subtitle="Pantau proses pengiriman dan konfirmasi sampel Anda.">
    <x-molecules.glass-tabs>
        <x-molecules.glass-tab-item 
            :active="$currentTab === 'request-sample'" 
            href="{{ route('affiliator.sample-request.index', ['tab' => 'request-sample']) }}">
            Diajukan
        </x-molecules.glass-tab-item>
        
        <x-molecules.glass-tab-item 
            :active="$currentTab === 'shipped'" 
            href="{{ route('affiliator.sample-request.index', ['tab' => 'shipped']) }}">
            Selesai
        </x-molecules.glass-tab-item>
    </x-molecules.glass-tabs>

        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @if($currentTab === 'request-sample')
                @include('pages.affiliator.sample-request.all.index', get_defined_vars())
            @elseif($currentTab === 'shipped')
                @include('pages.affiliator.sample-request.shipped.index', get_defined_vars())
            @endif
        </div>
    
</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
@endpush