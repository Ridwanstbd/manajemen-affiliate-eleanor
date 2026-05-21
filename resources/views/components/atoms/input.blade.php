@props(['disabled' => false, 'type' => 'text'])

@php
    $baseClass = 'form-control';
    if ($type === 'file') {
        $baseClass .= ' form-control-file';
    }
@endphp

@if($type === 'password')
    <div style="position: relative; display: flex; align-items: center; width: 100%;">
        <input {{ $disabled ? 'disabled' : '' }} type="password" {{ $attributes->merge(['class' => $baseClass]) }} style="padding-right: 40px;">
        <button type="button" 
                onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.style.color = input.type === 'text' ? '#3b82f6' : '#64748b';" 
                style="position: absolute; right: 12px; background: transparent; border: none; padding: 0; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; transition: color 0.2s;"
                title="Lihat/Sembunyikan Password">
            <x-atoms.icon name="eye" style="width: 18px; height: 18px;" />
        </button>
    </div>
@else
    <input {{ $disabled ? 'disabled' : '' }} type="{{ $type }}" {{ $attributes->merge(['class' => $baseClass]) }}>
@endif