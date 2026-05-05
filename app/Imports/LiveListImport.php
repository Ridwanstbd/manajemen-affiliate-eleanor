<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Product;
use App\Models\LiveStream;
use App\Models\LiveProductMetric;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LiveListImport implements ToModel, WithHeadingRow
{
    protected $importHistoryId;

    public function __construct($importHistoryId)
    {
        $this->importHistoryId = $importHistoryId;
    }

    public function model(array $row)
    {
        if (empty($row['live_id'])) {
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

        $liveStream = LiveStream::updateOrCreate(
            ['id' => $row['live_id']],
            [
                'user_id'    => $user->id,
                'title'      => $row['live_title'] ?? null,
                'start_time' => $this->parseDate($row['live_start_time'] ?? null),
                'end_time'   => $this->parseDate($row['live_end_time'] ?? null),
            ]
        );

        LiveProductMetric::updateOrCreate(
            [
                'import_history_id' => $this->importHistoryId,
                'live_stream_id'    => $liveStream->id,
                'product_id'        => $product->id,
            ],
            [
                'live_gmv'             => $this->cleanCurrency($row['gmv_dari_live_kreator'] ?? 0),
                'items_sold'           => $row['produk_yang_terjual_melalui_live'] ?? 0,
                'refunds'              => $this->cleanCurrency($row['pengembalian_dana'] ?? 0),
                'items_returned'       => $row['produk_yang_dikembalikan_dananya'] ?? ($row['produk_yang_dikembalikan_dana'] ?? 0),
                'orders'               => $row['pesanan_dari_live'] ?? 0,
                'aov'                  => $this->cleanCurrency($row['aov'] ?? 0),
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