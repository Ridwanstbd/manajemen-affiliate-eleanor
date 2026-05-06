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

    protected function casts(): array
    {
        return [
            'post_date' => 'datetime',
        ];
    }
    public $incrementing = false;

    public function productMetrics() {
        return $this->hasMany(VideoProductMetric::class);
    }
    public function user() { return $this->belongsTo(User::class); }
}
