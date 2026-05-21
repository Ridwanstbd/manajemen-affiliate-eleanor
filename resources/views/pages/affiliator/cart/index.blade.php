@extends('layouts.app')
@section('title', 'Keranjang')
@section('is_subpage', true)
@section('back_url', route('affiliator.catalog.index'))

@section('content')
<x-molecules.card title="Keranjang Pengajuan Sampel" description="Periksa kembali produk yang ingin Anda ajukan sebelum memproses checkout.">
        @if(empty($cartItems))
            <div style="text-align: center; padding: 48px 0; color: var(--text-tertiary);">
                <x-atoms.icon name="cart" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;" />
                <x-atoms.typography variant="body" style="font-size: 14px;">Keranjang pengajuan Anda masih kosong.</x-atoms.typography>
                <a href="{{ route('affiliator.catalog.index') }}" style="text-decoration: none; display: inline-block; margin-top: 16px;">
                    <x-atoms.button variant="primary">Lihat Katalog</x-atoms.button>
                </a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                @foreach($cartItems as $item)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; box-shadow: var(--glass-shadow);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 64px; height: 64px; border-radius: 8px; overflow: hidden; background: #f1f5f9; border: 1px solid var(--glass-border); flex-shrink: 0;">
                                <img src="{{ $item['image_path'] ? (Str::startsWith($item['image_path'], ['http://', 'https://']) ? $item['image_path'] : asset('storage/' . $item['image_path'])) : '' }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">{{ $item['category'] }}</span>
                                <x-atoms.typography variant="body" style="font-weight: 600; color: var(--text-primary); margin: 0;">
                                    {{ $item['name'] }}
                                </x-atoms.typography>
                                <div style="font-size: 13px; color: var(--primary-blue); font-weight: 600; margin-top: 2px;">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <form action="{{ route('affiliator.cart.destroy', $item['id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-atoms.button type="submit" variant="outline" style="border-color: var(--rose); color: var(--rose); padding: 8px;">
                                    <x-atoms.icon name="trash" style="width: 16px; height: 16px;" />
                                </x-atoms.button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="border-top: 1px solid var(--glass-border); padding-top: 24px; max-width: 600px;">
                <x-atoms.typography variant="h3" style="font-weight: 700; color: var(--text-primary); margin-bottom: 16px;">Konfirmasi Pengiriman</x-atoms.typography>
                <form action="{{ route('affiliator.cart.checkout') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <x-atoms.label value="Alamat Lengkap Pengiriman Sampel" for="address" style="margin-bottom: 8px; display: block;" />
                        <textarea name="address" id="address" required placeholder="Tuliskan alamat lengkap pengiriman paket beserta nomor HP alternatif jika ada..." style="width: 100%; border-radius: 8px; border: 1px solid var(--glass-border); padding: 12px; font-size: 13px; background: white; min-height: 100px; box-sizing: border-box; color: var(--text-primary); font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    @if($agreement)
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; padding: 16px; background: #fffbeb; border-radius: 8px; border: 1px solid #fef3c7;">
                            <x-atoms.typography variant="body" style="font-weight: 700; color: #b45309; margin: 0;">Syarat & Ketentuan Pengajuan:</x-atoms.typography>
                            <div style="font-size: 13px; color: #78350f; line-height: 1.5;">
                                {!! nl2br(e($agreement->content)) !!}
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 24px;">
                        <input type="checkbox" id="agree_terms" onchange="document.getElementById('btnSubmitCheckout').disabled = !this.checked;" style="width: 18px; height: 18px; margin-top: 2px; cursor: pointer;">
                        <label for="agree_terms" style="font-size: 13px; color: var(--text-secondary); cursor: pointer; user-select: none; line-height: 1.4;">
                            Saya menyatakan telah membaca, memahami, dan menyetujui seluruh syarat & ketentuan pengajuan sampel yang berlaku di atas.
                        </label>
                    </div>

                    <x-atoms.button type="submit" id="btnSubmitCheckout" disabled variant="primary" style="padding: 12px 32px; font-size: 14px; font-weight: 600;">
                        Kirim Pengajuan Sampel Gratis
                    </x-atoms.button>
                </form>
            </div>
        @endif
    </x-molecules.card>
@endsection
