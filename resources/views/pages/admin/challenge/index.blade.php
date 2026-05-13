@extends('layouts.app')
@section('title', 'Kelola Tantangan')
@section('content')

<x-molecules.card title="Daftar Tantangan Affiliator" description="Kelola periode tantangan, syarat ketentuan, dan hadiah untuk Affiliator.">
    <x-slot:headerAction>
        <x-atoms.button type="button" variant="primary" onclick="openOffcanvas('createChallengeOffcanvas')">
            <x-atoms.icon name="plus" style="width: 16px; height: 16px;" /> Tambah Tantangan
        </x-atoms.button>
    </x-slot:headerAction>
    <div class="tab-content">
        <x-organisms.datatables id="challengeTable"
        url="{{ route('admin-dashboard.challenge.data') }}"
        :columns="[
            ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
            ['data' => 'banner', 'name' => 'banner', 'title' => 'Banner'],
            ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
            ['data' => 'period', 'name' => 'period', 'title' => 'Periode'],
            ['data' => 'commission_bonus', 'name' => 'commission_bonus', 'title' => 'Bonus Komisi'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
        ]" />
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="createChallengeOffcanvas" title="Tantangan Baru" md>
    <form id="createChallengeForm" action="{{ route('admin-dashboard.challenge.create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Judul Tantangan" />
            <x-atoms.input type="text" name="title" required placeholder="Contoh: Affiliate Video Challenge" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <x-atoms.label value="Tanggal Mulai" />
                <x-atoms.input type="date" name="start_date" required />
            </div>
            <div>
                <x-atoms.label value="Tanggal Selesai" />
                <x-atoms.input type="date" name="end_date" required />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <x-atoms.label value="Bonus Komisi (Opsional)" />
                <x-atoms.input type="number" name="commission_bonus" placeholder="0" value="0" />
            </div>
            <div>
                <x-atoms.label value="Banner Image" />
                <x-atoms.input type="file" name="banner_image" accept="image/*" />
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Syarat & Ketentuan (Rules)" />
            <textarea name="rules" class="form-control" rows="6" required placeholder="Tuliskan syarat dan ketentuan secara lengkap..."></textarea>
        </div>

        <div style="margin-bottom: 24px; padding: 16px; background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border); border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <x-atoms.typography variant="card-title" style="font-size: 14px;">Tingkatan Hadiah (Rewards)</x-atoms.typography>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addRewardRow('create')">+ Tambah Hadiah</button>
            </div>
            <div id="rewards-container-create">
                </div>
        </div>

        <div id="footer-actions-form" style="display: flex; gap: 12px; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <x-atoms.button type="submit" form="createChallengeForm" variant="primary" style="width: 100%;">
                Simpan Tantangan
            </x-atoms.button>
        </div>
    </form>
</x-organisms.offcanvas>

<x-organisms.offcanvas id="editChallengeOffcanvas" title="Ubah Tantangan" md>
    <form id="editChallengeForm" action="{{ route('admin-dashboard.challenge.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_challenge_id">
        
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Judul Tantangan" />
            <x-atoms.input type="text" name="title" required placeholder="Contoh: Affiliate Video Challenge" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <x-atoms.label value="Tanggal Mulai" />
                <x-atoms.input type="date" name="start_date" required />
            </div>
            <div>
                <x-atoms.label value="Tanggal Selesai" />
                <x-atoms.input type="date" name="end_date" required />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <x-atoms.label value="Bonus Komisi (Opsional)" />
                <x-atoms.input type="number" name="commission_bonus" placeholder="0" value="0" />
            </div>
            <div>
                <x-atoms.label value="Banner Image (Biarkan kosong jika tdk ingin diubah)" />
                <x-atoms.input type="file" name="banner_image" accept="image/*" />
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Syarat & Ketentuan (Rules)" />
            <textarea name="rules" class="form-control" rows="6" required placeholder="Tuliskan syarat dan ketentuan secara lengkap..."></textarea>
        </div>

        <div style="margin-bottom: 24px; padding: 16px; background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border); border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <x-atoms.typography variant="card-title" style="font-size: 14px;">Tingkatan Hadiah (Rewards)</x-atoms.typography>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addRewardRow('edit')">+ Tambah Hadiah</button>
            </div>
            <div id="rewards-container-edit"></div>
        </div>
        <div id="footer-actions-form" style="display: flex; gap: 12px; border-top: 1px solid var(--glass-border, #cbd5e1); padding-top: 24px;">
            <x-atoms.button type="submit" form="editChallengeForm" variant="primary" style="width: 100%;">
                Simpan Perubahan
            </x-atoms.button>
        </div>
    </form>
