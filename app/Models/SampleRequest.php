<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'address', 'tracking_number', 'courier', 'shipping_cost'
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
        ];
    }

    public function taskReports()
    {
        return $this->belongsToMany(TaskReport::class, 'sample_task_reports', 'sample_request_id', 'task_report_id');
    }
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(SampleRequestDetail::class); }
}