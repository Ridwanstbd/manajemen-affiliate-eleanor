@extends('layouts.app')

@section('title', 'Form Pelaporan Tugas')
@section('is_subpage', true)
@section('back_url', route('affiliator.task.show', $task->id))

@section('content')
@php
    $daysRemaining = isset($task->due_date) ? \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($task->due_date)->startOfDay(), false) : null;
    $remainingText = $daysRemaining !== null ? ($daysRemaining >= 0 ? "(Sisa {$daysRemaining} Hari)" : "(Terlambat " . abs($daysRemaining) . " Hari)") : '';
@endphp

<x-organisms.mobile-page-wrapper title="Form Pelaporan Tugas" subtitle="Kirimkan bukti tautan video TikTok Anda untuk diverifikasi.">
    
    <form action="{{ route('affiliator.task.submit', $task->id) }}" method="POST">
        @csrf
        
        <x-molecules.card style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px; margin-top: 20px; box-shadow: none;">
            <x-atoms.typography variant="body" style="font-size: 14.5px; color: var(--text-secondary); margin-bottom: 6px; display: block;">
                ID Pengajuan: <strong style="color: var(--text-primary); font-weight: 800;">REQ-#{{ $task->id }}</strong>
            </x-atoms.typography>
            <x-atoms.typography variant="body" style="font-size: 14.5px; color: var(--text-secondary); display: block;">
                Tenggat Waktu: <strong style="color: var(--text-primary); font-weight: 800;">{{ isset($task->due_date) ? \Carbon\Carbon::parse($task->due_date)->translatedFormat('d F Y') : 'Tidak Ditentukan' }} {{ $remainingText }}</strong>
            </x-atoms.typography>
        </x-molecules.card>

        <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin-bottom: 24px;">

        <div style="margin-bottom: 24px;">
            <x-atoms.label style="font-weight: 800; color: var(--text-primary); font-size: 16px; margin-bottom: 4px; display: block;">
                Tautan Video TikTok <span style="color: var(--text-secondary); font-weight: normal;">(Wajib)</span>
            </x-atoms.label>
            <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; margin-top: 0; display: block;">
                Salin (copy) tautan video yang telah Anda unggah di TikTok.
            </x-atoms.typography>
            
            <x-atoms.input 
                type="url" 
                name="tiktok_video_link" 
                placeholder="Tempelkan (paste) tautan di sini..." 
                value="{{ old('tiktok_video_link') }}"
                required 
                style="width: 100%; box-sizing: border-box; padding: 12px 14px;"
            />
            @error('tiktok_video_link')
                <x-atoms.typography variant="body" style="color: var(--rose); font-size: 12px; margin-top: 6px; display: block;">
                    {{ $message }}
                </x-atoms.typography>
            @enderror
        </div>

        <div style="margin-bottom: 24px;">
            <x-atoms.label style="font-weight: 800; color: var(--text-primary); font-size: 16px; margin-bottom: 4px; display: block;">
                Pilih Produk yang Ditautkan
            </x-atoms.label>
            <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; margin-top: 0; display: block;">
                Tandai produk apa saja yang Anda sematkan pada keranjang kuning di dalam video tersebut.
            </x-atoms.typography>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($task->products as $product)
                    <label style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; background: #ffffff; margin: 0; transition: background 0.2s;">
                        <input type="checkbox" name="products[]" value="{{ $product->id }}" 
                            style="width: 20px; height: 20px; cursor: pointer; margin-top: 2px;" 
                            {{ (is_array(old('products')) && in_array($product->id, old('products'))) || count($task->products) === 1 ? 'checked' : '' }}>
                        <div>
                            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); margin-bottom: 2px; display: block;">
                                {{ $product->name }}
                            </x-atoms.typography>
                            <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block;">
                                Kategori: {{ $product->category ?? 'Lainnya' }}
                            </x-atoms.typography>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('products')
                <x-atoms.typography variant="body" style="color: var(--rose); font-size: 12px; margin-top: 6px; display: block;">
                    {{ $message }}
                </x-atoms.typography>
            @enderror
        </div>

        <x-molecules.card style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 32px; box-shadow: none;">
            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); margin-bottom: 12px; display: block;">
                Panduan Pelaporan
            </x-atoms.typography>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-secondary); font-size: 13.5px; display: flex; flex-direction: column; gap: 8px;">
                <li style="padding-left: 4px;">Pastikan video diatur ke mode Publik.</li>
                <li style="padding-left: 4px;">Pastikan keranjang kuning telah disematkan.</li>
                <li style="padding-left: 4px;">Tautan akan diverifikasi otomatis oleh sistem.</li>
            </ul>
        </x-molecules.card>

        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 40px;">
            <x-atoms.button type="submit" variant="primary" style="width: 100%; justify-content: center; padding: 14px 0; background-color: #333333; border: none;">
                Kirim Laporan Tugas
            </x-atoms.button>
            <x-atoms.button href="{{ route('affiliator.task.show', $task->id) }}" variant="outline" style="width: 100%; justify-content: center; padding: 14px 0; border: 1px solid #cbd5e1; color: var(--text-primary);">
                Batal
            </x-atoms.button>
        </div>

    </form>
</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
@endpush