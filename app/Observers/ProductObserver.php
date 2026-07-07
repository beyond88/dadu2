<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Warehouse;

class ProductObserver
{
    /**
     * After any new simple product is created — from any source (web, API, import,
     * WooCommerce sync, future code) — ensure a stock row exists in the default
     * warehouse. This is a safety net: if the caller already created the stock row,
     * this is a no-op (firstOrCreate finds the existing row).
     */
    public function created(Product $product): void
    {
        if ($product->is_variant) {
            // Variant stock rows are tied to individual variations which are created
            // after the product. The calling code is responsible for variant stock.
            return;
        }

        $warehouse = Warehouse::where('is_default', 1)->first()
            ?? Warehouse::where('status', Warehouse::STATUS_ACTIVE)->first();

        if (!$warehouse) {
            logger()->error("ProductObserver: could not create stock row for product {$product->id} — no warehouse found.");
            return;
        }

        ProductStock::firstOrCreate(
            [
                'product_id'  => $product->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'quantity'              => 0,
                'price'                 => 0,
                'customer_buying_price' => 0,
                'created_by'            => auth()->id() ?? 1,
                'updated_by'            => auth()->id() ?? 1,
            ]
        );
    }
}
