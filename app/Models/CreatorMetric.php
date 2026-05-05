<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreatorMetric extends Model
{
    use HasFactory;
    protected $fillable = [
        'import_history_id',
        'user_id',
        'affiliate_gmv',
        'refunds',
        'attributed_orders',
        'items_sold',
        'items_returned',
        'aov',
        'avg_daily_items_sold',
        'video_count',
        'live_count',
        'estimated_commission',
        'samples_sent',
    ];

    protected $casts = [
        'affiliate_gmv' => 'decimal:2',
        'refunds' => 'decimal:2',
        'attributed_orders' => 'integer',
        'items_sold' => 'integer',
        'items_returned' => 'integer',
        'aov' => 'decimal:2',
        'avg_daily_items_sold' => 'decimal:2',
        'video_count' => 'integer',
        'live_count' => 'integer',
        'estimated_commission' => 'decimal:2',
        'samples_sent' => 'integer',
    ];

    public function importHistory()
    {
        return $this->belongsTo(ImportHistory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}