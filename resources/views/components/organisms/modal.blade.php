@props(['id', 'title' => '', 'description' => ''])

<div class="modal-overlay" id="{{ $id }}">
    <div class="modal-container">
        
        <div class="modal-header">
            <div>
                <x-atoms.typography variant="card-title">{{ $title }}</x-atoms.typography>
                @if($description)
                    <p class="card-subtitle" style="margin-top: 4px; font-size: 13px; color: var(--text-secondary);">
                        {{ $description }}
                    </p>
                @endif
            </div>
            <button type="button" class="modal-close" onclick="closeModal('{{ $id }}')">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="modal-body">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="modal-footer">
                {{ $footer }}
            </div>
        @endisset
        
    </div>
</div>