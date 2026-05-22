<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductUpdateImport implements ToCollection, WithHeadingRow, WithChunkReading
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
                    'image_path' => $row['main_image'] ?? null,
                ]
            );
        }
    }
    public function chunkSize(): int
    {
        return 500;
    }
}