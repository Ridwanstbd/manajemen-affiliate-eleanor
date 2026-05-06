<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_history_id',
        'affiliate_gmv',
        'items_sold',
        'refunds',
        'items_returned',
        'avg_daily_buyers',
        'aov',
        'video_count',
        'live_count',
        'avg_daily_sales_creators',
        'avg_daily_posting_creators',
        'avg_daily_items_sold',
        'samples_sent',
        'estimated_commission'
    ];
    protected function casts(): array
    {
        return [
            'affiliate_gmv' => 'decimal:2',
            'refunds' => 'decimal:2',
            'aov' => 'decimal:2',
            'estimated_commission' => 'decimal:2',
            'items_sold' => 'integer',
            'items_returned' => 'integer',
            'avg_daily_buyers' => 'integer',
            'video_count' => 'integer',
            'live_count' => 'integer',
            'avg_daily_sales_creators' => 'integer',
            'avg_daily_posting_creators' => 'integer',
            'avg_daily_items_sold' => 'integer',
            'samples_sent' => 'integer',
        ];
    }

    public function importHistory()
    {
        return $this->belongsTo(ImportHistory::class);
    }
}
