<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'rules',
        'start_date',
        'end_date', 
        'commission_bonus',
        'banner_image_path',
        'is_active'  
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'commission_bonus' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function rewards() { return $this->hasMany(ChallengeReward::class); }
    public function winners() { return $this->hasMany(ChallengeWinner::class); }
}
