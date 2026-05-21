<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agreement extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'is_active',
        'is_kol'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_kol' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kolContracts(): HasMany
    {
        return $this->hasMany(KOLContract::class, 'agreement_id', 'id');
    }
}