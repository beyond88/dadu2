<?php

namespace App\DataTables;

use PDF;
use App\Models\WeightUnit;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * WeightUnitDataTable
 */
class WeightUnitDataTable extends DataTable
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
                if (auth()->user()->can('Edit Weight Unit')) {
                    $buttons .= '<a class="btn btn-sm btn-outline-primary ic-act-btn" href="' . route('admin.weight-units.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> ' . __('custom.edit') . ' </a>';
                }
                if (auth()->user()->can('Delete Weight Unit')) {
                    $buttons .= '<form action="' . route('admin.weight-units.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post" >
<input type="hidden" name="_token" value="' . csrf_token() . '">
<input type="hidden" name="_method" value="DELETE">
<button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>
';
                }
                return '<div class="ic-action-inline">' . $buttons . '</div>';
            })->rawColumns(['action'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(WeightUnit $model)
    {
        $query = $model->newQuery();

        if ($start = request('wu_start_date')) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end = request('wu_end_date')) {
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
            ->minifiedAjax('', "data.wu_start_date=$('#wu_start_date').val();data.wu_end_date=$('#wu_end_date').val();")
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
            Column::make('name', 'name')->title(__('custom.weight_unit_name'))
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'WeightUnit_' . date('YmdHis');
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
