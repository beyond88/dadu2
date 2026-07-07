<?php

namespace App\DataTables;

use PDF;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * TransactionDataTable
 */
class TransactionDataTable extends DataTable
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
                $buttons .= '<a class="dropdown-item" href="' . route('admin.transactions.show', $item->id) . '" title="Show"><i class="fa fa-eye"></i> ' . __('custom.show') . ' </a>';
                $buttons .= '<form action="' . route('admin.transactions.destroy', $item->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'' . __('custom.are_you_sure_to_delete') . '\');">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item text-danger"><i class="fa fa-trash"></i> ' . __('custom.delete') . '</button>
                             </form>';
                
                return '<div class="dropdown btn-group dropup">
            <a href="#" class="btn btn-dark btn-sm" data-toggle="dropdown" data-boundary="viewport"  aria-haspopup="true" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
            <div class="dropdown-menu">
            ' . $buttons . '
            </div>
            </div>';
            })
            ->editColumn('type', function ($item) {
                $badgeClass = in_array($item->type, ['add', 'transfer_in', 'invoice_payment', 'due_collection', 'opening_balance']) ? 'badge-success' : 
                    (in_array($item->type, ['reduce', 'transfer_out']) ? 'badge-danger' : 'badge-info');
                
                $typeLabels = [
                    'add' => __('custom.add_balance'),
                    'reduce' => __('custom.reduce_balance'),
                    'transfer_in' => __('custom.transfer_in'),
                    'transfer_out' => __('custom.transfer_out'),
                    'invoice_payment' => __('custom.invoice_payment'),
                    'due_collection' => __('custom.due_collection'),
                    'opening_balance' => __('custom.opening_balance')
                ];
                
                return '<span class="badge ' . $badgeClass . '">' . ($typeLabels[$item->type] ?? $item->type) . '</span>';
            })
            ->editColumn('amount', function ($item) {
                return '<strong>' . currencySymbol() . number_format($item->amount, 2) . '</strong>';
            })
            ->editColumn('balance_after', function ($item) {
                return currencySymbol() . number_format($item->balance_after, 2);
            })
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('d M Y H:i');
            })
            ->editColumn('from_to', function ($item) {
                $data = '';
                if ($item->from_account_id && $item->fromAccount) {
                    $data .= '<small>From: ' . $item->fromAccount->name . '</small><br>';
                }
                if ($item->to_account_id && $item->toAccount) {
                    $data .= '<small>To: ' . $item->toAccount->name . '</small>';
                }
                return $data ?: '-';
            })
            ->rawColumns(['type', 'amount', 'from_to', 'action'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Transaction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        $query = $model->with(['account', 'fromAccount', 'toAccount'])->orderBy('id', 'desc')->newQuery();

        if ($start = request('tr_start_date')) {
            $query->whereDate('transactions.created_at', '>=', $start);
        }
        if ($end = request('tr_end_date')) {
            $query->whereDate('transactions.created_at', '<=', $end);
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
        $params['order']    = [[1, 'desc']];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.tr_start_date=$('#tr_start_date').val();data.tr_end_date=$('#tr_end_date').val();")
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
                'action' => 'function(e, dt, node, config) { window.location.href = "' . route('admin.transactions.create') . '"; }',
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
            Column::make('created_at', 'created_at')->title(__('custom.date')),
            Column::make('account.name', 'account.name')->title(__('custom.account')),
            Column::make('type', 'type')->title(__('custom.type')),
            Column::make('amount', 'amount')->title(__('custom.amount')),
            Column::make('balance_after', 'balance_after')->title(__('custom.balance_after')),
            Column::computed('from_to', __('custom.from_to')),
            Column::make('note', 'note')->title(__('custom.note')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Transactions_' . date('YmdHis');
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
