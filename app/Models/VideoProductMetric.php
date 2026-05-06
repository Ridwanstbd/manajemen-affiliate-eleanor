<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProductMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'product_id',
        'import_history_id',
        'video_gmv',
        'orders',
        'aov',
        'avg_gmv_per_buyer',
        'items_sold',
        'refunds',
        'items_returned',
        'estimated_commission'
    ];

    protected function casts(): array
    {
        return [
            'video_gmv' => 'decimal:2',
            'aov' => 'decimal:2',
            'avg_gmv_per_buyer' => 'decimal:2',
            'refunds' => 'decimal:2',
            'estimated_commission' => 'decimal:2',
            'orders' => 'integer',
            'items_sold' => 'integer',
            'items_returned' => 'integer',
        ];
    }
    public function video() { return $this->belongsTo(Video::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function importHistory() { return $this->belongsTo(ImportHistory::class); }
}
