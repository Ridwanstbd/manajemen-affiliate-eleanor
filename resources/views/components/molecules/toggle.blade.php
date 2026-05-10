@props([
    'id', 
    'label' => '', 
    'checked' => false, 
    'disabled' => false, 
    'name' => ''
])

<div class="toggle-wrapper">
    @if($label)
        <label for="{{ $id }}" class="toggle-label">{{ $label }}</label>
    @endif
    
    <label class="toggle-switch">
        <input 
            type="checkbox" 
            id="{{ $id }}" 
            name="{{ $name }}"
            {{ $checked ? 'checked' : '' }} 
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'toggle-input']) }}
        >
        <span class="toggle-slider"></span>
    </label>
</div>

<style>
    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 12px; 
    }
    .toggle-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary, #0f172a);
        cursor: pointer;
        user-select: none;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .toggle-input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 24px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .toggle-input:checked + .toggle-slider {
        background-color: var(--primary-blue, #3b82f6);
    }
    .toggle-input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }
    .toggle-input:disabled + .toggle-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .toggle-wrapper:has(.toggle-input:disabled) .toggle-label {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>