@extends('layouts.app')

@section('title', 'Persetujuan Kerjasama')
@section('is_subpage', true)
@section('back_url', route('affiliator.profile.index'))

@section('content')
<x-organisms.mobile-page-wrapper title="Persetujuan Kerjasama" subtitle="Syarat & Ketentuan yang mengikat kemitraan Anda.">

    @if($status['is_blacklisted'])
        <div style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; margin-top: 20px; background: #fff1f2; border: 1px solid rgba(244,63,94,0.3); border-radius: 14px;">
            <div style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #fee2e2; border: 1px solid rgba(244,63,94,0.3); border-radius: 10px; flex-shrink: 0;">
                <x-atoms.icon name="minus" style="width: 18px; height: 18px; color: #e11d48;" />
            </div>
            <div>
                <x-atoms.typography variant="body" style="font-weight: 800; font-size: 14px; color: #e11d48; display: block; margin-bottom: 3px;">
                    Status: {{ $status['status_text'] }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 13px; color: #9f1239; display: block; line-height: 1.5;">
                    {{ $status['desc'] }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 11.5px; color: #e11d48; display: block; margin-top: 5px; font-weight: 600;">
                    {{ $status['date_label'] }} {{ \Carbon\Carbon::parse($status['date_value'])->translatedFormat('d F Y') }}
                </x-atoms.typography>
            </div>
        </div>
    @else
        <div style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; margin-top: 20px; background: rgba(240,253,244,0.8); border: 1px solid rgba(34,197,94,0.25); border-radius: 14px;">
            <div style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.25); border-radius: 10px; flex-shrink: 0;">
                <x-atoms.icon name="check-circle" style="width: 18px; height: 18px; color: #16a34a;" />
            </div>
            <div>
                <x-atoms.typography variant="body" style="font-weight: 800; font-size: 14px; color: #15803d; display: block; margin-bottom: 3px;">
                    Status: {{ $status['status_text'] }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block;">
                    {{ $status['desc'] }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 11.5px; color: #16a34a; display: block; margin-top: 5px; font-weight: 600;">
                    {{ $status['date_label'] }} {{ \Carbon\Carbon::parse($status['date_value'])->translatedFormat('d F Y') }}
                </x-atoms.typography>
            </div>
        </div>
    @endif

    @php $isKol = auth()->user()->is_kol; @endphp

    @if($isKol)

        @if($activeContract)
            <div style="margin-top: 28px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <x-atoms.icon name="invoices" style="width: 17px; height: 17px; color: var(--primary-blue);" />
                <x-atoms.typography variant="body" style="font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                    Kontrak Aktif Anda
                </x-atoms.typography>
            </div>

            <div style="background: linear-gradient(135deg, rgba(59,130,246,0.07), rgba(255,255,255,0.9)); border: 1px solid rgba(59,130,246,0.2); border-radius: 16px; padding: 18px; margin-bottom: 28px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-blue), #60a5fa); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <x-atoms.icon name="invoices" style="width: 20px; height: 20px; color: #fff;" />
                        </div>
                        <div>
                            <x-atoms.typography variant="card-title" style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0; display: block;">
                                Kontrak #{{ $activeContract->id }}
                            </x-atoms.typography>
                            <span style="font-size: 11px; color: var(--text-tertiary);">
                                Dibuat {{ $activeContract->created_at->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <x-atoms.badge status="paid">Aktif</x-atoms.badge>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px;">
                    <div style="background: rgba(255,255,255,0.7); border-radius: 10px; padding: 10px 12px;">
                        <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Mulai</span>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text-primary);">{{ $activeContract->start_date->translatedFormat('d M Y') }}</span>
                    </div>
                    <div style="background: rgba(255,255,255,0.7); border-radius: 10px; padding: 10px 12px;">
                        <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Berakhir</span>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text-primary);">{{ $activeContract->end_date->translatedFormat('d M Y') }}</span>
                    </div>
                    <div style="background: rgba(255,255,255,0.7); border-radius: 10px; padding: 10px 12px;">
                        <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Fee</span>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--primary-blue);">Rp {{ number_format($activeContract->contract_fee, 0, ',', '.') }}</span>
                    </div>
                    <div style="background: rgba(255,255,255,0.7); border-radius: 10px; padding: 10px 12px;">
                        <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Target Video</span>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text-primary);">{{ $activeContract->required_video_count }} video</span>
                    </div>
                </div>

                @if($activeContract->products->count())
                    <div style="border-top: 1px solid rgba(59,130,246,0.1); padding-top: 12px; margin-bottom: 14px;">
                        <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 8px; letter-spacing: 0.4px;">
                            Produk dalam Kontrak
                        </span>
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            @foreach($activeContract->products as $prod)
                                @php
                                    $prodImg = $prod->image_path
                                        ? (filter_var($prod->image_path, FILTER_VALIDATE_URL) ? $prod->image_path : asset('storage/' . $prod->image_path))
                                        : asset('img/default-product.png');
                                @endphp
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.7); border-radius: 8px; padding: 8px 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 6px; overflow: hidden; background: #f1f5f9; flex-shrink: 0;">
                                        <img src="{{ $prodImg }}" alt="{{ $prod->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text-primary); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $prod->name }}</span>
                                        <span style="font-size: 10.5px; color: var(--text-tertiary);">{{ $prod->category ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <a href="{{ route('affiliator.contract-kol.show', $activeContract->id) }}"
                   style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700; color: var(--primary-blue);">
                    <x-atoms.icon name="eye" style="width: 15px; height: 15px;" />
                    Lihat Detail Kontrak
                    <x-atoms.icon name="chevron-right" style="width: 14px; height: 14px;" />
                </a>
            </div>

            @if($personal->count())
                <div style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                    <x-atoms.icon name="check" style="width: 17px; height: 17px; color: var(--primary-blue);" />
                    <x-atoms.typography variant="body" style="font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                        Ketentuan Kontrak Anda
                    </x-atoms.typography>
                </div>
                <p style="font-size: 12.5px; color: var(--text-tertiary); margin: 0 0 14px; line-height: 1.5;">
                    Poin-poin berikut bersifat personal dan secara khusus melekat pada kontrak KOL Anda.
                </p>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 28px;">
                    @foreach($personal as $index => $agreement)
                        <div style="display: flex; gap: 14px; padding: 16px; background: linear-gradient(135deg, rgba(59,130,246,0.04), rgba(255,255,255,0.9)); border: 1px solid rgba(59,130,246,0.15); border-radius: 14px; ">
                            <div style="width: 26px; height: 26px; border-radius: 8px; background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--primary-blue); font-size: 12px; flex-shrink: 0; margin-top: 1px;">
                                {{ $index + 1 }}
                            </div>
                            <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6;">
                                {!! nl2br(e($agreement->content)) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @if($kolGeneral->count())
                <div style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                    <x-atoms.icon name="team" style="width: 17px; height: 17px; color: var(--text-secondary);" />
                    <x-atoms.typography variant="body" style="font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                        Ketentuan Umum KOL
                    </x-atoms.typography>
                </div>
                <p style="font-size: 12.5px; color: var(--text-tertiary); margin: 0 0 14px; line-height: 1.5;">
                    Berlaku untuk seluruh Key Opinion Leader yang bermitra dengan kami.
                </p>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px;">
                    @foreach($kolGeneral as $index => $agreement)
                        <div style="display: flex; gap: 14px; padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid var(--glass-border); border-radius: 14px;">
                            <div style="width: 26px; height: 26px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--text-secondary); font-size: 12px; flex-shrink: 0; margin-top: 1px;">
                                {{ $index + 1 }}
                            </div>
                            <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6;">
                                {!! nl2br(e($agreement->content)) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            <div style="margin-top: 24px; padding: 14px 16px; background: rgba(241,245,249,0.8); border: 1px solid var(--glass-border); border-radius: 12px; display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                <x-atoms.icon name="bell" style="width: 16px; height: 16px; color: var(--text-tertiary); flex-shrink: 0;" />
                <span style="font-size: 12.5px; color: var(--text-secondary); line-height: 1.5;">
                    Anda belum memiliki kontrak KOL yang aktif. Ketentuan di bawah berlaku secara umum untuk semua KOL mitra kami.
                </span>
            </div>

            @if($kolGeneral->count())
                <div style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                    <x-atoms.icon name="team" style="width: 17px; height: 17px; color: var(--primary-blue);" />
                    <x-atoms.typography variant="body" style="font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                        Ketentuan Umum KOL
                    </x-atoms.typography>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px;">
                    @foreach($kolGeneral as $index => $agreement)
                        <div style="display: flex; gap: 14px; padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid var(--glass-border); border-radius: 14px;">
                            <div style="width: 26px; height: 26px; border-radius: 8px; background: rgba(59,130,246,0.08); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--primary-blue); font-size: 12px; flex-shrink: 0; margin-top: 1px;">
                                {{ $index + 1 }}
                            </div>
                            <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6;">
                                {!! nl2br(e($agreement->content)) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px; background: rgba(255,255,255,0.5); border-radius: 16px; border: 1px dashed var(--glass-border); margin-bottom: 32px;">
                    <x-atoms.icon name="check-circle" style="width: 36px; height: 36px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.4;" />
                    <p style="font-size: 13.5px; color: var(--text-secondary); margin: 0;">Belum ada ketentuan KOL yang tersedia.</p>
                </div>
            @endif

        @endif

    @else

        <div style="margin-top: 28px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
            <x-atoms.icon name="check" style="width: 17px; height: 17px; color: var(--primary-blue);" />
            <x-atoms.typography variant="body" style="font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                Poin Perjanjian
            </x-atoms.typography>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 40px;">
            @forelse($general as $index => $agreement)
                <div style="display: flex; gap: 14px; padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid var(--glass-border); border-radius: 14px;">
                    <div style="width: 26px; height: 26px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--text-primary); font-size: 12px; flex-shrink: 0; margin-top: 1px;">
                        {{ $index + 1 }}
                    </div>
                    <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6;">
                        {!! nl2br(e($agreement->content)) !!}
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px 20px; background: rgba(255,255,255,0.5); border-radius: 16px; border: 1px dashed var(--glass-border);">
                    <x-atoms.icon name="check-circle" style="width: 36px; height: 36px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.4;" />
                    <p style="font-size: 13.5px; color: var(--text-secondary); margin: 0;">Belum ada data perjanjian yang aktif saat ini.</p>
                </div>
            @endforelse
        </div>
    @endif

    <div style="text-align: center; margin-bottom: 40px; padding-top: 8px; border-top: 1px dashed rgba(0,0,0,0.07);">
        <x-atoms.typography variant="body" style="font-size: 11.5px; color: var(--text-tertiary); display: block; line-height: 1.7;">
            Dokumen ini bersumber dari data mutakhir perusahaan.<br>
            PT Eleanor Project Global Indonesia.
        </x-atoms.typography>
    </div>

</x-organisms.mobile-page-wrapper>
@endsection