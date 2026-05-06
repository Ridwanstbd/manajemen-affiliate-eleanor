<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $fillable = [
    'content',
    'is_active'
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
