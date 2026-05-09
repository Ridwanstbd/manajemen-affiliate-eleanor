<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductUpdateImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['product_id']) || $row['product_id'] == 'V3' || $row['product_id'] == 'ID Produk') {
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
                ['id' => $row['id_produk']],
                [
                    'name' => $row['nama_produk'],
                    'category' => $row['kategori'] ?? null,
                    'sku_id' => $row['id_sku'] ?? null,
                    'variation_value' => $row['nilai_variasi'] ?? null,
                    'product_detail' => $row['deskripsi_produk'] ?? null,
                    'brand' => $row['merek'] ?? null,
                    'price' => isset($row['harga_ritel_(mata_uang_lokal)']) ? (float) $row['harga_ritel'] : 0, 
                    'seller_sku' => $row['sku_penjual'] ?? null,
                    
                    'parcel_weight' => isset($row['berat_paket(g)']) ? (float) $row['berat_paket(g)'] : null,
                    'parcel_length' => isset($row['panjang_paket(cm)']) ? (float) $row['panjang_paket(cm)'] : null,
                    'parcel_width'  => isset($row['lebar_paket(cm)']) ? (float) $row['lebar_paket(cm)'] : null,
                    'parcel_height' => isset($row['tinggi_paket(cm)']) ? (float) $row['tinggi_paket(cm)'] : null,
                    
                    'image_path' => $row['gambar_utama'] ?? null,
                    'additional_images' => $additionalImages, 
                ]
            );
        }
    }
}