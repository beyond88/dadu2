<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCarton extends Model
{
    protected $table = 'product_cartons';

    protected $fillable = [
        'product_id',
        'carton_product_id',
        'qty_per_carton',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function cartonProduct()
    {
        return $this->belongsTo(Product::class, 'carton_product_id');
    }
}
