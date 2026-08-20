<?php

namespace App\DataTables;

use PDF;
use App\Models\Account;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * AccountDataTable
 */
class AccountDataTable extends DataTable
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
                $buttons .= '<a class="btn btn-sm btn-outline-info ic-act-btn" href="' . route('admin.accounts.show', $item->id) . '" title="Show"><i class="fa fa-eye"></i> ' . __('custom.show') . ' </a>';
                
                if (auth()->user()->can('Edit Account')) {
                    $buttons .= '<a class="btn btn-sm btn-outline-primary ic-act-btn" href="' . route('admin.accounts.edit', $item->id) . '" title="Edit"><i class="fa fa-user-edit"></i> ' . __('custom.edit') . ' </a>';
                }
                
                if (auth()->user()->can('Delete Account')) {
                    $buttons .= '<form action="' . route('admin.accounts.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>
                    ';
                }
                
                return '<div class="ic-action-inline">' . $buttons . '</div>';
            })
            ->editColumn('type', function ($item) {
                $types = [
                    'cash' => __('custom.cash'),
                    'bank' => __('custom.bank'),
                    'mobile_banking' => __('custom.mobile_banking')
                ];
                return '<span class="badge badge-info">' . ($types[$item->type] ?? $item->type) . '</span>';
            })
            ->editColumn('current_balance', function ($item) {
                return '<strong>' . currencySymbol() . number_format($item->current_balance, 2) . '</strong>';
            })
            ->editColumn('status', function ($item) {
                $badge = $item->is_active ? "badge-success" : "badge-danger";
                $text = $item->is_active ? __('custom.active') : __('custom.inactive');
                return '<span class="badge ' . $badge . '">' . Str::upper($text) . '</span>';
            })
            ->rawColumns(['type', 'current_balance', 'status', 'action'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Account $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Account $model)
    {
        $query = $model->newQuery();

        if ($start = request('ac_start_date')) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end = request('ac_end_date')) {
            $query->whereDate('created_at', '<=', $end);
        }

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $params             = $this->getBuilderParameters();
        $params['order']    = [[1, 'asc']];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.ac_start_date=$('#ac_start_date').val();data.ac_end_date=$('#ac_end_date').val();")
            ->addAction(['width' => '55px', 'class' => "text-center", 'printable' => false, 'exportable' => false, 'title' => __('custom.action')])
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
                'action' => 'function(e, dt, node, config) { window.location.href = "' . route('admin.accounts.create') . '"; }',
            ],
            [ 'extend' => 'csv', 'text' => '<i class="fa fa-file-excel"></i> CSV', 'className' => 'btn btn-success' ],
            [ 'extend' => 'excel', 'text' => '<i class="fa fa-file-excel"></i> Excel', 'className' => 'btn btn-success' ],
            [ 'extend' => 'pdf', 'text' => '<i class="fa fa-file-pdf"></i> PDF', 'className' => 'btn btn-danger' ],
            [ 'extend' => 'print', 'text' => '<i class="fa fa-print"></i> Print', 'className' => 'btn btn-warning' ],
            [ 'extend' => 'reload', 'text' => '<i class="fa fa-sync"></i> Reload', 'className' => 'btn btn-info' ],
        ];
        return $params;
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
            Column::make('name', 'name')->title(__('custom.name')),
            Column::make('code', 'code')->title(__('custom.account_code')),
            Column::make('type', 'type')->title(__('custom.type')),
            Column::make('current_balance', 'current_balance')->title(__('custom.current_balance')),
            Column::make('status', 'status')->title(__('custom.status')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Accounts_' . date('YmdHis');
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
