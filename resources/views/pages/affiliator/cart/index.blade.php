@extends('layouts.app')
@section('title', 'Keranjang')
@section('is_subpage', true)

@section('content')
<x-organisms.mobile-page-wrapper title="Keranjang Sampel" subtitle="Tinjau produk sebelum melanjutkan pengajuan.">

    <div class="cart-items-container" style="margin-top: 24px;">
        @forelse($cartItems ?? [] as $item)
            <div class="cart-item">
                <div class="cart-item-img">
                    @if(!empty($item['image_path']))
                        <img src="{{ filter_var($item['image_path'], FILTER_VALIDATE_URL) ? $item['image_path'] : asset('storage/' . $item['image_path']) }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.8">
                            <rect x="2" y="2" width="20" height="20"></rect>
                            <path d="M2 2l20 20M2 22L22 2"></path>
                        </svg>
                    @endif
                </div>
                
                <div class="cart-item-info">
                    <div class="cart-item-title">{{ $item['name'] }}</div>
                    <div class="cart-item-meta">Wajib: {{ $item['mandatory_video_count'] ?? 1 }} Video</div>
                    <div class="cart-item-meta" style="font-weight: 800; color: var(--text-primary);">Qty: {{ $item['quantity'] ?? 1 }}</div>
                </div>

                <form action="{{ route('affiliator.cart.destroy', $item['id']) }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="cart-item-remove" aria-label="Hapus Item">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @empty
            <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                <x-atoms.icon name="cart" style="width: 48px; height: 48px; color: var(--text-tertiary); margin-bottom: 12px;"/>
                <p>Keranjang Anda masih kosong.</p>
                <x-atoms.button href="{{ route('affiliator.catalog.index') }}" variant="outline" style="margin-top: 16px;">Jelajahi Katalog</x-atoms.button>
            </div>
        @endforelse
    </div>

    @if(count($cartItems ?? []) > 0)
        <div class="cart-divider"></div>

        <div class="cart-summary">
            <div class="cart-summary-title">Total Produk: {{ count($cartItems) }}</div>
            @php
                $totalVideos = collect($cartItems)->sum(fn($item) => $item['mandatory_video_count'] ?? 1);
            @endphp
            <div class="cart-summary-subtitle">Total Kewajiban Video: {{ $totalVideos }} Video</div>
        </div>

        <div style="height: 120px;"></div>
        <div>
            <x-atoms.button type="button" onclick="openModal('agreementModal')"  type="submit" variant="primary" style="width: 100%; display: flex; align-items: center; justify-content: center;" >
                <x-atoms.icon name="cart" style="width: 18px; height: 18px; margin-right: 8px;"/>
                Checkout & Ajukan Sampel
            </x-atoms.button>
        </div>
    @endif

</x-organisms.mobile-page-wrapper>
<x-organisms.modal id="agreementModal" title="Persetujuan Kerja Sama">
    <form action="{{ route('affiliator.cart.checkout') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label">Alamat Pengiriman</label>
            <x-atoms.input type="text" name="address" placeholder="Tulis alamat pengiriman lengkap..." required />
        </div>

        <p style="font-size: 13px; color: var(--text-secondary);">Baca dan centang seluruh poin di bawah ini.</p>

        <div class="agreement-list">
            @foreach($agreements as $index => $agree)
                <label class="agreement-item">
                    <input type="checkbox" class="agreement-checkbox agreement-tick" onchange="checkAgreements()">
                    <div class="agreement-content">
                        {!! $agree->content !!}
                    </div>
                </label>
            @endforeach
        </div>

        <div class="agreement-instruction">
            Anda harus menyetujui seluruh poin (mencentang kotak) untuk dapat melanjutkan proses pengajuan.
        </div>

        <x-slot name="footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('agreementModal')">Batal</button>
            <x-atoms.button type="submit" id="btnSubmitCheckout" variant="primary" form="checkoutForm" disabled style="opacity: 0.5;">
                Saya Mengerti & Ajukan
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if(modal) modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if(modal) modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function checkAgreements() {
        const checkboxes = document.querySelectorAll('.agreement-tick');
        const submitBtn = document.getElementById('btnSubmitCheckout');
        
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        if (allChecked) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = "1";
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.5";
        }
    }
</script>
@endpush