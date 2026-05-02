<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAccessRequest extends Model
{
    use HasFactory;

    protected $table = 'system_access_requests';

    protected $fillable = [
        'tiktok_username',
        'phone_number',
        'email',
        'status',
    ];
}