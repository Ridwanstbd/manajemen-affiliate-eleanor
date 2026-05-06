@props([
    'title' => null, 
    'description' => null, 
    'linkText' => null, 
    'linkHref' => '#', 
    'icon' => null
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    
    @if($title || $description || $linkText || $icon || isset($headerAction))
        <div class="card-header-wrap" style="display: flex; justify-content: space-between; align-items: {{ $description ? 'flex-start' : 'center' }}; margin-bottom: 24px;">
            <div class="card-title-area">
                @if($title)
                    <h3 class="card-title" style="font-size: 18px; font-weight: 700; margin: 0; color: var(--text-primary);">{{ $title }}</h3>
                @endif
                
                @if($description)
                    <p class="card-subtitle" style="margin-top: 4px; font-size: 13px; color: var(--text-secondary);">{{ $description }}</p>
                @endif
            </div>

            <div class="card-action-area" style="display: flex; align-items: center; gap: 12px;">
                @if($linkText)
                    <a href="{{ $linkHref }}" style="font-size: 13px; color: var(--primary-blue, #3b82f6); font-weight: 600; text-decoration: none; transition: opacity 0.2s;">
                        {{ $linkText }}
                    </a>
                @endif

                @if($icon)
                    <x-atoms.icon name="{{ $icon }}" style="width: 20px; height: 20px; color: var(--text-secondary); cursor: pointer;" />
                @endif

                {{ $headerAction ?? '' }}
            </div>
        </div>
    @endif

    <div class="card-body" style="padding: 0;">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer" style="margin-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 16px;">
            {{ $footer }}
        </div>
    @endisset
</div>