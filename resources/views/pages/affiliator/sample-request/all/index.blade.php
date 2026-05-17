<div class="sample-requests-all" style="margin-top: 16px;">
    @forelse($data ?? [] as $request)
        <x-molecules.card style="padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: none; background: #ffffff;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                <div>
                    <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-tertiary);">
                        {{ $request->created_at->translatedFormat('d M Y, H:i') }}
                    </x-atoms.typography>
                </div>

                <div>
                    @if($request->status === 'PENDING')
                        <x-atoms.badge status="pending">Menunggu Persetujuan</x-atoms.badge>
                    @elseif($request->status === 'APPROVED')
                        <x-atoms.badge status="paid" style="background: #e0f2fe; color: #0369a1;">Disetujui Admin</x-atoms.badge>
                    @else
                        <x-atoms.badge status="overdue">Ditolak</x-atoms.badge>
                    @endif
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($request->details as $detail)
                    @php
                        $product = $detail->product;
                        $fallbackImage = 'https://placehold.co/400x400?text=No+Image';
                        $imageUrl = !empty($product->image_path) 
                                    ? (filter_var($product->image_path, FILTER_VALIDATE_URL) ? $product->image_path : asset('storage/' . $product->image_path)) 
                                    : $fallbackImage;
                    @endphp
                    
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 50px; height: 50px; border-radius: 6px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex-grow: 1;">
                            <x-atoms.typography variant="h3" style="font-size: 14px; margin-bottom: 2px;">
                                {{ $product->name ?? 'Produk Tidak Tersedia' }}
                            </x-atoms.typography>
                            <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary);">
                                Jumlah: {{ $detail->quantity }} pcs &bull; Wajib: {{ $product->mandatory_video_count ?? 1 }} Video
                            </x-atoms.typography>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($request->status === 'REJECTED' && $request->reject_reason)
                <div style="margin-top: 14px; background: var(--rose-soft); padding: 10px 14px; border-radius: 8px; border-left: 4px solid var(--rose);">
                    <x-atoms.typography variant="body" style="font-size: 12px; color: var(--rose); font-weight: 600;">
                        Alasan Penolakan: {{ $request->reject_reason }}
                    </x-atoms.typography>
                </div>
            @endif

            <div style="display: flex; justify-content: flex-end; margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                <x-atoms.button href="{{ route('affiliator.sample-request.show', $request->id) }}" variant="outline" size="sm" style="border-radius: 6px;">
                    Lihat Detail
                </x-atoms.button>
            </div>

        </x-molecules.card>
    @empty
        <div style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
            <x-atoms.icon name="bell" style="width: 48px; height: 48px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.5;"/>
            <p>Belum ada riwayat pengajuan sampel.</p>
        </div>
    @endforelse

    <div style="margin-top: 20px;">
        {{ $data->links() }}
    </div>
</div>