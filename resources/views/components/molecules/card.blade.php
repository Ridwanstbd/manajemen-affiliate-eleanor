@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    
    @if($title || $description)
        <div class="card-header-wrap">
            @if($title)
                <x-atoms.typography variant="hero-title">{{ $title }}</x-atoms.typography>
            @endif
            
            @if($description)
                <p class="hero-subtitle">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
    
</div>