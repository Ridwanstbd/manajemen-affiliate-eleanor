<?php

namespace App\Imports;

use App\Models\CoreMetric;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoreMetricsImport implements ToModel, WithHeadingRow
{
    protected $importHistoryId;

    public function __construct($importHistoryId)
    {
        $this->importHistoryId = $importHistoryId;
    }

    public function model(array $row)
    {
        if (!isset($row['produk_yang_terjual_melalui_afiliasi']) && !isset($row['produk_yang_terjual_mel'])) {
            return null;
        }

        CoreMetric::updateOrCreate(
            [
                'import_history_id' => $this->importHistoryId,
            ],
            [
                'affiliate_gmv'              => $this->cleanCurrency($row['gmv_dari_afiliasi'] ?? $row['gmv_dari_kreator'] ?? 0),
                'refunds'                    => $this->cleanCurrency($row['pengembalian_dana'] ?? 0),
                'aov'                        => $this->cleanCurrency($row['aov'] ?? 0),
                'estimated_commission'       => $this->cleanCurrency($row['perkiraan_komisi'] ?? 0),
                
                'items_sold'                 => $row['produk_yang_terjual_melalui_afiliasi'] ?? ($row['produk_yang_terjual_mel'] ?? 0),
                'items_returned'             => $row['produk_yang_dikembalikan_dananya'] ?? ($row['produk_yang_dikembalikan_dana'] ?? 0),
                'samples_sent'               => $row['sampel_terkirim'] ?? 0,
                
                'video_count'                => $row['video'] ?? 0,
                'live_count'                 => $row['siaran_live'] ?? 0,
                
                'avg_daily_buyers'           => $row['rata_rata_pembeli_harian'] ?? 0,
                'avg_daily_sales_creators'   => $row['rata_rata_kreator_dengan_penjualan_harian'] ?? 0,
                'avg_daily_posting_creators' => $row['rata_rata_kreator_yang_memosting_konten_harian'] ?? 0,
                'avg_daily_items_sold'       => $row['rata_rata_produk_terjual_harian'] ?? 0,
            ]
        );

        return null;
    }

    private function cleanCurrency($value)
    {
        if (!$value) return 0;
        return (float) preg_replace('/[^0-9]/', '', (string)$value);
    }
}