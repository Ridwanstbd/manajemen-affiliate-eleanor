<div style="display: flex; gap: 8px;">
    <x-molecules.dropdown>
            <x-slot:trigger>
                <x-atoms.button variant="primary" class="btn-more" size="sm" title="More">
                    <x-atoms.icon name="more-vertical" style="width: 14px; height: 14px;" />
                </x-atoms.button>
            </x-slot:trigger>

            <x-atoms.dropdown-item class="btn-detail" >
                    <x-atoms.icon name="eye" style="width: 14px; height: 14px;" />
                    Lihat Detail
            </x-atoms.dropdown-item>
             <x-atoms.dropdown-item class="btn-winner" >
                    <x-atoms.icon name="medal" style="width: 14px; height: 14px;" />
                    Kelola Pemenang
            </x-atoms.dropdown-item>
            <x-atoms.dropdown-item class="btn-edit" >
                    <x-atoms.icon name="edit" style="width: 14px; height: 14px;" />
                    Edit
            </x-atoms.dropdown-item>
            <x-atoms.dropdown-item class="btn-delete" >
                    <x-atoms.icon name="trash" style="width: 14px; height: 14px;" />
                    Hapus
            </x-atoms.dropdown-item>
                        
        </x-molecules.dropdown>
</div>