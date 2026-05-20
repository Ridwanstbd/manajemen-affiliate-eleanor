@extends('layouts.app')

@section('title', 'Persetujuan Kerjasama')
@section('is_subpage', true)
@section('back_url', route('affiliator.profile.index'))

@section('content')
<x-organisms.mobile-page-wrapper title="Persetujuan Kerjasama" subtitle="Syarat & Ketentuan yang mengikat kemitraan Anda.">

    @if($status['is_blacklisted'])
        <x-molecules.card style="background-color: #fff1f2; border: 1px solid #f43f5e; border-radius: 4px; padding: 16px; margin-top: 20px; box-shadow: none;">
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #f43f5e; border-radius: 4px; background: #ffffff; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
                <div>
                    <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: #e11d48; margin-bottom: 4px; display: block;">
                        Status: {{ $status['status_text'] }}
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block; line-height: 1.4;">
                        {{ $status['desc'] }}
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 12px; color: #e11d48; display: block; margin-top: 4px; font-weight: 600;">
                        {{ $status['date_label'] }} {{ \Carbon\Carbon::parse($status['date_value'])->translatedFormat('d F Y') }}
                    </x-atoms.typography>
                </div>
            </div>
        </x-molecules.card>
    @else
        <x-molecules.card style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 16px; margin-top: 20px; box-shadow: none;">
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div>
                    <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); margin-bottom: 4px; display: block;">
                        Status: {{ $status['status_text'] }}
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block;">
                        {{ $status['desc'] }}
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-tertiary); display: block; margin-top: 4px;">
                        {{ $status['date_label'] }} {{ \Carbon\Carbon::parse($status['date_value'])->translatedFormat('d F Y') }}
                    </x-atoms.typography>
                </div>
            </div>
        </x-molecules.card>
    @endif

    <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 24px 0;">

    <x-atoms.typography variant="h3" style="font-weight: 800; font-size: 16px; color: var(--text-primary); margin-bottom: 16px; display: block;">
        Poin Perjanjian (Agreements)
    </x-atoms.typography>

    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 40px;">
        @forelse($agreements as $index => $agreement)
            <x-molecules.card style="padding: 16px; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: none; background: #ffffff;">
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #333; font-size: 13px; flex-shrink: 0; margin-top: 2px;">
                        {{ $index + 1 }}
                    </div>
                    <div style="flex-grow: 1;">
                        <div style="font-size: 14.5px; color: var(--text-secondary); line-height: 1.5;">
                            {!! nl2br(e($agreement->content)) !!}
                        </div>
                    </div>
                </div>
            </x-molecules.card>
        @empty
            <div style="text-align: center; padding: 20px; color: var(--text-tertiary);">
                Belum ada data perjanjian yang aktif saat ini.
            </div>
        @endforelse
    </div>

    <div style="text-align: center; margin-bottom: 40px;">
        <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-tertiary); display: block; line-height: 1.6;">
            Dokumen ini bersumber dari data mutakhir perusahaan.<br>
            PT Eleanor Project Global Indonesia.
        </x-atoms.typography>
    </div>

</x-organisms.mobile-page-wrapper>
@endsection