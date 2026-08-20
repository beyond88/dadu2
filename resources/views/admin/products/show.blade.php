@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.product') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('custom.show_product_details') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4 class="header-title">{{ __('custom.show_product_details') }}</h4>
                        </div>
                        <div class="col-lg-6 d-print-none ic-print-btn-head">
                            <a href="{{ url()->previous() }}" class="btn btn-info mr-2"><i class="fa fa-arrow-left"></i>
                                Back</a>
                        </div>
                    </div>
                    <h4 class="header-title"></h4>

                    <dl class="row">
                        <dt class="col-sm-3">{{ __('custom.product_name') }}</dt>
                        <dd class="col-sm-9">: {{ $product_details->name }}</dd>

                        {{-- Parts No hidden per request
                        <dt class="col-sm-3">{{ __('custom.parts_no') }}</dt>
                        <dd class="col-sm-9">: {{ $product_details->parts_no }}</dd>
                        --}}
                        @php
                            $platforms = $product_details->platforms()->with('platform')->get();
                        @endphp

                        @if($platforms->isNotEmpty())
                            <dt class="col-sm-3">{{ __('custom.sales_channel') }}</dt>
                            <dd class="col-sm-9">
                                : {{ $platforms->pluck('platform.store_name')->filter()->implode(', ') }}
                            </dd>
                        @endif

                        <dt class="col-sm-3">Code</dt>
                        <dd class="col-sm-9">: {{ $product_details->sku }}</dd>

                        {{-- <dt class="col-sm-3">{{ __('custom.barcode') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->barcode }}
                            @if($product_details->barcode_image)
                                <br>
                                <img src="{{ getStorageImage(\App\Models\Product::BARCODE_STORE_PATH, $product_details->barcode_image) }}"
                                     alt="Barcode" class="mt-2" style="max-width: 200px;">
                            @endif
                        </dd> --}}

                        {{-- Category Name, Manufacturer, Model hidden per request
                        <dt class="col-sm-3 text-truncate">{{ __('custom.category_name') }}</dt>
                        <dd class="col-sm-9">: {{ optional($product_details->category)->name }}</dd>

                        <dt class="col-sm-3">{{ __('custom.manufacturer') }}</dt>
                        <dd class="col-sm-9">
                            : {{ optional($product_details->manufacturer)->name }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.model') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->model }}
                        </dd>
                        --}}
                        {{-- Barrel details --}}
                        @if($product_details->is_weight_based && (float) $product_details->kg_per_barrel > 0)
                        <dt class="col-sm-3">{{ __('custom.barrel_label') }}</dt>
                        <dd class="col-sm-9">: {{ $product_details->barrel_label ?: '-' }}</dd>

                        <dt class="col-sm-3">{{ __('custom.kg_per_barrel') }}</dt>
                        <dd class="col-sm-9">: {{ (float) $product_details->kg_per_barrel }} kg</dd>
                        @endif

                        {{-- Prices: Selling, Selling/kg, Buying, Buying/kg --}}
                        <dt class="col-sm-3">Selling Price</dt>
                        <dd class="col-sm-9">
                            : {{ currencySymbol() . ' ' . make2decimal($product_details->price) }}
                            @if($product_details->is_weight_based && (float) $product_details->kg_per_barrel > 0)
                                <small class="text-muted">/ {{ $product_details->barrel_label ?: __('custom.barrel') }}</small>
                            @endif
                        </dd>

                        @if($product_details->is_weight_based && (float) $product_details->kg_per_barrel > 0)
                        <dt class="col-sm-3">{{ __('custom.selling_price_per_kg') }}</dt>
                        <dd class="col-sm-9">
                            : {{ currencySymbol() . ' ' . make2decimal($product_details->price / (float) $product_details->kg_per_barrel) }}
                            <small class="text-muted">/kg</small>
                        </dd>
                        @endif

                        <dt class="col-sm-3">{{ __('custom.buying_price') }}</dt>
                        <dd class="col-sm-9">
                            @if($product_details->buying_price_per_barrel !== null)
                                : {{ currencySymbol() . ' ' . make2decimal($product_details->buying_price_per_barrel) }}
                                @if($product_details->is_weight_based && (float) $product_details->kg_per_barrel > 0)
                                    <small class="text-muted">/ {{ $product_details->barrel_label ?: __('custom.barrel') }}</small>
                                @endif
                            @else
                                : <span class="text-muted">-</span>
                            @endif
                        </dd>

                        @if($product_details->is_weight_based && (float) $product_details->kg_per_barrel > 0 && $product_details->buying_price)
                        <dt class="col-sm-3">{{ __('custom.buying_price_per_kg') }}</dt>
                        <dd class="col-sm-9">
                            : {{ currencySymbol() . ' ' . make2decimal($product_details->buying_price_per_kg) }}
                            <small class="text-muted">/kg</small>
                        </dd>
                        @endif
                        <dt class="col-sm-3">{{ __('custom.tax') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->tax_status == \App\Models\Product::TAX_INCLUDED ? 'included' : 'Excluded' }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.custom_tax_amount') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->custom_tax }} %
                        </dd>

                        {{-- Weight, Weight Unit, Notes, Description, Is Variant, Is Split sale, Image hidden per request --}}
                        {{--
                        <dt class="col-sm-3">{{ __('custom.weight') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->weight }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.weight_unit') }}</dt>
                        <dd class="col-sm-9">
                            : {{ optional($product_details->weight_unit)->name }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.notes') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->notes }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.desc') }}</dt>
                        <dd class="col-sm-9">
                            : {!! $product_details->desc !!}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.is_variant') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->is_variant ? 'Yes' : 'No' }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.is_split_sale') }}</dt>
                        <dd class="col-sm-9">
                            : {{ $product_details->split_sale ? 'Yes' : 'No' }}
                        </dd>
                        <dt class="col-sm-3">{{ __('custom.image') }}</dt>
                        <dd class="col-sm-9">
                            @if($product_details->thumb)
                                <img
                                    src="{{ getStorageImage(\App\Models\Product::FILE_STORE_PATH, $product_details->thumb) }}"
                                    width="60px" alt="{{ $product_details->name }}">
                            @else
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect fill='%23f0f0f0' width='60' height='60'/%3E%3C/svg%3E" width="60px" alt="No Image">
                            @endif
                        </dd>
                        --}}

                        <dt class="col-sm-3">{{ __('custom.stock_quantity') }}</dt>
                          <dd class="col-sm-9">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('custom.warehouse') }}</th>
                                        @if($product_details->is_variant)
                                            <th>{{ __('custom.variant') }}</th>
                                            <th>{{ __('custom.sku') }}</th>
                                            <th>{{ __('custom.barcode') }}</th>
                                        @else
                                            <th>{{ __('custom.sku') }}</th>
                                            <th>{{ __('custom.barcode') }}</th>
                                            <th>{{ __('custom.batch') }}</th>

                                        @endif
                                        <th>{{ __('custom.quantity') }}</th>
                                        <th>{{ __('custom.purchase_price') }}</th>
                                        <th>Selling Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product_details->allStock->groupBy('warehouse_id') as $warehouseId => $warehouseStocks)
                                        @foreach($warehouseStocks as $stock)
                                            <tr>
                                                <td>{{ optional($stock->warehouse)->name }}</td>
                                                @if($product_details->is_variant)
                                                    <td>{{ optional($stock->variation)->name }}</td>
                                                    <td>{{ optional($stock->variation)->sku }}</td>
                                                    <td>
                                                        @if(optional($stock->variation)->barcode_image)
                                                            <img src="{{ getStorageImage(\App\Models\Product::BARCODE_STORE_PATH, optional($stock->variation)->barcode_image) }}"
                                                                 alt="Barcode" style="max-width: 120px; height: auto;">
                                                        @else
                                                            {{ optional($stock->variation)->barcode ?? '-' }}
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>{{ $product_details->sku }}</td>
                                                    <td>
                                                        @if($product_details->barcode_image)
                                                            <img src="{{ getStorageImage(\App\Models\Product::BARCODE_STORE_PATH, $product_details->barcode_image) }}"
                                                                 alt="Barcode" style="max-width: 120px; height: auto;">
                                                        @else
                                                            {{ $product_details->barcode ?? '-' }}
                                                        @endif
                                                    </td>
                                                <td>{{ $stock->batch ?? '-' }}</td>

                                                @endif
                                                <td><strong>{{ $stock->quantity }}</strong></td>
                                                <td>{{ currencySymbol() . ' ' . ($product_details->is_variant ? optional($stock->variation)->customer_buying_price : $product_details->customer_buying_price) }}</td>
                                                <td>{{ currencySymbol() . ' ' . make2decimal($stock->price_for_sale) }}@if($product_details->is_weight_based && (float) $product_details->kg_per_barrel > 0)<small class="text-muted"> / {{ $product_details->barrel_label ?: __('custom.barrel') }}</small>@endif</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @if($product_details->allStock->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                {{ __('custom.no_stock_available') }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="4" class="text-right">{{ __('custom.current_stock') }}:</th>
                                        <th><strong>{{ $product_details->allStock->sum('quantity') }}</strong></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                         </dd>

                    </dl>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
@endpush

@push('script')
@endpush
