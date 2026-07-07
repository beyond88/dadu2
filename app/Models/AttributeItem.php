<?php

namespace App\Models;

use App\Traits\ModelBoot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AttributeItem
 */
class AttributeItem extends Model
{
    use HasFactory, ModelBoot;

    protected $appends = ['file_url'];
    protected $fillable = [
        'attribute_id',
        'name',
        'image',
        'status',
        'created_by',
        'updated_by',
    ];
    public const FILE_STORE_PATH = 'attribute_items';
protected static function booted()
{
    static::saved(function ($attributeItem) {
        try {
            logger()->info("AttributeItem saved: ID={$attributeItem->id}, Name={$attributeItem->name}");

            // Reload variations fresh with attributes
            $variations = $attributeItem->variations()->with(['attributeItems', 'product'])->get();

            if ($variations->isEmpty()) {
                logger()->warning("No variations found for AttributeItem ID={$attributeItem->id}");
            }

            foreach ($variations as $variation) {
                // Build new display name
                $name = $variation->attributeItems->pluck('name')->implode('-');

                // Update name & regenerate SKU
                $variation->forceFill(['name' => $name])->save();

                // Now refresh SKU as well
                $variation->refreshSku();
                $variation->refreshBarcodeImage();

                logger()->info("Variation {$variation->id} updated → Name={$variation->name}, SKU={$variation->sku}");
            }
        } catch (\Exception $e) {
            logger()->error("Error updating variations for AttributeItem ID={$attributeItem->id}: {$e->getMessage()}");
        }
    });
}

    public function variations()
    {
        return $this->belongsToMany(
            Variation::class,
            'attribute_item_variation', // pivot table
            'attribute_item_id',
            'variation_id'
        );
    }

    // MUTATORS & ACCESSORS
    /**
     * getFileUrlAttribute
     *
     * @return void
     */
    public function getFileUrlAttribute()
    {
        return getStorageImage(self::FILE_STORE_PATH, $this->image);
    }
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
