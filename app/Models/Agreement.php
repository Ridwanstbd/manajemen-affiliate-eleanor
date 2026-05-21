<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agreement extends Model
{
    protected $fillable = [
        'content',
        'is_active',
        'is_kol',
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_kol' => 'boolean',
        ];
    }

    public function kolContract(): HasOne
    {
        return $this->hasOne(KOLContract::class, 'agreement_id', 'id');
    }
}
