<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'address',
        'tracking_number',
        'courier',
        'shipping_cost'
    ];
    public function taskReports()
    {
        return $this->belongsToMany(TaskReport::class, 'sample_task_reports', 'sample_request_id', 'task_report_id');
    }
}
