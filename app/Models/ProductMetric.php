<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_history_id',
        'product_id',
        'affiliate_gmv',
        'refunds',
        'items_sold',
        'items_returned',
        'attributed_orders',
        'avg_daily_buyers',
        'avg_daily_sales_creators',
        'avg_daily_sales_videos',
        'avg_daily_sales_lives',
        'video_count',
        'live_count',
        'estimated_commission',
        'samples_sent'
    ];
    protected function casts(): array
    {
        return [
            'affiliate_gmv' => 'decimal:2',
            'refunds' => 'decimal:2',
            'estimated_commission' => 'decimal:2',
            'items_sold' => 'integer',
            'items_returned' => 'integer',
            'attributed_orders' => 'integer',
            'avg_daily_buyers' => 'integer',
            'avg_daily_sales_creators' => 'integer',
            'avg_daily_sales_videos' => 'integer',
            'avg_daily_sales_lives' => 'integer',
            'video_count' => 'integer',
            'live_count' => 'integer',
            'samples_sent' => 'integer',
        ];
    }

    public function importHistory()
    {
        return $this->belongsTo(ImportHistory::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}