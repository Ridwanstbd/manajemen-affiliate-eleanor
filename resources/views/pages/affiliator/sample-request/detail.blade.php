@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('is_subpage', true)

@section('content')
<x-organisms.mobile-page-wrapper title="Detail Pengajuan" subtitle="Pantau status persetujuan dan pelacakan pengiriman sampel gratis Anda.">

    <x-molecules.card style="padding: 16px; margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 14px;">
            <div>
                <x-atoms.typography variant="body" style="font-weight: 800; color: var(--text-primary); font-size: 15px;">
                    REQ-#{{ $sampleRequest->id }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-tertiary); margin-top: 2px;">
                    Tgl Pengajuan: {{ $sampleRequest->created_at->translatedFormat('d M Y, H:i') }}
                </x-atoms.typography>
            </div>
            <div>
                @if($sampleRequest->status === 'PENDING')
                    <x-atoms.badge status="pending">Menunggu Persetujuan</x-atoms.badge>
                @elseif($sampleRequest->status === 'APPROVED')
                    <x-atoms.badge status="paid" style="background: #e0f2fe; color: #0369a1;">Disetujui Admin</x-atoms.badge>
                @elseif($sampleRequest->status === 'SHIPPED')
                    <x-atoms.badge status="paid">Dalam Pengiriman</x-atoms.badge>
                @else
                    <x-atoms.badge status="overdue">Ditolak</x-atoms.badge>
                @endif
            </div>
        </div>

        <div style="margin-bottom: 4px;">
            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <x-atoms.icon name="map-pin" style="width: 16px; height: 16px; color: var(--text-secondary);" />
                <x-atoms.typography variant="body" style="font-weight: 700; font-size: 13.5px; color: var(--text-primary);">
                    Alamat Pengiriman
                </x-atoms.typography>
            </div>
            <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); line-height: 1.5; padding-left: 22px;">
                {{ $sampleRequest->address }}
            </x-atoms.typography>
        </div>
    </x-molecules.card>

    <x-molecules.card style="padding: 20px 16px; margin-top: 16px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <x-atoms.icon name="clock" style="width: 18px; height: 18px; color: var(--text-primary);" />
            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                Status Pelacakan Pengajuan
            </x-atoms.typography>
        </div>

        <div class="detail-timeline">
            @foreach($timeline as $step)
                <div class="detail-timeline-item {{ $step['is_completed'] ? 'completed' : 'pending' }} {{ isset($step['is_danger']) && $step['is_danger'] ? 'danger' : '' }}">
                    <div class="detail-timeline-badge">
                        @if(isset($step['is_danger']) && $step['is_danger'])
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        @elseif($step['is_completed'])
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                        @else
                            <div class="detail-timeline-dot"></div>
                        @endif
                    </div>
                    <div class="detail-timeline-content">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                            <x-atoms.typography variant="body" style="font-weight: 700; font-size: 14px; color: var(--text-primary);">
                                {{ $step['title'] }}
                            </x-atoms.typography>
                            @if($step['time'])
                                <span class="detail-timeline-time">{{ $step['time'] }}</span>
                            @endif
                        </div>
                        <x-atoms.typography variant="body" style="font-size: 12.5px; color: var(--text-secondary); margin-top: 4px; line-height: 1.4;">
                            {{ $step['description'] }}
                        </x-atoms.typography>
                    </div>
                </div>
            @endforeach
        </div>
    </x-molecules.card>

    <x-molecules.card style="padding: 16px; margin-top: 16px; margin-bottom: 40px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <x-atoms.icon name="cart" style="width: 18px; height: 18px; color: var(--text-primary);" />
            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                Produk yang Diajukan ({{ $sampleRequest->details->count() }} Item)
            </x-atoms.typography>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($sampleRequest->details as $detail)
                @php
                    $product = $detail->product;
                    $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
                    $imageUrl = !empty($product->image_path) 
                                ? (filter_var($product->image_path, FILTER_VALIDATE_URL) ? $product->image_path : asset('storage/' . $product->image_path)) 
                                : $fallbackImage;
                @endphp
                
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px; border: 1px solid #f1f5f9; border-radius: 8px; background: #fafafa;">
                    <div style="width: 56px; height: 56px; border-radius: 6px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; flex-shrink: 0;">
                        <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex-grow: 1;">
                        <x-atoms.typography variant="h3" style="font-size: 14.5px; font-weight: 700; color: #000000; margin-bottom: 3px; line-height: 1.3;">
                            {{ $product->name ?? 'Produk Tidak Tersedia' }}
                        </x-atoms.typography>
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary);">
                                Qty: <strong style="color: var(--text-primary);">{{ $detail->quantity }} pcs</strong>
                            </x-atoms.typography>
                            <span style="color: #cbd5e1; font-size: 12px;">&bull;</span>
                            <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary);">
                                Wajib: <strong style="color: var(--primary-blue);">{{ $product->mandatory_video_count ?? 1 }} Video</strong>
                            </x-atoms.typography>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-molecules.card>

</x-organisms.mobile-page-wrapper>
@endsection