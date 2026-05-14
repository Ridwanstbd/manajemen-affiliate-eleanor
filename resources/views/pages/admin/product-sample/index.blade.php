@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <x-molecules.glass-tabs>
            <h3 class="card-title">Data Produk</h3>
            <x-slot name="actions">
                <x-atoms.button variant="secondary" onclick="prepareMassEdit()" style="margin-right: 8px;">
                    <x-atoms.icon name="edit" style="width: 16px; height: 16px; margin-right: 8px;" />
                    Edit Massal
                </x-atoms.button>
                <x-atoms.button onclick="openModal('sinkronProdukModal')">
                    Sinkronkan Produk
                </x-atoms.button>
            </x-slot>
        </x-molecules.glass-tabs>
        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @php
                $tableColumns = [
                    ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
                    ['data' => 'image', 'name' => 'image', 'title' => 'Gambar'],
                    ['data' => 'name', 'name' => 'name', 'title' => 'Nama Produk', 'width' => '320px'],
                    ['data' => 'price_formated', 'name' => 'price', 'title' => 'Harga Retail'],
                    ['data' => 'stock', 'name' => 'stock', 'title' => 'Stok'],
                    ['data' => 'seller_sku', 'name' => 'seller_sku', 'title' => 'SKU Produk'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
                ];
            @endphp

            <x-organisms.datatables 
                id="productTable" 
                url="{{ route('admin-dashboard.product-data') }}" 
                :columns="$tableColumns" 
            />
        </div>
    </div>
</div>

<x-organisms.modal 
    id="sinkronProdukModal" 
    title="Sinkron Produk" 
    description="Masukkan informasi dasar untuk sinkron produk."
>
    <form id="importForm" action="{{ route('admin-dashboard.import-product-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="dropzone-container">
            <label class="dropzone" id="dropzone" for="file_selector" style="cursor: pointer; display: block;">
                <div class="dropzone-icon">
                    <x-atoms.icon name="download" style="width: 32px; height: 32px;" />
                </div>
                <div class="dropzone-text" id="dropzone-text">Tarik & Lepas File Di Sini</div>
                <div class="dropzone-subtext" id="dropzone-subtext">Atau klik area ini untuk memilih file dari komputer Anda</div>
                <input type="file" id="file_selector" name="files[]" multiple accept=".xlsx,.xls,.csv" style="display: none;">
            </label>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('sinkronProdukModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit" id="importForm" form="importForm">
                Simpan Produk
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

<x-organisms.modal id="editProductModal" title="Edit Produk">
    <form id="formEditProduct" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_id" name="id">
        
        <input type="hidden" id="edit_sku" name="seller_sku">

        <div class="form-group mb-3">
            <x-atoms.label value="Foto Produk" for="edit_image" />
            <x-atoms.image-upload id="edit_image" name="image" />
        </div>

        <div class="form-group mb-3">
            <x-atoms.label value="SKU Produk" for="edit_sku" />
            <x-atoms.input type="text" id="edit_sku" name="seller_sku" placeholder="Contoh: PRD-001" required />
        </div>

        <div class="form-group mb-3">
            <x-atoms.label for="editProductName" value="Nama Produk" />
            <x-atoms.input type="text" id="editProductName" name="name" required />
        </div>

        <div class="form-group mb-3">
            <x-atoms.label for="editProductCategory" value="Kategori" />
            <x-atoms.input type="text" id="editProductCategory" name="category" placeholder="Contoh: Alat Peternakan" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <x-atoms.label for="editProductVideoCount" value="Jumlah Wajib Video" />
                <x-atoms.input type="number" id="editProductVideoCount" name="mandatory_video_count" />
            </div>
            <div class="form-group">
                <x-atoms.label for="editProductStock" value="Jumlah Stok" />
                <x-atoms.input type="number" id="editProductStock" name="stock" />
            </div>
        </div>

        <div class="form-group mb-3">
            <x-atoms.label for="editProductDetail" value="Detail Produk / S&K Tambahan" />
            <textarea id="editProductDetail" name="product_detail" class="form-control" rows="5" style="border-radius: 8px; border: 1px solid var(--glass-border); padding: 10px; font-size: 13px;"></textarea>
            <small style="color: var(--text-secondary); font-size: 11px;">*Mendukung format tag HTML</small>
        </div>
        
        <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; margin-bottom: 16px;">
            <div>
                <x-atoms.typography variant="body" style="font-weight: 600; color: var(--text-primary); margin: 0; font-size: 14px;">Tampilkan di Katalog Affiliator</x-atoms.typography>
                <div style="color: var(--text-secondary); font-size: 12px; margin-top: 2px;">Produk sedang aktif dan dapat dilihat oleh affiliator.</div>
            </div>
            <x-molecules.toggle id="editProductVisible" name="is_visible" />
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('editProductModal')" style="background: white; border: 1px solid #cbd5e1; color: #475569;">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit" form="formEditProduct">
                Simpan Perubahan
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

<x-organisms.modal 
    id="massEditProductModal" 
    title="Atur Massal Produk" 
    description="Atur stok dan target video untuk produk yang dipilih."
>
    <form id="formMassEditProduct" method="POST" action="{{ route('admin-dashboard.product-mass-update') }}">
        @csrf
        <input type="hidden" name="product_ids" id="massSelectedIds">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <x-atoms.label for="massProductStock" value="Set Stok Baru" />
                <x-atoms.input type="number" id="massProductStock" name="stock" placeholder="Biarkan kosong jika tak diubah" />
            </div>
            <div class="form-group">
                <x-atoms.label for="massProductVideoCount" value="Set Target Video" />
                <x-atoms.input type="number" id="massProductVideoCount" name="mandatory_video_count" placeholder="Default: 3" />
            </div>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('massEditProductModal')">Batal</x-atoms.button>
            <x-atoms.button variant="primary" type="submit" form="formMassEditProduct">Terapkan ke Semua</x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>
@endsection

@push('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function prepareMassEdit() {
        openModal('massEditProductModal');
    }

    function editProduct(data) {
        const form = document.getElementById('formEditProduct');
        form.action = `/dashboard/products/${data.id}`;
        
        document.getElementById('edit_id').value = data.id || '';
        document.getElementById('edit_sku').value = data.seller_sku || '';
        document.getElementById('editProductName').value = data.name || '';  
        
        if(document.getElementById('editProductCategory')) document.getElementById('editProductCategory').value = data.category;
        if(document.getElementById('editProductVideoCount')) document.getElementById('editProductVideoCount').value = data.mandatory_video_count;
        if(document.getElementById('editProductStock')) document.getElementById('editProductStock').value = data.stock;
        if(document.getElementById('editProductDetail')) document.getElementById('editProductDetail').value = data.product_detail;
        
        const visibleToggle = document.getElementById('editProductVisible');
        if(visibleToggle) visibleToggle.checked = (data.is_visible == 1);

        const previewImg = document.getElementById('preview-img-edit_image');
        const previewPlaceholder = document.getElementById('preview-placeholder-edit_image');

        if (previewImg && previewPlaceholder) {
            if (data.image_url && data.image_url.trim() !== '') {
                previewImg.src = data.image_url;
                previewImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none';
                previewPlaceholder.style.display = 'flex';
            }
        }

        openModal('editProductModal');
    }
    document.addEventListener('DOMContentLoaded', function() {
        $('#productTable').on('click', '.btn-edit', function(e) {
            e.preventDefault();
            
            const productId = $(this).attr('data-id').trim();
            const productName = $(this).attr('data-name');
            const productPrice = $(this).attr('data-price');
            const productStock = $(this).attr('data-stock');
            const videoCount = $(this).attr('data-video-count');
            
            const form = document.getElementById('formEditProduct');
            if (form && productId) {
                form.action = `/dashboard/products/${productId}`;
            }
             
            const nameInput = document.getElementById('editProductName');
            if (nameInput) nameInput.value = productName || '';

            const priceInput = document.getElementById('editProductPrice');
            if (priceInput) priceInput.value = productPrice || 0;

            const stockInput = document.getElementById('editProductStock');
            if (stockInput) stockInput.value = productStock || 0;

            const videoInput = document.getElementById('editProductVideoCount');
            if (videoInput) videoInput.value = (videoCount !== undefined && videoCount !== '') ? videoCount : 3;
            
            openModal('editProductModal');
        });
    });
    
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file_selector');
        const dropzoneText = document.getElementById('dropzone-text');
        const dropzoneSubtext = document.getElementById('dropzone-subtext');

        fileInput.addEventListener('change', function() {
            updateDropzoneUI(this.files);
        });

        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = 'var(--primary-blue)';
            dropzone.style.backgroundColor = 'var(--primary-blue-soft, rgba(59, 130, 246, 0.1))';
        });

        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '';
            dropzone.style.backgroundColor = '';
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '';
            dropzone.style.backgroundColor = '';
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files; 
                updateDropzoneUI(e.dataTransfer.files);
            }
        });

        function updateDropzoneUI(files) {
            if (files.length > 0) {
                dropzoneText.innerText = files.length + " File Siap Diunggah";
                dropzoneText.style.color = "var(--primary-blue)";
                
                let fileNames = Array.from(files).map(f => f.name).join(', ');
                dropzoneSubtext.innerText = fileNames;
            } else {
                dropzoneText.innerText = "Tarik & Lepas File Di Sini";
                dropzoneText.style.color = "";
                dropzoneSubtext.innerText = "Atau klik area ini untuk memilih file dari komputer Anda";
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
@endpush