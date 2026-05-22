<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\User;
use App\Notifications\ImportFinishedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents; 
use Maatwebsite\Excel\Concerns\RegistersEventListeners; 
use Maatwebsite\Excel\Events\AfterImport; 

class ProductUpdateImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithEvents
{
    use RegistersEventListeners;
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
    public static function afterImport(AfterImport $event)
    {
        $admins = User::where('role', 'ADMINISTRATOR')->get();
        
        Notification::send($admins, new ImportFinishedNotification());
    }

    public function chunkSize(): int
    {
        return 500;
    }
}