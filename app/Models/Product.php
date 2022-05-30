<?php

namespace App\Models;

use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public $transformer = ProductTransformer::class;

    const UNAVAILABLE_PRODUCT = "unavailable";
    const AVAILABLE_PRODUCT = "available";

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status', // Available / Unavailable - We will use Constants
        'image',
        'seller_id'
    ];

    protected $hidden = [
        'pivot'
    ];

    public static function boot() {
        parent::boot();
        self::updated(function(Product $product) {
            if($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });
    }

    public function isAvailable()
    {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
