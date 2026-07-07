<?php

namespace App\DataTables;

use PDF;
use App\Models\DraftInvoice;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * AdminDraftInvoiceDataTable
 */
class AdminDraftInvoiceDataTable extends DataTable
{
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
            ->addColumn('action', function ($item) {
                $buttons = '';

                if (auth()->user()->can('Show Draft Invoice')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.draft-invoices.show', $item->id) . '" title="Show"><i class="mdi mdi mdi-desktop-mac"></i> ' . __('custom.show') . ' </a>';
                }

                if (auth()->user()->can('Delete Draft Invoice')) {
                    $buttons .= '<form action="' . route('admin.draft-invoices.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="dropdown-item text-danger delete-list-data" data-from-name="' . 'Inv-no ' . make8digits($item->id) . '" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>
                        ';
                }

                return '<div class="dropdown btn-group dropup">
                            <a href="#" class="btn btn-dark btn-sm" data-toggle="dropdown" data-boundary="viewport"  aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu">
                            ' . $buttons . '
                            </div>
                            </div>';
            })
            ->editColumn('id', function ($item) {
                return '<a class="btn btn-link" href="' . route('admin.draft-invoices.show', $item->id) . '" title="Show">' . make8digits($item->id) . ' </a>';
            })
            ->editColumn('customer', function ($item) {
                if ($item->customer_id) {
                    return ucfirst($item->customer['full_name'] ?? '');
                } else {
                    return ucfirst($item->customer['full_name'] ?? 'Walk-In Customer');
                }
            })
            ->editColumn('warehouse', function ($item) {
                if ($item->warehouse_id) {
                    return optional($item->warehouse)->name;
                }
            })
            ->editColumn('date', function ($item) {
                return custom_date($item->date);
            })
            ->editColumn('total', function ($item) {
                return currencySymbol() . $item->total;
            })
            ->filterColumn('id', function ($query, $keyword) {
                $query->orWhere('id', 'like', '%' . ltrim($keyword, '0') . '%');
            })
            ->rawColumns(['action', 'id', 'warehouse'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param DraftInvoice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DraftInvoice $model)
    {
        return $model->newQuery()->with('warehouse')
            ->select('draft_invoices.*');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '55px', 'class' => "text-center", 'printable' => false, 'exportable' => false, 'title' => 'ACTION'])
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
            Column::make('id', 'id')->title(__('custom.draft_invoice_id')),
            Column::make('warehouse', 'warehouse.name')->title(__('custom.warehouse')),
            Column::make('date', 'date')->title(__('custom.date')),
            Column::make('customer', 'customer')->title(__('custom.customer')),
            Column::make('total', 'total')->title(__('custom.total')),
        ];
    }

    /**
     * filename
     */
    protected function filename(): string
    {
        return 'DraftInvoice_' . date('YmdHis');
    }

    /**
     * pdf
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
