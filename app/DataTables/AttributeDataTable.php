<?php

namespace App\DataTables;

use PDF;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;


/**
 * AttributeDataTable
 */
class AttributeDataTable extends DataTable
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
                if (auth()->user()->can('Edit Attribute')) {
                    $buttons .= '<a class="btn btn-sm btn-outline-primary ic-act-btn" href="' . route('admin.attributes.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> ' . __('custom.edit') . ' </a>';
                }

                if (auth()->user()->can('Delete Attribute')) {
                    $buttons .= '<form action="' . route('admin.attributes.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
<input type="hidden" name="_token" value="' . csrf_token() . '">
<input type="hidden" name="_method" value="DELETE">
<button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"  type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>
';
                }

                return '<div class="ic-action-inline">' . $buttons . '</div>';
            })->editColumn('items.name', function ($item) {
                return commaSeparateObjectItem($item->items, 'name');
            })->editColumn('status', function ($item) {
                $badge = $item->status == Attribute::STATUS_ACTIVE ? "badge-success" : "badge-danger";
                $data = '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
                return $data;
            })
            ->rawColumns(['items', 'status', 'action'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Attribute $model)
    {
        $query = $model->newQuery()->with(['items'])->select('attributes.*');

        if ($start = request('at_start_date')) {
            $query->whereDate('attributes.created_at', '>=', $start);
        }
        if ($end = request('at_end_date')) {
            $query->whereDate('attributes.created_at', '<=', $end);
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
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.at_start_date=$('#at_start_date').val();data.at_end_date=$('#at_end_date').val();")
            ->addAction(['width' => '55px', 'class' => "text-center", 'width' => '55px', 'printable' => false, 'exportable' => false, 'title' => __('custom.action')])
            ->parameters($this->getBuilderParameters());
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
            Column::make('name', 'name')->title(__('custom.attribute_name')),
            Column::make('items.name', 'items.name')->title(__('custom.attribute_items')),
            Column::make('status', 'status')->title(__('custom.status'))
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Attribute_' . date('YmdHis');
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
