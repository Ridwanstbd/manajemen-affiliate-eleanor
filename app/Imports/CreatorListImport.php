<?php

namespace App\Imports;

use App\Models\CreatorMetric;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CreatorListImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function model(array $row)
    {
        $user = User::firstOrCreate(
            ['username' => $row['creator_name']],
            ['role' => 'AFFILIATOR'] 
        );
        return new CreatorMetric([
            'user_id'              => $user->id,
            'record_date'          => $this->startDate,
            'gmv_dari_afiliasi'    => $this->cleanCurrency($row['gmv_dari_afiliasi']),
            'pesanan_teratribusi'  => $row['pesanan_teratribusi'] ?? 0,
            'produk_terjual'       => $row['produk_yang_terjual_melalui_afiliasi'] ?? 0,
            'aov'                  => $this->cleanCurrency($row['aov']),
            'perkiraan_komisi'     => $this->cleanCurrency($row['perkiraan_komisi']),
        ]);
    }

    private function cleanCurrency($value)
    {
        if (!$value) return 0;
        return (float) preg_replace('/[^0-9]/', '', $value);
    }

    public function batchSize(): int
    {
        return 500;
    }
}
