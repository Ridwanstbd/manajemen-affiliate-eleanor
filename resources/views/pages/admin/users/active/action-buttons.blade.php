<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="outline" size="sm" type="button" class="btn-detail"
        data-id="{{ $row->id ?? '' }}"
        data-username="{{ $row->username ?? '' }}"
        data-email="{{ $row->email ?? '-' }}"
        data-phone="{{ $row->phone_number ?? '-' }}"
        data-gmv="{{ optional($row->metrics)->total_gmv ?? 0 }}"
        data-items="{{ optional($row->metrics)->total_items ?? 0 }}"
        data-commission="{{ optional($row->metrics)->total_commission ?? 0 }}"
        data-samples="{{ $row->total_samples_received ?? 0 }}" 
        data-aov="{{ optional($row->metrics)->avg_aov ?? 0 }}"
        data-refunds="{{ optional($row->metrics)->total_refunds ?? 0 }}"
        data-returned="{{ optional($row->metrics)->total_returned ?? 0 }}"
        data-videos="{{ optional($row->metrics)->total_videos ?? 0 }}"
        data-lives="{{ optional($row->metrics)->total_lives ?? 0 }}"
        data-product-tasks="{{ $row->completed_product_tasks ?? 0 }}" 
        data-month="{{ $row->month_label ?? '' }}" title="Detail Affiliator">
        Detail
    </x-atoms.button>
</div>