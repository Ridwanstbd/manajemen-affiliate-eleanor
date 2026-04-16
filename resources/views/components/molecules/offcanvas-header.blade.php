@props(['title', 'target'])

<div class="offcanvas-header">
    <h5 class="offcanvas-title">{{ $title }}</h5>
    <x-atoms.close-button onclick="toggleOffcanvas('{{ $target }}')" />
</div>