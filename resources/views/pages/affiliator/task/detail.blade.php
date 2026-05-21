@extends('layouts.app')

@section('title', 'Detail Tugas')
@section('is_subpage', true)
@section('back_url', route('affiliator.task.index'))

@section('content')
@php
    $isOverdue = $task->task_status === 'OVERDUE' || (isset($task->due_date) && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->task_status !== 'COMPLETED');
    
    $product = $task->products->first();
    $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
    $imageUrl = (!empty($product) && !empty($product->image_path)) 
                ? (filter_var($product->image_path, FILTER_VALIDATE_URL) ? $product->image_path : asset('storage/' . $product->image_path)) 
                : $fallbackImage;
@endphp

<x-organisms.mobile-page-wrapper title="Detail Peringatan Tugas" subtitle="Tinjau kewajiban tugas yang belum Anda selesaikan.">
    
    <x-molecules.card style="padding: 16px; margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
            <div>
                <x-atoms.typography variant="body" style="font-weight: 800; color: var(--text-primary); font-size: 15px;">
                    TUGAS-{{ $task->id }}
                </x-atoms.typography>
            </div>
            <div>
                @if($task->task_status === 'COMPLETED')
                    <x-atoms.badge status="paid">Selesai</x-atoms.badge>
                @elseif($isOverdue)
                    <x-atoms.badge status="overdue">Melewati Batas</x-atoms.badge>
                @else
                    <x-atoms.badge status="pending">Sedang Diproses</x-atoms.badge>
                @endif
            </div>
        </div>
        
        <div style="margin-top: 14px; background: {{ $isOverdue ? 'var(--rose-soft)' : '#f8fafc' }}; padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 10px; border: 1px solid {{ $isOverdue ? 'rgba(244, 63, 94, 0.2)' : '#e2e8f0' }};">
            <x-atoms.icon name="clock" style="width: 20px; height: 20px; color: {{ $isOverdue ? 'var(--rose)' : 'var(--text-secondary)' }};" />
            <div>
                <x-atoms.typography variant="body" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: {{ $isOverdue ? 'var(--rose)' : 'var(--text-tertiary)' }};">
                    Batas Akhir Pengerjaan
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 14px; font-weight: 700; color: {{ $isOverdue ? 'var(--rose)' : 'var(--text-primary)' }}; margin-top: 2px;">
                    {{ isset($task->due_date) ? \Carbon\Carbon::parse($task->due_date)->translatedFormat('l, d F Y') : 'Tidak Ditentukan' }}
                </x-atoms.typography>
            </div>
        </div>
    </x-molecules.card>

    <x-molecules.card style="padding: 16px; margin-top: 16px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <x-atoms.icon name="gift" style="width: 18px; height: 18px; color: var(--text-primary);" />
            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                Informasi Sampel Produk
            </x-atoms.typography>
        </div>
        
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 64px; height: 64px; border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex-grow: 1;">
                <x-atoms.typography variant="h3" style="font-size: 14.5px; font-weight: 700; line-height: 1.3; margin-bottom: 4px; color: #000000;">
                    {{ $product->name ?? 'Produk Sampel Tidak Diketahui' }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-tertiary); margin-bottom: 4px;">
                    Kategori: {{ $product->category ?? 'Lainnya' }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 12px; font-weight: 600; color: var(--primary-blue);">
                    Wajib Video: {{ $product->mandatory_video_count ?? 1 }} Video
                </x-atoms.typography>
            </div>
        </div>
    </x-molecules.card>

    <x-molecules.card style="padding: 16px; margin-top: 16px; margin-bottom: 40px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <x-atoms.icon name="link" style="width: 18px; height: 18px; color: var(--text-primary);" />
            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                Laporkan Bukti Video
            </x-atoms.typography>
        </div>

        @if($task->task_status === 'COMPLETED')
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); padding: 20px 16px; border-radius: 8px; text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                <x-atoms.typography variant="h4" style="color: #065f46; font-weight: 800; margin-bottom: 6px;">
                    Tugas Selesai!
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px; line-height: 1.4;">
                    Terima kasih telah menyelesaikan dan mengunggah video sampel produk ini.
                </x-atoms.typography>
                
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 12px; border-radius: 8px; margin-bottom: 13px;">
                    <a href="{{ $task->tiktok_video_link }}" target="_blank" style="color: var(--primary-blue); font-weight: 600; text-decoration: none;">
                        {{ $task->tiktok_video_link }}
                    </a>
                </div>
            </div>
        @else
            <div class="task-instruction-box" style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 12px; border-radius: 8px; margin-bottom: 18px;">
                <x-atoms.typography variant="body" style="font-size: 12.5px; color: var(--text-secondary); line-height: 1.5;">
                    <strong style="color: var(--text-primary);">Instruksi:</strong> Pastikan video TikTok yang Anda unggah menampilkan produk dengan jelas dan telah menambahkan keranjang kuning (Yellow Karts) produk di atas.
                </x-atoms.typography>
            </div>

            <x-atoms.button href="{{ route('affiliator.task.report', $task->id) }}" variant="primary" style="width: 100%; display: flex; align-items: center; justify-content: center;" >
                    Lapor Tugas 
            </x-atoms.button>
        @endif
    </x-molecules.card>

</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
@endpush