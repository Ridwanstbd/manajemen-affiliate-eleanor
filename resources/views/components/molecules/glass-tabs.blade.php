<div class="glass-tabs-container">
    <div class="glass-tabs">
        {{ $slot }}
    </div>
    @isset($actions)
        <div class="glass-tabs-actions">
            {{ $actions }}
        </div>
    @endisset
</div>
