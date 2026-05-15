@extends('layouts.app')

@section('title', 'Katalog Sampel Gratis')

@section('content')

<x-organisms.mobile-page-wrapper title="Katalog Sampel Gratis" subtitle="Pilih produk yang ingin Anda ulas. Ketuk untuk melihat detail.">
        <div class="search-container-mobile">
            <form action="{{ route('affiliator.catalog.index') }}" method="GET" id="search-form">
                <input type="text" name="search" class="search-box-mobile" 
                       placeholder="Cari nama produk atau kategori..." 
                       value="{{ request('search') }}"
                       onchange="this.form.submit()">
            </form>
        </div>
    
        <div class="product-grid-mobile" id="product-list">
            @include('pages.affiliator.catalog.partials.product_cards', ['products' => $products])
        </div>
    
        {{-- Infinite Scroll Sentinel --}}
        <div class="sentinel-mobile" id="sentinel" data-next="{{ $products->nextPageUrl() }}">
            <div class="spinner-mobile" id="loader"></div>
        </div>

</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const sentinel = document.getElementById('sentinel');
    const list = document.getElementById('product-list');
    const loader = document.getElementById('loader');
    let isFetching = false;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isFetching) {
                const nextUrl = sentinel.getAttribute('data-next');
                if (nextUrl) {
                    fetchNextPage(nextUrl);
                }
            }
        });
    }, { rootMargin: '200px' });

    observer.observe(sentinel);

    async function fetchNextPage(url) {
        isFetching = true;
        loader.classList.add('active');

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            list.insertAdjacentHTML('beforeend', data.html);
            
            if (data.next_page_url) {
                sentinel.setAttribute('data-next', data.next_page_url);
            } else {
                sentinel.removeAttribute('data-next');
                observer.unobserve(sentinel);
            }
        } catch (error) {
            console.error("Gagal memuat produk:", error);
        } finally {
            isFetching = false;
            loader.classList.remove('active');
        }
    }
});
</script>
@endpush