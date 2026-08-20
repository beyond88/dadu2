<?php

namespace App\Http\Controllers\Admin\Axios;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;

class AxiosController extends Controller
{
    /**
     * productSearchNameSku
     *
     * @param  mixed $query
     * @return void
     */
    public function productSearchNameSku($query)
    {
        return Product::query()
            ->where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->get(['id', 'name', 'sku', 'price', 'is_variant']);
    }
public function productStockSearchByWarehouse($query, $warehouse_id)
{
    $results = ProductStock::query()
        ->with(['product', 'variation'])
        ->where('warehouse_id', $warehouse_id)
        ->where(function ($q) use ($query) {
            $q->whereHas('product', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%")
                   ->orWhere('sku', 'like', "%{$query}%")
                   ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->orWhereHas('variation', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%")
                   ->orWhere('sku', 'like', "%{$query}%")
                   ->orWhere('barcode', 'like', "%{$query}%");
            });
        })
        ->whereHas('product', function ($q) {
            $q->where('status', 'active');
        })
        ->orderBy('id')
        ->get();

    $grouped = $results->groupBy('product_id');
    $filtered = collect();

    foreach ($grouped as $productId => $stocks) {
        $product = $stocks->first()->product;
        if ($product && $product->is_batch_product) {
            $filtered->push($stocks->first());
        } else {
            $filtered = $filtered->merge($stocks);
        }
    }

    return $filtered->values();
}

public function productStockSearchNameSku($query, Request $request)
{
    // Optional: scope the suggestions to the warehouse selected on the form, so the
    // same product is not offered from several warehouses at once.
    $warehouseId = $request->query('warehouse_id');

    $results = ProductStock::query()
        ->with([
            'product',
            'variation',
            'warehouse',
        ])
        ->where(function ($q) use ($query) {
            // First: search in product table (simple products)
            $q->whereHas('product', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%")
                   ->orWhere('sku', 'like', "%{$query}%")
                   ->orWhere('barcode', 'like', "%{$query}%");
            })
            // Then: OR search in variation table (variant products)
            ->orWhereHas('variation', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%")
                   ->orWhere('sku', 'like', "%{$query}%")
                   ->orWhere('barcode', 'like', "%{$query}%");
            });
        })
        // Ensure parent product is active
        ->whereHas('product', function ($q) {
            $q->where('status', 'active');
        })
        ->orderBy('id')
        ->get();

    // Group stock items by product
    $grouped = $results->groupBy('product_id');

    $filtered = collect();

    foreach ($grouped as $productId => $stocks) {
        $product = $stocks->first()->product;

        // Keep the rows of the selected warehouse. A product with no stock row there
        // yet is still offered (one entry) — the row is created when it is received.
        if ($warehouseId) {
            $scoped = $stocks->where('warehouse_id', (int) $warehouseId);
            $stocks = $scoped->isNotEmpty() ? $scoped : $stocks->take(1);
        }

        if ($product && $product->is_batch_product) {
            // Batch product: take only the first stock record
            $filtered->push($stocks->first());
        } else {
            // Non-batch product: take all stock records
            $filtered = $filtered->merge($stocks);
        }
    }

    return $filtered->values(); // reindex
}




    /**
     * purchaseItemDelete
     *
     * @param  mixed $query
     * @return void
     */
    public function purchaseItemDelete($query)
    {
        PurchaseItem::query()->findOrFail($query)->delete();

        return true;
    }
}
