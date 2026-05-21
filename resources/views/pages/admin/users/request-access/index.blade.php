<x-molecules.card title="Permintaan Akses Sistem">
    <x-slot name="headerAction">
        <x-molecules.dropdown>
            <x-slot:trigger>
                <x-atoms.button variant="secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); font-weight: 500;">
                    <x-atoms.icon name="journal" style="width: 16px; height: 16px; margin-right: 6px; display: inline-block; vertical-align: middle;" />
                    {{ $selectedLabel }} 
                </x-atoms.button>
            </x-slot:trigger>

            @forelse($availableMonths as $m)
                <x-atoms.dropdown-item href="?tab={{ $currentTab }}&selected_month={{ $m['value'] }}">
                    {{ $m['label'] }}
                </x-atoms.dropdown-item>
            @empty
                <x-atoms.dropdown-item>Belum ada data</x-atoms.dropdown-item>
            @endforelse
        </x-molecules.dropdown>
    </x-slot>
        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @php
                $tableColumns = [
                    ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
                    ['data' => 'tiktok_username', 'name' => 'tiktok_username', 'title' => 'Nama Kreator', 'width' => '320px'],
                    ['data' => 'phone_number', 'name' => 'phone_number', 'title' => 'Kontak'],
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                    ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
                ];
            @endphp

            <x-organisms.datatables 
                id="requestTable" 
                url="{{ route('admin-dashboard.users.request-access-data', request()->query()) }}" 
                :columns="$tableColumns" 
            />
        </div>
</x-molecules.card>

<x-organisms.modal id="detailRequestModal" title="Detail Permintaan Akses">
    <form id="formApproveRequest" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="request_id" name="id">
        <div class="form-group mb-3">
            <x-atoms.label value="Nama Pengguna" for="request_username" />
            <x-atoms.input type="text" id="request_username" name="username" disabled />
        </div>
        <div class="form-group mb-3">
            <x-atoms.label value="Email" for="request_email" />
            <x-atoms.input type="email" id="request_email" name="email" disabled />
        </div>
        <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; margin-bottom: 16px;">
            <div>
                <x-atoms.typography variant="body" style="font-weight: 600; color: var(--text-primary); margin: 0; font-size: 14px;">Terima sebagai Kontak KOL</x-atoms.typography>
                <div style="color: var(--text-secondary); font-size: 12px; margin-top: 2px;">Akun otomatis memiliki status is_kol = true saat disetujui.</div>
            </div>
            <x-molecules.toggle id="requestIsKol" name="is_kol" />
        </div>
        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('detailRequestModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit" form="formApproveRequest">
                Setujui Akses
            </x-atoms.button>
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
            document.body.style.removeProperty('overflow');
            document.body.style.overflow = 'auto';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('#requestTable').on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            const id = $(this).attr('data-id');
            let username = $(this).attr('data-username') || '';
            const email = $(this).attr('data-email') || '-';
            const phone = $(this).attr('data-phone') || '-';

            if (username && !username.startsWith('@')) {
                username = '@' + username;
            }
            const rawUsername = username.replace('@', ''); 

            document.getElementById('detail-username').innerText = username;
            document.getElementById('detail-tiktok-link').href = 'https://www.tiktok.com/@' + rawUsername;
            
            document.getElementById('detail-email').innerText = email;
            document.getElementById('detail-phone').innerText = phone;
            const waLink = document.getElementById('detail-wa-link');
            if (phone !== '-' && phone !== '') {
                const waNumber = phone.replace(/^0/, '62');
                waLink.href = 'https://wa.me/' + waNumber;
                waLink.style.display = 'inline-flex';
            } else {
                waLink.style.display = 'none'; 
            }

            document.getElementById('reject-id').value = id;
            document.getElementById('approve-id').value = id;
            const toggleKol = document.getElementById('requestIsKol');
            if(toggleKol) {
                toggleKol.checked = false;
            }
            openModal('detailRequestModal');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const openRequestId = urlParams.get('open_request');
        
        if (openRequestId) {
            $('#requestTable').on('draw.dt', function() {
                const btn = $('.btn-detail[data-id="' + openRequestId + '"]');
                if (btn.length > 0 && !btn.data('auto-clicked')) {
                    btn.data('auto-clicked', true);
                    setTimeout(() => {
                        btn.click();
                        
                        urlParams.delete('open_request');
                        const newUrl = urlParams.toString() ? window.location.pathname + '?' + urlParams.toString() : window.location.pathname;
                        window.history.replaceState({}, document.title, newUrl);
                        
                    }, 500);
                }
            });
        }
    });
</script>
@endpush