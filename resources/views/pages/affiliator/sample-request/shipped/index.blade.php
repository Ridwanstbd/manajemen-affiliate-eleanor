<div class="sample-requests-shipped" style="margin-top: 16px;">
    @forelse($data ?? [] as $request)
        <x-molecules.card style="padding: 16px; margin-bottom: 16px; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: none; background: #ffffff;">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                        <x-atoms.icon name="clock" style="width: 12px; height: 12px; color: var(--text-tertiary);" />
                        <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary);">
                            Dikirim {{ $request->updated_at->diffForHumans() }}
                        </x-atoms.typography>
                    </div>
                </div>
                <div>
                    <x-atoms.badge status="paid">Dalam Pengiriman</x-atoms.badge>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; margin-bottom: 14px;">
                <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); font-weight: 600;">
                    Isi Paket: {{ $request->details->count() }} Produk ({{ $request->details->sum('quantity') }} Pcs)
                </x-atoms.typography>
            </div>

            <div style="border: 1px solid var(--primary-blue-soft); background: rgba(59, 130, 246, 0.03); padding: 12px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <x-atoms.typography variant="body" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-tertiary); font-weight: bold;">
                        Ekspedisi / Kurir
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-top: 2px;">
                        {{ strtoupper($request->courier ?? 'Kurir Mitra') }}
                    </x-atoms.typography>
                </div>
                <div style="text-align: right;">
                    <x-atoms.typography variant="body" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-tertiary); font-weight: bold;">
                        No. Resi Pengiriman
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 14px; font-weight: 700; color: var(--primary-blue); margin-top: 2px;">
                        {{ $request->tracking_number ?? '-' }}
                    </x-atoms.typography>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 14px;">
                <x-atoms.button href="{{ route('affiliator.sample-request.show', $request->id) }}" variant="primary" size="sm" style="width: 100%; justify-content: center; border-radius: 6px;">
                    Lacak Perjalanan Paket
                </x-atoms.button>
            </div>

        </x-molecules.card>
    @empty
        <div style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
            <x-atoms.icon name="cart" style="width: 48px; height: 48px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.5;"/>
            <p>Tidak ada paket sampel yang sedang dalam pengiriman.</p>
        </div>
    @endforelse

    <div style="margin-top: 20px;">
        {{ $data->links() }}
    </div>
</div>