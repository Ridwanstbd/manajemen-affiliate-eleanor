@extends('layouts.app')
@section('title', 'Pemantauan Tugas')

@section('content')
<x-molecules.card title="Pemantauan Tugas" description="Pengawasan progres unggahan konten video berdasarkan sampel yang diterima.">
    <x-slot name="headerAction">
        <x-atoms.button variant="primary" onclick="openModal('editSettingModal')">
            <x-atoms.icon name="gear" style="width: 18px; height: 18px;" />
            Pengaturan
        </x-atoms.button>
    </x-slot>
    <div class="tab-content">
        <x-organisms.datatables id="taskMonitorTable" url="{{ route('admin-dashboard.task-monitoring.data') }}"
        :columns="[
            ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
            ['data' => 'username', 'name' => 'username', 'title' => 'AFFILIATOR'],
            ['data' => 'video_progress', 'name' => 'video_progress', 'title' => 'PROGRES VIDEO', 'searchable' => false],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'TERAKHIR DIPERBARUI', 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => 'STATUS TUGAS', 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'AKSI', 'orderable' => false, 'searchable' => false],
        ]"/>
    </div>
</x-molecules.card>

<x-organisms.modal 
    id="editSettingModal" 
    title="Pengaturan Tugas" 
>
    <form id="formEditSetting" method="POST" action="{{ route('admin-dashboard.settings.update-task-deadline') }}">
        @csrf
        @method('PUT')
        
        <div class="form-group" style="margin-bottom: 24px;">
            <x-atoms.label value="Batas Waktu Penyelesaian Tugas (Hari)" for="task_deadline_days" />
            <x-atoms.input 
                type="number" 
                id="task_deadline_days" 
                name="task_deadline_days" 
                value="{{ \App\Models\Setting::where('key', 'task_deadline_days')->value('value') ?? 7 }}" 
                required 
                min="1" 
                placeholder="Contoh: 7"
            />
            <div style="font-size: 12px; color: var(--text-tertiary); margin-top: 6px; line-height: 1.4;">
                Tentukan batas maksimal waktu (dalam hari) bagi affiliator untuk mengunggah tautan konten video sejak status pengiriman produk dinyatakan <b>SHIPPED</b> atau tiba.
            </div>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('editSettingModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit" form="formEditSetting">
                <x-atoms.icon name="check" style="width: 15px; height: 15px; margin-right: 6px;" />
                Simpan Perubahan
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

<x-organisms.offcanvas id="detailTaskOffcanvas" title="Detail Tugas">
    <div style="padding: 24px; padding-top: 0;">
        
        <div style="margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">Informasi Pembuat Konten</x-atoms.typography>
            <div id="tm-username" style="font-size: 14px; color: var(--text-primary); font-weight: 500;"></div>
        </div>

        <div style="margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">Ringkasan Kewajiban Laporan</x-atoms.typography>
            <div id="tm-ringkasan-kewajiban"></div>
        </div>

        <div style="margin-bottom: 32px; border-top: 1px dashed var(--glass-border); padding-top: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">Laporan Tautan Video (TikTok)</x-atoms.typography>
            <div style="font-size: 12px; color: var(--text-tertiary); margin-bottom: 16px;">Daftar konten yang telah diunggah oleh affiliator.</div>
            <div id="tm-laporan-tautan"></div>
        </div>

        <x-atoms.button type="button" variant="primary" style="width: 100%; background-color: #000; border-color: #000; color: white;" onclick="toggleOffcanvas('detailTaskOffcanvas')">
            Tutup Panel
        </x-atoms.button>
    </div>
</x-organisms.offcanvas>
@endsection

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

    document.addEventListener('DOMContentLoaded', function() {
        $('#taskMonitorTable').on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            const rowDataStr = $(this).attr('data-row');
            if(!rowDataStr) return;
            const d = JSON.parse(atob(rowDataStr));

            $('#tm-username').text('@' + d.username);

            const tasks = d.task_reports || [];
            let ringkasanHtml = '';
            let laporanHtml = '';

            if (tasks.length === 0) {
                const emptyState = `<div style="padding: 16px; border: 1px dashed #cbd5e1; border-radius: 8px; text-align: center; font-size: 12px; color: var(--text-tertiary);">Belum ada tugas yang dibuat untuk kreator ini.</div>`;
                ringkasanHtml = emptyState;
                laporanHtml = emptyState;
            } else {
                tasks.forEach(task => {
                    const taskTitle = `TASK-${task.id}`;
                    
                    const products = task.products || [];
                    const productNames = products.length > 0 
                        ? products.map(p => p.name).join(', ') 
                        : 'Produk Tidak Terdaftar';

                    let statusBadge = '';
                    if (task.task_status === 'COMPLETED') {
                        statusBadge = `<span style="padding: 4px 8px; background: #dcfce7; color: #16a34a; border-radius: 4px; font-size: 10px; font-weight: 700;">SELESAI</span>`;
                    } else if (task.task_status === 'OVERDUE') {
                        statusBadge = `<span style="padding: 4px 8px; background: #fee2e2; color: #dc2626; border-radius: 4px; font-size: 10px; font-weight: 700;">TERLAMBAT</span>`;
                    } else {
                        statusBadge = `<span style="padding: 4px 8px; background: #f1f5f9; color: #64748b; border-radius: 4px; font-size: 10px; font-weight: 700;">PENDING</span>`;
                    }

                    const dueDateRaw = task.due_date ? new Date(task.due_date) : null;
                    const dueDate = dueDateRaw ? dueDateRaw.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';

                    ringkasanHtml += `
                        <div style="padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; margin-bottom: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <div style="font-size: 12px; font-weight: 700; color: var(--primary-blue);">${taskTitle}</div>
                                ${statusBadge}
                            </div>
                            <div style="font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">Produk:</div>
                            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; line-height: 1.4;">${productNames}</div>
                            
                            <div style="display: flex; align-items: center; gap: 4px; font-size: 11px; color: var(--text-secondary);">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Batas Waktu: <span style="font-weight: 500; ${task.task_status === 'OVERDUE' ? 'color: #dc2626;' : ''}">${dueDate}</span>
                            </div>
                        </div>
                    `;

                    if (task.task_status === 'COMPLETED' && task.tiktok_video_link) {
                        const reportDateRaw = new Date(task.updated_at);
                        const reportDate = reportDateRaw.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});

                        laporanHtml += `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; margin-bottom: 12px; background: white; flex-wrap: wrap; gap: 12px;">
                                <div style="display: flex; gap: 16px; align-items: center; max-width: 100%;">
                                    <div style="width: 48px; height: 48px; min-width: 48px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary-blue);">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div style="overflow: hidden;"> 
                                        <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">${taskTitle}</div>
                                        <div style="font-size: 12px; color: var(--primary-blue); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 250px;" title="${task.tiktok_video_link}">${task.tiktok_video_link.length > 45 ? task.tiktok_video_link.substring(0, 30) + '...' : task.tiktok_video_link}</div>
                                        <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px;">Dilaporkan pada: ${reportDate}</div>
                                    </div>
                                </div>
                                <a href="${task.tiktok_video_link}" target="_blank" style="padding: 6px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-primary); text-decoration: none; display: flex; align-items: center; background: white; transition: 0.2s;">
                                    Buka ↗
                                </a>
                            </div>
                        `;
                    } else {
                        laporanHtml += `
                            <div style="padding: 16px; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; background: rgba(248, 250, 252, 0.5);">
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 2px;">${taskTitle}</div>
                                    <div style="font-size: 11px; color: var(--text-tertiary);">Belum ada tautan yang dilaporkan.</div>
                                </div>
                                ${statusBadge}
                            </div>
                        `;
                    }
                });
            }

            $('#tm-ringkasan-kewajiban').html(ringkasanHtml);
            $('#tm-laporan-tautan').html(laporanHtml);

            openOffcanvas('detailTaskOffcanvas');
        });
    });
</script>
@endpush