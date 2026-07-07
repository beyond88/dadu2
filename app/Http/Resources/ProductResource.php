<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'parts_no' => $this->parts_no,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'barcode_image' => $this->barcode_image,
            'category' => new CategoriesResource($this->category),
            'manufacturer' => new ManufacturerResource($this->manufacturer),
            'brand' => new BrandResource($this->brand),
            'measurement_unit' => new ManufacturerResource($this->measurement_unit),
            'weight_unit' => new WeightUnitResource($this->weight_unit),
            'model' => $this->model,
            // `price` already holds the final selling price (per-barrel for
            // sold-by-weight products = entered per-kg price x kg_per_barrel).
            'price' => $this->price,
            'is_weight_based' => (bool) $this->is_weight_based,
            'kg_per_barrel' => $this->kg_per_barrel,
            'barrel_label' => $this->barrel_label ?: __('custom.barrel'),
            'weight' => $this->weight,
            'notes' => $this->notes,
            'desc' => $this->desc,
            'stock' => $this->weight_unit ? $this->stock .' '.$this->weight_unit->name : $this->stock,
            'status' => $this->status,
            'custom_tax' => $this->custom_tax,
            'dimension_l' => $this->dimension_l,
            'dimension_w' => $this->dimension_w,
            'dimension_d' => $this->dimension_d,
            'customer_buying_price' => $this->customer_buying_price,
            'custom_tax_amount' => $this->custom_tax,
            'tax' => $this->tax_status == \App\Models\Product::TAX_INCLUDED ? 'included' : 'excluded',
            'is_variant' => $this->is_variant ? 'Yes' : 'No',
            'is_split_sale' => $this->split_sale ? 'Yes' : 'No',
            'is_available_for' => $this->available_for,
            'image' => getStorageImage(\App\Models\Product::FILE_STORE_PATH, $this->thumb),
            'variants' => $this->when($this->relationLoaded('variations') || $this->is_variant, function () {
                return $this->variations->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'barcode_image' => $variant->barcode_image,
                        'price' => $variant->price,
                        'customer_buying_price' => $variant->customer_buying_price,
                        'weight' => $variant->weight,
                        'dimension_l' => $variant->dimension_l,
                        'dimension_w' => $variant->dimension_w,
                        'dimension_d' => $variant->dimension_d,
                        'attribute_items' => $variant->attributeItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'attribute_id' => $item->attribute_id,
                            ];
                        }),
                    ];
                });
            }),
            // 'stock_quantity' => $this->getStockDetails($this,$warehouses),
            // 'product_create_info' => new ProductCreateResource($this['product_create_info']),
            // 'old_attribute_data' => json_decode($this['old_attribute_data'])
            // 'date_added' => $this->created_at->format('Y-m-d H:i:s'),
            // 'last_updated' => $this->updated_at->format('Y-m-d H:i:s'),
            // Add more custom keys as needed
        ];
    }


}

