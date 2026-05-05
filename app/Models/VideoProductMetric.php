<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoProductMetric extends Model
{
    protected $fillable = [
    'video_id', 'product_id', 'import_history_id', 'video_gmv', 'orders', 
    'aov', 'avg_gmv_per_buyer', 'items_sold', 'refunds', 'items_returned', 'estimated_commission'
];
}
