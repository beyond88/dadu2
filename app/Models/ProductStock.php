<?php

namespace App\Models;

use App\Traits\ProductRelationship;
use App\Traits\WarehouseRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductStock
 */
class ProductStock extends Model
{
    use HasFactory, ProductRelationship, WarehouseRelationship;

    protected $fillable = [
        'quantity',
        'product_id',
        'warehouse_id',
        'attribute_id',
        'attribute_item_id',
        'price',
        'batch',
        'expiry_date',
        'customer_buying_price',
        'supplier_id',
        'variation_id',
        'backorders_allowed',
        'manage_stock'
    ];

    protected $appends = ['price_for_sale'];

    /**
     * attribute
     *
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * attributeItem
     *
     * @return BelongsTo
     */
    public function attributeItem(): BelongsTo
    {
        return $this->belongsTo(AttributeItem::class, 'attribute_item_id', 'id');
    }
     public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
    public function getPriceForSaleAttribute()
    {
        // Sold-by-weight ("barrel") products store the FINAL selling price
        // (per-kg x kg_per_barrel) on the product itself, so use it directly —
        // no per-warehouse fallback and no re-multiplication.
        if (optional($this->product)->is_weight_based && (float) $this->product->kg_per_barrel > 0) {
            return (float) $this->product->price;
        }

        return $this->resolveBaseSalePrice();
    }

    /**
     * Resolve the sale price from the variation -> stock -> product fallback chain.
     */
    protected function resolveBaseSalePrice()
    {
        // Check if this product has a variant
        $variation = $this->variation ?? null; // adjust relation name if different

        if (auth()->guard('customer')->check()) {
            // 1. variation customer price
            if ($variation && $variation->customer_buying_price > 0) {
                return $variation->customer_buying_price;
            }

            // 2. variation price
            if ($variation && $variation->price > 0) {
                return $variation->price;
            }

            // 3. Item customer price
            if ($this->customer_buying_price > 0) {
                return $this->customer_buying_price;
            }

            // 4. Item price
            if ($this->price > 0) {
                return $this->price;
            }

            // 5. Product customer price
            if ($this->product->customer_buying_price > 0) {
                return $this->product->customer_buying_price;
            }

            // 6. Product price
            return $this->product->price;
        } else {
            // Non-customer logic
            if ($variation && $variation->price > 0) {
                return $variation->price;
            }

            if ($this->price > 0) {
                return $this->price;
            }

            return $this->product->price ?? 0;
        }
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'warehouse_id');
    }

}
