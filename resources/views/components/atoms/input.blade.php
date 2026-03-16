@props(['disabled' => false, 'type' => 'text'])

<input {{ $disabled ? 'disabled' : '' }} type="{{ $type }}" {{ $attributes->merge(['class' => 'form-control']) }}>