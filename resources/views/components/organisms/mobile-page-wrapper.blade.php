@props([
    'title' => null, 
    'subtitle' => null,
])
<div>
    <div class="header-section-mobile">
        <h1 class="page-title-mobile">{{ $title }}</h1>
        <p class="page-subtitle-mobile">{{ $subtitle }}</p>
    </div>
    {{ $slot }}
</div>