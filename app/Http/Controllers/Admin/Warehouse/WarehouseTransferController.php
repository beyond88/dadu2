<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseTransferService;

class WarehouseTransferController extends Controller
{
    protected WarehouseTransferService $transferService;

    public function __construct(WarehouseTransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function create()
    {
        set_page_meta('Warehouse Transfer');
        $warehouses = Warehouse::where('status', 'active')->get();
        return view('admin.warehouses.transfers.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id'         => 'required|integer|exists:warehouses,id',
            'to_warehouse_id'           => 'required|integer|exists:warehouses,id|different:from_warehouse_id',
            'notes'                     => 'nullable|string|max:1000',
            'items'                     => 'required|array|min:1',
            'items.*.product_stock_id'  => 'required|integer|exists:product_stocks,id',
            'items.*.quantity'          => 'required|integer|min:1',
        ]);

        $result = $this->transferService->transfer($request->only([
            'from_warehouse_id', 'to_warehouse_id', 'notes', 'items',
        ]));

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors($result['errors']);
        }

        flash('Transfer completed successfully.')->success();
        return redirect()->route('admin.warehouse-transfers.create');
    }
}
