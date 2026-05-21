<x-molecules.card title="Manajemen Kontrak KOL">
    <x-slot name="headerAction">
        <x-molecules.dropdown>
            <x-slot:trigger>
                <x-atoms.button variant="secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); font-weight: 500;">
                    <x-atoms.icon name="journal" style="width: 16px; height: 16px; margin-right: 6px;" />
                    {{ $selectedMonthLabel }} 
                </x-atoms.button>
            </x-slot:trigger>
            @foreach($availableMonths as $m)
                <x-atoms.dropdown-item href="?tab={{ $currentTab }}&selected_month={{ $m['value'] }}">{{ $m['label'] }}</x-atoms.dropdown-item>
            @endforeach
        </x-molecules.dropdown>
    </x-slot>

    <div class="tab-content">
        <x-organisms.datatables id="kolTable" url="{{ route('admin-dashboard.users.kol-contract-data', request()->query()) }}" 
            :columns="[
                ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
                ['data' => 'username','title' => 'Nama KOL'],
                ['data' => 'action', 'title' => 'Aksi']
                ]" />
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="offcanvasDetailKOL" title="Detail Kontrak KOL">
    <div style="padding: 24px; padding-top: 0;">
        <div style="margin-bottom: 24px;"><x-atoms.badge id="kol-badge" status="paid">Aktif</x-atoms.badge></div>
        
        <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px;">Informasi Kontrak</x-atoms.typography>
        <div id="det-contracts-wrapper" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;"></div>

    </div>
</x-organisms.offcanvas>

{{-- Offcanvas Perpanjang --}}
<x-organisms.offcanvas id="offcanvasExtendKOL" title="Perpanjang Kontrak KOL">
    <form action="{{ route('admin-dashboard.users.extend-kol-contract') }}" method="POST" style="padding: 24px; padding-top: 0;">
        @csrf
        <input type="hidden" name="original_contract_id" id="ext-orig-id">
        
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Biaya Kontrak Baru (Rp)" />
            <x-atoms.input type="number" name="contract_fee" id="ext-fee" required />
        </div>
        <x-organisms.grid-layout columns="1fr 1fr" gap="16px" marginBottom="16px">
            <div><x-atoms.label value="Tgl Mulai Baru" /><x-atoms.input type="date" name="start_date" required /></div>
            <div><x-atoms.label value="Tgl Selesai Baru" /><x-atoms.input type="date" name="end_date" required /></div>
        </x-organisms.grid-layout>
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Target Video" />
            <x-atoms.input type="number" name="required_video_count" id="ext-video" required />
        </div>
        <x-atoms.button type="submit" variant="primary" style="width: 100%;">Simpan Kontrak Baru</x-atoms.button>
    </form>
</x-organisms.offcanvas>

<x-organisms.offcanvas id="offcanvasEditKOL" title="Edit Kontrak KOL">
    <form id="editKolContractForm" action="{{ route('admin-dashboard.users.kol-contract.update') }}" method="POST" style="padding: 24px; padding-top: 0;">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit-contract-id">
        
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Biaya Kontrak (Rp)" />
            <x-atoms.input type="number" name="contract_fee" id="edit-contract-fee" required />
        </div>
        <x-organisms.grid-layout columns="1fr 1fr" gap="16px" marginBottom="16px">
            <div><x-atoms.label value="Tgl Mulai" /><x-atoms.input type="date" name="start_date" id="edit-start-date" required /></div>
            <div><x-atoms.label value="Tgl Selesai" /><x-atoms.input type="date" name="end_date" id="edit-end-date" required /></div>
        </x-organisms.grid-layout>
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Target Video" />
            <x-atoms.input type="number" name="required_video_count" id="edit-required-video-count" required />
        </div>
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Status" />
            <select name="status" id="edit-status" class="form-control" required style="font-size: 13px;">
                <option value="ACTIVE">ACTIVE</option>
                <option value="EXPIRED">EXPIRED</option>
                <option value="CANCELLED">CANCELLED</option>
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <x-atoms.searchable-multi-select 
                id="edit-product-select"
                name="product_ids" 
                label="Produk Terkait"
                placeholder="-- Cari dan Pilih Produk --"
                :options="$products->map(fn($p) => [
                    'id' => $p->id, 
                    'text' => $p->name
                ])"
            />
        </div>

        
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Isi Perjanjian Kontrak (Agreement)" />
            <textarea name="agreement_content" id="edit-agreement-content" class="form-control" rows="5" placeholder="Tuliskan syarat dan ketentuan perjanjian kontrak..." required style="height: auto; resize: vertical; padding: 12px; width: 100%; border-radius: 8px; border: 1px solid var(--glass-border); background: white; color: var(--text-primary); font-family: inherit; font-size: 13px; box-sizing: border-box; margin-top: 4px;"></textarea>
        </div>

        <div style="margin-top: 24px; border-top: 1px solid var(--glass-border); padding-top: 16px;">
            <x-atoms.button type="submit" variant="primary" style="width: 100%;">Simpan Perubahan</x-atoms.button>
        </div>
    </form>
