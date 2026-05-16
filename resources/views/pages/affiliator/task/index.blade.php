@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
<x-organisms.mobile-page-wrapper title="Daftar Tugas Anda" subtitle="Selesaikan kewajiban video dari sampel yang diterima.">
    <x-molecules.glass-tabs>
        <x-molecules.glass-tab-item href="?tab=process">Dalam Proses</x-molecules.glass-tab-item>
        <x-molecules.glass-tab-item href="?tab=completed">Riwayat Selesai</x-molecules.glass-tab-item>
    </x-molecules.glass-tabs>
    
</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
@endpush