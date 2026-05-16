@extends('layouts.app')

@section('title', 'Papan Peringkat')

@section('content')
<x-organisms.mobile-page-wrapper title="Papan Peringkat" subtitle="Pantau posisi Anda dan raih peringkat teratas!">
    <x-molecules.glass-tabs>
        <x-molecules.glass-tab-item href="?tab=process">Peringkat Bulanan</x-molecules.glass-tab-item>
        <x-molecules.glass-tab-item href="?tab=completed">Tantangan Spesifik</x-molecules.glass-tab-item>
    </x-molecules.glass-tabs>
    
</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
@endpush