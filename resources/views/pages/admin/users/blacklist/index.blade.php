<x-molecules.card title="Daftar Hitam Affiliator">
    <x-slot name="headerAction">
        <x-molecules.dropdown>
            <x-slot:trigger>
                <x-atoms.button variant="secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); font-weight: 500;">
                    <x-atoms.icon name="journal" style="width: 16px; height: 16px; margin-right: 6px; display: inline-block; vertical-align: middle;" />
                    {{ $selectedMonthLabel }} 
                </x-atoms.button>
            </x-slot:trigger>

            @forelse($availableMonths as $m)
                <x-atoms.dropdown-item href="?tab={{ $currentTab }}&selected_month={{ $m['value'] }}">
                    {{ $m['label'] }}
                </x-atoms.dropdown-item>
            @empty
                <x-atoms.dropdown-item>Belum ada data blacklist</x-atoms.dropdown-item>
            @endforelse
        </x-molecules.dropdown>
    </x-slot>

    <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
        @php
            $tableColumns = [
                ['data' => 'username', 'name' => 'username', 'title' => 'Nama Kreator', 'width' => '320px'],
                ['data' => 'phone_number', 'name' => 'phone_number', 'title' => 'Kontak'],
                ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
            ];
        @endphp

        <x-organisms.datatables 
            id="blacklistTable" 
            url="{{ route('admin-dashboard.users.blacklist-data', request()->query()) }}" 
            :columns="$tableColumns" 
        />
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="detailBlacklistOffcanvas" title="Detail Affiliator (Diblokir)">
    <div style="padding: 24px; padding-top: 0;">
        
        <div style="margin-bottom: 24px;">
            <x-atoms.badge status="overdue" style="border: 1px solid var(--rose); color: var(--rose); background: rgba(244, 63, 94, 0.1); padding: 6px 16px;">
                Daftar Hitam
            </x-atoms.badge>
        </div>

        <div style="background: rgba(244, 63, 94, 0.05); border: 1px solid rgba(244, 63, 94, 0.2); border-radius: 6px; padding: 16px; margin-bottom: 24px;">
            <div style="font-size: 12px; font-weight: 700; color: var(--rose); margin-bottom: 4px; text-transform: uppercase;">Alasan Daftar Hitam</div>
            <div id="off-bl-reason" style="font-size: 14px; color: var(--text-primary); font-weight: 500;"></div>
            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 8px;">Tanggal: <span id="off-bl-date"></span></div>
        </div>

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 15px;">Profil Pengguna</x-atoms.typography>
            <div id="off-bl-username" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"></div>
            <div id="off-bl-email" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;"></div>
            <div id="off-bl-phone" style="font-size: 13px; color: var(--text-secondary);"></div>
        </div>

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 15px;">
                Riwayat Performa Historis
            </x-atoms.typography>
            
            <x-organisms.grid-layout columns="1fr 1fr" gap="12px" marginBottom="0">
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Total GMV Afiliasi</div>
                    <div id="off-bl-gmv" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Barang Terjual</div>
                    <div id="off-bl-items" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Estimasi Komisi</div>
                    <div id="off-bl-commission" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
                <div style="border: 1px solid var(--glass-border, #e2e8f0); padding: 16px; border-radius: 4px; background: rgba(255,255,255,0.4);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Aktivitas Konten</div>
                    <div id="off-bl-content" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
                </div>
            </x-organisms.grid-layout>
        </div>
        
        <div style="display: flex; gap: 12px; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <form action="{{ route('admin-dashboard.users.restore-blacklist') }}" method="POST" style="flex: 1;" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan akun ini dari daftar hitam?')">
                @csrf
                <input type="hidden" name="id" id="off-bl-id">
                <x-atoms.button type="submit" variant="success" style="width: 100%; border: none; padding: 12px;">
                    Pulihkan Akun Affiliator
                </x-atoms.button>
            </form>
        </div>

    </div>
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
        }
    }

    function toggleOffcanvas(offcanvasId) {
        const offcanvas = document.getElementById(offcanvasId);
        const backdrop = document.getElementById(offcanvasId + '-backdrop');
        if (offcanvas) offcanvas.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function formatCurrencyShort(val) {
        let num = parseFloat(val) || 0;
        if (num >= 1000000000) return 'Rp ' + (num / 1000000000).toFixed(1) + 'B';
        if (num >= 1000000) return 'Rp ' + (num / 1000000).toFixed(1) + 'M';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
    }

    function formatNumber(val) {
        return new Intl.NumberFormat('id-ID').format(parseInt(val) || 0);
    }

    $(document).ready(function() {
        $('body').on('click', '.btn-detail-blacklist', function(e) {
            e.preventDefault();
            const d = $(this).data();

            $('#off-bl-username').text(d.username.startsWith('@') ? d.username : '@' + d.username);
            $('#off-bl-email').text(d.email);
            $('#off-bl-phone').text(d.phone);
            $('#off-bl-reason').text(d.reason);
            $('#off-bl-date').text(d.date);

            $('#off-bl-gmv').text(formatCurrencyShort(d.gmv));
            $('#off-bl-items').text(formatNumber(d.items) + ' Item');
            $('#off-bl-commission').text(formatCurrencyShort(d.commission));
            $('#off-bl-content').text(formatNumber(d.videos) + ' Vid / ' + formatNumber(d.lives) + ' Liv');

            $('#off-bl-id').val(d.id);

            openOffcanvas('detailBlacklistOffcanvas');
        });
    });
</script>
@endpush