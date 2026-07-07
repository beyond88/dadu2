<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductRelation extends Pivot
{
    protected $table = 'product_relations';

    public $incrementing = true;

    protected $fillable = [
        'parent_product_id',
        'related_product_id',
        'quantity',
    ];

    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }

    public function relatedProduct()
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }
}
