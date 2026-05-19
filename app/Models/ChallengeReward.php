<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeReward extends Model
{
    protected $fillable = [
        'challenge_id',
        'target_metric', 
        'reward_description'
    ];
    
    public function challenge() { return $this->belongsTo(Challenge::class); }
}