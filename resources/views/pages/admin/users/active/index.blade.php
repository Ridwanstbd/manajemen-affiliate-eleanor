<x-molecules.card title="Affiliator Aktif">
        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @php
                $tableColumns = [
                    ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
                    ['data' => 'username', 'name' => 'username', 'title' => 'Nama Kreator', 'width' => '320px'],
                    ['data' => 'phone_number', 'name' => 'phone_number', 'title' => 'Kontak'],
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
                ];
            @endphp

            <x-organisms.datatables 
                id="requestTable" 
                url="{{ route('admin-dashboard.users.active-data', request()->query()) }}" 
                :columns="$tableColumns" 
            />
        </div>
</x-molecules.card>
<x-organisms.offcanvas id="detailUserOffcanvas" title="Detail Performa Affiliator">
    <div style="padding: 24px; padding-top: 0;">
        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 15px;">Profil Pengguna</x-atoms.typography>
            <div id="off-username" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"></div>
            <div id="off-email" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;"></div>
            <div id="off-phone" style="font-size: 13px; color: var(--text-secondary);"></div>
        </div>

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 4px; font-size: 15px;">
                Metrik Utama
            </x-atoms.typography>
            
            <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary); margin-bottom: 16px;">
                Akumulasi performa berdasarkan data impor analitik terakhir.
            </x-atoms.typography>
            
            <x-organisms.grid-layout columns="1fr 1fr" gap="12px" marginBottom="0">
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Total GMV Afiliasi</div>
                    <div id="off-gmv" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Barang Terjual</div>
                    <div id="off-items" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Estimasi Komisi</div>
                    <div id="off-commission" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Sampel Diterima</div>
                    <div id="off-samples" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
            </x-organisms.grid-layout>
        </div>

        <div style="margin-bottom: 32px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 15px;">Rincian Operasional & Kualitas</x-atoms.typography>
            
            <x-organisms.grid-layout columns="1fr 1fr" gap="20px" marginBottom="16px">
                <div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Rata-rata Nilai Pesanan (AOV)</div>
                    <div id="off-aov" style="font-size: 14px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Total Pengembalian (Refunds)</div>
                    <div id="off-refunds" style="font-size: 14px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
            </x-organisms.grid-layout>
            
            <x-organisms.grid-layout columns="1fr 1fr" gap="20px" marginBottom="0">
                <div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Barang Diretur (Items Returned)</div>
                    <div id="off-returned" style="font-size: 14px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Aktivitas Konten (Video / Live)</div>
                    <div id="off-content" style="font-size: 14px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
            </x-organisms.grid-layout>
        </div>
        
        <div style="display: flex; gap: 12px; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <div style="flex: 1;">
                <input type="hidden" id="off-id-blacklist-hidden">
                <x-atoms.button type="button" variant="secondary" style="width: 100%; border-color: var(--rose); color: var(--rose); background: transparent;" onclick="openBlacklistModal()">
                    Masukkan Daftar Hitam
                </x-atoms.button>
            </div>

            <div style="flex: 1;">
                <input type="hidden" id="off-id-kol-hidden"> 
                <x-atoms.button type="button" variant="primary" style="width: 100%; " onclick="openCreateContractForm()">
                    Kontrak KOL
                </x-atoms.button>
            </div>
        </div>
    </div>
</x-organisms.offcanvas>

<x-organisms.offcanvas id="createKOLContractOffcanvas" title="Buat Kontrak KOL Baru">
    <form action="{{ route('admin-dashboard.users.store-kol-contract') }}" method="POST" style="padding: 24px; padding-top: 0;">
        @csrf
        <input type="hidden" name="user_id" id="create-contract-user-id">

        <div style="margin-bottom: 20px;">
            <x-atoms.searchable-multi-select 
                name="product_ids" 
                label="Pilih Produk untuk Kontrak"
                placeholder="-- Cari dan Pilih Produk --"
                :options="\App\Models\Product::all()->map(fn($product) => [
                    'id' => $product->id, 
                    'text' => $product->name
                ])"
            />
        </div>

        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Biaya Kontrak (Rp)" />
            <x-atoms.input type="number" name="contract_fee" placeholder="Contoh: 5000000" required />
        </div>

        <x-organisms.grid-layout columns="1fr 1fr" gap="16px" marginBottom="16px">
            <div>
                <x-atoms.label value="Tanggal Mulai" />
                <x-atoms.input type="date" name="start_date" required />
            </div>
            <div>
                <x-atoms.label value="Tanggal Selesai" />
                <x-atoms.input type="date" name="end_date" required />
            </div>
        </x-organisms.grid-layout>

        <div style="margin-bottom: 24px;">
            <x-atoms.label value="Target Wajib Video" />
            <x-atoms.input type="number" name="required_video_count" placeholder="Contoh: 5" required />
        </div>

        <x-atoms.button type="submit" variant="primary" style="width: 100%; background: var(--primary-blue);">
            Simpan Kontrak & Jadikan KOL
        </x-atoms.button>
    </form>
