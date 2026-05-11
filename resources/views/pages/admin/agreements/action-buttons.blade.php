<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="secondary" size="sm" type="button" 
        data-id="{{ $row->id }}"
        data-content="{{ $row->content }}"
        data-status="{{ $row->is_active }}"
        onclick="openEditModal(this)"
        title="Ubah Persetujuan">
        <x-atoms.icon name="edit" style="width: 14px; height: 14px;" />
    </x-atoms.button>

    <x-atoms.button variant="outline" size="sm" type="button" 
        style="color: var(--danger); border-color: var(--danger);"
        onclick="deleteAgreement('{{ $row->id }}')"
        title="Hapus Persetujuan">
        <x-atoms.icon name="trash" style="width: 14px; height: 14px;" />
    </x-atoms.button>
</div>