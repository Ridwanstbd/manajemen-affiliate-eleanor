<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTaskReport extends Model
{
    protected $table = 'product_task_reports';
    
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'task_report_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taskReport(): BelongsTo
    {
        return $this->belongsTo(TaskReport::class);
    }
}