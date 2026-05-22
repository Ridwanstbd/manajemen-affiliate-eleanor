@extends('layouts.app')

@section('title', 'Kontrak KOL')
@section('is_subpage', true)
@section('back_url', route('affiliator.profile.index'))

@section('content')
<x-organisms.mobile-page-wrapper title="Kontrak KOL Saya" subtitle="Daftar seluruh kontrak Key Opinion Leader yang terhubung dengan akun Anda.">

    <x-molecules.alert />

    <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px;">

        @forelse($contracts as $contract)
            @php
                $isActive  = $contract->status === 'ACTIVE';
                $isExpired = $contract->end_date->isPast();
                $badgeStatus = $isExpired ? 'overdue' : ($isActive ? 'paid' : 'pending');
                $badgeLabel  = $isExpired ? 'Kedaluwarsa' : ($isActive ? 'Aktif' : ucfirst(strtolower($contract->status)));
                $daysLeft    = now()->diffInDays($contract->end_date, false);
            @endphp

            <a href="{{ route('affiliator.contract-kol.show', $contract->id) }}"
               style="text-decoration: none; display: block;">
                <x-molecules.card style="border: 1px solid var(--glass-border); border-radius: 16px; padding: 20px; background: rgba(255,255,255,0.7); box-shadow: 0 2px 12px rgba(0,0,0,0.04); transition: box-shadow 0.2s, transform 0.2s;"
                    onmouseover="this.style.boxShadow='0 6px 24px rgba(0,0,0,0.10)'; this.style.transform='translateY(-1px)'"
                    onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'">

                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-blue), #60a5fa); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <x-atoms.icon name="invoices" style="width: 22px; height: 22px; color: #ffffff;" />
                            </div>
                            <div>
                                <x-atoms.typography variant="card-title" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.3;">
                                    Kontrak #{{ $contract->id }}
                                </x-atoms.typography>
                                <span style="font-size: 11px; color: var(--text-tertiary);">
                                    Dibuat {{ $contract->created_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <x-atoms.badge :status="$badgeStatus">{{ $badgeLabel }}</x-atoms.badge>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div style="background: rgba(241,245,249,0.6); border-radius: 10px; padding: 10px 12px;">
                            <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 2px;">Mulai</span>
                            <span style="font-size: 13px; font-weight: 700; color: var(--text-primary);">
                                {{ $contract->start_date->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div style="background: rgba(241,245,249,0.6); border-radius: 10px; padding: 10px 12px;">
                            <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 2px;">Berakhir</span>
                            <span style="font-size: 13px; font-weight: 700; color: {{ $isExpired ? '#ef4444' : 'var(--text-primary)' }};">
                                {{ $contract->end_date->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div style="background: rgba(241,245,249,0.6); border-radius: 10px; padding: 10px 12px;">
                            <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 2px;">Fee Kontrak</span>
                            <span style="font-size: 13px; font-weight: 700; color: var(--primary-blue);">
                                Rp {{ number_format($contract->contract_fee, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="background: rgba(241,245,249,0.6); border-radius: 10px; padding: 10px 12px;">
                            <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 2px;">Target Video</span>
                            <span style="font-size: 13px; font-weight: 700; color: var(--text-primary);">
                                {{ $contract->required_video_count }} video
                            </span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--glass-border); padding-top: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <x-atoms.icon name="vendors" style="width: 15px; height: 15px; color: var(--text-secondary);" />
                            <span style="font-size: 12.5px; color: var(--text-secondary);">
                                {{ $contract->products->count() }} produk terhubung
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 4px; color: var(--primary-blue); font-size: 12.5px; font-weight: 600;">
                            Lihat Detail
                            <x-atoms.icon name="chevron-right" style="width: 14px; height: 14px;" />
                        </div>
                    </div>

                </x-molecules.card>
            </a>
        @empty
            <div style="text-align: center; padding: 48px 24px; background: rgba(255,255,255,0.5); border-radius: 20px; border: 1px dashed var(--glass-border);">
                <x-atoms.icon name="invoices" style="width: 40px; height: 40px; color: var(--text-tertiary); margin-bottom: 16px; opacity: 0.4;" />
                <x-atoms.typography variant="card-title" as="h3" style="font-size: 15px; color: var(--text-secondary); margin-bottom: 6px;">
                    Belum Ada Kontrak
                </x-atoms.typography>
                <p style="font-size: 13px; color: var(--text-tertiary); margin: 0;">
                    Kontrak KOL Anda akan muncul di sini setelah admin menambahkannya.
                </p>
            </div>
        @endforelse

    </div>

</x-organisms.mobile-page-wrapper>
@endsection