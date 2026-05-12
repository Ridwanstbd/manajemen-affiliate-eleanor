<div style="display: flex; gap: 8px;">
    <x-atoms.button 
        variant="outline" 
        size="sm" 
        type="button" 
        class="btn-detail"
        data-row="{{ base64_encode(json_encode($row)) }}" 
        title="Detail Tugas">
        Detail
    </x-atoms.button>
</div>