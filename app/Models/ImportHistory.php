<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $fillable = [
        'admin_id', 'import_date', 'start_date', 'end_date',
    ];
    protected function casts(): array
    {
        return [
            'import_date' => 'datetime',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }

    public function coreMetrics() { return $this->hasMany(CoreMetric::class); }
    public function creatorMetrics() { return $this->hasMany(CreatorMetric::class); }
    public function productMetrics() { return $this->hasMany(ProductMetric::class); }
    public function videoProductMetrics() { return $this->hasMany(VideoProductMetric::class); }
    public function liveProductMetrics() { return $this->hasMany(LiveProductMetric::class); }
}