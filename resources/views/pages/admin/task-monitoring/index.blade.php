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

<x-organisms.offcanvas id="detailTaskOffcanvas" title="Detail Penugasan">
    <div style="padding: 24px; padding-top: 0;">
        
        <div style="margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">Informasi Pembuat Konten</x-atoms.typography>
            <div id="tm-username" style="font-size: 14px; color: var(--text-primary); font-weight: 500;"></div>
        </div>

        <div style="margin-bottom: 32px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">Data Penugasan Berdasarkan Paket</x-atoms.typography>
            <div id="tm-detail-tugas"></div>
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

    const courierNames = {
        'jne': 'JNE', 'jnt': 'J&T Express', 'ninja': 'Ninja Xpress', 'tiki': 'TIKI',
        'pos': 'POS Indonesia', 'anteraja': 'AnterAja', 'sicepat': 'SiCepat',
        'sap': 'SAP Express', 'lion': 'Lion Parcel', 'wahana': 'Wahana',
        'first': 'First Logistics', 'ide': 'ID Express'
    };

    document.addEventListener('DOMContentLoaded', function() {
        $('#taskMonitorTable').on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            const rowDataStr = $(this).attr('data-row');
            if(!rowDataStr) return;
            const d = JSON.parse(atob(rowDataStr));

            $('#tm-username').text('@' + (d.username || 'Tidak diketahui'));

            let packages = [];
            let manualTasks = [];

            if (d.sample_requests) {
                d.sample_requests.forEach(req => {
                    if (['SHIPPED', 'DELIVERED', 'COMPLETED', 'APPROVED'].includes(req.status)) {
                        let pkg = {
                            id: req.id,
                            courier: req.courier || 'Kurir',
                            tracking_number: req.tracking_number || 'Menunggu Resi',
                            products: []
                        };
                        
                        if (req.details) {
                            req.details.forEach(detail => {
                                if (detail.product) {
                                    let mCount = 1;
                                    if (detail.mandatory_video_count !== null && detail.mandatory_video_count !== undefined) {
                                        mCount = parseInt(detail.mandatory_video_count);
                                    } else if (detail.product.mandatory_video_count !== null && detail.product.mandatory_video_count !== undefined) {
                                        mCount = parseInt(detail.product.mandatory_video_count);
                                    }

                                    if (mCount > 0) {
                                        pkg.products.push({
                                            id: detail.product.id,
                                            name: detail.product.name,
                                            mandatory_count: mCount,
                                            tasks: []
                                        });
                                    }
                                }
                            });
                        }
                        
                        if (pkg.products.length > 0) {
                            packages.push(pkg);
                        }
                    }
                });
            }

            const tasks = d.task_reports || [];
            tasks.forEach(task => {
                let matched = false;
                if (task.products && task.products.length > 0) {
                    let pIds = task.products.map(p => p.id);
                    packages.forEach(pkg => {
                        pkg.products.forEach(prod => {
                            if (pIds.includes(prod.id)) {
                                prod.tasks.push(task);
                                matched = true;
                            }
                        });
                    });
                }
                
                if (!matched) {
                    manualTasks.push(task);
                }
            });

            let resultHtml = '';

            if (packages.length === 0 && manualTasks.length === 0) {
                resultHtml = `<div style="padding: 16px; border: 1px dashed #cbd5e1; border-radius: 8px; text-align: center; font-size: 12px; color: var(--text-tertiary);">Belum ada data paket atau penugasan video untuk kreator ini.</div>`;
            }

            packages.forEach(pkg => {
                let displayCourier = pkg.courier !== 'Kurir' ? (courierNames[pkg.courier.toLowerCase()] || pkg.courier.toUpperCase()) : pkg.courier;
                
                resultHtml += `
                    <div style="background: white; border: 1px solid var(--glass-border); border-radius: 8px; margin-bottom: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="padding: 16px;">
                `;
                
                pkg.products.forEach((prod, pIdx) => {
                    const isLastProduct = pIdx === pkg.products.length - 1;
                    
                    resultHtml += `
                        <div style="margin-bottom: ${isLastProduct ? '0' : '20px'};">
                            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 12px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <div>
                                    <div style="font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">Nama Produk:</div>
                                    <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary); line-height: 1.3;">${prod.name}</div>
                                </div>
                                <div style="font-size: 11px; font-weight: 600; color: var(--primary-blue); background: rgba(59, 130, 246, 0.1); padding: 4px 8px; border-radius: 4px; white-space: nowrap;">
                                    Wajib ${prod.mandatory_count} Video
                                </div>
                            </div>
                    `;
                    
                    if (prod.tasks.length === 0) {
                        resultHtml += `<div style="padding: 12px; border: 1px dashed #cbd5e1; border-radius: 6px; font-size: 11px; color: var(--text-tertiary); text-align: center; background: #f8fafc;">Tugas belum digenerate oleh sistem.</div>`;
                    } else {
                        prod.tasks.forEach((task) => {
                            const taskTitle = `TASK-${task.id}`;
                            let statusBadge = '';
                            if (task.task_status === 'COMPLETED') {
                                statusBadge = `<span style="padding: 3px 6px; background: #dcfce7; color: #16a34a; border-radius: 4px; font-size: 10px; font-weight: 700;">SELESAI</span>`;
                            } else if (task.task_status === 'OVERDUE') {
                                statusBadge = `<span style="padding: 3px 6px; background: #fee2e2; color: #dc2626; border-radius: 4px; font-size: 10px; font-weight: 700;">TERLAMBAT</span>`;
                            } else {
                                statusBadge = `<span style="padding: 3px 6px; background: #f1f5f9; color: #64748b; border-radius: 4px; font-size: 10px; font-weight: 700;">PENDING</span>`;
                            }
                            
                            const dueDateRaw = task.due_date ? new Date(task.due_date) : null;
                            const dueDate = dueDateRaw ? dueDateRaw.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-';

                            if (task.task_status === 'COMPLETED' && task.tiktok_video_link) {
                                const reportDateRaw = new Date(task.updated_at);
                                const reportDate = reportDateRaw.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});

                                resultHtml += `
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 12px; border: 1px solid rgba(0,0,0,0.06); border-radius: 6px; margin-bottom: 8px; background: #fff; flex-wrap: wrap; gap: 12px; border-left: 3px solid #10b981;">
                                        <div style="flex: 1; min-width: 200px;">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                                <div style="font-size: 12px; font-weight: 700; color: var(--text-primary);">${taskTitle}</div>
                                                ${statusBadge}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px;">Dilaporkan: ${reportDate}</div>
                                            <div style="font-size: 11px; color: var(--primary-blue); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 100%;" title="${task.tiktok_video_link}">${task.tiktok_video_link}</div>
                                        </div>
                                        <div>
                                            <a href="${task.tiktok_video_link}" target="_blank" style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11px; font-weight: 600; color: var(--text-primary); text-decoration: none; display: inline-block; background: #f8fafc; transition: all 0.2s;">
                                                Buka Link
                                            </a>
                                        </div>
                                    </div>
                                `;
                            } else {
                                resultHtml += `
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid rgba(0,0,0,0.06); border-radius: 6px; margin-bottom: 8px; background: #fff; border-left: 3px solid ${task.task_status === 'OVERDUE' ? '#ef4444' : '#e2e8f0'};">
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                <div style="font-size: 12px; font-weight: 700; color: var(--text-primary);">${taskTitle}</div>
                                                ${statusBadge}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-secondary);">Tenggat: <span style="${task.task_status === 'OVERDUE' ? 'color: #dc2626; font-weight: 600;' : 'font-weight: 500;'}">${dueDate}</span></div>
                                        </div>
                                    </div>
                                `;
                            }
                        });
                    }
                    resultHtml += `</div>`;
                });
                resultHtml += `
                        </div>
                    </div>
                `;
            });

            if (manualTasks.length > 0) {
                resultHtml += `
                    <div style="background: white; border: 1px solid var(--glass-border); border-radius: 8px; margin-bottom: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="background: #fffbeb; padding: 12px 16px; border-bottom: 1px solid #fef3c7; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #b45309;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span style="font-size: 13px; font-weight: 700; color: #92400e;">Penugasan Tanpa Paket (Lainnya)</span>
                            </div>
                        </div>
                        <div style="padding: 16px;">
                `;
                
                manualTasks.forEach((task) => {
                    const taskTitle = `TASK-${task.id}`;
                    const products = task.products || [];
                    const productNames = products.length > 0 ? products.map(p => p.name).join(', ') : 'Produk Tidak Terdaftar';
                    
                    let statusBadge = '';
                    if (task.task_status === 'COMPLETED') {
                        statusBadge = `<span style="padding: 3px 6px; background: #dcfce7; color: #16a34a; border-radius: 4px; font-size: 10px; font-weight: 700;">SELESAI</span>`;
                    } else if (task.task_status === 'OVERDUE') {
                        statusBadge = `<span style="padding: 3px 6px; background: #fee2e2; color: #dc2626; border-radius: 4px; font-size: 10px; font-weight: 700;">TERLAMBAT</span>`;
                    } else {
                        statusBadge = `<span style="padding: 3px 6px; background: #f1f5f9; color: #64748b; border-radius: 4px; font-size: 10px; font-weight: 700;">PENDING</span>`;
                    }

                    const dueDateRaw = task.due_date ? new Date(task.due_date) : null;
                    const dueDate = dueDateRaw ? dueDateRaw.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-';

                    resultHtml += `
                        <div style="margin-bottom: 8px; padding: 12px; border: 1px solid rgba(0,0,0,0.06); border-radius: 6px; background: #fff; border-left: 3px solid ${task.task_status === 'COMPLETED' ? '#10b981' : (task.task_status === 'OVERDUE' ? '#ef4444' : '#e2e8f0')};">
                            <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; line-height: 1.3;">${productNames}</div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary);">${taskTitle}</div>
                                    ${statusBadge}
                                </div>
                            </div>
                            <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px;">Tenggat: <span style="font-weight: 500;">${dueDate}</span></div>
                            ${task.tiktok_video_link ? `<div style="font-size: 11px; color: var(--primary-blue); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 100%; margin-top: 6px;"><a href="${task.tiktok_video_link}" target="_blank" style="color: inherit; text-decoration: none;">${task.tiktok_video_link}</a></div>` : ''}
                        </div>
                    `;
                });
                
                resultHtml += `
                        </div>
                    </div>
                `;
            }

            $('#tm-detail-tugas').html(resultHtml);

            openOffcanvas('detailTaskOffcanvas');
        });
    });
</script>
@endpush