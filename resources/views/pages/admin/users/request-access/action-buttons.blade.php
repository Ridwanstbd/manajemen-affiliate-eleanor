<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="outline" size="sm" type="button" class="btn-detail"
        data-id="{{ (string) $row->id }}"
        data-username="{{ $row->tiktok_username }}"
        data-email="{{ $row->email }}"
        data-phone="{{ $row->phone_number }}"
        title="Detail Pengajuan">
        Detail
    </x-atoms.button>
</div>