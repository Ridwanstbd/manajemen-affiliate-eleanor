@props(['headers' => []])

<div class="table-responsive">
    <table class="glass-table">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th style="text-align: {{ $header['align'] ?? 'left' }}">{{ $header['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
