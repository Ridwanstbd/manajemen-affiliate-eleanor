<div class="task-all-wrapper">
    <div id="task-infinite-container">
        @if($data->count() > 0)
            @include('pages.affiliator.task.all.partials.items', ['data' => $data])
        @else
            <div style="text-align: center; padding: 50px 20px; color: var(--text-secondary);">
                <x-atoms.icon name="bell" style="width: 48px; height: 48px; color: var(--text-tertiary); margin-bottom: 16px; opacity: 0.5;"/>
                <p>Luar biasa! Tidak ada tugas atau kewajiban video yang tersisa saat ini.</p>
            </div>
        @endif
    </div>

    <div id="task-loading-spinner" style="display: none; text-align: center; padding: 20px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin-animation" style="color: var(--primary-blue);">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
        </svg>
    </div>
</div>