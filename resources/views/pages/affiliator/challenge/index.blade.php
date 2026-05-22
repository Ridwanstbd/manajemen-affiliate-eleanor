@extends('layouts.app')

@section('title', 'Daftar Tantangan')
@section('is_subpage', true)
@section('back_url', route('affiliator.index'))

@section('content')
<x-organisms.mobile-page-wrapper title="Tantangan Kreator" subtitle="Ikuti tantangan, capai target, dan menangkan bonus komisi tambahan!">
    
    <div style="margin-top: 24px; margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <x-atoms.icon name="trend-up" style="width: 20px; height: 20px; color: var(--primary-blue);" />
            <x-atoms.typography variant="card-title" as="h3" style="font-size: 16px; margin: 0;">Sedang Berlangsung</x-atoms.typography>
        </div>

        @forelse($activeChallenges as $challenge)
            @php
                $bannerUrl = $challenge->banner_image_path ? (filter_var($challenge->banner_image_path, FILTER_VALIDATE_URL) ? $challenge->banner_image_path : asset('storage/' . $challenge->banner_image_path)) : null;
            @endphp
            <div style="margin-bottom: 16px;">
                <x-molecules.challenge-banner-slide
                    link="{{ route('affiliator.challenge.show', $challenge->id) }}"
                    tag="Aktif"
                    title="{{ $challenge->title }}"
                    description="Selesai: {{ \Carbon\Carbon::parse($challenge->end_date)->format('d M Y') }} "
                    icon="gift"
                    banner="{{ $bannerUrl }}"
                />
            </div>
        @empty
            <div style="text-align: center; padding: 32px 16px; background: rgba(255,255,255,0.5); border-radius: 16px; border: 1px dashed var(--glass-border);">
                <x-atoms.icon name="calendar" style="width: 32px; height: 32px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.5;" />
                <p style="color: var(--text-secondary); font-size: 13.5px; margin: 0;">Belum ada tantangan aktif saat ini.<br>Nantikan kejutan selanjutnya!</p>
            </div>
        @endforelse
    </div>

    @if($pastChallenges->count() > 0)
    <div style="margin-top: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <x-atoms.icon name="clock" style="width: 20px; height: 20px; color: var(--text-secondary);" />
            <x-atoms.typography variant="card-title" as="h3" style="font-size: 16px; margin: 0; color: var(--text-secondary);">Tantangan Berlalu</x-atoms.typography>
        </div>

        @foreach($pastChallenges as $challenge)
            @php
                $bannerUrl = $challenge->banner_image_path ? (filter_var($challenge->banner_image_path, FILTER_VALIDATE_URL) ? $challenge->banner_image_path : asset('storage/' . $challenge->banner_image_path)) : null;
            @endphp
            <div style="margin-bottom: 16px; opacity: 0.75; filter: grayscale(40%);">
                <x-molecules.challenge-banner-slide
                    link="{{ route('affiliator.challenge.show', $challenge->id) }}"
                    tag="Selesai"
                    title="{{ $challenge->title }}"
                    description="Berakhir: {{ \Carbon\Carbon::parse($challenge->end_date)->format('d M Y') }}"
                    icon="medal"
                    banner="{{ $bannerUrl }}"
                    gradient="linear-gradient(135deg, #64748b, #94a3b8)"
                />
            </div>
        @endforeach
    </div>
    @endif

</x-organisms.mobile-page-wrapper>
@endsection