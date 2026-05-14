@props(['id', 'name', 'value' => null])

<div class="image-upload-wrapper" id="wrapper-{{ $id }}">
    <div class="mb-2">
        <img id="preview-img-{{ $id }}" 
             src="{{ $value }}" 
             class="img-thumbnail" 
             style="max-height: 150px; display: {{ $value ? 'block' : 'none' }};">
             
        <div id="preview-placeholder-{{ $id }}" 
             class="align-items-center justify-content-center bg-light border rounded" 
             style="height: 150px; width: 150px; color: #adb5bd; display: {{ $value ? 'none' : 'flex' }};">
            <x-atoms.icon name="image" style="width: 48px; height: 48px;" />
        </div>
    </div>
    
    <x-atoms.input 
        type="file" 
        id="{{ $id }}" 
        name="{{ $name }}" 
        accept="image/*"
        onchange="handleImagePreview(event, '{{ $id }}')"
    />
</div>

@once
@push('scripts')
<script>
    function handleImagePreview(event, id) {
        const file = event.target.files[0];
        const imgElement = document.getElementById('preview-img-' + id);
        const placeholderElement = document.getElementById('preview-placeholder-' + id);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgElement.src = e.target.result;
                imgElement.style.display = 'block';
                placeholderElement.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            // Jika user batal memilih file, kembalikan ke state kosong
            imgElement.src = '';
            imgElement.style.display = 'none';
            placeholderElement.style.display = 'flex';
        }
    }
</script>
@endpush
@endonce