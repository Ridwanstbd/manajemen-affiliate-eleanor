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
            :columns="[['data' => 'username', 'title' => 'Nama KOL'], ['data' => 'action', 'title' => 'Aksi']]" />
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="offcanvasDetailKOL" title="Detail Kontrak KOL">
    <div style="padding: 24px; padding-top: 0;">
        <div style="margin-bottom: 24px;"><x-atoms.badge id="kol-badge" status="paid">Aktif</x-atoms.badge></div>
        
        <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px;">Informasi Kontrak</x-atoms.typography>
        <div id="det-contracts-wrapper" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;"></div>

        <x-atoms.button variant="primary" style="width: 100%;" onclick="openExtendForm()">Perpanjang Kontrak</x-atoms.button>
    </div>
</x-organisms.offcanvas>

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
@push('scripts')
<script>
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
            $('#ext-fee').val(fee);
            $('#ext-video').val(reqVideo);
        }
        toggleOffcanvas('offcanvasDetailKOL');
        setTimeout(() => {
            openOffcanvas('offcanvasExtendKOL');
        }, 350);
    }

    function formatCurrency(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(val) || 0);
    }

    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            try {
                const id = $(this).attr('data-id');
                const contractId = $(this).attr('data-contract-id');
                const fee = $(this).attr('data-fee');
                const reqVideo = $(this).attr('data-req-video');
                const start = $(this).attr('data-start');
                const end = $(this).attr('data-end');

                const contracts = JSON.parse($(this).attr('data-contracts') || '[]');
                const wrapper = $('#det-contracts-wrapper');
                wrapper.empty();

                contracts.forEach(function(c, index) {
                    const statusColor = c.status === 'ACTIVE' ? 'var(--emerald)' : 'var(--text-tertiary)';
                    const statusLabel = c.status === 'ACTIVE' ? 'Aktif' : (c.status === 'EXPIRED' ? 'Kedaluwarsa' : c.status);
                    
                    const productTags = c.products.length > 0
                        ? c.products.map(p =>
                            `<span style="display:inline-block; background: var(--primary-blue-soft); color: var(--primary-blue);
                                font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px; margin: 2px 2px 0 0;">
                                ${p.name}
                            </span>`
                        ).join('')
                        : '<span style="font-size:12px; color: var(--text-tertiary);">Tidak ada produk</span>';

                    wrapper.append(`
                        <div style="border: 1px solid var(--glass-border); border-radius: 10px; padding: 14px 16px;
                            background: rgba(255,255,255,0.2); position: relative;">

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-size: 12px; font-weight: 700; color: var(--text-secondary);">
                                    Kontrak #${index + 1}
                                </span>
                                <span style="font-size: 11px; font-weight: 700; color: ${statusColor};
                                    background: ${c.status === 'ACTIVE' ? 'var(--emerald-soft)' : 'rgba(0,0,0,0.05)'};
                                    padding: 2px 10px; border-radius: 999px;">
                                    ${statusLabel}
                                </span>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
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

                            <div>
                                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 6px;">Produk Terkait</div>
                                <div>${productTags}</div>
                            </div>

                            ${c.status === 'ACTIVE' ? `
                            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--glass-border);">
                                <button type="button" class="btn btn-primary btn-sm" style="width: 100%;"
                                    onclick="openExtendForm('${c.id}', '${c.fee}', '${c.req_video}')">
                                    Perpanjang Kontrak Ini
                                </button>
                            </div>` : ''}
                        </div>
                    `);
                });
                
                $('#ext-orig-id').val(contractId);
                $('#ext-fee').val(fee);
                $('#ext-video').val(reqVideo);
                
                openOffcanvas('offcanvasDetailKOL');

            } catch (error) {
                console.error("Gagal memproses data detail KOL:", error);
            }
        });
    });
</script>
@endpush