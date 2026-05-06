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
        'target',
        'prize',
        'commission_bonus',
        'banner_image_path'
    ];
    protected function casts(): array
    {
        return [
            'commission_bonus' => 'decimal:2',
            'target' => 'integer',
        ];
    }
}
