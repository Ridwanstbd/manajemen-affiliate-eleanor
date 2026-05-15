@props([
    'link' => '#',
    'tag' => null,
    'title' => '',
    'description' => '',
    'icon' => 'gift',
    'banner' => null, 
    'gradient' => 'linear-gradient(135deg, var(--primary-blue), #60a5fa)'
])

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'carousel-slide']) }}>
    <div class="banner-inner" style="
        background: {{ $banner ? "url('$banner') center/cover no-repeat" : $gradient }};
    ">
        @if($banner)
            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(15, 23, 42, 0.8), transparent); z-index: 1;"></div>
        @endif
        
        <div class="banner-ornament-circle"></div>
        
        <div class="banner-ornament-icon">
            <x-atoms.icon :name="$icon" />
        </div>

        <div class="banner-content">
            @if($tag)
                <span class="banner-tag">
                    {{ $tag }}
                </span>
            @endif

            <h3 class="banner-title">
                {{ $title }}
            </h3>
            
            <p class="banner-description">
                {{ $description }}
            </p>
            
            <span class="banner-btn">
                Lihat Detail <x-atoms.icon name="chevron-right" style="width: 14px; height: 14px;" />
            </span>
        </div>
    </div>
</a>