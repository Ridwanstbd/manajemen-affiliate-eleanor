<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','category','image_path','stock','product_detail','mandatory_video_count','is_visible'];
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'stock' => 'integer',
            'mandatory_video_count' => 'integer',
        ];
    }
    public $incrementing = false;

    public function metrics() { return $this->hasMany(ProductMetric::class); }

    public function videoMetrics() { return $this->hasMany(VideoProductMetric::class); }
    public function liveMetrics() { return $this->hasMany(LiveProductMetric::class); }
    public function taskReports() { return $this->belongsToMany(TaskReport::class, 'product_task_reports', 'product_id', 'task_report_id'); }
    public function sampleRequestDetails() { return $this->hasMany(SampleRequestDetail::class); }
}
