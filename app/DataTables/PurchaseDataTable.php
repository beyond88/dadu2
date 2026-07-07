<?php

namespace App\DataTables;

use PDF;
use App\Models\Purchase;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;


/**
 * PurchaseDataTable
 */
class PurchaseDataTable extends DataTable
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
            ->filterColumn('missing_item', function ($query, $keyword) {
            })
            ->filterColumn('purchaseItems', function ($query, $keyword) {
            })
            ->addColumn('action', function ($item) {
                // Inline action buttons (no 3-dots dropdown) — text buttons.
                $buttons = '';
                if (auth()->user()->can('Show Purchase')) {
                    $buttons .= '<a class="btn btn-sm btn-outline-info ic-act-btn" href="' . route('admin.purchases.show', $item->id) . '">' . __t('show') . '</a>';
                }
                if ($item->status != Purchase::STATUS_CANCEL) {
                    if ($item->received == null) {
                        if (auth()->user()->can('Edit Purchase')) {
                            $buttons .= '<a class="btn btn-sm btn-outline-primary ic-act-btn" href="' . route('admin.purchases.edit', $item->id) . '">' . __('custom.edit') . '</a>';
                        }
                        if (auth()->user()->can('Cancel Purchase')) {
                            $buttons .= '<a class="btn btn-sm btn-outline-warning ic-act-btn" href="' . route('admin.purchases.cancel', $item->id) . '">' . __('custom.cancel') . '</a>';
                        }
                    }
                    if (auth()->user()->can('Receive Purchase')) {
                        $buttons .= '<a class="btn btn-sm btn-outline-success ic-act-btn" href="' . route('admin.purchases.receive', $item->id) . '">' . __t('receive') . '</a>';
                    }
                    if ($item->status == Purchase::STATUS_CONFIRMED) {
                        if ($item->received) {
                            if (auth()->user()->can('Return Purchase')) {
                                $buttons .= '<a class="btn btn-sm btn-outline-secondary ic-act-btn" href="' . route('admin.purchases.return', $item->id) . '">' . __t('return') . '</a>';
                            }
                        }
                    }
                    if (auth()->user()->can('Add Purchase Payment')) {
                        $buttons .= '<a class="btn btn-sm btn-outline-success ic-act-btn" href="' . route('admin.purchases.payment.create', $item->id) . '">' . __t('make_payment') . '</a>';
                    }
                    if (auth()->user()->can('View Purchase Payment')) {
                        $buttons .= '<a class="btn btn-sm btn-outline-dark ic-act-btn" href="' . route('admin.purchases.payment.history', $item->id) . '">' . __t('payment_history') . '</a>';
                    }
                }

                if (auth()->user()->can('Show Purchase')) {
                    $buttons .= '<form action="' . route('admin.purchases.destroy', $item->id) . '" id="delete-form-' . $item->id . '" method="post" class="d-inline-block m-0">
<input type="hidden" name="_token" value="' . csrf_token() . '">
<input type="hidden" name="_method" value="DELETE">
<button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data" data-from-name="' . 'No-' . $item->purchase_number . '" data-from-id="' . $item->id . '" type="button">' . __('custom.delete') . '</button></form>';
                }
                return '<div class="ic-action-inline">' . $buttons . '</div>';
            })
            ->editColumn('date', function ($item) {
                return date('Y-m-d', strtotime($item->date));
            })
            ->editColumn('purchase_number', function ($item) {
                return '<a class="btn btn-link" href="' . route('admin.purchases.show', $item->id) . '">' . $item->purchase_number . '</a>';
            })
