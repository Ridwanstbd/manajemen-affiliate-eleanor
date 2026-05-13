@extends('layouts.app')
@section('title', 'Persetujuan & Pengiriman')

@section('content')
<x-molecules.card title="Persetujuan & Pengiriman" description="Kelola permintaan sampel dari affiliator dan perbarui status logistik pengiriman.">
    <x-slot:headerAction>
        <form action="{{ route('admin-dashboard.request-samples.sync-status') }}" method="POST">
            @csrf
                <x-atoms.button type="submit" variant="primary" onclick="this.innerHTML='Menyinkronkan...'; this.disabled=true; this.form.submit();">
                    <x-atoms.icon name="refresh" style="width: 16px; height: 16px;" /> Sinkronkan Status
                </x-atoms.button>
        </form>
    </x-slot:headerAction>
    <div class="tab-content">
        <x-organisms.datatables id="requestSampleTable"
        url="{{ route('admin-dashboard.request-samples.data') }}"
        :columns="[
            ['data' => 'username', 'name' => 'user.username', 'title' => 'Affiliator'],
            ['data' => 'details_sum_quantity', 'name' => 'details_sum_quantity', 'title' => 'Total Item'],
            ['data' =>'created_at', 'name' => 'created_at', 'title' => 'Tanggal'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
        ]" />
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="detailRequestsampleOffcanvas" title="Detail Pengajuan Sampel Gratis">
    <form id="updateResiForm" action="{{ route('admin-dashboard.request-samples.update-resi') }}" method="POST">
        @csrf
        <input type="hidden" name="sample_request_id" id="off-request-id">

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">Informasi Affiliator</x-atoms.typography>
            <div id="off-username" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"></div>
            <div id="off-contact" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;"></div>
            <div id="off-address" style="font-size: 13px; color: var(--text-secondary);"></div>
        </div>

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 14px; color: var(--text-secondary);">
                Rincian Paket (<span id="off-total-products">0</span> Produk)
            </x-atoms.typography>
            <div id="off-product-list" style="display: flex; flex-direction: column; gap: 8px;"></div>
        </div>

        <div id="section-form-update" style="padding-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 4px; font-size: 14px; color: var(--text-secondary);">Pembaruan Status Pengiriman</x-atoms.typography>
            <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 16px;">Masukkan detail logistik untuk memproses paket.</p>
            
            <div style="margin-bottom: 16px;">
                <x-atoms.label value="Kurir Pengiriman" style="font-size: 12px; margin-bottom: 4px; display: block;" />
                <x-atoms.select name="courier" id="off-courier">
                    <option value="">Pilih Kurir...</option>
                    <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                    <option value="jnt">J&T Express</option>
                    <option value="ninja">Ninja Xpress</option>
                    <option value="tiki">TIKI (Titipan Kilat)</option>
                    <option value="pos">POS Indonesia</option>
                    <option value="anteraja">AnterAja</option>
                    <option value="sicepat">SiCepat Ekspres</option>
                    <option value="sap">SAP Express</option>
                    <option value="lion">Lion Parcel</option>
                    <option value="wahana">Wahana Prestasi Logistik</option>
                    <option value="first">First Logistics</option>
                    <option value="ide">ID Express</option>
                </x-atoms.select>
            </div>

            <div style="margin-bottom: 16px;">
                <x-atoms.label value="Nomor Resi (Tracking Number)" style="font-size: 12px; margin-bottom: 4px; display: block;" />
                <x-atoms.input type="text" name="tracking_number" id="off-tracking" placeholder="Contoh: JNT-123456..." />
            </div>

            <div style="margin-bottom: 16px;">
                <x-atoms.label value="Biaya Ongkos Kirim (Rp)" style="font-size: 12px; margin-bottom: 4px; display: block;" />
                <x-atoms.input type="number" name="shipping_cost" id="off-shipping-cost" placeholder="0" />
            </div>
        </div>

        <div id="section-approved-info" style="display: none;">
            <div style="margin-bottom: 24px;">
                <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">Informasi Pengiriman</x-atoms.typography>
                <div class="shipping-info-box">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Kurir: <span id="lbl-courier"></span></div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">No. Resi: <span id="lbl-tracking-no"></span></div>
                    <div style="font-size: 12px; color: var(--text-secondary);">Biaya Ongkir: Rp <span id="lbl-cost"></span></div>
                    
                    <button type="button" onclick="copyResi()" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: white; border: 1px solid #cbd5e1; padding: 6px 16px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; color: var(--text-primary); transition: all 0.2s;">
                        Salin Resi
                    </button>
                </div>
            </div>

            <div style="padding-bottom: 24px;">
                <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">Status Pelacakan</x-atoms.typography>
                <div id="tracking-content">
                    {{-- Timeline akan di-generate via JS --}}
                    <div style="font-size: 13px; color: var(--text-secondary);">Memuat data pelacakan...</div>
                </div>
            </div>
        </div>

        <div id="section-rejected-info" style="display: none; padding-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 14px; color: var(--rose);">Alasan Penolakan</x-atoms.typography>
            <div class="rejection-info-box">
                <div id="lbl-reject-reason" style="font-size: 13.5px; color: var(--text-primary); line-height: 1.5; font-style: italic;"></div>
            </div>
        </div>
        
        <div id="footer-actions-form" style="display: flex; gap: 12px; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <div style="flex: 1;">
                <x-atoms.button type="button" variant="outline" style="width: 100%; border-color: var(--text-secondary); color: var(--text-primary); background: transparent;" onclick="openRejectForm()">
                    Tolak 
                </x-atoms.button>
            </div>
            <div style="flex: 1;">
                <x-atoms.button type="submit" variant="primary" style="width: 100%; background-color: #57534e; border-color: #57534e; color: white;">
                    Kirim Produk (Update Resi)
                </x-atoms.button>
            </div>
        </div>

        <div id="footer-actions-approved" style="display: none; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <x-atoms.button type="button" variant="outline" style="width: 100%; border-color: #cbd5e1; color: var(--text-primary); background: transparent; font-weight: 600;" onclick="toggleOffcanvas('detailRequestsampleOffcanvas')">
                Tutup
            </x-atoms.button>
        </div>

    </form>
