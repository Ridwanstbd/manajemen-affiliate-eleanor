<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $fillable = [
        'admin_id',
        'import_date',
        'start_date',
        'end_date',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}
