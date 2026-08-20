<?php

namespace App\Services\Warehouse;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Traits\ProductStockHistoryTrait;

class WarehouseTransferService
{
    use ProductStockHistoryTrait;

    /**
     * Validate and execute a multi-product warehouse transfer.
     *
     * @param array $data  [from_warehouse_id, to_warehouse_id, notes, items[]]
     * @return array  ['success' => bool, 'errors' => [], 'transfer' => WarehouseTransfer|null]
     */
    public function transfer(array $data): array
    {
        $fromId = $data['from_warehouse_id'];
        $toId   = $data['to_warehouse_id'];
        $items  = $data['items'] ?? [];

        // ── Pre-validate all rows before touching the DB ─────────────
        $errors = [];
        foreach ($items as $i => $item) {
            $productStockId = $item['product_stock_id'] ?? null;
            $qty            = (int) ($item['quantity'] ?? 0);

            if (!$productStockId || $qty < 1) {
                $errors[] = "Row " . ($i + 1) . ": invalid product or quantity.";
                continue;
            }

            $sourceStock = ProductStock::where('id', $productStockId)
                ->where('warehouse_id', $fromId)
                ->first();

            if (!$sourceStock) {
                $errors[] = "Row " . ($i + 1) . ": product stock not found in source warehouse.";
                continue;
            }

            if ($sourceStock->quantity < $qty) {
                $name = $sourceStock->product?->name ?? 'Product';
                $errors[] = "{$name}: requested {$qty}, available {$sourceStock->quantity}.";
                continue;
            }

            // Weight based products move in whole barrels only — 25 kg per barrel
            // means 25/50/75 kg is fine, 30 kg (1.2 barrel) is not.
            $kgPerBarrel = (float) ($sourceStock->product?->kg_per_barrel ?? 0);

            if ($kgPerBarrel > 0 && abs(fmod($qty, $kgPerBarrel)) > 0.0001) {
                $name    = $sourceStock->product?->name ?? 'Product';
                $barrels = round($qty / $kgPerBarrel, 2);
                $errors[] = "{$name}: {$qty} kg = {$barrels} barrel. Quantity must be a multiple of {$kgPerBarrel} kg (1 barrel).";
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'transfer' => null];
        }

        // ── Execute inside a transaction ─────────────────────────────
        try {
            DB::beginTransaction();

            $transfer = WarehouseTransfer::create([
                'transfer_number'   => 'TRF-' . strtoupper(Str::random(8)),
                'from_warehouse_id' => $fromId,
                'to_warehouse_id'   => $toId,
                'notes'             => $data['notes'] ?? null,
                'created_by'        => Auth::id(),
            ]);

            foreach ($items as $item) {
                $productStockId = $item['product_stock_id'];
                $qty            = (int) $item['quantity'];

                $sourceStock = ProductStock::where('id', $productStockId)
                    ->where('warehouse_id', $fromId)
                    ->first();

                // Deduct from source
                $sourceStock->decrement('quantity', $qty);

                // Add to destination (create stock row if it doesn't exist)
                $destStock = ProductStock::firstOrCreate(
                    ['product_id' => $sourceStock->product_id, 'warehouse_id' => $toId],
                    [
                        'quantity'            => 0,
                        'price'               => $sourceStock->price,
                        'customer_buying_price' => $sourceStock->customer_buying_price,
                        'manage_stock'        => $sourceStock->manage_stock,
                        'backorders_allowed'  => $sourceStock->backorders_allowed,
                    ]
                );
                $destStock->increment('quantity', $qty);

                // Save transfer item record
                WarehouseTransferItem::create([
                    'warehouse_transfer_id' => $transfer->id,
                    'product_id'            => $sourceStock->product_id,
                    'product_stock_id'      => $sourceStock->id,
                    'quantity'              => $qty,
                ]);

                // Stock history — source warehouse (OUT)
                $this->productStockHistoryCreate(
                    $sourceStock->id,
                    $fromId,
                    $sourceStock->product_id,
                    WarehouseTransfer::class,
                    $transfer->id,
                    $qty,
                    ProductStockHistory::TYPE_OUT,
                    ProductStockHistory::ACTION_FROM_TRANSFER_OUT,
                    false,
                    'Transferred to: ' . ($destStock->warehouse?->name ?? $toId)
                );

                // Stock history — destination warehouse (IN)
                $this->productStockHistoryCreate(
                    $destStock->id,
                    $toId,
                    $sourceStock->product_id,
                    WarehouseTransfer::class,
                    $transfer->id,
                    $qty,
                    ProductStockHistory::TYPE_IN,
                    ProductStockHistory::ACTION_FROM_TRANSFER_IN,
                    false,
                    'Received from: ' . ($sourceStock->warehouse?->name ?? $fromId)
                );
            }

            DB::commit();
            return ['success' => true, 'errors' => [], 'transfer' => $transfer];

        } catch (Throwable $th) {
            DB::rollBack();
            logger('Transfer error: ' . $th->getMessage());
            return ['success' => false, 'errors' => [$th->getMessage()], 'transfer' => null];
        }
    }
}
