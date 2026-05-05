<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','category','image_path','stock','product_detail','mandatory_video_count','is_visible'];
    public $incrementing = false;

    public function metrics() { return $this->hasMany(ProductMetric::class); }
}
