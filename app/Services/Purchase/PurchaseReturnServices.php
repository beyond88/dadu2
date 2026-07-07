<?php

namespace App\Services\Purchase;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Services\BaseService;
use App\Models\PurchaseReturn;
use App\Traits\ProductStockHistoryTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PurchaseReturnServices
 */
class PurchaseReturnServices extends BaseService
{
    use ProductStockHistoryTrait;

    protected $product_stock, $product;

    /**
     * __construct
     *
     * @param  mixed $model
     * @param  mixed $stock
     * @param  mixed $product
     * @return void
     */
    public function __construct(PurchaseReturn $model, ProductStock $stock, Product $product)
    {
        $this->model = $model;
        $this->product_stock = $stock;
        $this->product = $product;
    }

    /**
     * validate
     *
     * @param  mixed $request
     * @return PurchaseReturnServices
     */
    public function validate($request): PurchaseReturnServices
    {
        $request->validate([
            'return_date' => 'required|date_format:Y-m-d',
            'return_note' => 'required|string',
            'product_stock_id' => 'required',
            'total' => 'required|numeric',
            'product_id.*' => 'required|exists:products,id',
            'return_quantity.*' => 'nullable|numeric|min:0',

            'return_price.*' => 'nullable|numeric',
            'return_sub_total.*' => 'nullable|numeric'
        ]);

        // Barrel/drum (weight-based) products can only be returned in WHOLE barrels.
        // return_quantity holds the barrel count, so reject any fractional value.
        if (is_array($request->product_id)) {
            foreach ($request->product_id as $index => $productId) {
                $qty = $request->return_quantity[$index] ?? null;
                if ($qty === null || $qty === '' || (float) $qty <= 0) {
                    continue;
                }
                $product = \App\Models\Product::find($productId);
                if ($product && $product->is_weight_based) {
                    $barrels = (float) $qty;
                    if (abs($barrels - round($barrels)) > 1e-9) {
                        $label = $product->barrel_label ?: __('custom.barrels');
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "return_quantity.$index" => "Return quantity must be a whole {$label} (no fractional {$label}).",
                        ]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * store
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return void
     */
    public function store($request, $purchase)
    {
        DB::transaction(function () use ($request, $purchase) {
            // Resolve stocks first so we can save the correct product_stock_id
            $resolvedStocks = $this->resolveStocks($request, $purchase);

            $this->storePurchaseReturn($request, $purchase)
                ->storePurchaseReturnItem($request, $resolvedStocks)
                ->stockUpdate($request, $resolvedStocks);
        });
    }

    /**
     * storePurchaseReturn
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return PurchaseReturnServices
     */
    private function storePurchaseReturn($request, $purchase): PurchaseReturnServices
    {
        $this->model = $this->model
            ->newQuery()
            ->create([
                'purchase_id' => $purchase->id,
                'return_date' => $request->return_date,
                'note' => $request->return_note,
                'total' => $request->total,
            ]);

        return $this;
    }

    /**
     * storePurchaseReturnItem
     *
     * @param  mixed $request
     * @param  array $resolvedStocks  keyed by loop index → resolved ProductStock
     * @return PurchaseReturnServices
     */
    private function storePurchaseReturnItem($request, array $resolvedStocks): PurchaseReturnServices
    {
        if (isset($request->product_id) && is_array($request->product_id)) {
            $products = [];
            foreach ($request->product_id as $key => $product_id) {
                if ($request->return_quantity[$key]) {
                    $stock = $resolvedStocks[$key] ?? null;
                    $products[] = [
                        'purchase_return_id' => $this->model->id,
                        'product_id' => $product_id,
                        'product_stock_id' => $stock ? $stock->id : $request->product_stock_id[$key],
                        'purchase_item_id' => $request->purchase_item_id[$key],
                        'quantity' => $request->return_quantity[$key],
                        'price' => $request->return_price[$key],
                        'sub_total' => $request->return_sub_total[$key],
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ];
                }
            }

            $this->model->purchaseReturnItems()->insert($products);
        }

        return $this;
    }

    /**
     * resolveStocks — look up the correct ProductStock for each line item before any writes.
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return array  index → ProductStock|null
     */
    private function resolveStocks($request, $purchase): array
    {
        $resolved = [];
        if (isset($request->product_stock_id) && is_array($request->product_stock_id)) {
            foreach ($request->product_stock_id as $key => $product_stock_id) {
                if ($request->return_quantity[$key]) {
                    $stock = $this->getStock($product_stock_id, $purchase, $request->product_id[$key] ?? null);
                    if (!$stock) {
                        throw ValidationException::withMessages([
                            'product_stock_id' => __('Stock record not found for product at index :index. Cannot process return.', ['index' => $key + 1]),
                        ]);
                    }
                    $resolved[$key] = $stock;
                }
            }
        }
        return $resolved;
    }

    /**
     * stockUpdate
     *
     * @param  mixed $request
     * @param  array $resolvedStocks  keyed by loop index → resolved ProductStock
     * @return void
     */
    private function stockUpdate($request, array $resolvedStocks): void
    {
        foreach ($resolvedStocks as $key => $stock) {
            $returnQty = $request->return_quantity[$key];

            $stock->update([
                'quantity' => max(0, $stock->quantity - $returnQty)
            ]);
            $this->productStockHistoryCreate(
                $stock->id,
                $stock->warehouse_id,
                $stock->product_id,
                PurchaseReturn::class,
                $this->model->id,
                $returnQty,
                ProductStockHistory::TYPE_OUT,
                ProductStockHistory::ACTION_FROM_PURCHASE_RETURN
            );

            $product = $this->product->newQuery()->where('id', $request->product_id[$key])->first();
            if ($product) {
                $product->update([
                    'stock' => max(0, $product->stock - $returnQty)
                ]);
            }
        }
    }

    /**
     * getStock
     *
     * @param  mixed $product
     * @param  mixed $purchase
     * @return void
     */
    public function getStock($product_stock_id, $purchase, $product_id = null)
    {
        if ($purchase->warehouse_id) {
            $defaultWarehouse = $purchase->warehouse_id;
        } else {
            $defaultWarehouse = Warehouse::query()->where('is_default', true)->value('id');
            if (!$defaultWarehouse) {
                throw ValidationException::withMessages(['message' => __('Select a warehouse first')]);
            }
        }

        // Primary lookup: match saved stock_id AND purchase warehouse
        $stock = $this->product_stock->newQuery()
            ->where('id', $product_stock_id)
            ->where('warehouse_id', $defaultWarehouse)
            ->first();

        // Fallback: stock_id belongs to a different warehouse (mismatch from duplicate dropdown)
        // Find the correct row by product_id + purchase warehouse instead
        if (!$stock && $product_id) {
            $stock = $this->product_stock->newQuery()
                ->where('product_id', $product_id)
                ->where('warehouse_id', $defaultWarehouse)
                ->first();
        }

        return $stock;
    }
    public function all(){
        return $this->model->with(['purchase.supplier', 'purchase.warehouse', 'purchaseReturnItems'])
        ->newQuery()
        ->select('purchase_returns.*')
        ->leftJoin('purchases', 'purchase_returns.purchase_id', '=', 'purchases.id')
        ->orderByDesc('purchases.purchase_number') // Change 'asc' to 'desc' for descending order
        ->paginate(10);
    }
}
