<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStream extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'start_time',
        'end_time',
        'user_id',
    ];
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }
    public $incrementing = false;

    public function user() { return $this->belongsTo(User::class); }
    public function liveProductMetrics() { return $this->hasMany(LiveProductMetric::class); }
}