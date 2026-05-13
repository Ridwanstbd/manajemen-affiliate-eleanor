<div id="global-lightbox" class="lightbox-overlay" onclick="closeLightbox(event)">
    <div class="lightbox-container">
        <button type="button" class="lightbox-close" onclick="closeLightbox(event)" aria-label="Tutup Lightbox">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="Preview Gambar">
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openLightbox(imageSrc) {
        const lightbox = document.getElementById('global-lightbox');
        const img = document.getElementById('lightbox-img');
        
        img.src = imageSrc;
        lightbox.style.display = 'flex';
        setTimeout(() => {
            lightbox.classList.add('active');
        }, 10);
    }

    function closeLightbox(event) {
        const lightbox = document.getElementById('global-lightbox');
        
        if (event.target.id === 'global-lightbox' || event.target.closest('.lightbox-close')) {
            lightbox.classList.remove('active');
            
            setTimeout(() => {
                lightbox.style.display = 'none';
                document.getElementById('lightbox-img').src = '';
            }, 300);
        }
    }
</script>
@endpush