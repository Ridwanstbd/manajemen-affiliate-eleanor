@props(['count' => 0])

<div class="carousel-wrapper">
    <div class="carousel-track">
        {{ $slot }}
    </div>

    @if($count > 1)
        <button class="carousel-btn-prev btn-prev" aria-label="Previous">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="carousel-btn-next btn-next" aria-label="Next">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <div class="carousel-indicators">
            @for($i = 0; $i < $count; $i++)
                <div class="carousel-dot {{ $i == 0 ? 'active' : '' }}" data-index="{{ $i }}"></div>
            @endfor
        </div>
    @endif
</div>

@if($count > 1)
    @once
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const carousels = document.querySelectorAll('.carousel-wrapper');
                
                carousels.forEach(wrapper => {
                    const track = wrapper.querySelector('.carousel-track');
                    if (!track) return;
                    
                    const dots = wrapper.querySelectorAll('.carousel-dot');
                    const btnPrev = wrapper.querySelector('.btn-prev');
                    const btnNext = wrapper.querySelector('.btn-next');
                    const totalSlides = track.children.length;
                    
                    if (totalSlides <= 1) return;

                    let currentSlide = 0;
                    let slideInterval;

                    function updateCarousel() {
                        track.style.transform = `translateX(-${currentSlide * 100}%)`;
                        
                        dots.forEach((dot, index) => {
                            if (index === currentSlide) {
                                dot.classList.add('active');
                            } else {
                                dot.classList.remove('active');
                            }
                        });
                    }

                    function moveSlide(direction) {
                        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
                        updateCarousel();
                        resetInterval();
                    }

                    if (btnPrev) btnPrev.addEventListener('click', () => moveSlide(-1));
                    if (btnNext) btnNext.addEventListener('click', () => moveSlide(1));

                    dots.forEach((dot, index) => {
                        dot.addEventListener('click', () => {
                            currentSlide = index;
                            updateCarousel();
                            resetInterval();
                        });
                    });

                    function startInterval() {
                        slideInterval = setInterval(() => moveSlide(1), 5000);
                    }

                    function resetInterval() {
                        clearInterval(slideInterval);
                        startInterval();
                    }

                    startInterval();
                });
            });
        </script>
        @endpush
    @endonce
@endif