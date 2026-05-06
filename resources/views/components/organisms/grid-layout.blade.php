@props(['columns' => '1fr', 'gap' => '20px', 'marginBottom' => '24px'])

<div class="glass-grid-layout" style="--grid-cols: {{ $columns }}; --grid-gap: {{ $gap }}; --grid-mb: {{ $marginBottom }};">
    {{ $slot }}
</div>

<style>
    .glass-grid-layout {
        display: grid;
        grid-template-columns: var(--grid-cols);
        gap: var(--grid-gap);
        margin-bottom: var(--grid-mb);
    }
    @media (max-width: 1024px) {
        .glass-grid-layout {
            grid-template-columns: 1fr; 
        }
    }
</style>