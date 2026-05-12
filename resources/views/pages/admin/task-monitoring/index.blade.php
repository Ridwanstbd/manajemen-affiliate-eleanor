@extends('layouts.app')
@section('title', 'Pemantauan Tugas')

@section('content')
<x-molecules.card title="Pemantauan Tugas" description="Pengawasan progres unggahan konten video berdasarkan sampel yang diterima.">
    <div class="tab-content">
        <x-organisms.datatables id="taskMonitorTable" url="{{ route('admin-dashboard.task-monitoring.data') }}"
        :columns="[
            ['data' => 'username', 'name' => 'username', 'title' => 'AFFILIATOR'],
            ['data' => 'video_progress', 'name' => 'video_progress', 'title' => 'PROGRES VIDEO', 'searchable' => false],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'TERAKHIR DIPERBARUI', 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => 'STATUS TUGAS', 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'AKSI', 'orderable' => false, 'searchable' => false],
        ]"/>
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="detailTaskOffcanvas" title="Detail Tugas">
    <div style="padding: 24px; padding-top: 0;">
        
        <div style="margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">Informasi Pembuat Konten</x-atoms.typography>
            <div id="tm-username" style="font-size: 14px; color: var(--text-primary); font-weight: 500;"></div>
        </div>

        <div style="margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">Ringkasan Kewajiban (Berdasarkan Sampel)</x-atoms.typography>
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
        'jne': 'JNE',
        'jnt': 'J&T Express',
        'ninja': 'Ninja Xpress',
        'tiki': 'TIKI',
        'pos': 'POS Indonesia',
        'anteraja': 'AnterAja',
        'sicepat': 'SiCepat',
        'sap': 'SAP Express',
        'lion': 'Lion Parcel',
        'wahana': 'Wahana',
        'first': 'First Logistics',
        'ide': 'ID Express'
    };

    document.addEventListener('DOMContentLoaded', function() {
        $('#taskMonitorTable').on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            const rowDataStr = $(this).attr('data-row');
            if(!rowDataStr) return;
            const d = JSON.parse(atob(rowDataStr));

            $('#tm-username').text('@' + d.username);

            let productMap = {};
            let productIds = [];
            
            if (d.sample_requests) {
                d.sample_requests.forEach(req => {
                    if(req.status === 'SHIPPED' || req.status === 'APPROVED' || req.status === 'COMPLETED') {
                        if (req.details) {
                            req.details.forEach(detail => {
                                if (detail.product) {
                                    let pid = detail.product.id;
                                    if (!productMap[pid]) {
                                        productMap[pid] = {
                                            name: detail.product.name,
                                            mandatory_count: detail.product.mandatory_video_count || 1,
                                            tasks: [],
                                            courier: req.courier,
                                            tracking_number: req.tracking_number
                                        };
                                        productIds.push(pid);
                                    } else {
                                        productMap[pid].mandatory_count += (detail.product.mandatory_video_count || 1);
                                    }
                                }
                            });
                        }
                    }
                });
            }

            const tasks = d.task_reports || [];
            tasks.forEach(task => {
                let assigned = false;
                if (task.products && task.products.length > 0) {
                    let pid = task.products[0].id;
                    if (productMap[pid]) {
                        productMap[pid].tasks.push(task);
                        assigned = true;
                    }
                }
                
                if (!assigned && productIds.length > 0) {
                    productMap[productIds[0]].tasks.push(task);
                } else if(!assigned && productIds.length === 0) {
                    if(!productMap['manual']) {
                         productMap['manual'] = { 
                             name: "Produk Tidak Terdaftar (Inject)", 
                             mandatory_count: tasks.length, 
                             tasks: [],
                             courier: null,
                             tracking_number: null
                        };
                    }
                    productMap['manual'].tasks.push(task);
                }
            });

            let ringkasanHtml = '';
            let laporanHtml = '';

            Object.values(productMap).forEach(prod => {
                let completedCount = prod.tasks.filter(t => t.task_status === 'COMPLETED').length;

                ringkasanHtml += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; margin-bottom: 8px;">
                        <div>
                            <div style="font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">Kewajiban Produk:</div>
                            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">${prod.name} (Wajib: ${prod.mandatory_count} Video)</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 16px; font-weight: 800; color: var(--text-primary);">${completedCount} / ${prod.tasks.length > prod.mandatory_count ? prod.tasks.length : prod.mandatory_count}</div>
                            <div style="font-size: 11px; color: var(--text-tertiary);">Terkirim</div>
                        </div>
                    </div>
                `;

                let courierNameDisplay = 'Kurir Tidak Diketahui';
                if (prod.courier) {
                    let cCode = prod.courier.toLowerCase();
                    courierNameDisplay = courierNames[cCode] ? courierNames[cCode] : prod.courier.toUpperCase();
                }

                laporanHtml += `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; margin-bottom: 8px;">
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">Paket ${courierNameDisplay}</span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">${prod.tracking_number || '-'}</span>
                    </div>
                    <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px;">${prod.name}</div>
                `;

                if(prod.tasks.length === 0) {
                    laporanHtml += `
                        <div style="padding: 24px 16px; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 12px; text-align: center; background: rgba(248, 250, 252, 0.5);">
                            <span style="font-size: 12px; color: var(--text-tertiary);">Belum ada tugas yang dibuat untuk produk ini.</span>
                        </div>
                    `;
                }

                prod.tasks.forEach((task, index) => {
                    let videoNum = index + 1;
                    
                    if (task.task_status === 'COMPLETED' && task.tiktok_video_link) {
                        const rawDate = new Date(task.updated_at);
                        const reportDate = rawDate.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                        
                        laporanHtml += `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; margin-bottom: 12px; background: white; flex-wrap: wrap; gap: 12px;">
                                <div style="display: flex; gap: 16px; align-items: center; max-width: 100%;">
                                    <div style="width: 48px; height: 48px; min-width: 48px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div style="overflow: hidden;">
                                        <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Tautan Video ${videoNum}</div>
                                        <div style="font-size: 12px; color: var(--primary-blue); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 250px;">${task.tiktok_video_link}</div>
                                        <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px;">Waktu Lapor: ${reportDate}</div>
                                    </div>
                                </div>
                                <a href="${task.tiktok_video_link}" target="_blank" style="padding: 6px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-primary); text-decoration: none; display: flex; align-items: center; background: white; transition: 0.2s;">
                                    Buka ↗
                                </a>
                            </div>
                        `;
                    } else {
                        laporanHtml += `
                            <div style="padding: 24px 16px; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 12px; text-align: center; background: rgba(248, 250, 252, 0.5);">
                                <span style="font-size: 12px; color: var(--text-tertiary);">Belum ada tautan yang dikirimkan (Video ${videoNum})</span>
                            </div>
                        `;
                    }
                });
            });

            $('#tm-ringkasan-kewajiban').html(ringkasanHtml);
            $('#tm-laporan-tautan').html(laporanHtml);

            openOffcanvas('detailTaskOffcanvas');
        });
    });
</script>
@endpush