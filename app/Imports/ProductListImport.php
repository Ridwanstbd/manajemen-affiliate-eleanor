<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductMetric;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductListImport implements ToModel, WithHeadingRow
{
    protected $importHistoryId;

    public function __construct($importHistoryId)
    {
        $this->importHistoryId = $importHistoryId;
    }

    public function model(array $row)
    {
        if (empty($row['product_id'])) {
            return null;
        }

        $product = Product::updateOrCreate(
            ['id' => $row['product_id']],
            [
                'name'     => $row['product_name'] ?? null,
                'category' => $row['product_category'] ?? null,
            ]
        );

        ProductMetric::updateOrCreate(
            [
                'import_history_id' => $this->importHistoryId,
                'product_id'        => $product->id,
            ],
            [
                'affiliate_gmv'            => $this->cleanCurrency($row['gmv_dari_afiliasi'] ?? 0),
                'refunds'                  => $this->cleanCurrency($row['pengembalian_dana'] ?? 0),
                
                'items_sold'               => $row['produk_yang_terjual_melalui_afiliasi'] ?? ($row['produk_yang_terjual_mel'] ?? 0),
                'items_returned'           => $row['produk_yang_dikembalikan_dananya'] ?? ($row['produk_yang_dikembalikan_dana'] ?? 0),
                'attributed_orders'        => $row['pesanan_teratribusi'] ?? 0,
                
                'avg_daily_buyers'         => $row['rata_rata_pembeli_harian'] ?? 0,
                'avg_daily_sales_creators' => $row['rata_rata_kreator_dengan_penjualan_harian'] ?? 0,
                'avg_daily_sales_videos'   => $row['rata_rata_video_dengan_penjualan_harian'] ?? 0,
                'avg_daily_sales_lives'    => $row['rata_rata_siaran_live_dengan_penjualan_harian'] ?? 0,
                
                'video_count'              => $row['video'] ?? 0,
                'live_count'               => $row['siaran_live'] ?? 0,
                'estimated_commission'     => $this->cleanCurrency($row['perkiraan_komisi'] ?? 0),
                'samples_sent'             => $row['sampel_terkirim'] ?? 0,
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