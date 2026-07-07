<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Laravel can auto-detect 'variations', so this is optional.
     */
    protected $table = 'variations';
    // protected $appends = ['display_name'];
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'product_id',
        'sku',
        'price',
        'customer_buying_price',
        'barcode',
        'barcode_image',
        'desc',
        'thumb',
        'stock_quantity',
        'stock_status',
        'manage_stock',
        'weight',
        'dimension_l',
        'dimension_w',
        'dimension_d',
    ];
        protected $casts = [
        'manage_stock' => 'boolean',
    ];
    // public function getDisplayNameAttribute()
    // {
    //     // Refresh the relationship if it's already loaded
    //     $this->load('attributeItems');
    //     return $this->attributeItems->pluck('name')->implode('-');
    // }
    public function refreshSku()
{
    $this->loadMissing(['product', 'attributeItems']);

    $baseSku = $this->product->sku ?? 'P' . $this->product_id;

    $attributes = $this->attributeItems
        ->pluck('name')
        ->map(fn($n) => str_replace(' ', '', $n))
        ->implode('-');

    $this->sku = $attributes
        ? $attributes . '-' . $this->id  // clean + unique
        : $baseSku . '-' . $this->id;

    $this->save();
}
public function refreshBarcodeImage()
{
    // Build attributes string from current attribute items
    $attributeString = $this->attributeItems->pluck('name')->implode('-');

    // Current barcode (e.g. Red-S-1_1759053308.png)
    $oldBarcode = $this->barcode_image;

    // Find the suffix: everything from "-{id}_" onwards
    if (preg_match('/-\d+_.+$/', $oldBarcode, $matches)) {
        $suffix = $matches[0]; // e.g. "-1_1759053308.png"
    } else {
        // fallback in case of unexpected format
        $suffix = '-' . $this->id . '_' . time() . '.png';
    }

    // Rebuild with new attribute string but keep suffix
    $this->barcode_image = $attributeString . $suffix;
    $this->save();
}


    /**
     * Relationships
     */

    // Variation belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Variation has many AttributeItems through pivot table
    public function attributeItems()
    {
        return $this->belongsToMany(
            AttributeItem::class,
            'attribute_item_variation', // pivot table
            'variation_id',
            'attribute_item_id'
        );
    }


    public function platforms()
    {
        return $this->hasMany(VariationPlatform::class);
    }
    // Variation has stock (optional, if you manage stock separately)
    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'variation_id');
    }
}