</x-organisms.offcanvas>

<x-organisms.modal id="modalDeleteKOL" title="Hapus Kontrak KOL">
    <form id="deleteKolContractForm" action="{{ route('admin-dashboard.users.kol-contract.destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" id="delete-contract-id">
        
        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">
            Apakah Anda yakin ingin menghapus kontrak KOL ini?<br>
            <strong style="color: #ef4444;">Peringatan:</strong> Data yang terhapus tidak dapat dikembalikan.
        </p>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('modalDeleteKOL')">Batal</x-atoms.button>
            <x-atoms.button variant="primary" type="submit" form="deleteKolContractForm" style="background-color: #ef4444; border-color: #ef4444;">Ya, Hapus</x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

@push('scripts')
<script>
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

    function openOffcanvas(offcanvasId) {
        const offcanvas = document.getElementById(offcanvasId);
        const backdrop = document.getElementById(offcanvasId + '-backdrop');

        if (offcanvas) {
            offcanvas.classList.add('show');
            document.body.style.overflow = 'hidden'; 
            if (backdrop) backdrop.classList.add('show');
        } else {
            console.error("Offcanvas " + offcanvasId + " tidak ditemukan!");
        }
    }

    function toggleOffcanvas(offcanvasId) {
        const offcanvas = document.getElementById(offcanvasId);
        const backdrop = document.getElementById(offcanvasId + '-backdrop');
        
        if (offcanvas) offcanvas.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function openExtendForm(contractId, fee, reqVideo) {
        if (contractId) {
            $('#ext-orig-id').val(contractId);
            $('#ext-fee').val(Math.floor(fee));
            $('#ext-video').val(reqVideo);
        }
        toggleOffcanvas('offcanvasDetailKOL');
        setTimeout(() => {
            openOffcanvas('offcanvasExtendKOL');
        }, 350);
    }

    function openEditForm(id, fee, reqVideo, start, end, status, productIds, encodedAgreement) {
        $('#edit-contract-id').val(id);
        $('#edit-contract-fee').val(Math.floor(fee));
        $('#edit-required-video-count').val(reqVideo);
        $('#edit-start-date').val(start);
        $('#edit-end-date').val(end);
        $('#edit-status').val(status);
        
        const decodedAgreement = encodedAgreement ? decodeURIComponent(escape(atob(encodedAgreement))) : '';
        $('#edit-agreement-content').val(decodedAgreement);
        
        $('#edit-product-select-list input[type="checkbox"]').prop('checked', false);
        
        if (productIds && productIds.length > 0) {
            productIds.forEach(function(productId) {
                $(`#edit-product-select-opt-${productId}`).prop('checked', true);
            });
        }
        
        if (typeof updateMultiSelectText === 'function') {
            updateMultiSelectText('edit-product-select', '-- Cari dan Pilih Produk --');
        }
        
        toggleOffcanvas('offcanvasDetailKOL');
        setTimeout(() => {
            openOffcanvas('offcanvasEditKOL');
        }, 350);
    }

    function openDeleteForm(id) {
        $('#delete-contract-id').val(id);
        
        toggleOffcanvas('offcanvasDetailKOL');
        setTimeout(() => {
            openModal('modalDeleteKOL');
        }, 350);
    }

    function formatCurrency(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(val) || 0);
    }

    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            try {
                const contracts = JSON.parse($(this).attr('data-contracts') || '[]');
                const wrapper = $('#det-contracts-wrapper');
                wrapper.empty();

                if(contracts.length === 0) {
                    wrapper.append('<div style="font-size: 13px; color: var(--text-tertiary);">Belum ada riwayat kontrak.</div>');
                }

                contracts.forEach(function(c, index) {
                    const statusColor = c.status === 'ACTIVE' ? 'var(--emerald)' : (c.status === 'CANCELLED' ? '#ef4444' : 'var(--text-tertiary)');
                    const statusBg = c.status === 'ACTIVE' ? 'var(--emerald-soft)' : (c.status === 'CANCELLED' ? '#fef2f2' : 'rgba(0,0,0,0.05)');
                    const statusLabel = c.status === 'ACTIVE' ? 'Aktif' : (c.status === 'EXPIRED' ? 'Kedaluwarsa' : 'Dibatalkan');
                    
                    const productTags = c.products.length > 0
                        ? c.products.map(p =>
                            `<span style="display:inline-block; background: var(--primary-blue-soft); color: var(--primary-blue);
                                font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px; margin: 2px 2px 0 0;">
                                ${p.name}
                            </span>`
                        ).join('')
                        : '<span style="font-size:12px; color: var(--text-tertiary);">Tidak ada produk</span>';

                    const productIdsArr = JSON.stringify(c.products.map(p => p.id));
                    const encodedAgreement = btoa(unescape(encodeURIComponent(c.agreement_content || '')));

                    let actionsHtml = '';
                    
                    if (c.status === 'ACTIVE' || c.status === 'EXPIRED') {
                        actionsHtml += `
                            <button type="button" class="btn btn-primary btn-sm" style="flex: 1;"
                                onclick="openExtendForm('${c.id}', '${c.fee}', '${c.req_video}')">
                                Perpanjang
                            </button>
                        `;
                    }
                    
                    actionsHtml += `
                        <button type="button" class="btn btn-outline btn-sm" style="flex: 1; border-color: var(--glass-border); color: var(--text-primary);"
                            onclick='openEditForm("${c.id}", "${c.fee}", "${c.req_video}", "${c.start}", "${c.end}", "${c.status}", ${productIdsArr}, "${encodedAgreement}")'>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            Edit
                        </button>
                        <button type="button" class="btn btn-outline btn-sm" style="background: #fef2f2; color: #ef4444; border-color: #fca5a5;"
                            onclick="openDeleteForm('${c.id}')" title="Hapus">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    `;

                    wrapper.append(`
                        <div style="border: 1px solid var(--glass-border); border-radius: 10px; padding: 14px 16px;
                            background: rgba(255,255,255,0.2); position: relative;">

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-size: 12px; font-weight: 700; color: var(--text-secondary);">
                                    Kontrak #${index + 1}
                                </span>
                                <span style="font-size: 11px; font-weight: 700; color: ${statusColor};
                                    background: ${statusBg}; padding: 2px 10px; border-radius: 999px;">
                                    ${statusLabel}
                                </span>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                <div>
                                    <div style="font-size: 11px; color: var(--text-secondary);">Biaya Kontrak</div>
                                    <div style="font-weight: 700;">${formatCurrency(c.fee)}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: var(--text-secondary);">Target Video</div>
                                    <div style="font-weight: 700;">${c.req_video} Video</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: var(--text-secondary);">Mulai</div>
                                    <div style="font-weight: 700;">${c.start}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: var(--text-secondary);">Berakhir</div>
                                    <div style="font-weight: 700;">${c.end}</div>
                                </div>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 6px;">Produk Terkait</div>
                                <div>${productTags}</div>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 6px;">Isi Perjanjian (Agreement)</div>
                                <div style="font-size: 13px; color: var(--text-primary); background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--glass-border); white-space: pre-wrap; word-break: break-word;">${c.agreement_content ? c.agreement_content : '<span style="color: var(--text-tertiary); font-style: italic;">Tidak ada perjanjian yang dilampirkan.</span>'}</div>
                            </div>

                            <div style="padding-top: 12px; border-top: 1px solid var(--glass-border); display: flex; gap: 8px;">
                                ${actionsHtml}
                            </div>
                        </div>
                    `);
                });
                
                openOffcanvas('offcanvasDetailKOL');

            } catch (error) {
                console.error("Gagal memproses data detail KOL:", error);
            }
        });
    });
</script>
@endpush