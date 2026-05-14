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

<x-organisms.modal id="detailRequestModal" title="Validasi Pengajuan Akses">
    
    <div style="margin-bottom: 24px;">
        <x-atoms.badge status="pending">
            Menunggu
        </x-atoms.badge>
    </div>

    <h4 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px;">
        Data Identitas Pendaftar
    </h4>

    <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Username TikTok</div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span id="detail-username" style="font-size: 15px; font-weight: 700; color: var(--text-primary);">
                    </span>
                <a id="detail-tiktok-link" href="#" target="_blank" style="font-size: 11px; color: var(--primary-blue, #3b82f6); text-decoration: none; padding: 4px 8px; background: rgba(59, 130, 246, 0.1); border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                    Buka Profil TikTok 
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <div>
            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Alamat Email</div>
            <div id="detail-email" style="font-size: 14px; font-weight: 500; color: var(--text-primary);">
                </div>
        </div>
        <div>
            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">No. Telepon</div>
            <div id="detail-phone" style="font-size: 14px; font-weight: 500; color: var(--text-primary); margin-bottom: 8px;">
                </div>
            <a id="detail-wa-link" href="#" target="_blank" style="font-size: 11px; color: var(--primary-blue, #3b82f6); text-decoration: none; padding: 4px 8px; background: rgba(59, 130, 246, 0.1); border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                Hubungi Whatsapp
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </div>

    <div style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.06); padding: 16px; border-radius: 6px; font-size: 13px; color: var(--text-secondary);">
        Jika disetujui, akun baru akan dibuatkan secara otomatis.
    </div>

    <x-slot:footer>
        <div style="display: flex; gap: 16px; width: 100%;">
            
            <form action="{{ route('admin-dashboard.users.reject-access') }}" method="POST" style="flex: 1;" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')">
                @csrf
                <input type="hidden" name="id" id="reject-id">
                <button type="submit" style="width: 100%; padding: 10px; background: transparent; border: 1px solid var(--glass-border, #cbd5e1); border-radius: 6px; color: var(--text-primary); font-weight: 600; cursor: pointer;">
                    Tolak Pengajuan
                </button>
            </form>

            <form action="{{ route('admin-dashboard.users.approve-access') }}" method="POST" style="flex: 1;">
                @csrf
                <input type="hidden" name="id" id="approve-id">
                <button type="submit" style="width: 100%; padding: 10px; background: #64748b; border: 1px solid #64748b; border-radius: 6px; color: white; font-weight: 600; cursor: pointer;">
                    Setujui & Buat Akun
                </button>
            </form>

        </div>
    </x-slot:footer>
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

            openModal('detailRequestModal');
        });
    });
</script>
@endpush