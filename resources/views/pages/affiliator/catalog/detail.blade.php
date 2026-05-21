@extends('layouts.app')
@section('title', 'Detail Produk')
@section('is_subpage', true)
@section('back_url', route('affiliator.catalog.index'))

@section('content')
<x-organisms.mobile-page-wrapper>
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <div class="card-img-container">
            <img src="{{ $product->image_path ? (Str::startsWith($product->image_path, ['http://', 'https://']) ? $product->image_path : asset('storage/' . $product->image_path)) : '' }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        
        <div style="flex: 1.5; display: flex; flex-direction: column; gap: 16px;">
            <div>
                <span style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 4px;">{{ $product->category }}</span>
                <x-atoms.typography variant="h2" style="font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.3;">
                    {{ $product->name }}
                </x-atoms.typography>
            </div>

            <div style="padding: 16px; background: rgba(59, 130, 246, 0.05); border-radius: 8px; border: 1px solid rgba(59, 130, 246, 0.1);">
                <span style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 2px;">Harga Retail</span>
                <x-atoms.typography variant="h3" style="color: var(--primary-blue); font-weight: 700; margin: 0;">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </x-atoms.typography>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <x-atoms.typography variant="body" style="font-weight: 600; color: var(--text-primary); margin: 0;">SKU Penjual</x-atoms.typography>
                <div style="font-size: 14px; color: var(--text-secondary);">{{ $product->seller_sku ?? '-' }}</div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px; border-top: 1px solid var(--glass-border); padding-top: 16px;">
                <x-atoms.typography variant="body" style="font-weight: 600; color: var(--text-primary); margin: 0;">Deskripsi & Detail Produk</x-atoms.typography>
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                    {!! $product->product_detail ?? '' !!}
                </div>
            </div>

            <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid var(--glass-border);">
                <form action="{{ route('affiliator.cart.store', $product->id) }}" method="POST">
                    @csrf
                    <x-atoms.button type="submit" variant="primary" style="width: 100%; padding: 12px 32px; font-size: 14px; font-weight: 600;">
                        Tambahkan ke Pengajuan Sampel
                    </x-atoms.button>
                </form>
            </div>
        </div>
    </div>
</x-organisms.mobile-page-wrapper>
@endsection