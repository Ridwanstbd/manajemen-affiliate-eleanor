@php
    $productData = [
        'id' => $row->id,
        'seller_sku' => $row->seller_sku ?? '',
        'name' => $row->name ?? '',
        'category' => $row->category ?? '',
        'mandatory_video_count' => $row->mandatory_video_count ?? 0,
        'stock' => $row->stock ?? 0,
        'product_detail' => $row->product_detail ?? '',
        'is_visible' => $row->is_visible ? 1 : 0,
        'image_url' => $row->image_path ? (\Illuminate\Support\Str::startsWith($row->image_path, ['http://', 'https://']) ? $row->image_path : asset('storage/' . $row->image_path)) : ''
    ];
@endphp

<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="primary" class="btn-edit" size="sm"
        data-product="{{ json_encode($productData) }}"
        onclick="editProduct(JSON.parse(this.getAttribute('data-product')))"
        title="Edit Produk">
        <x-atoms.icon name="edit" style="width: 14px; height: 14px;" />
    </x-atoms.button>
</div>