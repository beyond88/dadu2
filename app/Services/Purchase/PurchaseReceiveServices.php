<?php

namespace App\Services\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Platform;
use App\Models\Warehouse;
use App\Traits\SetModel;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Services\BaseService;
use App\Models\PurchaseReceive;
use App\Traits\ProductStockHistoryTrait;
use App\Traits\Woocommerce;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PurchaseReceiveServices
 */
class PurchaseReceiveServices extends BaseService
{
    use SetModel, Woocommerce, ProductStockHistoryTrait;

    public $product;
    public $product_stock;

    /**
     * __construct
     *
     * @param  mixed $model
     * @param  mixed $product
     * @param  mixed $product_stock
     * @return void
     */
    public function __construct(PurchaseReceive $model, Product $product, ProductStock $product_stock)
    {
        $this->model = $model;
        $this->product = $product;
        $this->product_stock = $product_stock;
    }

    /**
     * validate
     *
     * @param  mixed $request
     * @return PurchaseReceiveServices
     */public function validate($request): PurchaseReceiveServices
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'total' => 'required|numeric',
            'product_stock_id' => 'required|array',
            'product_id' => 'required|array',
            'receive_quantity.*' => 'nullable|numeric|min:0',
            'receive_price.*' => 'required|numeric',
            'receive_sub_total.*' => 'nullable|numeric',
            // These are only required for batch products — handled below manually
            'batch.*' => 'nullable|string',
            'expiry_date.*' => 'nullable|date',
        ]);

        // Manual validation for batch fields
        foreach ($request->product_id as $index => $productId) {
            $product = Product::find($productId);

            // Barrel (weight-based) products are received in whole barrels — a
            // fractional barrel quantity is not allowed, just like in purchase.
            if ($product && $product->is_weight_based) {
                $qty = $request->receive_quantity[$index] ?? null;
                if ($qty !== null && $qty !== '' && abs((float) $qty - round((float) $qty)) > 1e-6) {
                    $label = $product->barrel_label ?: 'barrel';
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "receive_quantity.$index" => "Receive quantity must be a whole {$label} (no fractional {$label}).",
                    ]);
                }
            }

            if ($product && $product->is_batch_product) {
                $batch = $request->batch[$index] ?? null;
                $expiry = $request->expiry_date[$index] ?? null;
                $warehouseId = $request->warehouse_id ?? null;

                if (empty($batch)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "batch.$index" => 'Batch number is required for batch product.',
                    ]);
                }

                if (empty($expiry)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "expiry_date.$index" => 'Expiry date is required for batch product.',
                    ]);
                }

                // Check uniqueness manually
                $exists = \App\Models\ProductStock::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('batch', $batch)
                    ->exists();

                if ($exists) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "batch.$index" => "The batch '$batch' already exists for this product in the selected warehouse.",
                    ]);
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
     * @return bool
     */
    public function store($request, $purchase): bool
    {
        DB::transaction(function () use ($request, $purchase) {

            $purchase->update(['received' => true]);

            // Resolve the correct stock rows before any writes so we save accurate ids
            $resolvedStocks = $this->resolveNonBatchStocks($request, $purchase);

            $this->storePurchaseReceive($request, $purchase->id)
                ->storePurchaseReceiveItem($request, $resolvedStocks)
                ->stockUpdate($request, $purchase);
        });
        $wooStores = getConnectedWooCommerceStores();
        if($wooStores->isNotEmpty())
        {
            $data = [
                'product_stock_id'  => $request->product_stock_id,
                'product_id'        => $request->product_id,
                'receive_quantity'  => $request->receive_quantity,
            ];
            foreach($wooStores as $store)
            {

                \App\Jobs\SyncWooCommerceStockJob::dispatch($store->id,null,$data,true);

            }
        }

        return true;
    }

    /**
     * storePurchaseReceive
     *
     * @param  mixed $request
     * @param  mixed $purchase_id
     * @return PurchaseReceiveServices
     */
    private function storePurchaseReceive($request, $purchase_id): PurchaseReceiveServices
    {


        $this->model = $this->model
            ->newQuery()
            ->create([
                'purchase_id' => $purchase_id,
                'receive_date' => $request->date,
                'total' => $request->total,
            ]);

        $purchase = Purchase::find($purchase_id);

        if($purchase && $purchase->status == Purchase::STATUS_REQUESTED) {
            $purchase->update(['status' => Purchase::STATUS_CONFIRMED]);
        }

        return $this;
    }

    /**
     * resolveNonBatchStocks — look up the correct ProductStock for non-batch line items.
     * Batch products create a new stock row during stockUpdate, so we skip them here.
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return array  index → ProductStock|null
     */
    private function resolveNonBatchStocks($request, $purchase): array
    {
        $resolved = [];
        if (isset($request->product_stock_id) && is_array($request->product_stock_id)) {
            foreach ($request->product_stock_id as $key => $product_stock_id) {
                $receiveQty = $request->receive_quantity[$key] ?? 0;
                if ($receiveQty > 0) {
                    $product = Product::find($request->product_id[$key]);
                    if ($product && !$product->is_batch_product) {
                        $resolved[$key] = $this->getProductStock($product_stock_id, $purchase, $request->product_id[$key] ?? null);
                    }
                }
            }
        }
        return $resolved;
    }

    /**
     * storePurchaseReceiveItem
     *
     * @param  mixed $request
     * @param  array $resolvedStocks  keyed by loop index → resolved ProductStock|null
     * @return PurchaseReceiveServices
     */
    private function storePurchaseReceiveItem($request, array $resolvedStocks = []): PurchaseReceiveServices
    {
        if (isset($request->purchase_item_id) && is_array($request->purchase_item_id)) {
            foreach ($request->purchase_item_id as $key => $purchase_item_id) {
                if ($request->receive_quantity[$key] > 0) {
                    $resolvedStock = $resolvedStocks[$key] ?? null;
                    $this->model
                        ->purchaseItemReceives()
                        ->create([
                            'purchase_receive_id' => $this->model->id,
                            'purchase_item_id' => $purchase_item_id,
                            'product_id' => $request->product_id[$key],
                            'product_stock_id' => $resolvedStock ? $resolvedStock->id : $request->product_stock_id[$key],
                            'quantity' => $request->receive_quantity[$key],
                            'price' => $request->receive_price[$key],
                            'sub_total' => $request->receive_sub_total[$key],
                        ]);
                }
            }
        }

        return $this;
    }

    /**
     * stockUpdate
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return PurchaseReceiveServices
     */
    private function stockUpdate($request, $purchase): PurchaseReceiveServices
    {
        $w_id = $request->warehouse_id ?? optional(Warehouse::where('is_default', true)->first())->id;

        if (!$w_id) {
            throw ValidationException::withMessages(['message' => __('Select a warehouse first')]);
        }

        if (isset($request->product_stock_id) && is_array($request->product_stock_id)) {
            foreach ($request->product_stock_id as $key => $product_stock_id) {
                $receiveQty = $request->receive_quantity[$key] ?? 0;

                if ($receiveQty > 0) {
                    $product = $this->product->with('allStock')->findOrFail($request->product_id[$key]);

                    if ($product->is_batch_product) {
                        // Create new record for each batch
                        $batchStock = $this->product_stock->create([
                            'product_id'    => $product->id,
                            'warehouse_id'  => $w_id,
                            'batch'         => $request->batch[$key] ?? null,
                            'expiry_date'   => $request->expiry_date[$key] ?? null,
                            'quantity'      => $receiveQty,
                            'created_by'    => auth()->id(),
                            'updated_by'    => auth()->id(),
                        ]);
                        $this->productStockHistoryCreate(
                            $batchStock->id, $w_id, $product->id,
                            PurchaseReceive::class, $this->model->id,
                            $receiveQty,
                            ProductStockHistory::TYPE_IN,
                            ProductStockHistory::ACTION_FROM_PURCHASE_RECEIVE
                        );
                    } else {
                        $stock = $this->getProductStock($product_stock_id, $purchase, $request->product_id[$key] ?? null);

                        if ($stock) {
                            $stock->update([
                                'quantity' => $stock->quantity + $receiveQty,
                            ]);
                            $this->productStockHistoryCreate(
                                $stock->id, $stock->warehouse_id, $stock->product_id,
                                PurchaseReceive::class, $this->model->id,
                                $receiveQty,
                                ProductStockHistory::TYPE_IN,
                                ProductStockHistory::ACTION_FROM_PURCHASE_RECEIVE
                            );
                        } else {
                            if ($product->is_variant == 0) {
                                $newStock = $this->product_stock->create([
                                    'product_id'   => $product->id,
                                    'warehouse_id' => $w_id,
                                    'quantity'     => $receiveQty,
                                    'created_by'   => auth()->id(),
                                    'updated_by'   => auth()->id(),
                                ]);
                                $this->productStockHistoryCreate(
                                    $newStock->id, $w_id, $product->id,
                                    PurchaseReceive::class, $this->model->id,
                                    $receiveQty,
                                    ProductStockHistory::TYPE_IN,
                                    ProductStockHistory::ACTION_FROM_PURCHASE_RECEIVE
                                );
                            } else {
                                foreach ($product->allStock as $p_stock) {
                                    $this->product_stock->create([
                                        'product_id'        => $product->id,
                                        'warehouse_id'      => $w_id,
                                        'attribute_id'      => $p_stock->attribute_id,
                                        'attribute_item_id' => $p_stock->attribute_item_id,
                                        'created_by'        => auth()->id(),
                                        'updated_by'        => auth()->id(),
                                    ]);
                                }

                                $old_stock = ProductStock::find($product_stock_id);

                                if (!$old_stock) {
                                    // stock_id from form doesn't exist; skip variant stock update
                                    continue;
                                }

                                $stock = $this->product_stock->where([
                                    'warehouse_id'      => $w_id,
                                    'product_id'        => $product->id,
                                    'attribute_id'      => $old_stock->attribute_id,
                                    'attribute_item_id' => $old_stock->attribute_item_id,
                                ])->first();

                                $stock?->update([
                                    'quantity' => $receiveQty,
                                ]);
                                if ($stock) {
                                    $this->productStockHistoryCreate(
                                        $stock->id, $stock->warehouse_id, $stock->product_id,
                                        PurchaseReceive::class, $this->model->id,
                                        $receiveQty,
                                        ProductStockHistory::TYPE_IN,
                                        ProductStockHistory::ACTION_FROM_PURCHASE_RECEIVE
                                    );
                                }
                            }
                        }
                    }

                    // Update product total stock
                    $product->increment('stock', $receiveQty);
                }
            }
        }

        return $this;
    }



    /**
     * getProductStock
     *
     * @param  mixed $product_id
     * @param  mixed $purchase
     * @return void
     */
    private function getProductStock($product_stock_id, $purchase, $product_id = null)
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
        return $this->model
        ->with(['purchase.supplier', 'purchase.warehouse', 'purchaseItemReceives'])
        ->newQuery()
        ->select('purchase_receives.*')
        ->leftJoin('purchases', 'purchase_receives.purchase_id', '=', 'purchases.id')
        ->orderByDesc('purchases.purchase_number') // Change 'asc' to 'desc' for descending order
        ->paginate(10);
    }
}
