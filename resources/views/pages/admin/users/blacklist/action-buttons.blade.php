<div style="display: flex; gap: 8px;">
    <x-atoms.button variant="outline" size="sm" type="button" class="btn-detail-blacklist"
        data-id="{{ $row->id ?? '' }}"
        data-username="{{ $row->username ?? '' }}"
        data-email="{{ $row->email ?? '-' }}"
        data-phone="{{ $row->phone_number ?? '-' }}"
        data-gmv="{{ optional($row->metrics)->total_gmv ?? 0 }}"
        data-items="{{ optional($row->metrics)->total_items ?? 0 }}"
        data-commission="{{ optional($row->metrics)->total_commission ?? 0 }}"
        data-samples="{{ optional($row->metrics)->total_samples_metric ?? 0 }}" 
        data-aov="{{ optional($row->metrics)->avg_aov ?? 0 }}"
        data-refunds="{{ optional($row->metrics)->total_refunds ?? 0 }}"
        data-returned="{{ optional($row->metrics)->total_returned ?? 0 }}"
        data-videos="{{ optional($row->metrics)->total_videos ?? 0 }}"
        data-lives="{{ optional($row->metrics)->total_lives ?? 0 }}"
        data-reason="{{ optional($row->blacklist_info)->violation_reason ?? 'Pelanggaran Tidak Diketahui' }}"
        data-date="{{ optional($row->blacklist_info)->blacklist_date ? \Carbon\Carbon::parse($row->blacklist_info->blacklist_date)->translatedFormat('d M Y') : '-' }}"
        title="Detail Blacklist">
        Detail
    </x-atoms.button>
</div>