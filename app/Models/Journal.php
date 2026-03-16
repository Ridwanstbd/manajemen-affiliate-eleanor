<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = ['date', 'description'];

    public function details() {
        return $this->hasMany(JournalDetail::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime', // atau 'date'
        ];
    }


}
