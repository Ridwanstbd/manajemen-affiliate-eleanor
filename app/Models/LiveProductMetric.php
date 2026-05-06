<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveProductMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_history_id',
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

    protected function casts(): array
    {
        return [
            'live_gmv' => 'decimal:2',
            'refunds' => 'decimal:2',
            'aov' => 'decimal:2',
            'estimated_commission' => 'decimal:2',
            'items_sold' => 'integer',
            'items_returned' => 'integer',
            'orders' => 'integer',
        ];
    }
    public function liveStream()
    {
        return $this->belongsTo(LiveStream::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function importHistory() { return $this->belongsTo(ImportHistory::class); }
}