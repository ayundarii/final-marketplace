<?php

namespace App\Models;

use App\Traits\ImageUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, ImageUploader, SoftDeletes;

    protected $fillable = ['nama', 'description'];

    protected $dates = ['deleted_at'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

     /**
     * Get the brand's image.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
