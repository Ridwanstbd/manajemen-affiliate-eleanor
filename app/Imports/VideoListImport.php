<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Product;
use App\Models\Video;
use App\Models\VideoProductMetric;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class VideoListImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['video_id'])) {
            return null;
        }
        $user = User::firstOrCreate(
            ['username' => $row['creator_name']],
            ['role' => 'AFFILIATOR']
        );
        $product = Product::firstOrCreate(
            ['id' => $row['product_id']],
            ['name' => $row['product_name'] ?? null]
        );
        $video = Video::updateOrCreate(
            ['id' => $row['video_id']],
            [
                'user_id'   => $user->id,
                'title'     => $row['video_title'] ?? null,
                'post_date' => $this->parseDate($row['post_date'] ?? null),
                'link'      => $row['video_link'] ?? null,
            ]
        );
        VideoProductMetric::updateOrCreate(
            [
                'video_id'   => $video->id,
                'product_id' => $product->id,
            ],
            [
                'video_gmv'            => $this->cleanCurrency($row['gmv_dari_video_afiliasi'] ?? 0),
                'orders'               => $row['pesanan_dari_video'] ?? 0,
                'aov'                  => $this->cleanCurrency($row['aov'] ?? 0),
                'avg_gmv_per_buyer'    => $this->cleanCurrency($row['rata_rata_gmv_per_pembeli'] ?? 0),
                'items_sold'           => $row['produk_yang_terjual_melalui_video'] ?? 0,
                'refunds'              => $this->cleanCurrency($row['pengembalian_dana'] ?? 0),
                'items_returned'       => $row['produk_yang_dikembalikan_dananya'] ?? ($row['produk_yang_dikembalikan_dana'] ?? 0),
                'estimated_commission' => $this->cleanCurrency($row['perkiraan_komisi'] ?? 0),
            ]
        );

        return null;
    }

    private function cleanCurrency($value)
    {
        if (!$value) return 0;
        return (float) preg_replace('/[^0-9]/', '', (string)$value);
    }

    private function parseDate($value)
    {
        if (!$value) return null;

        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value));
            }
            
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null; 
        }
    }
}