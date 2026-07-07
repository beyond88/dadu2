<?php

namespace App\DataTables;

use PDF;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * SupplierDataTable
 */
class SupplierDataTable extends DataTable
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
                if (auth()->user()->can('List Supplier')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.suppliers.show', $item->id) . '" title="Edit"><i class="fa fa-eye"></i> ' . __('custom.show') . ' </a>';
                }
                if (auth()->user()->can('Edit Supplier')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.suppliers.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> ' . __('custom.edit') . ' </a>';
                }
                
                if (auth()->user()->can('Add Purchase Payment')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.suppliers.payment.create', $item->id) . '" title="Make Payment"><i class="fa fa-credit-card"></i> ' . __t('make_payment') . ' </a>';
                }

                if (auth()->user()->can('View Purchase Payment')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.suppliers.payment.history', $item->id) . '" title="Payment History"><i class="fa fa-history"></i> ' . __t('payment_history') . ' </a>';
                }

                if (auth()->user()->can('Delete Supplier')) {
                    $buttons .= '<form action="' . route('admin.suppliers.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
<input type="hidden" name="_token" value="' . csrf_token() . '">
<input type="hidden" name="_method" value="DELETE">
<button class="dropdown-item text-danger delete-list-data" data-from-name="' . $item->full_name . '" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>
';
                }
                return '<div class="dropdown btn-group dropup">
  <a href="#" class="btn btn-dark btn-sm" data-toggle="dropdown" data-boundary="viewport"  aria-haspopup="true" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
  <div class="dropdown-menu">
  ' . $buttons . '
  </div>
</div>';
            })->editColumn('avatar', function ($item) {
                return '<img class="img-64" src="' . getStorageImage(Supplier::FILE_STORE_PATH, $item->avatar) . '" alt="' . $item->name . '" />';
            })->editColumn('status', function ($item) {
                $badge = $item->status == Supplier::STATUS_ACTIVE ? "badge-success" : "badge-danger";
                $data = '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
                if ($item->is_default) {
                    $data .= '<br><small class="text-info">Default</small>';
                }
                return $data;
            })->addColumn('total_amount', function ($item) {
                return currencySymbol() . ' ' . number_format(($item->total_purchase_amount ?? 0) + ($item->opening_balance ?? 0), 2);
            })->addColumn('total_paid', function ($item) {
                return currencySymbol() . ' ' . number_format($item->total_paid_amount ?? 0, 2);
            })->addColumn('total_due', function ($item) {
                $totalAmount = ($item->total_purchase_amount ?? 0) + ($item->opening_balance ?? 0);
                $totalPaid = $item->total_paid_amount ?? 0;
                $totalDue = $totalAmount - $totalPaid;
                return currencySymbol() . ' ' . number_format($totalDue, 2);
            })->rawColumns(['status', 'avatar', 'action'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Supplier $model)
    {
        $prefix = \DB::getTablePrefix();

        $query = $model->newQuery()
            ->selectRaw($prefix . 'suppliers.*,
                COALESCE(purchase_totals.total_amount, 0) as total_purchase_amount,
                COALESCE(payment_totals.total_paid, 0) as total_paid_amount')
            ->leftJoin(\DB::raw('(
                SELECT supplier_id, SUM(total) as total_amount
                FROM ' . $prefix . 'purchases
                GROUP BY supplier_id
            ) as purchase_totals'), function($join) {
                $join->on('suppliers.id', '=', \DB::raw('purchase_totals.supplier_id'));
            })
            ->leftJoin(\DB::raw('(
                SELECT p.supplier_id, SUM(pp.amount) as total_paid
                FROM ' . $prefix . 'purchases p
                INNER JOIN ' . $prefix . 'purchase_payments pp ON p.id = pp.purchase_id
                GROUP BY p.supplier_id
            ) as payment_totals'), function($join) {
                $join->on('suppliers.id', '=', \DB::raw('payment_totals.supplier_id'));
            });

        if ($start = request('su_start_date')) {
            $query->whereDate('suppliers.created_at', '>=', $start);
        }
        if ($end = request('su_end_date')) {
            $query->whereDate('suppliers.created_at', '<=', $end);
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
        $params['order']    = [[2, 'asc']];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.su_start_date=$('#su_start_date').val();data.su_end_date=$('#su_end_date').val();")
            ->addAction(['width' => '55px', 'class' => "text-center", 'width' => '55px', 'printable' => false, 'exportable' => false, 'title' => __('custom.action')])
            ->parameters($params);
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
            Column::make('avatar', 'avatar')->title(__('custom.avatar'))->visible(false),
            Column::make('first_name', 'first_name')->title(__('custom.first_name')),
            Column::make('last_name', 'last_name')->title(__('custom.last_name')),
            Column::make('email', 'email')->title(__('custom.email')),
            Column::make('phone', 'phone')->title(__('custom.phone')),
            Column::make('status', 'status')->title(__('custom.status')),
            Column::computed('total_amount')->title(__('custom.total_amount')),
            Column::computed('total_paid')->title(__('custom.total_paid')),
            Column::computed('total_due')->title(__('custom.total_due')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Supplier_' . date('YmdHis');
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
