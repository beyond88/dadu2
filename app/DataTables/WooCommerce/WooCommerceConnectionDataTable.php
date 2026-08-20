<?php

namespace App\DataTables\WooCommerce;

use App\Models\Platform;
use App\Models\SystemCountry;
use PDF;
use App\Models\Brand;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;


/**
 * BrandDataTable
 */
class WooCommerceConnectionDataTable extends DataTable
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
            $formId = "connect-disconnect-form-{$item->id}";

            // Always show "Show" button
            $buttons = '<a class="btn btn-sm btn-outline-info ic-act-btn" href="' . route('woocommerce.addon-configurations.show', $item->id) . '" title="Show"><i class="fa fa-eye"></i> ' . __('custom.show') . '</a>';

            if ($item->is_connected) {
                // Disconnect button (red)
                $buttons .= '<button class="btn btn-sm btn-outline-warning ic-act-btn disconnect-list-data"
                                data-from-name="' . e($item->name) . '"
                                data-from-id="' . $item->id . '"
                                data-from-text="' . __('custom.disconnect_confirmation') . '"
                                data-action="disconnect"
                                type="button"
                                title="' . __('custom.disconnect') . '">
                                <i class="fa fa-power-off"></i> ' . __('custom.disconnect') . '
                            </button>';

                // Hidden form for disconnect (PUT)
                $form = '
                <form id="' . $formId . '" action="' . route('woocommerce.addon-configurations.update', $item->id) . '" method="post" style="display:none;">
                    ' . csrf_field() . '
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="action" value="disconnect">
                </form>';
            } else {
                // Connect button (green)
                $buttons .= '<button class="btn btn-sm btn-outline-success ic-act-btn disconnect-list-data"
                                data-from-name="' . e($item->name) . '"
                                data-from-id="' . $item->id . '"
                                data-from-text="' . __('custom.connect_confirmation') . '"
                                data-action="connect"
                                type="button"
                                title="' . __('custom.connect') . '">
                                <i class="fa fa-plug"></i> ' . __('custom.connect') . '
                            </button>';

                // Hidden form for connect (PUT)
                $form = '
                <form id="' . $formId . '" action="' . route('woocommerce.addon-configurations.update', $item->id) . '" method="post" style="display:none;">
                    ' . csrf_field() . '
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="action" value="connect">
                </form>';
            }

            // Return the inline action buttons
            return $form . '<div class="ic-action-inline">' . $buttons . '</div>';
        })
        ->editColumn('is_connected', function ($item) {
            if ($item->is_connected) {
                return '<span class="badge badge-success"><i class="fa fa-check-circle"></i> ' . __('custom.connected') . '</span>';
            } else {
                return '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> ' . __('custom.disconnected') . '</span>';
            }
        })
        ->rawColumns(['action', 'is_connected'])
        ->addIndexColumn();
}


    /**
     * Get query source of dataTable.
     *
     * @param SystemCountry $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Platform $model)
    {

        return $model->newQuery()->where('type', 'wooCommerce');
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
            ->minifiedAjax()
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
            Column::make('store_name', 'store_name')->title(__('custom.store_name')),
            Column::make('store_url', 'store_url')->title(__('custom.store_url')),
            Column::make('is_connected', 'is_connected')->title(__('custom.status')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'SystemCountry' . date('YmdHis');
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
