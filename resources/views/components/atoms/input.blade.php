@props(['disabled' => false, 'type' => 'text'])

@php
    $baseClass = 'form-control';
    if ($type === 'file') {
        $baseClass .= ' form-control-file';
    }
@endphp

<input {{ $disabled ? 'disabled' : '' }} type="{{ $type }}" {{ $attributes->merge(['class' => $baseClass]) }}>