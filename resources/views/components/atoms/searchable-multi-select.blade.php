@props([
    'id' => 'multi-select-' . uniqid(),
    'name' => '',
    'label' => null,
    'placeholder' => '-- Pilih Opsi --',
    'options' => [] 
])

<div {{ $attributes->merge(['class' => 'custom-multi-select-container']) }} 
     style="position: relative; width: 100%;" 
     id="{{ $id }}-container">
    
    @if($label)
        <x-atoms.label :value="$label" />
    @endif

    <div id="{{ $id }}-trigger" class="form-control" 
         style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: rgba(241, 245, 249, 0.5); min-height: 42px;" 
         onclick="toggleMultiSelect('{{ $id }}')">
        <span id="{{ $id }}-trigger-text" style="color: var(--text-secondary); font-size: 13.5px;">{{ $placeholder }}</span>
        <x-atoms.icon name="chevron-down" style="width: 16px; height: 16px; color: var(--text-tertiary);" />
    </div>

    <div id="{{ $id }}-dropdown" 
         style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; margin-top: 8px; background: #ffffff; border: 1px solid var(--glass-border); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); padding: 12px;">

        <div style="margin-bottom: 12px;">
            <input type="text" id="{{ $id }}-search-input" 
                   placeholder="Ketik untuk mencari..." 
                   onkeyup="filterMultiOptions('{{ $id }}')" 
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--glass-border); border-radius: 8px; background: #f8fafc; color: var(--text-primary); font-size: 13px; outline: none;">
        </div>

        <div id="{{ $id }}-list" style="max-height: 200px; overflow-y: auto; scrollbar-width: thin;">
            @foreach($options as $option)
                <div class="multi-option-item" 
                     data-text="{{ strtolower($option['text']) }}"
                     style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s; font-size: 13.5px; color: var(--text-primary); margin-bottom: 2px;"
                     onmouseover="this.style.background='var(--primary-blue-soft)'" 
                     onmouseout="this.style.background='transparent'">
                    
                    <input type="checkbox" 
                           name="{{ $name }}[]" 
                           value="{{ $option['id'] }}" 
                           id="{{ $id }}-opt-{{ $option['id'] }}"
                           onchange="updateMultiSelectText('{{ $id }}', '{{ $placeholder }}')"
                           style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--primary-blue);">
                    
                    <label for="{{ $id }}-opt-{{ $option['id'] }}" style="cursor: pointer; flex: 1; margin: 0; font-weight: 500;">
                        {{ $option['text'] }}
                    </label>
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
    function toggleMultiSelect(id) {
        const dropdown = document.getElementById(`${id}-dropdown`);
        const isOpen = dropdown.style.display === 'block';
        
        document.querySelectorAll('.custom-multi-select-container [id$="-dropdown"]').forEach(el => {
            el.style.display = 'none';
        });
        
        if (!isOpen) {
            dropdown.style.display = 'block';
            document.getElementById(`${id}-search-input`).focus();
        }
    }

    function updateMultiSelectText(id, placeholder) {
        const container = document.getElementById(`${id}-list`);
        const checkedBoxes = container.querySelectorAll(`input[type="checkbox"]:checked`);
        const triggerText = document.getElementById(`${id}-trigger-text`);

        if (checkedBoxes.length > 0) {
            triggerText.innerText = `${checkedBoxes.length} Produk Dipilih`;
            triggerText.style.color = 'var(--text-primary)';
            triggerText.style.fontWeight = '600';
        } else {
            triggerText.innerText = placeholder;
            triggerText.style.color = 'var(--text-secondary)';
            triggerText.style.fontWeight = 'normal';
        }
    }

    function filterMultiOptions(id) {
        const input = document.getElementById(`${id}-search-input`).value.toLowerCase();
        const container = document.getElementById(`${id}-list`);
        const items = container.querySelectorAll('.multi-option-item');
        let hasVisible = false;

        items.forEach(item => {
            const text = item.getAttribute('data-text');
            if (text.includes(input)) {
                item.style.display = 'flex';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById(`${id}-no-result`).style.display = hasVisible ? 'none' : 'block';
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-multi-select-container')) {
            document.querySelectorAll('.custom-multi-select-container [id$="-dropdown"]').forEach(el => el.style.display = 'none');
        }
    });
</script>
@endpush
@endonce