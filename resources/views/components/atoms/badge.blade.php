@props([
    'status' => 'paid', // 'paid', 'pending', 'overdue'
])

<span {{ $attributes->merge(['class' => 'status-badge status-' . $status]) }}>
    {{ $slot }}
</span>