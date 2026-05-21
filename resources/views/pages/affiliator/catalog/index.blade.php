@extends('layouts.app')
@section('title', 'Katalog Produk')

@section('content')
<x-organisms.mobile-page-wrapper title="Katalog Produk" description="Pilih produk sampel gratis yang ingin Anda ajukan untuk direview.">
    <div style="margin-bottom: 24px; display: flex; gap: 16px; max-width: 480px;">
        <form action="{{ route('affiliator.catalog.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%;">
            <x-atoms.input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." style="flex: 1;" />
            <x-atoms.button type="submit" variant="primary">Cari</x-atoms.button>
        </form>
    </div>

    <div id="product-list-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
        @include('pages.affiliator.catalog.partials.product_cards', ['products' => $products])
    </div>

    <div id="loading-indicator" style="text-align: center; padding: 24px; display: {{ $products->nextPageUrl() ? 'block' : 'none' }};">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="animation: spin 1s linear infinite; color: var(--primary-blue); display: inline-block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span style="font-size: 13px; color: var(--text-secondary); margin-left: 8px;">Memuat produk...</span>
    </div>

    @if(!$products->nextPageUrl())
        <div class="end-of-data" style="text-align: center; color: var(--text-tertiary); margin-top: 24px; font-size: 14px;">
            Semua produk telah ditampilkan.
        </div>
    @endif
</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let nextPageUrl = '{{ $products->nextPageUrl() }}';
        let isLoading = false;
        const container = document.getElementById('product-list-container');
        const loadingIndicator = document.getElementById('loading-indicator');

        if (nextPageUrl && loadingIndicator) {
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading && nextPageUrl) {
                    loadMoreProducts();
                }
            }, { rootMargin: '150px' });

            observer.observe(loadingIndicator);
        }

        async function loadMoreProducts() {
            isLoading = true;

            try {
                const response = await fetch(nextPageUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Terjadi kesalahan pada jaringan');

                const data = await response.json();

                container.insertAdjacentHTML('beforeend', data.html);
                nextPageUrl = data.next_page_url;

                if (!nextPageUrl) {
                    loadingIndicator.style.display = 'none';
                    if (!document.querySelector('.end-of-data')) {
                        loadingIndicator.insertAdjacentHTML('afterend', '<div class="end-of-data" style="text-align: center; color: var(--text-tertiary); margin-top: 24px; font-size: 14px;">Semua produk telah ditampilkan.</div>');
                    }
                }

            } catch (error) {
                console.error(error);
            } finally {
                isLoading = false;
            }
        }
    });
</script>
@endpush