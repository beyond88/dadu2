<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Product
 */
class Product extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'name',
        'parts_no',
        'sku',
        'barcode',
        'barcode_image',
        'brand_id',
        'manufacturer_id',
        'model',
        'price',
        'buying_price',
        'weight',
        'weight_unit_id',
        'dimension_l',
        'dimension_w',
        'dimension_d',
        'measurement_unit_id',
        'notes',
        'desc',
        'thumb',
        'is_variant',
        'status',
        'tax_status',
        'custom_tax',
        'created_by',
        'updated_by',
        'stock',
        'is_variant',
        'is_batch_product',
        'split_sale',
        'available_for',
        'customer_buying_price',
        'is_weight_based',
        'kg_per_barrel',
        'barrel_label',

    ];

    /**
     * appends
     *
     * @var array
     */
    protected $appends = ['thumb_url', 'average_cost'];

    // CONST
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const TAX_INCLUDED = 'included';
    public const TAX_EXCLUDED = 'excluded';

    public const FILE_STORE_PATH = 'products';
    public const BARCODE_STORE_PATH = 'product_barcodes';

    public const SALE_AVAILABLE_FOR = [
        'all'       => 'all',
        'customer'  => 'customer',
        'warehouse' => 'warehouse',
    ];

    public function invoiceItem()
    {
        return $this->hasMany(InvoiceItem::class);
    }


    /**
     * totalSale
     *
     * @return void
     */
    public function totalSale()
    {
        return InvoiceItem::where('product_id', $this->id)->sum('sub_total');
    }
    public function totalSaleQty()
    {
        return InvoiceItem::where('product_id', $this->id)->sum('quantity');
    }

    /**
     * getThumbUrlAttribute
     *
     * @return void
     */
    public function getThumbUrlAttribute()
    {
        return getStorageImage(self::FILE_STORE_PATH, $this->thumb);
    }
   public function platforms()
    {
        return $this->hasMany(ProductPlatform::class)
                    ->where('is_active', true);
    }


    // RELATIONS
    /**
     * category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * attributes
     *
     * @return void
     */
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function mainAttributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes', 'product_id', 'attribute_id')->distinct();
    }
    /**
     * stock
     *
     * @return void
     */
    public function stock()
    {
        return $this->hasOne(ProductStock::class);
    }
    public function productStock()
    {
        return $this->hasOne(ProductStock::class);
    }

    /**
     * warehouseStock
     *
     * @param  mixed $warehouse
     * @return void
     */
    public function warehouseStock($warehouse)
    {
        return $this->stock()
            ->where('warehouse_id', $warehouse)
            ->first()
            ->quantity ?? 0;
    }

    public function warehouseStockQty()
    {
        return $this->hasOne(ProductStock::class, 'product_id', 'id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id', 'id');
    }

    public function weight_unit()
    {
        return $this->belongsTo(WeightUnit::class, 'weight_unit_id', 'id');
    }


    public function allStock()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function purchaseItemReceives()
    {
        return $this->hasMany(PurchaseItemReceive::class);
    }

    /**
     * averageCost
     *
     * @return float
     */
    public function getAverageCostAttribute()
    {
        $receiveItems = $this->purchaseItemReceives;

        if ($receiveItems->isEmpty()) {
            return $this->price ?: 0;
        }

        $totalQuantity = $receiveItems->sum('quantity');

        if ($totalQuantity <= 0) {
            return $this->price ?: 0;
        }

        $totalCost = $receiveItems->reduce(function ($carry, $item) {
            $unitPrice = $item->price ?: 0;
            return $carry + ($unitPrice * $item->quantity);
        }, 0);

        return $totalCost / $totalQuantity;
    }

    /**
     * stockValue
     *
     * @return float
     */
    public function getStockValueAttribute()
    {
        $averageCost = $this->average_cost;
        $currentQuantity = $this->stock ?? $this->allStock->sum('quantity');

        return $averageCost * $currentQuantity;
    }

    /**
     * The product's base buying price expressed PER KG.
     * `buying_price` is stored per-kg for sold-by-weight products, so this is the raw value.
     */
    public function getBuyingPricePerKgAttribute()
    {
        return $this->buying_price !== null ? (float) $this->buying_price : null;
    }

    /**
     * The product's base buying price expressed PER BARREL (container).
     * For sold-by-weight products this converts the per-kg price using kg_per_barrel;
     * for other products the stored buying_price is already per-unit.
     */
    public function getBuyingPricePerBarrelAttribute()
    {
        if ($this->buying_price === null) {
            return null;
        }

        if ($this->is_weight_based && (float) $this->kg_per_barrel > 0) {
            return (float) $this->buying_price * (float) $this->kg_per_barrel;
        }

        return (float) $this->buying_price;
    }

    public function measurement_unit(){
        return $this->belongsTo(MeasurementUnit::class);

    }
    public function brand(){
        return $this->belongsTo(Brand::class);

    }
    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function carton()
    {
        return $this->hasOne(ProductCarton::class, 'product_id');
    }

    public function subProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_relations',
            'parent_product_id',
            'related_product_id'
        )->using(ProductRelation::class)->withPivot('quantity')->withTimestamps();
    }
}
