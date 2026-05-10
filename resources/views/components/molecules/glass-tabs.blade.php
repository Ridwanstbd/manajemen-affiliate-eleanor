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

<style>
    .glass-tabs-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--glass-border, rgba(0,0,0,0.1));
        margin-bottom: 24px;
        padding-bottom: 8px;
    }
    .glass-tabs-actions {
        display: flex;
        gap: 20px;
    }
    .glass-tabs {
        display: flex;
        gap: 24px;
    }
</style>