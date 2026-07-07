<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'price', 'regular_price', 'sku', 'attributes', 'stock_quantity', 'stock_status', 'manage_stock', 'created_by', 'updated_by'
    ];

    
    protected $casts = [
        'attributes' => 'array',
        'manage_stock' => 'boolean',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function platforms()
    {
        return $this->hasMany(ProductVariationPlatform::class);
    }
}
