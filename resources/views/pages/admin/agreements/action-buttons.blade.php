<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="outline" size="sm" type="button" 
        onclick="openEditModal('{{ $row->id }}', '{{ base64_encode(rawurlencode($row->content)) }}', '{{ $row->is_active ? 1 : 0 }}', '{{ $row->is_kol ? 1 : 0 }}')">
        Edit
    </x-atoms.button>
    <x-atoms.button variant="outline" size="sm" type="button" style="border-color: var(--rose); color: var(--rose);" 
        onclick="deleteAgreement('{{ $row->id }}')">
        Hapus
    </x-atoms.button>
</div>