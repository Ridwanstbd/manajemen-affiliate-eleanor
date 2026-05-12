<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeWinner extends Model
{
    protected $fillable = [
        'challenge_id',
        'user_id',
        'category', // contoh: 'GMV TERTINGGI', 'VIDEO TERBANYAK'
        'reward_given'
    ];

    public function challenge() { return $this->belongsTo(Challenge::class); }
    public function user() { return $this->belongsTo(User::class); }
}