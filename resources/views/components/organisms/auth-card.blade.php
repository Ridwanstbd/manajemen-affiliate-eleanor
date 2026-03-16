@props(['title', 'subtitle', 'useIcon' => false, 'iconSvg' => null])

<div class="auth-card">
    <div class="auth-header">
        
        @if($useIcon)
            <div class="brand-icon" style="margin: 0 auto 24px auto;">
                {!! $iconSvg !!}
            </div>
        @else
            <div class="auth-brand">
                <div class="brand-icon">L</div>
                <span class="brand-text">LedgerFlow</span>
            </div>
        @endif
        
        <h1 class="auth-title">{{ $title }}</h1>
        <p class="auth-subtitle">{{ $subtitle }}</p>
    </div>

    {{ $slot }}

    @isset($footer)
        <div class="auth-footer">
            {{ $footer }}
        </div>
    @endisset
</div>