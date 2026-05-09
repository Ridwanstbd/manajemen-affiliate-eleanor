<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KOLContract extends Model
{
    use HasFactory;

    protected $table = 'kol_contracts';

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 
        'contract_fee', 'required_video_count', 'status', 'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'kol_contract_product', 'kol_contract_id', 'product_id');
    }

    public function getIsPerformanceTargetMetAttribute()
    {
        $actualVideos = $this->user->creatorMetrics()
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->sum('video_count');

        return $actualVideos >= $this->required_video_count;
    }
}