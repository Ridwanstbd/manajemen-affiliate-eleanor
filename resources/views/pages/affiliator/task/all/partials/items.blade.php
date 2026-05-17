@foreach($data as $task)
    @php
        $product = $task->products->first();
        $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
        $imageUrl = (!empty($product) && !empty($product->image_path)) 
                    ? (filter_var($product->image_path, FILTER_VALIDATE_URL) ? $product->image_path : asset('storage/' . $product->image_path)) 
                    : $fallbackImage;
        
        $isOverdue = $task->task_status === 'OVERDUE' || (isset($task->due_date) && \Carbon\Carbon::parse($task->due_date)->isPast());
    @endphp

    <x-molecules.card style="padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <div>
                <x-atoms.typography variant="body" style="font-weight: 700; color: var(--text-primary);">
                    TASK-#{{ $task->id }}
                </x-atoms.typography>
                <div style="display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                    <x-atoms.icon name="clock" style="width: 13px; height: 13px; color: {{ $isOverdue ? 'var(--rose)' : 'var(--text-secondary)' }};" />
                    <x-atoms.typography variant="body" style="font-size: 12px; color: {{ $isOverdue ? 'var(--rose)' : 'var(--text-secondary)' }}; font-weight: {{ $isOverdue ? '700' : 'normal' }};">
                        Tenggat: {{ isset($task->due_date) ? \Carbon\Carbon::parse($task->due_date)->translatedFormat('d M Y') : '-' }}
                    </x-atoms.typography>
                </div>
            </div>
            <div>
                @if($isOverdue)
                    <x-atoms.badge status="overdue">Melewati Batas</x-atoms.badge>
                @else
                    <x-atoms.badge status="pending">Sedang Diproses</x-atoms.badge>
                @endif
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 54px; height: 54px; border-radius: 6px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex-grow: 1;">
                <x-atoms.typography variant="h3" style="font-size: 14.5px; font-weight: 700; line-height: 1.3; margin-bottom: 3px; color: #000000;">
                    {{ $product->name ?? 'Produk Sampel Tidak Diketahui' }}
                </x-atoms.typography>
                <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-tertiary);">
                    Kategori: {{ $product->category ?? '-' }}
                </x-atoms.typography>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
            <x-atoms.button href="{{ route('affiliator.task.show', $task->id) }}" variant="primary" size="sm" style="border-radius: 6px; font-weight: 700;">
                Laporkan Tugas
            </x-atoms.button>
        </div>

    </x-molecules.card>
@endforeach