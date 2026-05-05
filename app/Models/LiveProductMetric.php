<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveProductMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_stream_id',
        'product_id',
        'live_gmv',
        'items_sold',
        'refunds',
        'items_returned',
        'orders',
        'aov',
        'estimated_commission'
    ];
    public function liveStream()
    {
        return $this->belongsTo(LiveStream::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}