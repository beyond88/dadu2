<?php

namespace App\Services\Warehouse;

use App\Models\Warehouse;
use App\Services\BaseService;

/**
 * WarehouseService
 */
class WarehouseService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Warehouse $model)
    {
        parent::__construct($model);
    }

    public function defaultWareHouse()
    {
        return $this->model->newQuery()->where('is_default', 1)->first();
    }

    public function getWareHouse($wareHouseId)
    {
        return $this->model->newQuery()->where('id', $wareHouseId)->where('status', Warehouse::STATUS_ACTIVE)->first();
    }

    public function pluck()
    {
        return $this->model->newQuery()->where('status', Warehouse::STATUS_ACTIVE)->pluck('name', 'id');
    }

    public function firstWarehouse()
    {
        return $this->model->newQuery()->where('status', Warehouse::STATUS_ACTIVE)->first();
    }
    public function all()
    {
      return $this->model->orderBy('status','asc')->paginate(10);
    }

    public function getWarehouseStocksWithPagination($warehouseId, $search = '', $page = 1)
    {
        $query = \App\Models\ProductStock::query()
            ->with(['product.category', 'product.manufacturer', 'product.weight_unit', 'variation'])
            ->where('warehouse_id', $warehouseId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('manufacturer', function ($mq) use ($search) {
                            $mq->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orWhereHas('variation', function ($vq) use ($search) {
                    $vq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            });
        }

        return $query->latest('id')->paginate(10, ['*'], 'stock_page', $page)
            ->appends(['stock_search' => $search]);
    }

    public function getAllWarehouseStocks($warehouseId, $search = '')
    {
        $query = \App\Models\ProductStock::query()
            ->with(['product.category', 'product.manufacturer', 'product.weight_unit', 'variation'])
            ->where('warehouse_id', $warehouseId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('manufacturer', function ($mq) use ($search) {
                            $mq->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orWhereHas('variation', function ($vq) use ($search) {
                    $vq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            });
        }

        return $query->latest('id')->get();
    }
}
