<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'images',
        'weight',
        'height',
        'width',
        'length',
    ];

    protected $casts = [
        'images' => 'array',
    ];
    
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFirstImageUrlAttribute()
    {
        if (empty($this->images)) {
            return null;
        }
        
        $firstImage = is_string($this->images) ? json_decode($this->images, true)[0] : $this->images[0];

    
        return $firstImage  ? url('storage/'. $firstImage) : null;
        
    }
}
