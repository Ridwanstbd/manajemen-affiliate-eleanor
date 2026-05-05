<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'id', 
        'user_id',
        'title',
        'post_date',
        'link'
        ];
    public $incrementing = false;

    public function productMetrics() {
        return $this->hasMany(VideoProductMetric::class);
    }
}
