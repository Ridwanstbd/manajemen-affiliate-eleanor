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

    <x-molecules.card style="padding: 0; border: 1px solid #c8cbf1; border-radius: 4px; box-shadow: none;">
        <a href="{{ route('affiliator.catalog.show', $product->id) }}" style="text-decoration: none; color: inherit;">
            <div class="card-img-container">
                <img src="{{ $imageUrl }}" 
                     alt="{{ $product->name }}" 
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
            </div>
            
            <div class="card-info">
                <span class="category-label">
                    <x-atoms.badge status="pending" style="background: #f0f0f0; color: #666; border: none; font-size: 11px;">
                        {{ $product->category ?? 'Umum' }}
                    </x-atoms.badge>
                </span>
                
                <x-atoms.typography variant="h3">{{ $product->name }}</x-atoms.typography>
                
                <div class="stock-info">
                    @if($product->stock > 0)
                        Stok Tersedia: {{ $product->stock }}
                    @else
                        <span class="stock-out">Stok Habis</span>
                    @endif
                </div>
            </div>
        </a>
    </x-molecules.card>
@endforeach