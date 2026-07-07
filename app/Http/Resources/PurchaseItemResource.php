<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Get SKU from variation if exists, otherwise from product (same as blade)
        $sku = $this->variation ? $this->variation->sku : $this->product->sku;

        // Build variant info string if it's a variant product
        $variant_info = null;
        if($this->product->is_variant == 1 && $this->variation)
        {
            $variant_info = $this->variation->name;
        }

        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $this->product->name,
                'is_variant' => $this->product->is_variant,
                'stock_id' => $this->product_stock_id,
            ],
            'variation' => $this->variation ? [
                'id' => $this->variation->id,
                'sku' => $this->variation->sku,
                'name' => $this->variation->name,
            ] : null,
            'sku' => $sku,
            'variant_info' => $variant_info,
            'quantity' => $this->quantity,
            'price' => make2decimal($this->price),
            'note' => $this->note,
            'sub_total' => make2decimal($this->sub_total),
            'receive_items' => $this->receiveItems,
            'return_items' => $this->returnItem,
            'stock_quantity' => $this->calculateStockQuantity($this),
            'available_quantity' => $this->calculateAvailableQuantity($this),
            'stock_available_quantity' => $this->calculateStockAvailableQuantity($this)

        ];
    }
    public function calculateStockAvailableQuantity($purchaseItem){
        $stockAvailableQty = 0;
        $stockQty = $this->calculateStockQuantity($purchaseItem);
        $availAbleQty = $this->calculateAvailableQuantity($purchaseItem);

        if ($stockQty > $availAbleQty){
            $stockAvailableQty = $availAbleQty;
            }else{
            $stockAvailableQty = $stockQty;
            }
            return $stockAvailableQty;

    }
    public function calculateStockQuantity($purchaseItem){
      return optional($purchaseItem->product)->warehouseStock($purchaseItem->purchase->warehouse->id);
    }
    public function calculateAvailableQuantity($purchaseItem){
     return $purchaseItem->receiveItems->sum('quantity') -
     optional($purchaseItem->returnItem)->sum('quantity');
    }
}
