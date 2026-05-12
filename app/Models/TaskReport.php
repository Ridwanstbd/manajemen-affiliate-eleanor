<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiktok_video_link', 'task_status','due_date'
    ];
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function sampleRequests()
    {
        return $this->belongsToMany(SampleRequest::class, 'sample_task_reports', 'task_report_id', 'sample_request_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_task_reports', 'task_report_id', 'product_id');
    }
}