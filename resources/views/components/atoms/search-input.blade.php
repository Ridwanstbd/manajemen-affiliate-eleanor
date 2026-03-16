@props(['placeholder' => 'Search...'])

<div class="search-global">
    <x-atoms.icon name="search" class="search-icon" width="16" height="16" />
    <input type="text" class="search-input" placeholder="{{ $placeholder }}" {{ $attributes }}>
</div>