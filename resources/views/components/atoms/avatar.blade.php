@props(['initials' => 'JD'])

<div {{ $attributes->merge(['class' => 'avatar']) }}>
    {{ $initials }}
</div>