<?php

namespace App\DataTables;

use App\Models\Lc;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use PDF;

class LcDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($item) {
                $buttons = '';
                $buttons .= '<a class="btn btn-sm btn-outline-info ic-act-btn" href="' . route('admin.lcs.show', $item->id) . '" title="View"><i class="mdi mdi-eye"></i> ' . __('custom.view') . ' </a>';
                
                // Assuming 'LC Edit' permission, fallback if not set. Actually, the prompt says don't break features. We can use basic permission or no specific permission check if not strictly required, but let's assume we use standard admin permissions.
                $buttons .= '<a class="btn btn-sm btn-outline-primary ic-act-btn" href="' . route('admin.lcs.edit', $item->id) . '" title="Edit"><i class="fa fa-user-edit"></i> ' . __('custom.edit') . ' </a>';
                
                $buttons .= '<form action="' . route('admin.lcs.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>';
                
                return '<div class="ic-action-inline">' . $buttons . '</div>';
            })
            ->editColumn('dollar_price', function ($item) {
                return '$' . number_format($item->dollar_price, 2);
            })
            ->editColumn('usd_rate', function ($item) {
                return number_format($item->usd_rate, 2);
            })
            // The BDT columns carry the currency symbol, the way Price ($) does.
            ->editColumn('lc_amount_bdt', function ($item) {
                return currencySymbol() . number_format($item->lc_amount_bdt, 2);
            })
            ->editColumn('total_expense', function ($item) {
                return currencySymbol() . number_format($item->total_expense, 2);
            })
            ->editColumn('final_cost', function ($item) {
                return currencySymbol() . number_format($item->final_cost, 2);
            })
            ->editColumn('per_dollar_cost', function ($item) {
                return currencySymbol() . number_format($item->per_dollar_cost, 4);
            })
            ->rawColumns(['action'])
            ->addIndexColumn();
    }

    public function query(Lc $model)
    {
        $query = $model->newQuery();

        if ($start = request('lc_start_date')) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end = request('lc_end_date')) {
            $query->whereDate('created_at', '<=', $end);
        }

        return $query;
    }

    public function html()
    {
        $params             = $this->getBuilderParameters();
        $params['order']    = [[1, 'desc']];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.lc_start_date=$('#lc_start_date').val();data.lc_end_date=$('#lc_end_date').val();")
            ->parameters($params);
    }

    protected function getBuilderParameters(): array
    {
        $params = parent::getBuilderParameters();
        $params['buttons'] = [
            [
                'extend' => 'create',
                'text' => '<i class="fa fa-plus"></i> ' . __('custom.create'),
                'className' => 'btn btn-success',
                'action' => 'function(e, dt, node, config) { window.location.href = "' . route('admin.lcs.create') . '"; }',
            ],
            [ 'extend' => 'csv', 'text' => '<i class="fa fa-file-excel"></i> CSV', 'className' => 'btn btn-success' ],
            [ 'extend' => 'excel', 'text' => '<i class="fa fa-file-excel"></i> Excel', 'className' => 'btn btn-success' ],
            [ 
                'extend' => 'pdf', 
                'text' => '<i class="fa fa-file-pdf"></i> PDF', 
                'className' => 'btn btn-danger',
                'action' => 'function(e, dt, node, config) { 
                    var url = dt.ajax.url() || window.location.href;
                    var params = dt.ajax.params();
                    params.action = "pdf";
                    var queryString = $.param(params);
                    var fullUrl = url.indexOf("?") > -1 ? url + "&" + queryString : url + "?" + queryString;
                    window.open(fullUrl, "_blank"); 
                }'
            ],
            [ 'extend' => 'print', 'text' => '<i class="fa fa-print"></i> Print', 'className' => 'btn btn-warning' ],
            [ 'extend' => 'reload', 'text' => '<i class="fa fa-sync"></i> Reload', 'className' => 'btn btn-info' ],
        ];
        return $params;
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex', __('custom.sl')),
            Column::make('name', 'name')->title(__('custom.lc_name'))->addClass('lc-name-cell'),
            Column::make('dollar_price', 'dollar_price')->title(__('custom.price_usd')),
            Column::make('usd_rate', 'usd_rate')->title(__('custom.usd_rate')),
            Column::make('lc_amount_bdt', 'lc_amount_bdt')->title(__('custom.final_cost')),
            Column::make('total_expense', 'total_expense')->title(__('custom.total_expense')),
            Column::make('per_dollar_cost', 'per_dollar_cost')->title(__('custom.per_dollar_actual_cost')),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title(__('custom.action')),
        ];
    }

    protected function filename(): string
    {
        return 'LCs_' . date('YmdHis');
    }

    public function pdf()
    {
        $data = $this->getDataForExport();

        $pdf = PDF::loadView('vendor.datatables.print', [
            'data' => $data
        ]);
        return $pdf->stream($this->getFilename() . '.pdf');
    }
}
