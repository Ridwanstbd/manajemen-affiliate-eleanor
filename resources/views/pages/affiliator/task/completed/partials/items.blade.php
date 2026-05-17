@foreach($data as $task)
    @php
        $product = $task->products->first();
        $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
        $imageUrl = (!empty($product) && !empty($product->image_path)) 
                    ? (filter_var($product->image_path, FILTER_VALIDATE_URL) ? $product->image_path : asset('storage/' . $product->image_path)) 
                    : $fallbackImage;
    @endphp

    <x-molecules.card style="padding: 16px; margin-bottom: 16px; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: none; background: #ffffff;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <div>
                <x-atoms.typography variant="body" style="font-weight: 700; color: var(--text-primary);">
                    TASK-#{{ $task->id }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 11.5px; color: var(--text-tertiary); margin-top: 1px;">
                    Selesai pada: {{ $task->updated_at->translatedFormat('d M Y') }}
                </x-atoms.typography>
            </div>
            <div>
                <x-atoms.badge status="paid">Selesai</x-atoms.badge>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 12px;">
            <div style="width: 54px; height: 54px; border-radius: 6px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex-grow: 1;">
                <x-atoms.typography variant="h3" style="font-size: 14.5px; font-weight: 700; line-height: 1.3; margin-bottom: 2px; color: #000000;">
                    {{ $product->name ?? 'Produk Sampel' }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary); max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;">
                    Link: <a href="{{ $task->tiktok_video_link }}" target="_blank" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">{{ $task->tiktok_video_link }}</a>
                </x-atoms.typography>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; padding-top: 8px; border-top: 1px solid #f1f5f9;">
            <x-atoms.button href="{{ route('affiliator.task.show', $task->id) }}" variant="outline" size="sm" style="border-radius: 6px; width: 100%; justify-content: center;">
                Lihat Detail Rincian
            </x-atoms.button>
        </div>

    </x-molecules.card>
@endforeach