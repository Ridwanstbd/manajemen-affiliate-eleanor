@extends('layouts.app')
@section('title', 'Import Data Analitik')

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <x-molecules.card title="Import Data Analitik" description="Unggah 5 file Excel (.xlsx) sekaligus. Pastikan nama file dan rentang tanggal sesuai dengan standar export.">
        
        <form id="importForm" action="{{ url('/dashboard/import-data') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="file_core_metrics" id="input_core_metrics" accept=".xlsx" style="display: none;">
            <input type="file" name="file_creator_list" id="input_creator_list" accept=".xlsx" style="display: none;">
            <input type="file" name="file_live_list" id="input_live_list" accept=".xlsx" style="display: none;">
            <input type="file" name="file_product_list" id="input_product_list" accept=".xlsx" style="display: none;">
            <input type="file" name="file_video_list" id="input_video_list" accept=".xlsx" style="display: none;">
            
            <div class="dropzone-container">
                <div class="dropzone" id="dropzone">
                    <div class="dropzone-icon">
                        <x-atoms.icon name="download" style="width: 32px; height: 32px;" />
                    </div>
                    <div class="dropzone-text">Tarik & Lepas Kelima File Di Sini</div>
                    <div class="dropzone-subtext">Atau klik area ini untuk memilih file dari komputer Anda</div>
                    
                    <input type="file" id="file_selector" multiple accept=".xlsx" style="display: none;">
                </div>

                <div>
                    <x-atoms.typography variant="nav-label" style="margin-bottom: 12px;">Status File yang Dibutuhkan</x-atoms.typography>
                    <div class="file-requirements">
                        
                        <div class="file-item" id="item_core_metrics">
                            <div class="file-item-info">
                                <div class="file-icon"><x-atoms.icon name="reports" style="width: 18px; height: 18px;" /></div>
                                <div>
                                    <div class="file-name-req">Core Metrics</div>
                                    <div class="file-status" id="status_core_metrics">Menunggu file...</div>
                                </div>
                            </div>
                        </div>

                        <div class="file-item" id="item_creator_list">
                            <div class="file-item-info">
                                <div class="file-icon"><x-atoms.icon name="team" style="width: 18px; height: 18px;" /></div>
                                <div>
                                    <div class="file-name-req">Creator List</div>
                                    <div class="file-status" id="status_creator_list">Menunggu file...</div>
                                </div>
                            </div>
                        </div>

                        <div class="file-item" id="item_live_list">
                            <div class="file-item-info">
                                <div class="file-icon"><x-atoms.icon name="eye" style="width: 18px; height: 18px;" /></div>
                                <div>
                                    <div class="file-name-req">Live List</div>
                                    <div class="file-status" id="status_live_list">Menunggu file...</div>
                                </div>
                            </div>
                        </div>

                        <div class="file-item" id="item_product_list">
                            <div class="file-item-info">
                                <div class="file-icon"><x-atoms.icon name="revenue" style="width: 18px; height: 18px;" /></div>
                                <div>
                                    <div class="file-name-req">Product List</div>
                                    <div class="file-status" id="status_product_list">Menunggu file...</div>
                                </div>
                            </div>
                        </div>

                        <div class="file-item" id="item_video_list">
                            <div class="file-item-info">
                                <div class="file-icon"><x-atoms.icon name="journal" style="width: 18px; height: 18px;" /></div>
                                <div>
                                    <div class="file-name-req">Video List</div>
                                    <div class="file-status" id="status_video_list">Menunggu file...</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="form-actions">
                <x-atoms.button type="button" variant="secondary" style="margin-right: 12px;" onclick="window.location.reload()">Reset</x-atoms.button>
                <x-atoms.button type="submit" variant="primary" id="btnSubmit" disabled>Mulai Import Data</x-atoms.button>
            </div>
        </form>
    </x-molecules.card>
    <x-molecules.card title="Riwayat Import" description="Daftar batch import analitik yang pernah dilakukan.">
        @php
            $tableColumns = [
                ['data' => 'admin_name', 'name' => 'admin_name', 'title' => 'Admin', 'orderable' => false],
                ['data' => 'import_date', 'name' => 'import_date', 'title' => 'Tanggal Import', 'width' => '120px'],
                ['data' => 'start_date', 'name' => 'start_date', 'title' => 'Tgl Mulai'],
                ['data' => 'end_date', 'name' => 'end_date', 'title' => 'Tgl Akhir'],
            ];
        @endphp
        
        <x-organisms.datatables 
            id="importHistoryTable" 
            url="{{ route('admin-dashboard.import-data') }}" 
            :columns="$tableColumns" 
        />
    </x-molecules.card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileSelector = document.getElementById('file_selector');
    const btnSubmit = document.getElementById('btnSubmit');

    const expectedPrefixes = {
        'Transaction_Analysis_Core_Metrics_': 'core_metrics',
        'Transaction_Analysis_Creator_List_': 'creator_list',
        'Transaction_Analysis_Live_List_': 'live_list',
        'Transaction_Analysis_Product_List_': 'product_list',
        'Transaction_Analysis_Video_List_': 'video_list'
    };

    let uploadedFilesCount = 0;

    dropzone.addEventListener('click', () => fileSelector.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    fileSelector.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (!file.name.endsWith('.xlsx')) return; 

            for (const [prefix, idKey] of Object.entries(expectedPrefixes)) {
                if (file.name.startsWith(prefix)) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('input_' + idKey).files = dt.files;
                    
                    updateStatusUI(idKey, file.name);
                    break; 
                }
            }
        });
        
        checkReadyStatus();
    }

    function updateStatusUI(idKey, filename) {
        const statusEl = document.getElementById('status_' + idKey);
        const itemEl = document.getElementById('item_' + idKey);
        
        if(statusEl && !itemEl.classList.contains('ready')) {
            statusEl.innerHTML = `<span class="file-status success"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> Terlampir</span>`;
            statusEl.title = filename; 
            itemEl.classList.add('ready');
            uploadedFilesCount++;
        }
    }

    function checkReadyStatus() {
        if (uploadedFilesCount === 5) {
            btnSubmit.removeAttribute('disabled');
        }
    }
    
    document.getElementById('importForm').addEventListener('submit', function() {
        btnSubmit.innerHTML = `
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Memproses...
        `;
        btnSubmit.setAttribute('disabled', 'true');
    });
});
</script>
<style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endpush
@endsection