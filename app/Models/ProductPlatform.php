<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPlatform extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'platform_name',
        'platform_id',
        'platform_data',
        'ecommerce_id',
        'is_active',
        
    ];
    protected $casts = [
        'platform_data' => 'array',
    ];
    public function platforms()
    {
        return $this->belongsToMany(Platform::class,'product_platform','platform_id', 'product_id', 'id');
    }
     public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }
}
