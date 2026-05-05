<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','category','image_path','stock','product_detail','mandatory_video_count','is_visible'];
    public $incrementing = false;
}
