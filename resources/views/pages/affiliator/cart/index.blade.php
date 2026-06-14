@extends('layouts.app')
@section('title', 'Keranjang')
@section('is_subpage', true)
@section('back_url', route('affiliator.catalog.index'))

@section('content')
<x-organisms.mobile-page-wrapper title="Keranjang Pengajuan Sampel" subtitle="Periksa kembali produk yang ingin Anda ajukan sebelum memproses checkout.">
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
                <form action="{{ route('affiliator.cart.checkout') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <x-atoms.label value="Alamat Lengkap Pengiriman Sampel" for="address" style="margin-bottom: 8px; display: block;" />
                        <textarea name="address" id="address" required placeholder="Tuliskan alamat lengkap pengiriman paket beserta nomor HP alternatif jika ada..." style="width: 100%; border-radius: 8px; border: 1px solid var(--glass-border); padding: 12px; font-size: 13px; background: white; min-height: 100px; box-sizing: border-box; color: var(--text-primary); font-family: inherit; resize: vertical;">{{ old('address') }}</textarea>
                        @error('address')
                            <div style="color: var(--rose, #f43f5e); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 20px;">
                        <x-atoms.label value="Screenshot Affiliate Center 7 Hari Terakhir" for="affiliate_center_screenshot" style="margin-bottom: 8px; display: block;" />
                        <p style="font-size: 12px; color: var(--text-secondary); margin: 0 0 10px 0; line-height: 1.5;">
                            Unggah tangkapan layar dari dashboard <strong>Affiliate Center</strong> yang menampilkan performa 7 hari terakhir Anda (grafik, statistik, atau ringkasan komisi). Format: JPG, PNG, atau WebP. Maks. 5MB.
                        </p>

                        <label for="affiliate_center_screenshot" id="screenshotDropZone" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; width: 100%; min-height: 120px; border: 2px dashed var(--glass-border); border-radius: 10px; background: #f8fafc; cursor: pointer; box-sizing: border-box; transition: border-color 0.2s, background 0.2s; padding: 20px;">
                            <div id="screenshotPlaceholder" style="display: flex; flex-direction: column; align-items: center; gap: 6px; pointer-events: none;">
                                <x-atoms.icon name="upload" style="width: 32px; height: 32px; color: var(--text-secondary); opacity: 0.6;" />
                                <span style="font-size: 13px; color: var(--text-secondary);">Klik atau seret gambar ke sini</span>
                                <span style="font-size: 11px; color: var(--text-tertiary);">JPG · PNG · WebP · GIF — maks. 5 MB</span>
                            </div>
                            <img id="screenshotPreview" src="#" alt="Preview screenshot" style="display: none; max-width: 100%; max-height: 220px; border-radius: 8px; object-fit: contain;" />
                        </label>

                        <input type="file"
                               name="affiliate_center_screenshot"
                               id="affiliate_center_screenshot"
                               accept="image/jpeg,image/png,image/webp,image/gif"
                               required
                               style="display: none;"
                               onchange="previewScreenshot(this)">

                        @error('affiliate_center_screenshot')
                            <div style="color: var(--rose, #f43f5e); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($personalAgreements->count() || $generalAgreements->count())
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; padding: 16px; background: #fffbeb; border-radius: 8px; border: 1px solid #fef3c7;">
                            <x-atoms.typography variant="body" style="font-weight: 700; color: #b45309; margin: 0;">Syarat & Ketentuan Pengajuan:</x-atoms.typography>

                            @if($personalAgreements->count())
                                @if($generalAgreements->count())
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #92400e; letter-spacing: 0.4px;">Ketentuan Khusus Anda</div>
                                @endif
                                @foreach($personalAgreements as $agr)
                                    <div style="font-size: 13px; color: #78350f; line-height: 1.5; padding-left: 8px;">
                                        {!! nl2br(e($agr->content)) !!}
                                    </div>
                                @endforeach
                            @endif

                            @if($generalAgreements->count())
                                @if($personalAgreements->count())
                                    <div style="border-top: 1px dashed #fde68a; padding-top: 10px; margin-top: 2px;">
                                        <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #92400e; letter-spacing: 0.4px; margin-bottom: 10px;">Ketentuan Umum</div>
                                    </div>
                                @endif
                                @foreach($generalAgreements as $agr)
                                    <div style="font-size: 13px; color: #78350f; line-height: 1.5;">
                                        {!! nl2br(e($agr->content)) !!}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    <div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 24px;">
                        <input type="checkbox" id="agree_terms" onchange="document.getElementById('btnSubmitCheckout').disabled = !this.checked;" style="width: 18px; height: 18px; margin-top: 2px; cursor: pointer;">
                        <label for="agree_terms" style="font-size: 13px; color: var(--text-secondary); cursor: pointer; user-select: none; line-height: 1.4;">
                            Saya menyatakan telah membaca, memahami, dan menyetujui seluruh syarat & ketentuan pengajuan sampel yang berlaku di atas.
                        </label>
                    </div>

                    <x-atoms.button type="submit" id="btnSubmitCheckout" disabled variant="primary" style="width:100%; padding: 12px 32px; font-size: 14px; font-weight: 600;">
                        Kirim Pengajuan Sampel Gratis
                    </x-atoms.button>
                </form>
            </div>
        @endif
    </x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
<script>
function previewScreenshot(input) {
    const preview    = document.getElementById('screenshotPreview');
    const placeholder = document.getElementById('screenshotPlaceholder');
    const dropZone   = document.getElementById('screenshotDropZone');

    if (input.files && input.files[0]) {
        const file   = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            dropZone.style.borderColor = 'var(--primary-blue, #3b82f6)';
            dropZone.style.background  = '#eff6ff';
        };

        reader.readAsDataURL(file);
    } else {
        preview.style.display    = 'none';
        placeholder.style.display = 'flex';
        dropZone.style.borderColor = 'var(--glass-border)';
        dropZone.style.background  = '#f8fafc';
    }
}

// Drag-and-drop support
(function () {
    const dropZone = document.getElementById('screenshotDropZone');
    const fileInput = document.getElementById('affiliate_center_screenshot');

    if (!dropZone || !fileInput) return;

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.borderColor = 'var(--primary-blue, #3b82f6)';
        dropZone.style.background  = '#eff6ff';
    });

    dropZone.addEventListener('dragleave', function () {
        if (!fileInput.files || !fileInput.files[0]) {
            dropZone.style.borderColor = 'var(--glass-border)';
            dropZone.style.background  = '#f8fafc';
        }
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            previewScreenshot(fileInput);
        }
    });
})();
</script>
@endpush