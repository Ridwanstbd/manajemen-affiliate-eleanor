<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="primary" class="btn-edit" size="sm"
        data-id="{{ (string) $row->id }}" 
        data-name="{{ htmlspecialchars($row->name ?? '') }}" 
        data-price="{{ $row->price ?? 0 }}" 
        data-stock="{{ $row->stock ?? 0 }}" 
        data-video-count="{{ $row->mandatory_video_count ?? 3 }}"
        title="Edit Produk">
        <x-atoms.icon name="edit" style="width: 14px; height: 14px;" />
    </x-atoms.button>
</div>