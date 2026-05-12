<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeReward extends Model
{
    protected $fillable = [
        'challenge_id',
        'target_metric', // contoh: 'video_count', 'gmv'
        'target_value',  // contoh: 5, 10, 15
        'reward_description' // contoh: 'Free produk Eleanor Farm + Voucher belanja'
    ];
    
    public function challenge() { return $this->belongsTo(Challenge::class); }
}