//            ->editColumn('status', function ($item) {
//                $badge = $item->status == Purchase::STATUS_REQUESTED ? "badge-success" : ($item->status == Purchase::STATUS_CONFIRMED ? "badge-primary" : "badge-danger");
//                return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
//            })
            ->editColumn('received', function ($item) {

                if ($item->received) {
                    return '<span class="badge badge-success">' . __t('received') . '</span>';

                } else {
                    if ($item->status == \App\Models\Purchase::STATUS_CANCEL) {
                        return '<span class="badge badge-danger">' . ucfirst(\App\Models\Purchase::STATUS_CANCEL) . '</span>';
                    } elseif ($item->status == \App\Models\Purchase::STATUS_REQUESTED) {
                        return '<span class="badge badge-info">' . ucfirst(\App\Models\Purchase::STATUS_REQUESTED) . '</span>';
                    } elseif ($item->status == \App\Models\Purchase::STATUS_CONFIRMED) {
                        return '<span class="badge badge-success">' . ucfirst(\App\Models\Purchase::STATUS_CONFIRMED) . '</span>';
                    } else {
                        return '<span class="badge badge-warning">' . __t('not_received_yet') . '</span>';
                    }
                }

            })
            ->editColumn('purchaseItems', function ($item) {
                return $item->purchaseItems->count();
            })
            ->addColumn('purchased_qty', function ($item) {
                // Sum purchased quantity: barrel + kg for weight-based products, units otherwise.
                $totalBarrels = 0; $totalKg = 0; $totalUnits = 0; $labels = [];
                foreach ($item->purchaseItems as $pi) {
                    if ($pi->product && $pi->product->is_weight_based) {
                        $kpb = (float) $pi->product->kg_per_barrel;
                        $totalBarrels += (float) $pi->quantity;
                        $totalKg      += (float) $pi->quantity * $kpb;
                        $labels[]     = $pi->product->barrel_label ?: __('custom.barrels');
                    } else {
                        $totalUnits += (float) $pi->quantity;
                    }
                }
                // Use the actual unit label when all weight-based items share one; else generic.
                $barrelLabel = count(array_unique($labels)) === 1 ? $labels[0] : __('custom.barrels');
                $parts = [];
                if ($totalBarrels > 0) {
                    $parts[] = (float) $totalBarrels . ' ' . $barrelLabel
                        . ' <small class="text-muted">(' . (float) $totalKg . ' kg)</small>';
                }
                if ($totalUnits > 0) {
                    $parts[] = (float) $totalUnits;
                }
                return implode('<br>', $parts) ?: '—';
            })
            ->addColumn('buying_price_per_kg', function ($item) {
                // Per-kg buying price for weight-based products, unit price otherwise.
                $parts = [];
                foreach ($item->purchaseItems as $pi) {
                    if ($pi->product && $pi->product->is_weight_based) {
                        $kpb   = (float) $pi->product->kg_per_barrel;
                        $perKg = $kpb ? ($pi->price / $kpb) : 0;
                        $parts[] = currencySymbol() . make2decimal($perKg) . ' <small class="text-muted">/kg</small>';
                    } else {
                        $parts[] = currencySymbol() . make2decimal($pi->price);
                    }
                }
                return implode('<br>', $parts) ?: '—';
            })
            ->addColumn('price_per_barrel', function ($item) {
                // The stored per-barrel price for weight-based products.
                $parts = [];
                foreach ($item->purchaseItems as $pi) {
                    if ($pi->product && $pi->product->is_weight_based) {
                        $label   = $pi->product->barrel_label ?: __('custom.barrels');
                        $parts[] = currencySymbol() . make2decimal($pi->price)
                            . ' <small class="text-muted">/' . e($label) . '</small>';
                    } else {
                        $parts[] = '—';
                    }
                }
                return implode('<br>', $parts) ?: '—';
            })
            ->addColumn('amount', function ($item) {
                // Total purchased amount (sum of item sub totals).
                return currencySymbol() . make2decimal($item->purchaseItems->sum('sub_total'));
            })
            ->editColumn('missing_item', function ($item) {
                if ($item->received) {
                    $purchaseItemQty = $item->purchaseItems->sum('quantity');
                    $purchaseReceiveItemQty = 0;
                    foreach ($item->purchaseItems as $purchaseItems) {
                        $purchaseReceiveItemQty += $purchaseItems->receiveItems->sum('quantity');
                    }
                    if ($purchaseItemQty != $purchaseReceiveItemQty) {
                        return '<span class="badge badge-danger">' . __t('missing') .' ('.$purchaseItemQty - $purchaseReceiveItemQty.')' .'</span>';
                    }
                }
            })
            ->editColumn('total', function ($item) {
                return currencySymbol() . make2decimal($item->total);
            })
            ->addColumn('paid', function ($item) {
                $paid = $item->payments->sum('amount');
                return currencySymbol() . make2decimal($paid);
            })
            ->addColumn('due', function ($item) {
                $paid = $item->payments->sum('amount');
                $due = $item->total - $paid;
                $color = $due > 0 ? 'text-danger' : 'text-success';
                return '<span class="' . $color . ' font-weight-bold">' . currencySymbol() . make2decimal($due) . '</span>';
            })
            ->editColumn('supplier.first_name', function ($item) {
                return $item->supplier->full_name;
            })
            ->rawColumns(['status', 'received', 'date', 'purchaseItems', 'purchased_qty', 'buying_price_per_kg', 'price_per_barrel', 'action', 'missing_item', 'supplier.first_name', 'purchase_number','due'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Purchase $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Purchase $model)
    {
        $query = $model->with(['supplier', 'warehouse', 'purchaseItems.product', 'payments'])->newQuery()->orderBy('id','desc')->select('purchases.*');

        if ($start = request('pu_start_date')) {
            $query->whereDate('purchases.date', '>=', $start);
        }
        if ($end = request('pu_end_date')) {
            $query->whereDate('purchases.date', '<=', $end);
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
        // purchase_number moved to index 4 (after SL, Received, Action, Missing Item) — keep the same default sort.
        $params['order']    = [[4, 'desc']];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.pu_start_date=$('#pu_start_date').val();data.pu_end_date=$('#pu_end_date').val();")
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
            Column::make('received', 'received')->title(__t('received')),
            Column::computed('action', __('custom.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(260)
                ->addClass('text-center'),
            Column::make('purchase_number', 'purchase_number')->title(__t('purchase_number')),
            Column::make('supplier.first_name', 'supplier.first_name')->title(__t('supplier_name')),
            Column::make('warehouse.name', 'warehouse.name')->title(__t('warehouse')),
            Column::make('date', 'date')->title(__t('date')),
            Column::make('total', 'total')->title(__t('total')),
            Column::computed('paid', __t('paid'))->addClass('text-right'),
            Column::computed('due', __t('due'))->addClass('text-right'),
            // Column::make('purchaseItems', 'purchaseItems')->title(__t('total_unique_product'))->addClass('text-center'),
            Column::computed('purchased_qty', __('custom.purchased_quantity'))->addClass('text-center'),
            Column::computed('buying_price_per_kg', __('custom.buying_price_per_kg'))->addClass('text-right'),
            Column::computed('price_per_barrel', __('custom.price_per_barrel', ['label' => __('custom.barrel')]))->addClass('text-right'),
            Column::computed('amount', __('custom.amount'))->addClass('text-right'),
            Column::make('missing_item', 'missing_item')->title(__t('missing_item')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Purchase_' . date('YmdHis');
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
