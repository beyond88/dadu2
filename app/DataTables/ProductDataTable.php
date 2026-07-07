<?php

namespace App\DataTables;

use PDF;
use App\Models\Product;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * ProductDataTable
 */
class ProductDataTable extends DataTable
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
            ->addColumn('id', function ($row) {
                return '<input type="checkbox" class="product_checkbox" name="product_id[]" value="' . $row->id . '">';
            })
            ->addColumn('action', function ($item) {
                $buttons = '';
                $buttons .= '<a class="dropdown-item" href="' . route('admin.products.show', $item->id) . '" title="Show"><i class="fa fa-eye"></i> ' . __('custom.show') . ' </a>';
                if (auth()->user()->can('Edit Product')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.products.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> ' . __('custom.edit') . ' </a>';
                }
                if (auth()->user()->can('Stock Product')) {
                    $buttons .= '<a class="dropdown-item" href="' . route('admin.product-stocks.edit', $item->id) . '" title="Stock And Price"><i class="mdi mdi-format-list-bulleted"></i> ' . __('custom.stock_and_price') . ' </a>';
                }

                // $buttons .= '<a class="dropdown-item update-stock" data-id="' . $item->id . '" href="#" title="Update Stock"><i class="mdi mdi-stack-exchange"></i> ' . __('custom.update_stock') . ' </a>';

                $buttons .= '<a class="dropdown-item" href="' . route('admin.products.barcode.download', $item->id) . '" title="Download Barcode"><i class="mdi mdi-download"></i> ' . __('custom.download_barcode') . ' </a>';
                if (auth()->user()->can('Delete Product')) {
                    $buttons .= '<form action="' . route('admin.products.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post" >
<input type="hidden" name="_token" value="' . csrf_token() . '">
<input type="hidden" name="_method" value="DELETE">
<button class="dropdown-item text-danger delete-list-data" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('custom.delete') . '</button></form>
';
                }
                return '<div class="dropdown btn-group dropup">
  <a href="#" class="btn btn-dark btn-sm" data-toggle="dropdown" data-boundary="viewport"  aria-haspopup="true" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
  <div class="dropdown-menu">
  ' . $buttons . '
  </div>
</div>';
            })
            ->editColumn('is_variant', function ($item) {
                if ($item->is_variant) {
                    return '<span class="badge badge-success">Yes</span>';
                } else {
                    return '<span class="badge badge-secondary">No</span>';
                }
            })
              ->editColumn('is_batch_product', function ($item) {
                if ($item->is_batch_product) {
                    return '<span class="badge badge-success">Yes</span>';
                } else {
                    return '<span class="badge badge-secondary">No</span>';
                }
            })
            ->editColumn('name', function ($item) {
                return '<a class="btn btn-link" href="' . route('admin.products.show', $item->id) . '">' . $item->name . '</a>';
            })
            ->editColumn('sku', function ($item) {
                $sku = e($item->sku);
                return '<span class="ic-code-value">' . $sku . '</span>'
                    . ' <button type="button" class="btn btn-sm p-0 ml-1 ic-copy-code" data-code="' . $sku . '" title="Copy code" style="line-height:1;"><i class="mdi mdi-content-copy text-muted"></i></button>';
            })
           ->editColumn('price', function ($item) {
            // If the product has variants
            if ($item->is_variant) {
                // Get all variant prices (assuming relation: $item->variations)
                $prices = $item->variations()->pluck('price')->filter()->toArray();

                if (count($prices) > 0) {
                    $minPrice = min($prices);
                    $maxPrice = max($prices);

                    // If both prices are the same, show one price
                    if ($minPrice == $maxPrice) {
                        return currencySymbol() . make2decimal($minPrice);
                    }

                    // Otherwise, show price range
                    return currencySymbol() . make2decimal($minPrice) . ' - ' . currencySymbol() . make2decimal($maxPrice);
                } else {
                    return '<span class="text-muted">-</span>';
                }
            }

            // For non-variant product, check if there is warehouse-specific stock price
            $sellingPrice = $item->price;
            if (!$item->is_variant) {
                $stock = $item->allStock->first();
                if ($stock && $stock->customer_buying_price > 0) {
                    $sellingPrice = $stock->customer_buying_price;
                }
            }

            // For non-variant product. `price` already holds the final selling
            // price (per-barrel for sold-by-weight products), so show it directly.
            if ($item->is_weight_based && (float) $item->kg_per_barrel > 0) {
                return currencySymbol() . make2decimal($sellingPrice)
                    . ' <small class="text-muted">/ ' . e($item->barrel_label ?: __('custom.barrel')) . '</small>';
            }

            return $sellingPrice ? currencySymbol() . make2decimal($sellingPrice) : '<span class="text-muted">-</span>';
        })

            ->addColumn('selling_price_per_kg', function ($item) {
                // For sold-by-weight products, selling_price (customer_buying_price in stock) is stored per-barrel; show per-kg.
                if ($item->is_weight_based && (float) $item->kg_per_barrel > 0) {
                    $sellingPrice = $item->price;
                    if (!$item->is_variant) {
                        $stock = $item->allStock->first();
                        if ($stock && $stock->customer_buying_price > 0) {
                            $sellingPrice = $stock->customer_buying_price;
                        }
                    }
                    if ($sellingPrice) {
                        $perKg = $sellingPrice / (float) $item->kg_per_barrel;
                        return currencySymbol() . make2decimal($perKg) . ' <small class="text-muted">/kg</small>';
                    }
                }
                return '<span class="text-muted">-</span>';
            })

            ->editColumn('buying_price', function ($item) {
                // Sold-by-weight: show buying price PER BARREL. Stock.price is stored per-barrel;
                // the product's own buying_price is per-kg (converted via the accessor).
                if ($item->is_weight_based && (float) $item->kg_per_barrel > 0) {
                    $stock = !$item->is_variant ? $item->allStock->first() : null;
                    $perBarrel = ($stock && $stock->price > 0)
                        ? (float) $stock->price
                        : (float) $item->buying_price_per_barrel;
                    if ($perBarrel) {
                        return currencySymbol() . make2decimal($perBarrel)
                            . ' <small class="text-muted">/ ' . e($item->barrel_label ?: __('custom.barrel')) . '</small>';
                    }
                    return '<span class="text-muted">-</span>';
                }

                // Non-weight products: buying_price is the unit price (with stock override if any).
                $buyingPrice = $item->buying_price;
                if (!$item->is_variant) {
                    $stock = $item->allStock->first();
                    if ($stock && $stock->price > 0) {
                        $buyingPrice = $stock->price;
                    }
                }
                return $buyingPrice
                    ? currencySymbol() . make2decimal($buyingPrice)
                    : '<span class="text-muted">-</span>';
            })

            ->addColumn('buying_price_per_kg', function ($item) {
                // Sold-by-weight: show buying price PER KG. Stock.price is per-barrel (convert down);
                // the product's own buying_price is already per-kg (accessor returns it raw).
                if ($item->is_weight_based && (float) $item->kg_per_barrel > 0) {
                    $stock = !$item->is_variant ? $item->allStock->first() : null;
                    $perKg = ($stock && $stock->price > 0)
                        ? ((float) $stock->price / (float) $item->kg_per_barrel)
                        : (float) $item->buying_price_per_kg;
                    if ($perKg) {
                        return currencySymbol() . make2decimal($perKg) . ' <small class="text-muted">/kg</small>';
                    }
                }
                return '<span class="text-muted">-</span>';
            })

            ->editColumn('stock', function ($item) {
                // For weight-based products break a fractional barrel into
                // whole barrels + remaining kg, e.g. 9.5 => "9 DRUM 10 kg".
                if ($item->is_weight_based) {
                    $label        = $item->barrel_label ?: __('custom.barrels');
                    $kpb          = (float) $item->kg_per_barrel;
                    $stockVal     = (float) $item->stock;
                    $wholeBarrels = (int) floor($stockVal);
                    $remainderKg  = $kpb > 0 ? round(($stockVal - $wholeBarrels) * $kpb, 2) : 0;

                    $text = $wholeBarrels . ' ' . $label;
                    if ($remainderKg > 0) {
                        $text .= ' ' . rtrim(rtrim(number_format($remainderKg, 2, '.', ''), '0'), '.') . ' kg';
                    }
                    return $text;
                }

                $stock = rtrim(rtrim(number_format((float) $item->stock, 2, '.', ''), '0'), '.');
                $unit  = optional($item->weight_unit)->name;
                return $stock . ' ' . $unit;
            })
            ->editColumn('category.name', function ($item) {
                return $item->category->name ?? '';
            })
            ->editColumn('thumb', function ($item) {
                return '<img class="img-64" src="' . $item->thumb_url . '" alt="' . $item->name . '" />';
            })->editColumn('status', function ($item) {
                $badge = $item->status == Product::STATUS_ACTIVE ? "badge-success" : "badge-danger";
                return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
            })
            /*->editColumn('tax_status', function ($item) {
                return Str::upper($item->tax_status);
            })*/
            ->editColumn('stock_value', function ($item) {
                $stockValue = $item->stock_value;
                return currencySymbol() . make2decimal($stockValue);
            })
            ->rawColumns(['tax_status', 'status', 'thumb', 'action', 'id', 'name', 'sku', 'is_variant', 'is_batch_product', 'price', 'selling_price_per_kg', 'buying_price', 'buying_price_per_kg', 'stock_value'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model)
    {
        $query = $model->with(['category', 'manufacturer', 'weight_unit', 'allStock', 'purchaseItemReceives'])->newQuery()->select('products.*')->orderByDesc('products.id');

        if ($start = request('pd_start_date')) {
            $query->whereDate('products.created_at', '>=', $start);
        }
        if ($end = request('pd_end_date')) {
            $query->whereDate('products.created_at', '<=', $end);
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
        // No default column ordering — the query returns newest products first.
        $params['order']    = [];

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.pd_start_date=$('#pd_start_date').val();data.pd_end_date=$('#pd_end_date').val();")
            ->addAction(
                [
                    'width' => '55px',
                    'class' => "text-center",
                    'printable' => false,
                    'exportable' => false,
                    'title' => __('custom.action'),
                    'scrollY' => '320px'
                ],
            )
            ->parameters($params);
    }
    protected function getBuilderParameters(): array
    {
        $params = parent::getBuilderParameters();
        $params['buttons'] = [
            [
                'extend' => 'create',
                'text' => '<i class="fa fa-plus"></i> ' . 'Create',
            ],
            [
                'text' => '<i class="fa fa-upload"></i> ' . 'Import',
                'className' => 'import_btn',
                'action' => 'function(e, dt, node, config) { $("#importModal").modal("show"); }',
            ],
            [ 'extend' => 'csv', 'text' => '<i class="fa fa-file-excel"></i> CSV' ],
            [ 'extend' => 'excel', 'text' => '<i class="fa fa-file-excel"></i> Excel' ],
            [ 'extend' => 'pdf', 'text' => '<i class="fa fa-file-pdf"></i> PDF' ],
            [ 'extend' => 'print', 'text' => '<i class="fa fa-print"></i> Print' ],
            [ 'extend' => 'reload', 'text' => '<i class="fa fa-sync"></i> Reload' ],
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
            Column::make('id', 'id')
                ->orderable(false)
                ->searchable(false)
                ->printable(false)
                ->exportable(false)
                ->className('select_all_checkbox')
                ->title('<input type="checkbox" class="select_all"/>')
                ->render(''),
            Column::computed('DT_RowIndex', __('custom.sl')),
            Column::make('thumb', 'thumb')->title(__('custom.thumb'))->visible(false),
            Column::make('name', 'name')->title(__('custom.product_name')),
            Column::make('sku', 'sku')->title('Code'),
            Column::make('category.name', 'category.name')->title(__('custom.category'))->visible(false),
            Column::make('price', 'price')->title(__('custom.selling_price')),
            Column::computed('selling_price_per_kg', __('custom.selling_price_per_kg')),
            Column::make('buying_price', 'buying_price')->title(__('custom.buying_price')),
            Column::computed('buying_price_per_kg', __('custom.buying_price_per_kg')),
            Column::make('stock', 'stock')->title(__('custom.stock_quantity')),
            Column::computed('stock_value', __('custom.stock_value')),
            Column::make('is_variant', 'is_variant')->title(__('custom.variant'))->visible(false),
            Column::make('is_batch_product', 'is_batch_product')->title(__('custom.batch'))->visible(false),
            /*Column::make('tax_status', 'tax_status')->title(__('custom.tax')),*/
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
        return 'Product_' . date('YmdHis');
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
