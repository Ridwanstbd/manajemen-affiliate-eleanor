@props([
    'id',
    'url',
    'columns'=>[]
])

<div class="glass-datatable-wrapper">
    <table id="{{ $id }}" class="w-100 display ">
        <thead>
            <tr>
                @foreach ($columns as $col)
                <th @isset($col['width']) style="width: {{ $col['width'] }}; min-width: {{ $col['width'] }}; white-space: normal;" @endisset>
                    {{ $col['title'] ?? ucfirst($col['data'])}}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ($.fn.DataTable.isDataTable('#{{ $id }}')) {
            $('#{{ $id }}').DataTable().destroy();
        }

        $('#{{ $id }}').DataTable({
            processing: true,  
            serverSide: true,  
            responsive: false,
            scrollX: true,  
            autoWidth: false, 
            ajax: '{!! $url !!}',
            columns: @json($columns), 
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampil _MENU_ data",
                info: "_START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "0 - 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                loadingRecords: "",
                zeroRecords: "Data tidak ditemukan",
                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                paginate: {
                    first: `<x-atoms.icon name="chevron-first" style="width: 18px; height: 18px; vertical-align: -4px;" />`,
                    last: `<x-atoms.icon name="chevron-last" style="width: 18px; height: 18px; vertical-align: -4px;" />`,
                    previous: `<x-atoms.icon name="chevron-left" style="width: 18px; height: 18px; vertical-align: -4px;" />`,
                    next: `<x-atoms.icon name="chevron-right" style="width: 18px; height: 18px; vertical-align: -4px;" />`
                }
            },
        });
    });
</script>
@endpush