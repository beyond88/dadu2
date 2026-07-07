<?php

namespace App\Services\Product;

use App\Models\DraftInvoiceItem;
use App\Models\InvoiceItem;
use App\Models\ProductStockHistory;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseItemReceive;
use App\Models\PurchaseReceive;
use App\Services\Purchase\PurchaseServices;
use App\Traits\ProductStockHistoryTrait;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Models\Product;
use App\Models\ProductStock;
use App\Services\BaseService;
use Illuminate\Validation\Rule;

/**
 * ProductStockService
 */
class ProductStockService extends BaseService
{
    use ProductStockHistoryTrait;
    /**
     * __construct
     *
     * @param mixed $model
     * @return void
     */
    public function __construct(ProductStock $model)
    {
        parent::__construct($model);
    }

    /**
     * getProductStock
     *
     * @param mixed $id
     * @return void
     */
    public function getProductStock($id)
    {
        return $this->model::where('product_id', $id)->get();
    }

    /**
     * variantStockUpdate
     *
     * @param mixed $data
     * @param mixed $id
     * @return void
     */
    public function variantStockUpdate(array $data, $id)
    {
        $product = Product::find($id);
        $update_stock = 0;

        $old_stocks = $this->getProductStock($id);
        if (blank($old_stocks) && isset($data['supplier_id'])) {
            $this->insertPurchaseStock($data, $id);
        }

        logger('variantdata: ', $data);

        foreach ($data['warehouse_stock'] as $item) {
            if (isset($item['warehouse']) && isset($item['quantity'])) {
                foreach ($item['quantity'] as $variationId => $qty) {
                    $stock = ProductStock::where('product_id', $id)
                        ->where('warehouse_id', $item['warehouse'])
                        ->where('variation_id', $variationId)
                        ->first();

                    if (blank($stock)) {
                        $stock = new ProductStock();
                    }

                    $stock->product_id            = $id;
                    $stock->warehouse_id          = $item['warehouse'];
                    $stock->variation_id          = $variationId;
                    $stock->price                 = $item['price'][$variationId];
                    $stock->customer_buying_price = $item['customer_buying_price'][$variationId];
                    $stock->manage_stock          = $item['manage_stock'][$variationId] ?? false;
                    $stock->backorders_allowed    = $item['backorders_allowed'][$variationId] ?? false;
                    $stock->quantity              = $item['adjust_type'] === "Add"
                        ? $item['stock'][$variationId] + $qty
                        : ($item['adjust_type'] === "Subtract"
                            ? $item['stock'][$variationId] - $qty
                            : $item['stock'][$variationId]);

                    $stock->save();

                    $inOrOut = $item['adjust_type'] === "Add"
                        ? ProductStockHistory::TYPE_IN
                        : ProductStockHistory::TYPE_OUT;

                    $this->productStockHistoryCreate(
                        $stock->id,
                        $stock->warehouse_id,
                        $stock->product_id,
                        ProductStock::class,
                        $stock->product_id,
                        $stock->quantity,
                        $inOrOut,
                        ProductStockHistory::ACTION_FROM_STOCK_UPDATE,
                        false,
                        $item['note'] ?? null
                    );

                    $update_stock += $stock->quantity;
                }
            }
        }

        $product->stock = $update_stock;
        $product->stock_alert_quantity = $data['alert_quantity'] ?: null;
        $product->save();

        return true;
    }


