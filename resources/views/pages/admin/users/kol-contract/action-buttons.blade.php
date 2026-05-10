<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="outline" size="sm" type="button" class="btn-detail"
        data-id="{{ (string) $row->id }}"
        data-username="{{ $row->username }}"
        data-contracts="{{ $row->kolContracts->map(fn($c) => [
            'id'        => $c->id,
            'fee'       => $c->contract_fee,
            'req_video' => $c->required_video_count,
            'start'     => $c->start_date?->format('Y-m-d') ?? '-',
            'end'       => $c->end_date?->format('Y-m-d') ?? '-',
            'status'    => $c->status,
            'products'  => $c->products->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->toArray(),
        ])->toJson() }}"
        title="Detail Kontrak">
        Detail
    </x-atoms.button>
</div>