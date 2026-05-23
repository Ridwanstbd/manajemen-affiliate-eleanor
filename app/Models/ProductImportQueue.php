<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportQueue extends Model
{
    protected $table = 'product_import_queue';

    protected $fillable = [
        'admin_id',
        'file_path',
        'status',
        'error_message',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}