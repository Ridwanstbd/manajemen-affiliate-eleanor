<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SampleTaskReport extends Model
{
    protected $table = 'sample_task_reports';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'sample_request_id',
        'task_report_id',
    ];

    public function sampleRequest(): BelongsTo
    {
        return $this->belongsTo(SampleRequest::class);
    }

    public function taskReport(): BelongsTo
    {
        return $this->belongsTo(TaskReport::class);
    }
}