</x-organisms.offcanvas>

<x-organisms.offcanvas id="detailChallengeOffcanvas" title="Detail Tantangan" md>
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Judul Tantangan" />
            <x-atoms.input type="text" name="title" readonly />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <x-atoms.label value="Tanggal Mulai" />
                <x-atoms.input type="date" name="start_date" readonly />
            </div>
            <div>
                <x-atoms.label value="Tanggal Selesai" />
                <x-atoms.input type="date" name="end_date" readonly />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <x-atoms.label value="Bonus Komisi" />
                <x-atoms.input type="text" name="commission_bonus" readonly />
            </div>
            <div>
                <x-atoms.label value="Banner Image" />
                <div id="detail_banner_container" style="display: none; margin-top: 8px;">
                    <img id="detail_banner_preview" 
                        src="" 
                        style="width: 100%; height: 90px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: cover; cursor: pointer;" 
                        onclick="openLightbox(this.src)"
                        title="Klik untuk memperbesar">
                </div>
                <div id="detail_banner_empty" style="font-size: 13px; color: var(--text-tertiary); margin-top: 8px;">Tidak ada banner</div>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Syarat & Ketentuan (Rules)" />
            <textarea name="rules" class="form-control" rows="6" readonly></textarea>
        </div>

        <div style="margin-bottom: 24px; padding: 16px; background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border); border-radius: 8px;">
            <div style="margin-bottom: 16px;">
                <x-atoms.typography variant="card-title" style="font-size: 14px;">Tingkatan Hadiah (Rewards)</x-atoms.typography>
            </div>
            {{-- ID Diubah menjadi rewards-container-detail --}}
            <div id="rewards-container-detail"></div>
        </div>
</x-organisms.offcanvas>

