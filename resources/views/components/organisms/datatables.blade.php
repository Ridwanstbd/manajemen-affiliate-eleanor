@props([
    'id',
    'url',
    'columns'=>[]
])

<div class="table-responsive">
    <table id="{{ $id }}" class="table table-stripe table-hover table-bordered w-100 display responsive nowrap">
        <thead>
            <tr>
                @foreach ($columns as $col)
                <th>{{ $col['title'] ?? ucfirst($col['data'])}}</th>
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
            responsive: true,  
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
                    previous: "<",
                    next: ">"}
            },
        });
    });
</script>
@endpush