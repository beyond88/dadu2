<?php

namespace App\DataTables;

use App\Models\ProductStockHistory;
use App\Models\SystemCity;
use Carbon\Carbon;
use PDF;
use App\Models\Brand;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;


/**
 * BrandDataTable
 */
class WarehouseStockHistoryDatatable extends DataTable
{
    public $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->filterColumn('product_id', function ($query, $keyword) {
                $query->whereHas('product', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('type', function ($query, $keyword) {
                $query->where('type', 'like', "%{$keyword}%");
            })
            ->filterColumn('action_from', function ($query, $keyword) {
                $query->where('action_from', 'like', "%{$keyword}%");
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->where('created_at', 'like', "%{$keyword}%");
            })
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->whereHas('createdBy', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('product_id', function ($item) {
                return $item->product->name;
            })
           ->addColumn('quantity', function ($item) {
            return $item->quantity . ' ' . (optional($item->product?->weight_unit)->name ?? '');
        })

            //            ->addColumn('warehouse_id', function ($item) {
            //                return $item->warehouse->name;
            //            })
            ->addColumn('type', function ($item) {
                return $item->type == 'in' ? 'In' : 'Out';
            })->addColumn('action_from', function ($item) {
                $label = Str::upper(str_replace('_', ' ', $item->action_from));

                // Invoice → link to invoice
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_INVOICE && $item->from_id) {
                    $url = route('admin.invoices.show', $item->from_id);
                    return '<a href="' . $url . '" class="badge badge-primary" title="View Invoice">'
                        . $label . ' <i class="fa fa-external-link-alt" style="font-size:.6rem;"></i></a>';
                }

                // Purchase Receive → link to receive show
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_PURCHASE_RECEIVE && $item->from_id) {
                    $url = route('admin.purchases.receive.show', $item->from_id);
                    return '<a href="' . $url . '" class="badge badge-success" title="View Purchase Receive">'
                        . $label . ' <i class="fa fa-external-link-alt" style="font-size:.6rem;"></i></a>';
                }

                // Purchase Return → link to return show
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_PURCHASE_RETURN && $item->from_id) {
                    $url = route('admin.purchases.return.show', $item->from_id);
                    return '<a href="' . $url . '" class="badge badge-warning text-dark" title="View Purchase Return">'
                        . $label . ' <i class="fa fa-external-link-alt" style="font-size:.6rem;"></i></a>';
                }

                // Invoice Return (Sale Return) → link to sale return show
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_INVOICE_RETURN && $item->from_id) {
                    $url = route('admin.sales-return.show', $item->from_id);
                    return '<a href="' . $url . '" class="badge badge-info" title="View Sale Return">'
                        . $label . ' <i class="fa fa-external-link-alt" style="font-size:.6rem;"></i></a>';
                }

                // Free item → link to invoice
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_FREE_ITEM && $item->from_id) {
                    $url = route('admin.invoices.show', $item->from_id);
                    return '<a href="' . $url . '" class="badge badge-warning text-dark" title="View Invoice">'
                        . $label . ' <i class="fa fa-external-link-alt" style="font-size:.6rem;"></i></a>';
                }

                // Transfer out → link to destination warehouse history
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_TRANSFER_OUT && $item->from_id) {
                    $transfer = \App\Models\WarehouseTransfer::with('toWarehouse')->find($item->from_id);
                    if ($transfer?->toWarehouse) {
                        $url  = route('admin.warehouses.show-storage-store-and-out', $transfer->to_warehouse_id);
                        $name = $transfer->toWarehouse->name;
                        return '<a href="' . $url . '" class="badge badge-danger" title="Transferred to: ' . $name . '">'
                            . $label . ' → ' . $name . '</a>';
                    }
                }

                // Transfer in → link to source warehouse history
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_TRANSFER_IN && $item->from_id) {
                    $transfer = \App\Models\WarehouseTransfer::with('fromWarehouse')->find($item->from_id);
                    if ($transfer?->fromWarehouse) {
                        $url  = route('admin.warehouses.show-storage-store-and-out', $transfer->from_warehouse_id);
                        $name = $transfer->fromWarehouse->name;
                        return '<a href="' . $url . '" class="badge badge-success" title="Received from: ' . $name . '">'
                            . $label . ' ← ' . $name . '</a>';
                    }
                }

                // Damage → amber badge
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_DAMAGE) {
                    return '<span class="badge badge-warning text-dark" title="' . ($item->note ?? '') . '">'
                        . $label . '</span>';
                }

                // Loss → red badge
                if ($item->action_from === \App\Models\ProductStockHistory::ACTION_FROM_LOSS) {
                    return '<span class="badge badge-danger" title="' . ($item->note ?? '') . '">'
                        . $label . '</span>';
                }

                return $label;
            })
            ->rawColumns(['action_from'])
            ->addColumn('created_at', function ($item) {
                //upper case
                return $item->created_at ? Carbon::parse($item->created_at)->format('d-m-Y h:i A') : '';
            })
            ->addColumn('note', function ($item) {
                return $item->note ?? '-';
            })
            ->addColumn('created_by', function ($item) {
                //upper case
                return $item->created_by ? $item->createdBy->name : '';
            })
            //            ->rawColumns([ 'action'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param SystemCity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ProductStockHistory $model)
    {
        return $model->with(['product', 'warehouse'])
            ->where('warehouse_id', $this->id)
            ->select(['*'])
            ->latest()
            ->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
//        $params             = $this->getBuilderParameters();
//        $params['order']    = [[1, 'asc']];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '55px', 'class' => "text-center", 'printable' => false, 'exportable' => false, 'title' => __('custom.action')])
            ->parameters([
                'dom'     => 'Bfrtilp',
                'order'   => [[1, 'desc']],
                'buttons' => [
                    'csv',
                    'excel',
                    'pdf',
                    'print',
                    'reload',
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex', __('custom.sl')),
            //            Column::make('warehouse_id', 'warehouse_id')->title(__('custom.warehouse')),
            Column::make('product_id', 'product_id')->title(__('custom.product')),
            Column::make('type', 'type')->title(__('custom.type')),
            Column::make('action_from', 'action_from')->title(__('custom.action_from')),
            Column::make('quantity', 'quantity')->title(__('custom.quantity')),
            Column::make('note', 'note')->title(__('custom.note')),
            Column::make('created_at', 'created_at')->title(__('custom.created_at')),
            Column::make('created_by', 'created_by')->title(__('custom.created_by')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'history_' . date('YmdHis');
    }


    /**
     * pdf
     *
     * @return void
     */
    public function pdf()
    {
        $data = $this->getDataForExport();

        $pdf = PDF::loadView('vendor.datatables.print', [
            'data' => $data
        ]);
        return $pdf->download($this->getFilename() . '.pdf');
    }
}