<x-organisms.modal id="hapusChallengeModal" title="Hapus Challenge">
    <form id="deleteForm" action="{{ route('admin-dashboard.challenge.destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" id="delete_challenge_id">
        
        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">
            Apakah Anda yakin ingin menghapus Challenge ini?<br>
            <strong style="color: #ef4444;">Peringatan:</strong> Data tingkatan hadiah dan daftar pemenang yang berelasi dengan challenge ini akan ikut terhapus secara permanen.
        </p>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('hapusChallengeModal')">Batal</x-atoms.button>
            <x-atoms.button variant="primary" type="submit" form="deleteForm" style="background-color: #ef4444; border-color: #ef4444;">Ya, Hapus</x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

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

    function addRewardRow(formType, metric = 'video_count', value = '', desc = '') {
        const container = document.getElementById(`rewards-container-${formType}`);
        const index = container.children.length;
        
        const row = document.createElement('div');
        row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr 2fr auto; gap: 8px; margin-bottom: 12px; align-items: start;';
        
        const selectedVideo = metric === 'video_count' ? 'selected' : '';
        const selectedGmv = metric === 'gmv' ? 'selected' : '';
        const selectedViews = metric === 'views' ? 'selected' : '';

        row.innerHTML = `
            <select name="rewards[${index}][target_metric]" class="form-control" required style="font-size: 13px;">
                <option value="video_count" ${selectedVideo}>Upload Video</option>
                <option value="gmv" ${selectedGmv}>Pencapaian GMV</option>
                <option value="views" ${selectedViews}>Performa Views</option> </select>
            </select>
            <input type="number" name="rewards[${index}][target_value]" class="form-control" placeholder="Nilai Target" value="${value}" required style="font-size: 13px;">
            <input type="text" name="rewards[${index}][reward_description]" class="form-control" placeholder="Deskripsi Hadiah (Cth: Kaos)" value="${desc}" required style="font-size: 13px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="this.parentElement.remove()" style="padding: 8px 12px; color: #ef4444; border-color: #fca5a5; background: #fef2f2;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        `;
        
        container.appendChild(row);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        addRewardRow('create');
    });

    document.addEventListener('click', function(e) {
        const btnEdit = e.target.closest('.btn-edit');
        if (btnEdit) {
            const rowData = JSON.parse(btnEdit.getAttribute('data-row'));
            const editForm = document.getElementById('editChallengeForm');
            
            editForm.querySelector('[name="id"]').value = rowData.id;
            editForm.querySelector('[name="title"]').value = rowData.title;
            editForm.querySelector('[name="start_date"]').value = rowData.start_date ? rowData.start_date.substring(0, 10) : '';
            editForm.querySelector('[name="end_date"]').value = rowData.end_date ? rowData.end_date.substring(0, 10) : '';
            editForm.querySelector('[name="commission_bonus"]').value = Math.floor(rowData.commission_bonus);
            editForm.querySelector('[name="rules"]').value = rowData.rules;

            const editContainer = document.getElementById('rewards-container-edit');
            editContainer.innerHTML = '';
            if(rowData.rewards && rowData.rewards.length > 0) {
                rowData.rewards.forEach(r => addRewardRow('edit', r.target_metric, r.target_value, r.reward_description));
            } else {
                addRewardRow('edit');
            }

            openOffcanvas('editChallengeOffcanvas');
            btnEdit.closest('.dropdown-menu').style.display = 'none'; 
        }

        const btnDetail = e.target.closest('.btn-detail');
        if (btnDetail) {
            const rowData = JSON.parse(btnDetail.getAttribute('data-row'));
            const detailCanvas = document.getElementById('detailChallengeOffcanvas');
            
            detailCanvas.querySelector('[name="title"]').value = rowData.title;
            detailCanvas.querySelector('[name="start_date"]').value = rowData.start_date ? rowData.start_date.substring(0, 10) : '';
            detailCanvas.querySelector('[name="end_date"]').value = rowData.end_date ? rowData.end_date.substring(0, 10) : '';
            detailCanvas.querySelector('[name="commission_bonus"]').value = "Rp " + new Intl.NumberFormat('id-ID').format(rowData.commission_bonus);
            detailCanvas.querySelector('[name="rules"]').value = rowData.rules;

            const bannerContainer = document.getElementById('detail_banner_container');
            const bannerPreview = document.getElementById('detail_banner_preview');
            const bannerEmpty = document.getElementById('detail_banner_empty');
            
            if (rowData.banner_image_path) {
                bannerPreview.src = "{{ asset('storage') }}/" + rowData.banner_image_path;
                bannerContainer.style.display = 'block';
                bannerEmpty.style.display = 'none';
            } else {
                bannerContainer.style.display = 'none';
                bannerEmpty.style.display = 'block';
            }

            const detailContainer = document.getElementById('rewards-container-detail');
            detailContainer.innerHTML = '';
            if(rowData.rewards && rowData.rewards.length > 0) {
                rowData.rewards.forEach((r) => {
                    let labelMetrik = 'Upload Video';
                        if (r.target_metric === 'gmv') {
                            labelMetrik = 'Pencapaian GMV';
                        } else if (r.target_metric === 'views') {
                            labelMetrik = 'Performa Views';
                        }
                    detailContainer.innerHTML += `
                        <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 8px; margin-bottom: 8px; padding: 12px; background: white; border: 1px solid var(--glass-border); border-radius: 6px;">
                            <div><span style="font-size: 11px; color: var(--text-tertiary); display: block;">Target</span><span style="font-size: 13px; font-weight: 600;">${labelMetrik}</span></div>
                            <div><span style="font-size: 11px; color: var(--text-tertiary); display: block;">Nilai</span><span style="font-size: 13px; font-weight: 600;">${r.target_value}</span></div>
                            <div><span style="font-size: 11px; color: var(--text-tertiary); display: block;">Hadiah</span><span style="font-size: 13px; font-weight: 600;">${r.reward_description}</span></div>
                        </div>
                    `;
                });
            } else {
                detailContainer.innerHTML = '<div style="font-size: 13px; color: var(--text-tertiary);">Belum ada tingkat hadiah yang diatur.</div>';
            }

            openOffcanvas('detailChallengeOffcanvas');
            btnDetail.closest('.dropdown-menu').style.display = 'none'; 
        }

        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            const id = btnDelete.getAttribute('data-id');
            document.getElementById('delete_challenge_id').value = id; 
            openModal('hapusChallengeModal'); 
            btnDelete.closest('.dropdown-menu').style.display = 'none'; 
        }
    });
</script>
@endpush