    /**
     * normalStockUpdate
     *
     * @param mixed $data
     * @param mixed $id
     * @return void
     */
    public function normalStockUpdate(array $data, $id)
    {
        logger('normalstockupdate');

        $product = Product::find($id);
        $update_stock = 0;
        $old_stocks = $this->getProductStock($id);

        if (blank($old_stocks) && isset($data['supplier_id'])) {
            $this->insertPurchaseStock($data, $id);
        }

        foreach ($data['warehouse_stock'] as $item) {
            logger('item', $item);
            if (isset($item['warehouse'])) {
                $stock = ProductStock::where('product_id', $id)
                    ->where('warehouse_id', $item['warehouse'])
                    ->first();

                if (blank($stock)) {
                    $stock = new ProductStock();
                }

                $stock->product_id            = $id;
                $stock->warehouse_id          = $item['warehouse'];
                $stock->backorders_allowed    = $data['backorders_allowed'] ?? $stock->backorders_allowed ?? 0;
                $stock->manage_stock          = $data['manage_stock'] ?? $stock->manage_stock ?? 0;
                $stock->price                 = $item['price'];
                $stock->customer_buying_price = $item['customer_buying_price'];
                $stock->quantity              = $item['adjust_type'] == "Add"
                    ? $item['stock'] + $item['quantity']
                    : ($item['adjust_type'] == "Subtract" ? $item['stock'] - $item['quantity'] : $item['stock']);

                $stock->save();

                $update_stock += $stock->quantity;

                $inOrOut = $item['adjust_type'] == "Add"
                    ? ProductStockHistory::TYPE_IN
                    : ProductStockHistory::TYPE_OUT;

                $this->productStockHistoryCreate(
                    $stock->id,
                    $stock->warehouse_id,
                    $stock->product_id,
                    ProductStock::class,
                    $stock->product_id,
                    $stock->quantity,
                    $inOrOut,
                    ProductStockHistory::ACTION_FROM_STOCK_UPDATE,
                    false,
                    $item['note'] ?? null
                );
            }
        }

        $product->stock = $update_stock;
        $product->stock_alert_quantity = $data['alert_quantity'] ?: null;
        $product->save();

        return true;
    }
protected function insertPurchaseStock($data, $productId)
{
    $product = Product::find($productId);
    $variations = $product->variations;

    foreach ($data['warehouse_stock'] as $item) {
        $warehouseId = $item['warehouse'];

        if ($variations->count()) {
            // Product has variants
            foreach ($variations as $variation) {
                $qty = (int)($item['quantity'][$variation->id] ?? 0);
                $price = (float)($item['price'][$variation->id] ?? 0);


                $purchase = Purchase::create([
                    'purchase_number' => $data['supplier_id'] .$variation->id. date('His') . rand(3,4),
                    'supplier_id' => $data['supplier_id'],
                    'warehouse_id' => $warehouseId,
                    'date' => now(),
                    'notes' => 'In house purchase',
                    'total' => $price * $qty,
                    'status' => Purchase::STATUS_CONFIRMED,
                    'received' => 1,
                    'created_by' => auth()->user()->id ?? 1,
                ]);
                logger('Purchase created: ', ['id' => $purchase->id]);
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'variation_id' => $variation->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $price * $qty,
                    'created_by' => auth()->user()->id ?? 1,
                ]);

                $purchaseReceive = PurchaseReceive::create([
                    'purchase_id' => $purchase->id,
                    'receive_date' => now(),
                    'total' => $purchase->total,
                    'created_by' => auth()->user()->id ?? 1,
                ]);

                $purchaseReceiveItem = PurchaseItemReceive::create([
                    'purchase_receive_id' => $purchaseReceive->id,
                    'purchase_item_id' => $purchaseItem->id,
                    'product_id' => $productId,
                    // 'variation_id' => $variation->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'sub_total' => $price * $qty,
                ]);
            }
        } else {
            // Normal product logic (no variants)
            $qty = is_array($item['quantity']) ? array_sum($item['quantity']) : $item['quantity'];
            $price = isset($item['price']) ? (float)$item['price'] : $product->price;

            $purchase = Purchase::create([
                'purchase_number' => $data['supplier_id'] .$productId. date('His') . rand(3,4),
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $warehouseId,
                'date' => now(),
                'notes' => 'In house purchase',
                'total' => $price * $qty,
                'status' => Purchase::STATUS_CONFIRMED,
                'received' => 1,
                'created_by' => auth()->user()->id ?? 1,
            ]);

            $purchaseItem = PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $productId,
                'quantity' => $qty,
                'price' => $price,
                'total' => $price * $qty,
                'created_by' => auth()->user()->id ?? 1,
            ]);

            $purchaseReceive = PurchaseReceive::create([
                'purchase_id' => $purchase->id,
                'receive_date' => now(),
                'total' => $purchase->total,
                'created_by' => auth()->user()->id ?? 1,
            ]);

            $purchaseReceiveItem = PurchaseItemReceive::create([
                'purchase_receive_id' => $purchaseReceive->id,
                'purchase_item_id' => $purchaseItem->id,
                'product_id' => $productId,
                'quantity' => $qty,
                'price' => $price,
                'sub_total' => $price * $qty,
            ]);
        }
    }
}



    /**
     * storePurchaseItems
     *
     * @param  mixed $request
     * @return PurchaseServices
     */
    public function storePurchaseItems($request): PurchaseServices
    {
        if (isset($request->product_id) && is_array($request->product_id)) {
            $products = [];
            foreach ($request->product_id as $key => $product_id) {
                $products[] = [
                    'purchase_id' => $this->model->id,
                    'product_id'  => $product_id,
                    'quantity'    => $request->quantity[$key],
                    'price'       => $request->price[$key],
                    'note'        => $request->product_note[$key],
                    'sub_total'   => $request->sub_total[$key],
                    'created_by'  => auth()->id(),
                    'updated_by'  => auth()->id(),
                ];
            }

            $this->model->purchaseItems()->insert($products);
        }

        return $this;
    }

    public function updateByStock($data,$id){
        $total_qty = 0;
        try {
            $product = Product::find($id);
//            if($product->variant == 1) {
    //            $this->variantStockUpdate($data,$id);
//            }else{
                foreach ($data['stock_id'] as $key => $value) {
                    $product_stock              = ProductStock::find($value);
                    $product_stock->quantity    = $data['quantity'][$key];
                    $product_stock->save();

                    $total_qty += $data['quantity'][$key];

                    //todo: add history

                }

//            }
            $product->stock = $total_qty;
            $product->save();
            return true;

            } catch (Throwable $th) {
                return false;
            }
    }
}