</x-organisms.offcanvas>

<x-organisms.offcanvas id="rejectRequestsampleOffcanvas" title="Tolak Pengajuan">
    <form action="{{ route('admin-dashboard.request-samples.reject') }}" method="POST">
        @csrf
        <input type="hidden" name="sample_request_id" id="rej-request-id">

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">Informasi Affiliator</x-atoms.typography>
            <div id="rej-username" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"></div>
            <div id="rej-contact" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;"></div>
            <div id="rej-address" style="font-size: 13px; color: var(--text-secondary);"></div>
        </div>

        <div style="border-bottom: 1px dashed var(--glass-border, #cbd5e1); padding-bottom: 24px; margin-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 14px; color: var(--text-secondary);">
                Rincian Paket yang Dibatalkan (<span id="rej-total-products">0</span> Produk)
            </x-atoms.typography>
            <div id="rej-product-list" style="display: flex; flex-direction: column; gap: 8px;"></div>
        </div>

        <div style="padding-bottom: 24px;">
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 4px; font-size: 14px;">Form Penolakan Pengajuan</x-atoms.typography>
            <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 16px;">Anda akan menolak pengajuan ini. Berikan alasan penolakan agar affiliator mengetahui penyebabnya.</p>
            
            <div style="margin-bottom: 16px;">
                <x-atoms.label value="Alasan Penolakan (Wajib Diisi)" style="font-size: 12px; margin-bottom: 4px; display: block;" />
                <textarea name="reject_reason" id="rej-reason" class="form-control" rows="4" placeholder="Tuliskan alasan penolakan di sini..." required style="height: auto;"></textarea>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 8px;">Gunakan template cepat:</span>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" onclick="setRejectReason('Performa belum memenuhi')" style="background: #fffbeb; color: #b45309; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;">Performa belum memenuhi</button>
                    <button type="button" onclick="setRejectReason('Kategori tidak relevan')" style="background: #fffbeb; color: #b45309; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;">Kategori tidak relevan</button>
                    <button type="button" onclick="setRejectReason('Alamat tidak terjangkau')" style="background: #fffbeb; color: #b45309; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;">Alamat tidak terjangkau</button>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <div style="flex: 1;">
                <x-atoms.button type="button" variant="outline" style="width: 100%; border-color: #cbd5e1; color: var(--text-primary); background: transparent;" onclick="backToDetail()">
                    Kembali
                </x-atoms.button>
            </div>
            <div style="flex: 1;">
                <x-atoms.button type="submit" variant="primary" style="width: 100%; background-color: #57534e; border-color: #57534e; color: white;">
                    Konfirmasi Tolak Pengajuan
                </x-atoms.button>
            </div>
        </div>
    </form>
