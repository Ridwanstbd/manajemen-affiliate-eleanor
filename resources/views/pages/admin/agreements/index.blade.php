@extends('layouts.app')
@section('title', 'Manajemen Peraturan')

@section('content')
<x-molecules.card title="Manajemen Peraturan" description="Kelola syarat dan ketentuan pengajuan sampel untuk pengguna (Global).">
    <x-slot name="headerAction">
        <x-atoms.button variant="primary" onclick="openCreateModal()">
            <x-atoms.icon name="plus" style="width: 18px; height: 18px;" /> Tambah Persetujuan
        </x-atoms.button>
    </x-slot>

    <div class="tab-content">
        <x-organisms.datatables id="agreementsTable" url="{{ route('admin-dashboard.agreements.data') }}"
            :columns="[
                ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
                ['data' => 'target', 'name' => 'target', 'title' => 'Target Pengguna', 'orderable' => false],
                ['data' => 'content', 'name' => 'content', 'title' => 'Isi Persetujuan'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Diperbarui', 'searchable' => false],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'searchable' => false],
                ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'width' => '120px'],
            ]"/>
    </div>
</x-molecules.card>

<x-organisms.offcanvas id="offcanvasAgreement" title="Form Persetujuan Global">
    <form id="formAgreement" method="POST" style="padding: 24px; padding-top: 0;">
        @csrf
        <div id="method-container"></div>
        
        <div style="margin-bottom: 24px;">
            <x-atoms.label value="Isi Syarat & Ketentuan" />
            <textarea name="content" id="form-content" class="form-control" rows="8" placeholder="Tuliskan syarat & ketentuan..." required style="width: 100%; border-radius: 8px; border: 1px solid var(--glass-border); padding: 12px; font-size: 13px; font-family: inherit; resize: vertical;"></textarea>
        </div>

        <div style="display: flex; gap: 24px; margin-bottom: 24px; background: rgba(0,0,0,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <x-molecules.toggle name="is_active" id="form-is-active" />
                <div>
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-primary); display: block;">Status Aktif</span>
                    <span style="font-size: 11px; color: var(--text-secondary);">Terapkan aturan ini</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <x-molecules.toggle name="is_kol" id="form-is-kol" />
                <div>
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-primary); display: block;">Khusus KOL</span>
                    <span style="font-size: 11px; color: var(--text-secondary);">Hanya tampil bagi KOL</span>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px; border-top: 1px solid var(--glass-border); padding-top: 16px;">
            <x-atoms.button type="submit" variant="primary" style="width: 100%;">Simpan Persetujuan</x-atoms.button>
        </div>
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

    function openCreateModal() {
        const form = document.getElementById('formAgreement');
        form.action = "{{ route('admin-dashboard.agreements.store') }}";
        document.getElementById('method-container').innerHTML = '';
        
        document.getElementById('form-content').value = '';
        document.getElementById('form-is-active').checked = true;
        document.getElementById('form-is-kol').checked = false;
        
        openOffcanvas('offcanvasAgreement');
    }

    function openEditModal(id, contentBase64, isActive, isKol) {
        const form = document.getElementById('formAgreement');
        let url = "{{ route('admin-dashboard.agreements.update', ':id') }}";
        form.action = url.replace(':id', id);
        
        document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('form-content').value = decodeURIComponent(escape(atob(contentBase64)));
        document.getElementById('form-is-active').checked = isActive == '1';
        document.getElementById('form-is-kol').checked = isKol == '1';
        
        openOffcanvas('offcanvasAgreement');
    }

    function deleteAgreement(id) {
        if (confirm('Apakah Anda yakin ingin menghapus persetujuan ini?')) {
            let url = "{{ route('admin-dashboard.agreements.destroy', ':id') }}";
            url = url.replace(':id', id);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $('#agreementsTable').DataTable().ajax.reload();
                } else {
                    alert('Gagal menghapus data.');
                }
            });
        }
    }
</script>
@endpush
@endsection