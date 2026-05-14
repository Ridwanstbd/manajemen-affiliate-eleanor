@props(['columns' => '1fr', 'gap' => '20px', 'marginBottom' => '24px'])

<div class="glass-grid-layout" style="--grid-cols: {{ $columns }}; --grid-gap: {{ $gap }}; --grid-mb: {{ $marginBottom }};">
    {{ $slot }}
</div>
