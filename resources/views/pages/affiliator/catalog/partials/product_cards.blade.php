@foreach($products as $product)
@php
        $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
        $imageUrl = $fallbackImage;
        if (!empty($product->image_path)) {
            $imageUrl = filter_var($product->image_path, FILTER_VALIDATE_URL) 
                        ? $product->image_path 
                        : asset('storage/' . $product->image_path);
        }
    @endphp

    <div class="product-card" style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px; box-shadow: var(--glass-shadow);">
        <div class="card-img-container">
                <img src="{{ $imageUrl }}" 
                     alt="{{ $product->name }}" 
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
            </div>
        <div style="display: flex; flex-direction: column; gap: 4px; flex: 1;">
            <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">{{ $product->category }}</span>
            <x-atoms.typography variant="body" style="font-weight: 600; color: var(--text-primary); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px; line-height: 1.4;">
                {{ $product->name }}
            </x-atoms.typography>
            <x-atoms.typography variant="h4" style="color: var(--primary-blue); font-weight: 700; margin-top: 4px;">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </x-atoms.typography>
        </div>
        <div style="display: flex; gap: 8px; margin-top: auto;">
            <a href="{{ route('affiliator.catalog.show', $product->id) }}" style="flex: 1; text-decoration: none;">
                <x-atoms.button variant="outline" style="width: 100%; padding: 8px;">Detail</x-atoms.button>
            </a>
            <form action="{{ route('affiliator.cart.store', $product->id) }}" method="POST" style="flex: 1;">
                @csrf
                <x-atoms.button type="submit" variant="primary" style="width: 100%; padding: 8px;">Ambil</x-atoms.button>
            </form>
        </div>
    </div>
@endforeach