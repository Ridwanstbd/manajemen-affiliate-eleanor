@extends('layouts.app')

@section('title', 'Detail Kontrak')
@section('is_subpage', true)
@section('back_url', route('affiliator.contract-kol.index'))

@section('content')
@php
    $isActive  = $contract->status === 'ACTIVE';
    $isExpired = $contract->end_date->isPast();
    $badgeStatus = $isExpired ? 'overdue' : ($isActive ? 'paid' : 'pending');
    $badgeLabel  = $isExpired ? 'Kedaluwarsa' : ($isActive ? 'Aktif' : ucfirst(strtolower($contract->status)));
    $daysLeft    = (int) now()->diffInDays($contract->end_date, false);
    $cartItems   = session('affiliate_cart', []);
    $cartIds     = collect($cartItems)->pluck('id')->toArray();
@endphp

<x-organisms.mobile-page-wrapper>
    <x-molecules.card style="border: 1px solid var(--glass-border); border-radius: 20px; padding: 24px; margin-bottom: 20px; background: linear-gradient(135deg, rgba(59,130,246,0.07), rgba(255,255,255,0.8));">

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, var(--primary-blue), #60a5fa); display: flex; align-items: center; justify-content: center;">
                    <x-atoms.icon name="invoices" style="width: 24px; height: 24px; color: #ffffff;" />
                </div>
                <div>
                    <x-atoms.typography variant="card-title" style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0;">
                        Kontrak #{{ $contract->id }}
                    </x-atoms.typography>
                    <span style="font-size: 11px; color: var(--text-tertiary);">
                        Dibuat {{ $contract->created_at->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <x-atoms.badge :status="$badgeStatus">{{ $badgeLabel }}</x-atoms.badge>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div style="background: rgba(255,255,255,0.7); border-radius: 12px; padding: 12px 14px; border: 1px solid rgba(0,0,0,0.04);">
                <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 3px; letter-spacing: 0.5px;">Tanggal Mulai</span>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <x-atoms.icon name="clock" style="width: 14px; height: 14px; color: var(--text-secondary);" />
                    <span style="font-size: 13px; font-weight: 700; color: var(--text-primary);">{{ $contract->start_date->translatedFormat('d M Y') }}</span>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.7); border-radius: 12px; padding: 12px 14px; border: 1px solid rgba(0,0,0,0.04);">
                <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 3px; letter-spacing: 0.5px;">Tanggal Selesai</span>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <x-atoms.icon name="clock" style="width: 14px; height: 14px; color: {{ $isExpired ? '#ef4444' : 'var(--text-secondary)' }};" />
                    <span style="font-size: 13px; font-weight: 700; color: {{ $isExpired ? '#ef4444' : 'var(--text-primary)' }};">{{ $contract->end_date->translatedFormat('d M Y') }}</span>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.7); border-radius: 12px; padding: 12px 14px; border: 1px solid rgba(0,0,0,0.04);">
                <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 3px; letter-spacing: 0.5px;">Fee Kontrak</span>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <x-atoms.icon name="revenue" style="width: 14px; height: 14px; color: var(--primary-blue);" />
                    <span style="font-size: 13px; font-weight: 700; color: var(--primary-blue);">Rp {{ number_format($contract->contract_fee, 0, ',', '.') }}</span>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.7); border-radius: 12px; padding: 12px 14px; border: 1px solid rgba(0,0,0,0.04);">
                <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 3px; letter-spacing: 0.5px;">Target Video</span>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <x-atoms.icon name="video" style="width: 14px; height: 14px; color: var(--text-secondary);" />
                    <span style="font-size: 13px; font-weight: 700; color: var(--text-primary);">{{ $contract->required_video_count }} video</span>
                </div>
            </div>
        </div>

        @if(!$isExpired)
            <div style="margin-top: 14px; padding: 10px 14px; background: rgba(59,130,246,0.08); border-radius: 10px; border: 1px solid rgba(59,130,246,0.15); display: flex; align-items: center; gap: 8px;">
                <x-atoms.icon name="clock" style="width: 15px; height: 15px; color: var(--primary-blue); flex-shrink: 0;" />
                <span style="font-size: 12.5px; color: var(--primary-blue); font-weight: 600;">
                    {{ $daysLeft > 0 ? 'Sisa ' . $daysLeft . ' hari lagi' : 'Berakhir hari ini' }}
                </span>
            </div>
        @endif

        @if($contract->notes)
            <div style="margin-top: 14px; padding: 12px 14px; background: rgba(241,245,249,0.7); border-radius: 10px; border-left: 3px solid var(--primary-blue);">
                <span style="font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 4px;">Catatan</span>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0; line-height: 1.5;">{{ $contract->notes }}</p>
            </div>
        @endif

    </x-molecules.card>

    <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <x-atoms.icon name="vendors" style="width: 18px; height: 18px; color: var(--primary-blue);" />
        <x-atoms.typography variant="card-title" as="h3" style="font-size: 16px; font-weight: 700; margin: 0;">
            Produk Terhubung
        </x-atoms.typography>
        <span style="background: var(--primary-blue); color: #fff; font-size: 11px; font-weight: 700; border-radius: 20px; padding: 1px 8px; margin-left: 2px;">
            {{ $contract->products->count() }}
        </span>
    </div>

    @forelse($contract->products as $product)
        @php
            $imgSrc = $product->image_path
                ? (filter_var($product->image_path, FILTER_VALIDATE_URL)
                    ? $product->image_path
                    : asset('storage/' . $product->image_path))
                : asset('img/default-product.png');
            $inCart = in_array($product->id, $cartIds);
        @endphp

        <x-molecules.card style="border: 1px solid var(--glass-border); border-radius: 16px; padding: 16px; margin-bottom: 14px; background: rgba(255,255,255,0.7);">
            <div style="display: flex; gap: 14px; align-items: flex-start;">

                <div style="width: 72px; height: 72px; border-radius: 12px; overflow: hidden; flex-shrink: 0; background: #f1f5f9; border: 1px solid var(--glass-border);">
                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}"
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.src='{{ asset('img/default-product.png') }}'">
                </div>

                <div style="flex: 1; min-width: 0;">
                    <span style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: var(--text-tertiary); letter-spacing: 0.5px;">
                        {{ $product->category ?? '-' }}
                    </span>
                    <x-atoms.typography variant="card-title" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 2px 0 6px; line-height: 1.3; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $product->name }}
                    </x-atoms.typography>

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
                        <span style="font-size: 14px; font-weight: 800; color: var(--primary-blue);">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>

                        @if($product->sku_id)
                            <span style="font-size: 10px; color: var(--text-tertiary); background: rgba(0,0,0,0.04); border-radius: 6px; padding: 2px 7px;">
                                SKU: {{ $product->sku_id }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div style="margin-top: 14px; border-top: 1px solid var(--glass-border); padding-top: 12px;">
                @if($inCart)
                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: rgba(34,197,94,0.1); border-radius: 10px; border: 1px solid rgba(34,197,94,0.2);">
                        <x-atoms.icon name="check-circle" style="width: 16px; height: 16px; color: #16a34a;" />
                        <span style="font-size: 13px; font-weight: 600; color: #16a34a;">Sudah di Keranjang</span>
                    </div>
                @else
                    <form action="{{ route('affiliator.cart.store', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect_back" value="{{ url()->current() }}">
                        <x-atoms.button type="submit" variant="primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; font-size: 13.5px; font-weight: 600; border-radius: 10px;">
                            <x-atoms.icon name="cart" style="width: 16px; height: 16px;" />
                            Minta Sample
                        </x-atoms.button>
                    </form>
                @endif
            </div>

        </x-molecules.card>
    @empty
        <div style="text-align: center; padding: 40px 24px; background: rgba(255,255,255,0.5); border-radius: 16px; border: 1px dashed var(--glass-border); margin-bottom: 40px;">
            <x-atoms.icon name="vendors" style="width: 36px; height: 36px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.4;" />
            <x-atoms.typography variant="card-title" as="h3" style="font-size: 14px; color: var(--text-secondary); margin-bottom: 4px;">
                Belum Ada Produk
            </x-atoms.typography>
            <p style="font-size: 13px; color: var(--text-tertiary); margin: 0;">
                Admin belum menambahkan produk ke kontrak ini.
            </p>
        </div>
    @endforelse

    @php $cartCount = count(session('affiliate_cart', [])); @endphp
    @if($cartCount > 0)
        <div style="position: fixed; bottom: 88px; left: 50%; transform: translateX(-50%); z-index: 100; width: calc(100% - 48px); max-width: 420px;">
            <a href="{{ route('affiliator.cart.index') }}"
               style="display: flex; align-items: center; justify-content: space-between; background: var(--primary-blue); color: #fff; text-decoration: none; padding: 14px 20px; border-radius: 16px; box-shadow: 0 8px 24px rgba(59,130,246,0.35); font-weight: 700; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <x-atoms.icon name="cart" style="width: 20px; height: 20px;" />
                    Lihat Keranjang
                </div>
                <span style="background: rgba(255,255,255,0.25); border-radius: 20px; padding: 2px 10px; font-size: 13px; font-weight: 800;">
                    {{ $cartCount }} item
                </span>
            </a>
        </div>
        <div style="height: 72px;"></div>
    @endif

    <div style="height: 24px;"></div>

</x-organisms.mobile-page-wrapper>
@endsection