</x-organisms.offcanvas>
@endsection

@push('scripts')
<script>
    let currentRequestData = null;
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

    function copyResi() {
        const resi = document.getElementById('lbl-tracking-no').innerText;
        navigator.clipboard.writeText(resi).then(() => {
            const btn = document.querySelector('button[onclick="copyResi()"]');
            const originalText = btn.innerText;
            btn.innerText = 'Tersalin!';
            btn.style.backgroundColor = '#f0fdf4';
            btn.style.borderColor = '#16a34a';
            btn.style.color = '#16a34a';
            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.backgroundColor = 'white';
                btn.style.borderColor = '#cbd5e1';
                btn.style.color = 'var(--text-primary)';
            }, 2000);
        });
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
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

    function setRejectReason(text) {
        document.getElementById('rej-reason').value = text;
    }

    function openRejectForm() {
        if (!currentRequestData) return;
        
        $('#rej-request-id').val(currentRequestData.id);
        
        const username = currentRequestData.user?.username || 'Tidak Diketahui';
        $('#rej-username').text(username.startsWith('@') ? username : '@' + username);
        $('#rej-contact').text(`${currentRequestData.user?.name || username} (${currentRequestData.user?.phone_number || '-'})`);
        $('#rej-address').text(`Alamat: ${currentRequestData.address || '-'}`);
        
        $('#rej-product-list').html($('#off-product-list').html());
        $('#rej-total-products').text($('#off-total-products').text());
        $('#rej-reason').val(''); 
        
        toggleOffcanvas('detailRequestsampleOffcanvas');
        setTimeout(() => {
            openOffcanvas('rejectRequestsampleOffcanvas');
        }, 350);
    }

    function backToDetail() {
        toggleOffcanvas('rejectRequestsampleOffcanvas');
        setTimeout(() => {
            openOffcanvas('detailRequestsampleOffcanvas');
        }, 350);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        $('#requestSampleTable').on('click', '.btn-detail', function(e) {
            e.preventDefault();
            
            const rowDataStr = $(this).attr('data-row');
            if(!rowDataStr) return;
            const d = JSON.parse(atob(rowDataStr));

            currentRequestData = d;
            $('#off-request-id').val(d.id);
            
            const username = d.user?.username || 'Tidak Diketahui';
            $('#off-username').text(username.startsWith('@') ? username : '@' + username);
            $('#off-contact').text(`${d.user?.name || username} (${d.user?.phone_number || '-'})`);
            $('#off-address').text(`Alamat: ${d.address || '-'}`);

            let productListHtml = '';
            let totalProducts = 0;
            if (d.details && d.details.length > 0) {
                d.details.forEach(item => {
                    const quantity = item.quantity || 0;
                    const productName = item.product?.name || 'Produk Dihapus/Tidak Valid';
                    const videoCount = item.product?.mandatory_video_count || 0;
                    totalProducts += quantity;
                    productListHtml += `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid rgba(0,0,0,0.05); border-radius: 4px; margin-bottom: 8px;">
                            <div>
                                <div style="font-size: 14px; font-weight: 500; color: var(--text-primary);">${productName}</div>
                                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Kewajiban: ${videoCount} Video</div>
                            </div>
                            <div style="font-size: 14px; font-weight: 700; color: var(--text-primary);">${quantity}x</div>
                        </div>
                    `;
                });
            } else {
                productListHtml = `<div style="font-size: 13px; color: var(--text-secondary);">Tidak ada data produk.</div>`;
            }
            $('#off-product-list').html(productListHtml);
            $('#off-total-products').text(totalProducts);

            $('#section-form-update, #section-approved-info, #section-rejected-info').hide();
            $('#footer-actions-form, #footer-actions-approved').hide();

            if (d.status === 'APPROVED' || d.status === 'SHIPPED') {
                $('#section-approved-info').show();
                $('#footer-actions-approved').show();

                const displayCourier = courierNames[d.courier] || (d.courier ? d.courier.toUpperCase() : 'Tidak diketahui');
                $('#lbl-courier').text(displayCourier);
                $('#lbl-tracking-no').text(d.tracking_number || '-');
                $('#lbl-cost').text(d.shipping_cost ? formatRupiah(d.shipping_cost) : '0');

                $('#tracking-content').html('<div style="font-size: 13px; color: var(--text-secondary);">Memuat data pelacakan dari server...</div>');
                if(d.tracking_number) {
                    $.ajax({
                        url: `/dashboard/request-samples/track/${d.id}`,
                        method: 'GET',
                        success: function(res) {
                            $('#requestSampleTable').DataTable().ajax.reload(null, false);
                            let historyHtml = '<div class="tracking-timeline">';
                            const dateApproved = new Date(d.updated_at || d.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' WIB';
                            let trackingDetails = [];
                            if (res && res.data && res.data.manifest) {
                                trackingDetails = res.data.manifest.slice().reverse();
                            }

                            if(trackingDetails && trackingDetails.length > 0) {
                                
                                trackingDetails.forEach((item, index) => {
                                    let dotClass = index === 0 ? 'current' : 'completed';
                                    
                                    let statusDesc = item.manifest_description || item.title || 'Update Status';
                                    let statusDate = item.manifest_date || '';
                                    let statusTime = item.manifest_time || '';
                                    
                                    let formattedDate = `${statusDate} ${statusTime}`.trim();

                                    historyHtml += `
                                        <div class="timeline-item ${dotClass}">
                                            <div class="timeline-title">${statusDesc}</div>
                                            <div class="timeline-date">${formattedDate}</div>
                                        </div>
                                    `;
                                });
                                
                                historyHtml += `
                                    <div class="timeline-item completed">
                                        <div class="timeline-title">Pesanan Disetujui</div>
                                        <div class="timeline-date">${dateApproved}</div>
                                    </div>
                                `;

                                const isDelivered = res.data.delivered === true || (res.data.summary && res.data.summary.status === 'DELIVERED');
                                
                                if(!isDelivered) {
                                    historyHtml = `
                                        <div class="timeline-item pending">
                                            <div class="timeline-title">Paket Diterima oleh Affiliator</div>
                                            <div class="timeline-date">Menunggu konfirmasi</div>
                                        </div>
                                    ` + historyHtml;
                                }

                            } else {
                                historyHtml += `
                                    <div class="timeline-item pending">
                                        <div class="timeline-title">Paket Diterima oleh Affiliator</div>
                                        <div class="timeline-date">Menunggu konfirmasi</div>
                                    </div>
                                    <div class="timeline-item current">
                                        <div class="timeline-title">Menunggu Update dari Kurir</div>
                                        <div class="timeline-date">Resi: ${d.tracking_number}</div>
                                    </div>
                                    <div class="timeline-item completed">
                                        <div class="timeline-title">Pesanan Disetujui</div>
                                        <div class="timeline-date">${dateApproved}</div>
                                    </div>
                                `;
                            }
                            
                            historyHtml += '</div>';
                            $('#tracking-content').html(historyHtml);
                        },
                        error: function() {
                            $('#tracking-content').html('<span style="color: #ef4444; font-size: 13px;">Gagal mengambil status pelacakan. Pastikan Nomor Resi dan Kurir sesuai.</span>');
                        }
                    });
                }

            } else if (d.status === 'REJECTED') {
                $('#section-rejected-info').show();
                $('#footer-actions-approved').show();
                
                $('#lbl-reject-reason').text(d.reject_reason || 'Tidak ada alasan penolakan yang dicantumkan.');
                
            } else {
                $('#section-form-update').show();
                $('#footer-actions-form').show();
                
                $('#off-courier').val(d.courier || '');
                $('#off-tracking').val(d.tracking_number || '');
                $('#off-shipping-cost').val(d.shipping_cost ? Math.floor(d.shipping_cost) : '');
            }

            openOffcanvas('detailRequestsampleOffcanvas');
        });
    });
</script>
@endpush