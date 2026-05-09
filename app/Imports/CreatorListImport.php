<?php

namespace App\Imports;

use App\Models\CreatorMetric;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CreatorListImport implements ToModel, WithHeadingRow
{
    protected $importHistoryId;

    public function __construct($importHistoryId)
    {
        $this->importHistoryId = $importHistoryId;
    }

    public function model(array $row)
    {
        if (empty($row['creator_name'])) {
            return null;
        }

        $user = User::firstOrCreate(
            ['username' => $row['creator_name']],
            ['role' => 'AFFILIATOR'] 
        );

        CreatorMetric::updateOrCreate(
            [
                'import_history_id' => $this->importHistoryId,
                'user_id'           => $user->id,
            ],
            [
                'affiliate_gmv'        => $this->cleanCurrency($row['gmv_dari_afiliasi'] ?? $row['gmv_dari_kreator'] ?? 0),
                'refunds'              => $this->cleanCurrency($row['pengembalian_dana'] ?? 0),
                'aov'                  => $this->cleanCurrency($row['aov'] ?? 0),
                'estimated_commission' => $this->cleanCurrency($row['perkiraan_komisi'] ?? 0),
                'attributed_orders'    => $row['pesanan_teratribusi'] ?? 0,
                'items_sold'           => $row['produk_yang_terjual_melalui_afiliasi'] ?? ($row['produk_yang_terjual_mel'] ?? 0),
                'items_returned'       => $row['produk_yang_dikembalikan'] ?? ($row['produk_yang_dikembalik'] ?? 0),
                'avg_daily_items_sold' => $row['rata_rata_produk_terjual'] ?? ($row['rata_rata_produk_terjua'] ?? 0),
                'video_count'          => $row['video'] ?? 0,
                'live_count'           => $row['siaran_live'] ?? 0,
                'samples_sent'         => $row['sampel_terkirim'] ?? 0,
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