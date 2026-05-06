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

<style>
    .glass-table {
        width: 100%;
        text-align: left;
        border-collapse: collapse;
        font-size: 14px;
    }
    .glass-table th {
        padding: 12px 8px;
        font-weight: 600;
        border-bottom: 1px solid var(--glass-border, rgba(0,0,0,0.1));
        color: var(--text-secondary);
        font-size: 12px;
        text-transform: uppercase;
    }
    .glass-table td {
        padding: 16px 8px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: background-color 0.2s ease;
    }
    .glass-table tbody tr:hover td {
        background-color: rgba(0,0,0,0.02);
    }
</style>