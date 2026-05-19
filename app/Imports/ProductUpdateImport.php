<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
class ProductUpdateImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading
{
    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['product_id']) || !is_numeric($row['product_id'])) {
                continue;
            }

            $additionalImages = [];
            for ($i = 2; $i <= 9; $i++) {
                $imageKey = 'image_' . $i;
                if (!empty($row[$imageKey])) {
                    $additionalImages[] = $row[$imageKey];
                }
            }

            $isCodSupported = false;
            if (isset($row['cod']) && (strtolower($row['cod']) == 'supported' || $row['cod'] == 1 || strtolower($row['cod']) == 'ya')) {
                $isCodSupported = true;
            }

            Product::updateOrCreate(
                ['id' => $row['product_id']],
                [
                    'name' => $row['product_name'],
                    'category' => $row['category'] ?? null,
                    'sku_id' => $row['sku_id'] ?? null,
                    'variation_value' => $row['variation_value'] ?? null,
                    'product_detail' => $row['product_description'] ?? null,
                    'brand' => $row['brand'] ?? null,
                    'price' => isset($row['price']) ? (float) $row['price'] : 0,
                    'seller_sku' => $row['seller_sku'] ?? null,
                    
                    'parcel_weight' => isset($row['parcel_weight']) ? (float) $row['parcel_weight'] : null,
                    'parcel_length' => isset($row['parcel_length']) ? (float) $row['parcel_length'] : null,
                    'parcel_width'  => isset($row['parcel_width']) ? (float) $row['parcel_width'] : null,
                    'parcel_height' => isset($row['parcel_height']) ? (float) $row['parcel_height'] : null,
                    'image_path' => $row['main_image'] ?? null,
                    'additional_images' => $additionalImages,
                ]
            );
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}