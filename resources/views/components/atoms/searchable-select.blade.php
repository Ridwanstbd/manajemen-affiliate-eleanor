@props([
    'id' => 'custom-select-' . uniqid(),
    'name' => '',
    'label' => null,
    'placeholder' => '-- Cari & Pilih --',
    'options' => [], 
    'required' => false
])

<div {{ $attributes->merge(['class' => 'custom-searchable-select-container']) }} 
     style="position: relative; width: 100%;" 
     id="{{ $id }}-container">
    
    @if($label)
        <x-atoms.label :value="$label" />
    @endif

    <input type="hidden" name="{{ $name }}" id="{{ $id }}-hidden-input" {{ $required ? 'required' : '' }}>

    <div id="{{ $id }}-trigger" class="form-control" 
         style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: rgba(241, 245, 249, 0.5); min-height: 42px;" 
         onclick="toggleSearchableSelect('{{ $id }}')">
        <span id="{{ $id }}-trigger-text" style="color: var(--text-secondary); font-size: 13.5px;">{{ $placeholder }}</span>
        <x-atoms.icon name="chevron-down" style="width: 16px; height: 16px; color: var(--text-tertiary);" />
    </div>

    <div id="{{ $id }}-dropdown" 
         style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; margin-top: 8px; background: #ffffff; border: 1px solid var(--glass-border); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); padding: 12px;">

        <div style="margin-bottom: 12px;">
            <input type="text" id="{{ $id }}-search-input" 
                   placeholder="Ketik untuk mencari..." 
                   onkeyup="filterSearchableOptions('{{ $id }}')" 
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--glass-border); border-radius: 8px; background: #f8fafc; color: var(--text-primary); font-size: 13px; outline: none;">
        </div>

        <div id="{{ $id }}-list" style="max-height: 200px; overflow-y: auto; scrollbar-width: thin;">
            @foreach($options as $option)
                <div class="search-option-item" 
                     data-text="{{ strtolower($option['text'] . ' ' . ($option['subtext'] ?? '')) }}"
                     onclick="selectSearchableOption('{{ $id }}', '{{ $option['id'] }}', '{{ $option['text'] }}')" 
                     style="padding: 10px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s; font-size: 13.5px; color: var(--text-primary); margin-bottom: 2px;"
                     onmouseover="this.style.background='var(--primary-blue-soft)'" 
                     onmouseout="this.style.background='transparent'">
                    
                    <div style="font-weight: 600;">{{ $option['text'] }}</div>
                    @if(isset($option['subtext']))
                        <div style="font-size: 11px; color: var(--text-tertiary);">{{ $option['subtext'] }}</div>
                    @endif
                </div>
            @endforeach
            
            <div id="{{ $id }}-no-result" style="display: none; font-size: 13px; color: var(--text-tertiary); text-align: center; padding: 20px 0;">
                Data tidak ditemukan
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function toggleSearchableSelect(id) {
        const dropdown = document.getElementById(`${id}-dropdown`);
        const isOpen = dropdown.style.display === 'block';
        
        document.querySelectorAll('[id$="-dropdown"]').forEach(el => el.style.display = 'none');
        
        if (!isOpen) {
            dropdown.style.display = 'block';
            document.getElementById(`${id}-search-input`).focus();
        }
    }

    function selectSearchableOption(id, value, text) {
        document.getElementById(`${id}-hidden-input`).value = value;
        const triggerText = document.getElementById(`${id}-trigger-text`);
        triggerText.innerText = text;
        triggerText.style.color = 'var(--text-primary)';
        document.getElementById(`${id}-dropdown`).style.display = 'none';
    }

    function filterSearchableOptions(id) {
        const input = document.getElementById(`${id}-search-input`).value.toLowerCase();
        const container = document.getElementById(`${id}-list`);
        const items = container.querySelectorAll('.search-option-item');
        let hasVisible = false;

        items.forEach(item => {
            const text = item.getAttribute('data-text');
            if (text.includes(input)) {
                item.style.display = 'block';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById(`${id}-no-result`).style.display = hasVisible ? 'none' : 'block';
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-searchable-select-container')) {
            document.querySelectorAll('[id$="-dropdown"]').forEach(el => el.style.display = 'none');
        }
    });
</script>
@endpush
@endonce