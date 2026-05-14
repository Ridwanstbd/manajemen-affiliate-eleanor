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
