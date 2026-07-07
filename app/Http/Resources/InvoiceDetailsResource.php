<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Call the parent method to include other properties
        $data = parent::toArray($request);

        if ($this->items->isNotEmpty()) {
            // Add the 'return_quantity' field to each item in the 'items' key
            $data['items'] = $this->items->map(function ($item) {
                $itemArray = $item->toArray();
                $variation = $item->variation ?: optional($item->productStock)->variation;

                if ($variation) {
                    $itemArray['variation'] = $variation->toArray();
                }

                return array_merge($itemArray, [
                    'return_quantity' => $item->returnQuantity(),
                    'variant_name' => optional($variation)->name,
                ]);
            });
        }

        return $data;
    }
}
