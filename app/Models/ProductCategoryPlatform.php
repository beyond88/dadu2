<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryPlatform extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_category_id',
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
        return $this->belongsToMany(Platform::class,'product_platform','platform_id', 'product_category_id', 'id');
    }
  public  function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }
}
