@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <x-molecules.glass-tabs>
                <h3 class="card-title">Data Produk</h3>
                <x-slot name="actions">
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
                        ['data' => 'seller_sku', 'name' => 'seller_sku', 'title' => 'SKU Produk'],
                        ['data' => 'is_visible', 'name' => 'is_visible', 'title' => 'Tampil di Katalog', 'orderable' => true, 'searchable' => false],
                        ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
                    ];
                @endphp

                <x-organisms.datatables id="productTable" url="{{ route('admin-dashboard.product-data') }}"
                    :columns="$tableColumns" />
            </div>
        </div>
    </div>

    <x-organisms.modal id="sinkronProdukModal" title="Sinkron Produk"
        description="Masukkan informasi dasar untuk sinkron produk.">
        <form id="importForm" action="{{ route('admin-dashboard.import-product-update') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="dropzone-container">
                <label class="dropzone" id="dropzone" for="file_selector" style="cursor: pointer; display: block;">
                    <div class="dropzone-icon">
                        <x-atoms.icon name="download" style="width: 32px; height: 32px;" />
                    </div>
                    <div class="dropzone-text" id="dropzone-text">Tarik & Lepas File Di Sini</div>
                    <div class="dropzone-subtext" id="dropzone-subtext">Atau klik area ini untuk memilih file dari komputer
                        Anda</div>
                    <input type="file" id="file_selector" name="files[]" multiple accept=".xlsx,.xls,.csv"
                        style="display: none;">
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



            <div class="form-group mb-3">
                <x-atoms.label value="Foto Produk" for="edit_image" />
                <x-atoms.image-upload id="edit_image" name="image" />
            </div>

            <div class="form-group mb-3">
                <x-atoms.label value="SKU Produk" for="edit_sku" />
                <x-atoms.input type="text" id="edit_sku" name="seller_sku" placeholder="Contoh: PRD-001" />
            </div>

            <div class="form-group mb-3">
                <x-atoms.label for="editProductName" value="Nama Produk" />
                <x-atoms.input type="text" id="editProductName" name="name" />
            </div>

            <div class="form-group mb-3">
                <x-atoms.label for="editProductCategory" value="Kategori" />
                <x-atoms.input type="text" id="editProductCategory" name="category" placeholder="Contoh: Alat Peternakan" />
            </div>

            <div class="form-group mb-3">
                <x-atoms.label for="editProductDetail" value="Detail Produk / S&K Tambahan" />
                <textarea id="editProductDetail" name="product_detail" class="form-control" rows="5"
                    style="border-radius: 8px; border: 1px solid var(--glass-border); padding: 10px; font-size: 13px;"></textarea>
                <small style="color: var(--text-secondary); font-size: 11px;">*Mendukung format tag HTML</small>
            </div>

            <div class="form-group"
                style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; margin-bottom: 16px;">
                <div>
                    <x-atoms.typography variant="body"
                        style="font-weight: 600; color: var(--text-primary); margin: 0; font-size: 14px;">Tampilkan di
                        Katalog Affiliator</x-atoms.typography>
                    <div style="color: var(--text-secondary); font-size: 12px; margin-top: 2px;">Produk sedang aktif dan
                        dapat dilihat oleh affiliator.</div>
                </div>
                <x-molecules.toggle id="editProductVisible" name="is_visible" />
            </div>

            <x-slot name="footer">
                <x-atoms.button variant="secondary" type="button" onclick="closeModal('editProductModal')"
                    style="background: white; border: 1px solid #cbd5e1; color: #475569;">
                    Batal
                </x-atoms.button>
                <x-atoms.button variant="primary" type="submit" form="formEditProduct">
                    Simpan Perubahan
                </x-atoms.button>
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

        function editProduct(data) {
            const form = document.getElementById('formEditProduct');
            form.action = `/dashboard/products/${data.id}`;

            document.getElementById('edit_id').value = data.id || '';
            document.getElementById('edit_sku').value = data.seller_sku || '';
            document.getElementById('editProductName').value = data.name || '';

            if (document.getElementById('editProductCategory')) document.getElementById('editProductCategory').value = data.category;
            if (document.getElementById('editProductDetail')) document.getElementById('editProductDetail').value = data.product_detail;

            const visibleToggle = document.getElementById('editProductVisible');
            if (visibleToggle) visibleToggle.checked = (data.is_visible == 1);

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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#productTable').on('change', '.toggle-visibility-inline', function () {
                const id = $(this).data('id');
                const isVisible = $(this).is(':checked') ? 1 : 0;
                const toggle = $(this);

                toggle.prop('disabled', true);

                $.ajax({
                    url: `/dashboard/products/${id}/toggle-visibility`,
                    type: 'PATCH',
                    data: {
                        is_visible: isVisible,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        toggle.prop('disabled', false);
                    },
                    error: function () {
                        toggle.prop('disabled', false);
                        toggle.prop('checked', !isVisible);
                        alert('Gagal mengubah status. Silakan coba lagi.');
                    }
                });
            });

            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('file_selector');
            const dropzoneText = document.getElementById('dropzone-text');
            const dropzoneSubtext = document.getElementById('dropzone-subtext');

            fileInput.addEventListener('change', function () {
                updateDropzoneUI(this.files);
            });

            dropzone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropzone.style.borderColor = 'var(--primary-blue)';
                dropzone.style.backgroundColor = 'var(--primary-blue-soft, rgba(59, 130, 246, 0.1))';
            });

            dropzone.addEventListener('dragleave', function (e) {
                e.preventDefault();
                dropzone.style.borderColor = '';
                dropzone.style.backgroundColor = '';
            });

            dropzone.addEventListener('drop', function (e) {
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

            document.getElementById('importForm').addEventListener('submit', function () {
                const btnSubmit = document.querySelector('button[form="importForm"]');
                btnSubmit.innerHTML = `
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Memproses...
                `;
                btnSubmit.setAttribute('disabled', 'true');
            });
        });
    </script>
@endpush