@extends('layouts.app')
@section('title', 'Katalog Produk')

@section('content')
<x-molecules.card title="Katalog Produk" description="Pilih produk sampel gratis yang ingin Anda ajukan untuk direview.">
    <div style="margin-bottom: 24px; display: flex; gap: 16px; max-width: 480px;">
        <form action="{{ route('affiliator.catalog.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%;">
            <x-atoms.input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." style="flex: 1;" />
            <x-atoms.button type="submit" variant="primary">Cari</x-atoms.button>
        </form>
    </div>

    <div id="product-list-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
        @include('pages.affiliator.catalog.partials.product_cards', ['products' => $products])
    </div>

    <div style="margin-top: 32px; display: flex; justify-content: center;">
        {{ $products->links() }}
    </div>
</x-molecules.card>
@endsection