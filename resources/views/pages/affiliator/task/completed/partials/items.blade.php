@php
    $grouped = $data->groupBy(function($task) {
        $product = $task->products->first();
        return $product ? $product->id : 'no-product';
    });
@endphp

@foreach($grouped as $productId => $tasks)
    @php
        $product  = $tasks->first()->products->first();
        $fallback = 'https://placehold.co/400x400?text=No+Image';
        $imageUrl = (!empty($product) && !empty($product->image_path))
                    ? (filter_var($product->image_path, FILTER_VALIDATE_URL)
                        ? $product->image_path
                        : asset('storage/' . $product->image_path))
                    : $fallback;
    @endphp

    <x-molecules.card style="border: 1px solid var(--glass-border); border-radius: 16px; margin-bottom: 20px; padding: 0; overflow: hidden; background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.04);">

        <div style="display: flex; align-items: center; gap: 14px; padding: 16px; background: rgba(240,253,244,0.8); border-bottom: 1px solid rgba(34,197,94,0.12);">
            <div style="width: 54px; height: 54px; border-radius: 10px; overflow: hidden; background: #f1f5f9; border: 1px solid var(--glass-border); flex-shrink: 0;">
                <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}"
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.src='{{ $fallback }}'">
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: var(--text-tertiary); letter-spacing: 0.5px; display: block;">
                    {{ $product->category ?? 'Produk' }}
                </span>
                <x-atoms.typography variant="card-title" style="font-size: 14.5px; font-weight: 800; color: var(--text-primary); margin: 2px 0 0; line-height: 1.3; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $product->name ?? 'Produk Sampel' }}
                </x-atoms.typography>
                @if($product && $product->mandatory_video_count)
                    <span style="font-size: 11px; color: #16a34a; font-weight: 600; margin-top: 2px; display: inline-block;">
                        {{ $tasks->count() }} dari {{ $product->mandatory_video_count }} video selesai
                    </span>
                @endif
            </div>
            <div style="flex-shrink: 0;">
                <span style="background: rgba(34,197,94,0.1); color: #16a34a; font-size: 11px; font-weight: 700; border-radius: 20px; padding: 3px 10px; white-space: nowrap;">
                    {{ $tasks->count() }} selesai
                </span>
            </div>
        </div>

        <div style="padding: 8px 0;">
            @foreach($tasks as $i => $task)
                @php
                    $isLast = $i === $tasks->count() - 1;
                @endphp

                <a href="{{ route('affiliator.task.show', $task->id) }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; text-decoration: none; transition: background 0.15s; {{ !$isLast ? 'border-bottom: 1px solid rgba(0,0,0,0.04);' : '' }}"
                   onmouseover="this.style.background='rgba(240,253,244,0.5)'"
                   onmouseout="this.style.background='transparent'">

                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(34,197,94,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-atoms.icon name="check-circle" style="width: 16px; height: 16px; color: #16a34a;" />
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 12.5px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px;">
                            Tugas #{{ $task->id }}
                        </div>
                        @if($task->tiktok_video_link)
                            <div style="font-size: 11px; color: var(--primary-blue); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 180px;">
                                {{ $task->tiktok_video_link }}
                            </div>
                        @endif
                        <span style="font-size: 11px; color: var(--text-tertiary);">
                            Selesai {{ $task->updated_at->translatedFormat('d M Y') }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                        <x-atoms.badge status="paid">Selesai</x-atoms.badge>
                        <x-atoms.icon name="chevron-right" style="width: 16px; height: 16px; color: var(--text-tertiary);" />
                    </div>

                </a>
            @endforeach
        </div>

    </x-molecules.card>
@endforeach