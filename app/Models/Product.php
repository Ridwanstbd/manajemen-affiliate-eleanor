<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'name', 'category', 'sku_id', 'variation_value',
        'product_detail', 'brand', 'price', 'stock', 'seller_sku',
        'minimum_order_quantity', 'parcel_weight', 'parcel_length',
        'parcel_width', 'parcel_height', 'is_cod_supported',
        'image_path', 'additional_images', 'size_chart', 
        'tts_product_url', 'toko_product_url', 
        'mandatory_video_count', 'is_visible'
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'is_visible' => 'boolean',
            'is_cod_supported' => 'boolean',
            'stock' => 'integer',
            'minimum_order_quantity' => 'integer',
            'mandatory_video_count' => 'integer',
            'price' => 'decimal:2',
            'parcel_weight' => 'decimal:2',
            'parcel_length' => 'decimal:2',
            'parcel_width' => 'decimal:2',
            'parcel_height' => 'decimal:2',
            'additional_images' => 'array', 
        ];
    }

    public function metrics() { 
        return $this->hasMany(ProductMetric::class); 
    }

    public function videoMetrics() { 
        return $this->hasMany(VideoProductMetric::class); 
    }

    public function liveMetrics() { 
        return $this->hasMany(LiveProductMetric::class); 
    }

    public function taskReports() { 
        return $this->belongsToMany(TaskReport::class, 'product_task_reports', 'product_id', 'task_report_id'); 
    }

    public function sampleRequestDetails() { 
        return $this->hasMany(SampleRequestDetail::class); 
    }

    public function kolContracts() {
        return $this->belongsToMany(KOLContract::class, 'kol_contract_product');
    }
}