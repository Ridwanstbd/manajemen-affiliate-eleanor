@extends('layouts.app')

@section('title', 'Detail Tantangan')
@section('is_subpage', true)
@section('back_url', route('affiliator.challenge.index'))

@section('content')
@php
    $fallbackImage = 'https://placehold.co/800x600?text=Challenge+Banner';
    $imageUrl = (!empty($challenge) && !empty($challenge->banner_image_path)) 
                ? (filter_var($challenge->banner_image_path, FILTER_VALIDATE_URL) ? $challenge->banner_image_path : asset('storage/' . $challenge->banner_image_path)) 
                : $fallbackImage;
                
    $now = \Carbon\Carbon::now();
    $isActive = $challenge->is_active && $now->between(\Carbon\Carbon::parse($challenge->start_date), \Carbon\Carbon::parse($challenge->end_date));
@endphp

<div style="width: 100%; height: 220px; background: url('{{ $imageUrl }}') center/cover no-repeat; position: relative; margin-bottom: -30px;">
    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.2) 60%, transparent 100%);"></div>
    <div style="position: absolute; bottom: 40px; left: 20px; right: 20px;">
        <x-atoms.badge status="{{ $isActive ? 'paid' : 'pending' }}" style="margin-bottom: 10px; background: {{ $isActive ? 'var(--primary-blue)' : '#64748b' }}; color: white; border: none;">
            {{ $isActive ? 'Sedang Berlangsung' : 'Telah Berakhir' }}
        </x-atoms.badge>
        <h1 style="color: #ffffff; font-size: 22px; font-weight: 800; margin: 0; line-height: 1.3;">{{ $challenge->title }}</h1>
    </div>
</div>

<x-organisms.mobile-page-wrapper>
    
    <x-molecules.card style="padding: 20px; border: 1px solid var(--glass-border); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); position: relative; z-index: 10;">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px dashed var(--glass-border);">
            <div>
                <div style="font-size: 12px; color: var(--text-tertiary); margin-bottom: 4px;">Periode Tantangan</div>
                <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">
                    {{ \Carbon\Carbon::parse($challenge->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($challenge->end_date)->format('d M Y') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-tertiary); margin-bottom: 4px;">Ekstra Komisi Bonus</div>
                <div style="font-size: 14px; font-weight: 800; color: var(--primary-blue);">
                    Up to Rp {{ number_format($challenge->commission_bonus, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div style="margin-bottom: 8px;">
            <x-atoms.typography variant="card-title" as="h3" style="font-size: 15px; margin-bottom: 12px;">Aturan & Mekanisme</x-atoms.typography>
            <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; white-space: pre-line;">
                {!! nl2br(e($challenge->rules)) !!}
            </div>
        </div>
    </x-molecules.card>

    @if($challenge->rewards && $challenge->rewards->count() > 0)
    <x-molecules.card title="Target & Hadiah" icon="gift" style="padding: 16px; margin-top: 16px; border: 1px solid var(--glass-border); border-radius: 16px; background: rgba(255, 255, 255, 0.6);">
        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px;">
            @foreach($challenge->rewards as $reward)
                <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px; background: #ffffff; border: 1px solid rgba(0,0,0,0.03); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    <div style="background: var(--primary-blue-soft, #eff6ff); padding: 8px; border-radius: 10px; color: var(--primary-blue, #3b82f6);">
                        <x-atoms.icon name="check-circle" style="width: 20px; height: 20px;" />
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">{{ $reward->target_metric }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.4;">{{ $reward->reward_description }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-molecules.card>
    @endif

    @if($challenge->winners && $challenge->winners->count() > 0)
    <x-molecules.card title="Pemenang Tantangan" icon="medal" style="padding: 16px; margin-top: 16px; border: 1px solid var(--glass-border); border-radius: 16px; background: rgba(255, 255, 255, 0.6);">
        <div style="display: flex; flex-direction: column; margin-top: 12px;">
            @foreach($challenge->winners as $winner)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: {{ $loop->last ? 'none' : '1px dashed var(--glass-border)' }};">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @php
                            $username = $winner->user->username ?? 'Affiliator';
                            $initials = strtoupper(substr($username, 0, 2));
                        @endphp
                        <x-atoms.avatar initials="{{ $initials }}" style="width: 36px; height: 36px; font-size: 14px; background: var(--primary-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;" />
                        <div>
                            <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary);">{{ $username }}</div>
                            <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 2px;">{{ $winner->category }}</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; font-weight: 800; color: var(--primary-blue);">
                            {{ $winner->reward_given }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-molecules.card>
    @elseif(!$isActive)
    <x-molecules.card style="padding: 16px; margin-top: 16px; border: 1px solid var(--glass-border); border-radius: 16px; text-align: center; background: rgba(255, 255, 255, 0.4);">
        <p style="font-size: 13px; color: var(--text-tertiary); margin: 0;">Pengumuman pemenang sedang diproses. Nantikan infonya!</p>
    </x-molecules.card>
    @endif

</x-organisms.mobile-page-wrapper>
@endsection