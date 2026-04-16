<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="primary" class="btn-edit" size="sm"
        data-id="{{ $row->id }}" 
        data-name="{{ $row->name }}" 
        data-role="{{ $row->role }}"
        title="Edit Role">
        <x-atoms.icon name="edit" style="width: 14px; height: 14px;" />
    </x-atoms.button>

    <x-atoms.button variant="danger" class="btn-delete" size="sm"
        data-id="{{ $row->id }}" 
        data-name="{{ $row->name }}"
        title="Hapus Pengguna">
        <x-atoms.icon name="trash" style="width: 14px; height: 14px;" />
    </x-atoms.button>
</div>