</x-organisms.offcanvas>

<x-organisms.modal id="blacklistModal" title="Masukkan Daftar Hitam" description="Tindakan ini akan memblokir akses pengguna dan membatalkan aktivitas afiliasinya.">
    <form id="formBlacklist" action="{{ route('admin-dashboard.users.store-blacklist') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" id="modal-blacklist-user-id">
        
        <div class="form-group" style="margin-bottom: 24px;">
            <x-atoms.label for="violation_reason" value="Alasan Pemblokiran" />
            <textarea name="violation_reason" id="violation_reason" class="form-control" rows="4" placeholder="Jelaskan secara spesifik alasan pelanggaran..." required style="height: auto; resize: vertical; padding: 12px;"></textarea>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('blacklistModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit" style="background: var(--rose); border: none;" form="formBlacklist">
                Konfirmasi Blokir
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

@push('scripts')
<script>
    function openOffcanvas(offcanvasId) {
        const offcanvas = document.getElementById(offcanvasId);
        const backdrop = document.getElementById(offcanvasId + '-backdrop');
        if (offcanvas) {
            offcanvas.classList.add('show');
            document.body.style.overflow = 'hidden';
            if(backdrop) backdrop.classList.add('show');
        }
    }

    function toggleOffcanvas(offcanvasId) {
        const offcanvas = document.getElementById(offcanvasId);
        const backdrop = document.getElementById(offcanvasId + '-backdrop');
        if (offcanvas) offcanvas.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function openCreateContractForm() {
        const userId = document.getElementById('off-id-kol-hidden').value;
        
        toggleOffcanvas('detailUserOffcanvas');
        
        setTimeout(() => {
            document.getElementById('create-contract-user-id').value = userId;
            openOffcanvas('createKOLContractOffcanvas');
        }, 350);
    }

    function formatCurrencyShort(val) {
        let num = parseFloat(val) || 0;
        if (num >= 1000000000) return 'Rp ' + (num / 1000000000).toFixed(1) + 'B';
        if (num >= 1000000) return 'Rp ' + (num / 1000000).toFixed(1) + 'M';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
    }

    function formatCurrency(val) {
        let num = parseFloat(val) || 0;
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
    }

    function formatNumber(val) {
        let num = parseInt(val) || 0;
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function openBlacklistModal() {
        const userId = document.getElementById('off-id-blacklist-hidden').value;
        
        toggleOffcanvas('detailUserOffcanvas');
        
        document.getElementById('modal-blacklist-user-id').value = userId;
        
        setTimeout(() => {
            openModal('blacklistModal');
        }, 300);
    }

    function filterProducts() {
        const searchValue = document.getElementById('search-product-input').value.toLowerCase();
        
        const items = document.querySelectorAll('.product-item');
        let hasVisibleProduct = false;

        items.forEach(item => {
            const labelText = item.querySelector('.product-label').innerText.toLowerCase();
            
            if (labelText.includes(searchValue)) {
                item.style.display = 'flex';
                hasVisibleProduct = true;
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById('no-product-msg').style.display = hasVisibleProduct ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('#requestTable').on('click', '.btn-detail', function(e) {
            e.preventDefault();
            const d = $(this).data();

            $('#off-username').text(d.username.startsWith('@') ? d.username : '@' + d.username);
            $('#off-email').text(d.email);
            $('#off-phone').text(d.phone);
            $('#off-month').text(d.month);

            $('#off-gmv').text(formatCurrencyShort(d.gmv));
            $('#off-items').text(formatNumber(d.items) + ' Item');
            $('#off-commission').text(formatCurrencyShort(d.commission));
            $('#off-samples').text(formatNumber(d.samples) + ' Produk');
            $('#off-aov').text(formatCurrency(d.aov));
            $('#off-refunds').text(formatCurrency(d.refunds));
            $('#off-returned').text(formatNumber(d.returned) + ' Item');
            $('#off-content').text(formatNumber(d.videos) + ' / ' + formatNumber(d.lives));

            $('#off-id-blacklist').val(d.id);
            $('#off-id-blacklist-hidden').val(d.id);
            $('#off-id-kol-hidden').val(d.id); 

            openOffcanvas('detailUserOffcanvas');
        });
    });
</script>
@endpush