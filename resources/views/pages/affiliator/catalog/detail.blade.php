@extends('layouts.app')

@section('title', 'Detail Produk')
@section('is_subpage', true)

@section('content')
    @php
        $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
        $imageUrl = !empty($product->image_path) 
                    ? (filter_var($product->image_path, FILTER_VALIDATE_URL) ? $product->image_path : asset('storage/' . $product->image_path)) 
                    : $fallbackImage;
    @endphp

    <x-molecules.card style="padding: 20px;">
        
        <div style="width: 100%; border-radius: var(--radius-xl); overflow: hidden; margin-bottom: 24px; background: #eee; aspect-ratio: 1/1;">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
        </div>
        
        <div class="card-info">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                
                <x-atoms.badge status="pending" style="background: var(--primary-blue-soft); color: var(--primary-blue); border: none;">
                    {{ $product->category ?? 'Umum' }}
                </x-atoms.badge>
                
                <div class="stock-info">
                    @if($product->stock > 0)
                        <x-atoms.badge status="paid">Tersedia: {{ $product->stock }}</x-atoms.badge>
                    @else
                        <x-atoms.badge status="overdue">Stok Habis</x-atoms.badge>
                    @endif
                </div>
            </div>
            
            <x-atoms.typography variant="card-title" style="font-size: 22px; margin-bottom: 12px;">
                {{ $product->name }}
            </x-atoms.typography>
            
            <x-atoms.typography variant="stat-value" style="font-size: 24px; font-weight: 800; color: var(--primary-blue); margin-bottom: 20px;">
                Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
            </x-atoms.typography>
            
            <x-atoms.typography variant="card-title" style="font-size: 15px; margin-bottom: 8px;">
                Deskripsi Produk
            </x-atoms.typography>
            
            <x-atoms.typography variant="body" style="color: var(--text-secondary); line-height: 1.6; font-size: 14px; margin-bottom: 30px;">
                {{ $product->description ?? 'Tidak ada deskripsi detail untuk produk ini.' }}
            </x-atoms.typography>

            <div style="display: flex; gap: 12px; margin-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 20px;">
                <x-atoms.button variant="primary" style="width: 100%; display: flex; align-items: center; justify-content: center;" :disabled="$product->stock <= 0">
                    <x-atoms.icon name="cart" style="width: 18px; height: 18px;"/>
                    Tambah ke Keranjang
                </x-atoms.button>
            </div>
        </div>
    </x-molecules.card>
@endsection