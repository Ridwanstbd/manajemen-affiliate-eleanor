@extends('layouts.app')
@section('title', 'Manajemen Persetujuan')

@section('content')
<x-molecules.card title="Manajemen Persetujuan">
    <x-slot name="headerAction">
        <x-atoms.button variant="primary" onclick="openCreateModal()">
            <x-atoms.icon name="plus" style="width: 16px; height: 16px; margin-right: 6px;" />
            Tambah Persetujuan
        </x-atoms.button>
    </x-slot>

    <div class="tab-content">
        <x-organisms.datatables id="agreementsTable" 
            url="{{ route('admin-dashboard.agreements.data') }}" 
            :columns="[
                ['data' => 'content', 'title' => 'KONTEN PERSETUJUAN (S&K)'],
                ['data' => 'status', 'title' => 'Status'],
                ['data' => 'updated_at', 'title' => 'Terakhir diperbarui'],
                ['data' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false]
            ]" />
    </div>
</x-molecules.card>

<x-organisms.modal id="modalCreateAgreement" title="Tambah Persetujuan Baru">
    <form action="{{ route('admin-dashboard.agreements.store') }}" method="POST">
        @csrf
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Konten / Isi" />
            <textarea name="content" rows="5" class="form-control" 
                style="width: 100%; border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px;" required></textarea>
        </div>

        <div style="margin-bottom: 24px;">
            <x-atoms.label value="Status" />
            <x-atoms.select name="is_active">
                <option value="1">Aktif</option>
                <option value="0">Non-Aktif</option>
            </x-atoms.select>
        </div>

        <x-atoms.button type="submit" variant="primary" style="width: 100%;">Simpan Persetujuan</x-atoms.button>
    </form>
</x-organisms.modal>


<x-organisms.modal id="modalEditAgreement" title="Ubah Persetujuan">
    <form id="formEditAgreement" method="POST">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 16px;">
            <x-atoms.label value="Konten / Isi" />
            <textarea name="content" id="edit-content" rows="5" class="form-control" 
                style="width: 100%; border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px;" required></textarea>
        </div>

        <div style="margin-bottom: 24px;">
            <x-atoms.label value="Status" />
            <x-atoms.select name="is_active" id="edit-status">
                <option value="1">Aktif</option>
                <option value="0">Non-Aktif</option>
            </x-atoms.select>
        </div>

        <x-atoms.button type="submit" variant="primary" style="width: 100%;">Simpan Perubahan</x-atoms.button>
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

    function openCreateModal() {
        openModal('modalCreateAgreement');
    }

    function openEditModal(button) {
        const id = $(button).data('id');
        const content = $(button).data('content');
        const isActive = $(button).data('status');
        
        $('#formEditAgreement').attr('action', `/dashboard/agreements/${id}`);
        
        $('#edit-content').val(content);
        
        const statusVal = (isActive == 1 || isActive === true || isActive === '1') ? '1' : '0';
        $('#edit-status').val(statusVal);

        openModal('modalEditAgreement');
    }

    function deleteAgreement(id) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.post(`/dashboard/agreements/${id}`, {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            }, function() {
                $('#agreementsTable').DataTable().ajax.reload();
            });
        }
    }
</script>
@endpush