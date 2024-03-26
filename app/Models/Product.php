<?php

namespace App\Models;

use App\Traits\ImageUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, ImageUploader, SoftDeletes;

    protected $fillable = ['nama', 'price', 'quantity', 'description', 'category_id', 'brand_id', 'user_id'];

    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Get the products's image.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
