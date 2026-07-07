@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.warehouse') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('custom.warehouse_details') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-16 text-uppercase mt-0 text-white-50">{{ __('custom.total_qty') }} ({{ __('custom.all') }})</h5>
                        <h4 class="font-500">{{ $grand_total_quantity }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stat bg-success text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-16 text-uppercase mt-0 text-white-50">{{ __('custom.total_price') }} ({{ __('custom.all') }})</h5>
                        <h4 class="font-500">{{ currencySymbol() . make2decimal($grand_total_price) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="emp-profile ic-employee-warper-container d-print-none">
                        <div class="ic-customer-details-warper ic-details-wharphase">
                            <div class="ic-customer-profile-basic-info ic-details-warephaseInfo p-3 border ">
                                <div class="ic-customer-basic-info pl-0 mt-0">
                                    <div class="profile-head">
                                        <h5 class="text-muted">
                                            {{ $warehouse_details->name }}
                                        </h5>
                                        <h6>
                                            {{ __t('email') }}: {{ $warehouse_details->email }}
                                        </h6>
                                        <p class="mb-0 ic-discription-customer">
                                            {{ __t('phone') }}: {{ $warehouse_details->phone }}
                                        </p>
                                        <p class="mb-0 ic-discription-customer">
                                            {{ __t('company') }}: {{ $warehouse_details->company_name }}
                                        </p>
                                        <p class="mb-0 ic-discription-customer">
                                            {{ __t('address') }}: {{ $warehouse_details->address_1 }} <br>
                                            {{ $warehouse_details->address_1 }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ic-profile-details-goback">
                                <a href="{{ route('admin.warehouses.show-pdf', ['warehouse' => $warehouse_details->id, 'stock_search' => $search ?? '']) }}"
                                    class="btn btn-info mr-2"><i class="fa fa-download"></i>
                                    {{ __('custom.download') }}</a>

                                <a href="{{ route('admin.warehouses.export-excel', ['warehouse' => $warehouse_details->id, 'stock_search' => $search ?? '']) }}"
                                    class="btn btn-success mr-2"><i class="fa fa-file-excel"></i>
                                    {{ __('custom.excel') }}</a>

                                <button class="btn btn-success mr-2" type="button" id="printWareHouseDetails"><i
                                        class="fa fa-print"></i> {{ __('custom.print') }}</button>
                                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-primary float-right"><i
                                        class="fa fa-backspace"></i> {{ __t('back') }}</a>
                            </div>
                        </div>
                    </div>


                    <div class="row d-print-block" style="display: none">
                        <div class="col-sm-12">
                            <div class="profile-head text-center">
                                <h5 class="text-muted">
                                    {{ $warehouse_details->name }}
                                </h5>
                                <h6>
                                    {{ __t('email') }}: {{ $warehouse_details->email }}
                                </h6>
                                <p class="mb-0 ic-discription-customer">
                                    {{ __t('phone') }}: {{ $warehouse_details->phone }}
                                </p>
                                <p class="mb-0 ic-discription-customer">
                                    {{ __t('company') }}: {{ $warehouse_details->company_name }}
                                </p>
                                <p class="mb-0 ic-discription-customer">
                                    {{ __t('address') }}: {{ $warehouse_details->address_1 }} <br>
                                    {{ $warehouse_details->address_1 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>{{ __('custom.product_stock') }}</h5>
                                <form action="" method="GET" class="d-flex" style="max-width: 350px;">
                                    <input type="text" name="stock_search" class="form-control form-control-sm"
                                        placeholder="{{ __t('search_placeholder_product_history') }}"
                                        value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-sm btn-primary ml-2">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    @if(!empty($search))
                                        <a href="{{ route('admin.warehouses.show', $warehouse_details->id) }}"
                                        class="btn btn-sm btn-danger ml-1">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    @endif
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="table-active">
                                            <th><strong>{{ __('custom.product') }}</strong></th>
                                            <th><strong>{{ __('custom.sku') }}</strong></th>
                                            <th><strong>{{ __('custom.category') }}</strong></th>
                                            <th>
                                                <stong>{{ __('custom.manufacturer') }}</stong>
                                            </th>
                                            <th><strong>{{ __('custom.quantity') }}</strong></th>
                                            <th><strong>{{ __('custom.price') }}</strong></th>
                                            <th><strong>{{ __('custom.total') }}</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $pageTotalQty = 0;
                                            $pageTotalPrice = 0;
                                        @endphp
                                        @forelse ($product_stocks as $product_stock)
                                            @php
                                                $pageTotalQty += $product_stock->quantity;
                                                $pageTotalPrice += (optional($product_stock->product)->price ?? 0) * $product_stock->quantity;
                                            @endphp
                                            <tr>
                                                <td class="text-left">
                                                    <a href="{{ route('admin.products.show', $product_stock->product_id) }}"
                                                        class="text-decoration-none"><img
                                                            class="img-40 p-1 border mb-2 mr-2"
                                                            src="{{ getStorageImage(\App\Models\Product::FILE_STORE_PATH, optional($product_stock->product)->thumb) }}"
                                                            alt="{{ optional($product_stock->product)->name }}"
                                                            width="80px">
                                                        {{ optional($product_stock->product)->name }}
                                                        @if (optional($product_stock->product)->is_variant == 1)
                                                            <small>({{ __("custom.variant") }}: {{ optional($product_stock->variation)->name ?? '' }}
                                                                )</small>
                                                        @endif
                                                    </a>
                                                </td>
                                                <td>{{$product_stock->variation->sku ?? optional($product_stock->product)->sku}}</td>
                                                <td>{{ optional(optional($product_stock->product)->category)->name }}</td>
                                                <td>{{ optional(optional($product_stock->product)->manufacturer)->name }}
                                                </td>
                                                <td>
                                                    @if ($product_stock->quantity <= 0)
                                                        <span
                                                            class="text-danger"><b>{{ $product_stock->quantity }}</b></span>
                                                        {{ optional(optional($product_stock->product)->weight_unit)->name }}
                                                    @else
                                                        <span
                                                            class="text-success"><b>{{ $product_stock->quantity }}</b></span>
                                                        {{ optional(optional($product_stock->product)->weight_unit)->name }}
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    {{ currencySymbol() . make2decimal(optional($product_stock->product)->price) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ currencySymbol() . make2decimal(optional($product_stock->product)->price * $product_stock->quantity) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">{{ __t('no_data_found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="4" class="text-right">{{ __('custom.total') }}</th>
                                            <th>{{ $pageTotalQty }}</th>
                                            <th></th>
                                            <th class="text-right">{{ currencySymbol() . make2decimal($pageTotalPrice) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center">
                                {!! $product_stocks->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
    @endpush

    @push('script')
    @endpush
