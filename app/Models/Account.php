<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Account extends Model
{
   public const ALLOWED_TYPES = ['asset', 'liability', 'equity', 'revenue', 'expense'];
   protected $fillable = ['code', 'name', 'type'];

   protected static function booted(): void
    {
        static::saving(function ($account) {
            if (!in_array($account->type, self::ALLOWED_TYPES)) {
                throw new InvalidArgumentException(
                    "Invalid account type. The type must be one of: " . implode(', ', self::ALLOWED_TYPES)
                );
            }
        });
    }
   public function details() {
        return $this->hasMany(JournalDetail::class);
    }
}
