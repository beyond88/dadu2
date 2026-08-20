@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.customer') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('custom.customer_details') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="emp-profile ic-employee-warper-container">
                            <div class="ic-customer-details-warper">
                                <div class="ic-customer-profile-basic-info">
                                    <div class="profile-img">
                                        <img class="img-thumbnail"
                                            src="{{ getStorageImage(\App\Models\Customer::FILE_STORE_PATH, $customer->avatar) }}"
                                            alt="{{ $customer->full_name }}" />
                                    </div>
                                    <div class="ic-customer-basic-info">
                                        <h5 class="text-muted">{{ __t('basic_info') }}</h5>
                                        <div class="profile-head">
                                            <h5>
                                                {{ $customer->full_name }}
                                            </h5>
                                            <h6>
                                                {{ $customer->email }}
                                            </h6>
                                            @if($customer->code)
                                            <p class="mb-0 ic-discription-customer">
                                                {{ __('custom.customer_code') }}: {{ $customer->code }}
                                            </p>
                                            @endif
                                            <p class="mb-0 ic-discription-customer">
                                                {{ $customer->phone }}
                                            </p>
                                            <p class="mb-0 ic-discription-customer">
                                                {{ __t('company') }}: {{ $customer->company }}
                                            </p>
                                            <p class="mb-0 ic-discription-customer">
                                                {{ __t('designation') }}: {{ $customer->designation }}
                                            </p>
                                            <p class="mb-0 ic-discription-customer">
                                                {{ __t('opening_balance') }}: {{ currencySymbol() . make2decimal($customer->opening_balance) }}
                                            </p>
                                            <p class="mb-0 ic-discription-customer text-danger">
                                                <strong>{{ __t('total_due') }}: {{ currencySymbol() . make2decimal($totalDue) }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ic-profile-details-goback">
                                    <a href="{{ route('admin.customers.index') }}" class="btn btn-primary float-right"><i
                                            class="fa fa-backspace"></i> {{ __t('back') }}</a>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white text-center p-3">
                                        <h6>{{ __t('opening_balance') }}</h6>
                                        <h4>{{ currencySymbol() . make2decimal($customer->opening_balance ?? 0) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white text-center p-3">
                                        <h6>{{ __t('total_invoiced') }}</h6>
                                        <h4>{{ currencySymbol() . make2decimal($totalInvoiced) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white text-center p-3">
                                        <h6>{{ __t('total_paid') }}</h6>
                                        <h4>{{ currencySymbol() . make2decimal($totalPaid) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white text-center p-3">
                                        <h6>{{ __t('total_due') }}</h6>
                                        <h4>{{ currencySymbol() . make2decimal($totalDue) }}</h4>
                                    </div>
                                </div>
                            </div>

                            {{-- Credit minus due: matches the Balance column on the customer list. --}}
                            @php($netBalance = ($customer->opening_balance ?? 0) - $totalDue)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card {{ $netBalance < 0 ? 'bg-danger' : 'bg-success' }} text-white text-center p-3">
                                        <h6>{{ __t('balance') }}</h6>
                                        <h4>{{ currencySymbol() . make2decimal($netBalance) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="ic-customer-details-info-warper">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="customer-billing-info">
                                            <h5 class="text-muted">{{ __t('billing_info') }}</h5>
                                            <div class="profile-head">
                                                <h5>
                                                    {{ $customer->b_first_name . ' ' . $customer->b_last_name }}
                                                </h5>
                                                <h6>
                                                    {{ $customer->b_email }}
                                                </h6>
                                                <p class="mb-0 ic-discription-customer">
                                                    {{ $customer->b_phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="ic-customer-address">
                                            <div class="profile-head">
                                                <h5 class="text-muted">{{ __t('address') }}</h5>
                                                <address class="ic-address-info-customer">
                                                    {!! $customer->address_line_1 ? $customer->address_line_1 . ', <br>' : '' !!}
                                                    {!! $customer->address_line_2 ? $customer->address_line_2 . ', <br>' : '' !!}
                                                    {!! $customer->city ? optional($customer->systemCity)->name . ', ' : '' !!}
                                                    {!! $customer->state ? optional($customer->systemState)->name . ', ' : '' !!}
                                                    {!! $customer->country
                                                        ? optional($customer->systemCountry)->name .
                                                            ',
                                                                                                        '
                                                        : '' !!}
                                                    {!! $customer->zipcode !!},
                                                </address>
                                                <address class="ic-address-info-customer">
                                                    {{ __t('short_address') }}: {{ $customer->short_address }}
                                                </address>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="ic-customer-billing-address">
                                            <div class="profile-head">
                                                <h5 class="text-muted">{{ __t('billing_address') }}</h5>
                                                <address class="ic-address-info-customer">
                                                    {!! $customer->b_address_line_1
                                                        ? $customer->b_address_line_1 .
                                                            ',
                                                                                                        <br>'
                                                        : '' !!}
                                                    {!! $customer->b_address_line_2
                                                        ? $customer->b_address_line_2 .
                                                            ',
                                                                                                        <br>'
                                                        : '' !!}
                                                    {!! optional($customer->b_city_data)->name ? optional($customer->b_city_data)->name . ',' : '' !!}
                                                    {!! optional($customer->b_state_data)->name ? optional($customer->b_state_data)->name . ',' : '' !!}
                                                    {!! optional($customer->b_country_data)->name ? optional($customer->b_country_data)->name . ',' : '' !!}
                                                    {!! $customer->b_zipcode ? $customer->b_zipcode . ',' : '' !!}
                                                </address>
                                                <address class="ic-address-info-customer">
                                                    {{ __t('short_address') }}: {{ $customer->b_short_address }}
                                                </address>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="ic-customer-status">
                                            <h5 class="text-muted">{{ __t('status') }}</h5>
                                            <h6 title="{{ __t('status') }}">
                                                @if ($customer->status == \App\Models\Customer::STATUS_ACTIVE)
                                                    <span class="badge badge-success"><i class="fa fa-check-circle"></i>
                                                        {{ ucfirst($customer->status) }}</span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fa fa-times-circle"></i>
                                                        {{ ucfirst($customer->status) }}</span>
                                                @endif
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <section id="tabs" class="project-tab mt-4">
                                <div class="ic-employee-warper-container">
                                    <div class="row">
                                        <div class="col-md-12 p-0">
                                            <nav class="ic-customer-details-tab">
                                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                    <a class="nav-item nav-link {{ request('tab', 'invoice') == 'invoice' ? 'active' : '' }}"
                                                       id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home"
                                                       aria-selected="{{ request('tab', 'invoice') == 'invoice' ? 'true' : 'false' }}">{{ __t('invoice_history') }}</a>
                                                    <a class="nav-item nav-link {{ request('tab') == 'topay' ? 'active' : '' }}"
                                                       id="nav-topay-tab" data-toggle="tab" href="#nav-topay" role="tab" aria-controls="nav-topay"
                                                       aria-selected="{{ request('tab') == 'topay' ? 'true' : 'false' }}">{{ __t('to_pay') }}</a>
                                                    <a class="nav-item nav-link {{ request('tab') == 'product' ? 'active' : '' }}"
                                                       id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile"
                                                       aria-selected="{{ request('tab') == 'product' ? 'true' : 'false' }}">{{ __t('product_history') }}</a>
                                                </div>
                                            </nav>
                                            <div class="tab-content" id="nav-tabContent">
                                                {{-- Invoice History Tab --}}
                                                <div class="tab-pane fade {{ request('tab', 'invoice') == 'invoice' ? 'show active' : '' }}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                                                <h6 class="text-muted mb-0">{{ __t('invoice_history') }}</h6>
                                                                <div class="d-flex">
                                                                    <form action="" method="GET" class="d-flex" style="max-width: 350px;">
                                                                        <input type="hidden" name="tab" value="invoice">
                                                                        <input type="hidden" name="product_search" value="{{ $productSearch ?? '' }}">
                                                                        <input type="hidden" name="topay_search" value="{{ $toPaySearch ?? '' }}">
                                                                        <input type="hidden" name="product_page" value="{{ request('product_page', 1) }}">
                                                                        <input type="hidden" name="topay_page" value="{{ request('topay_page', 1) }}">
                                                                        <input type="text" name="invoice_search" class="form-control form-control-sm"
                                                                            placeholder="{{ __t('search_placeholder_invoice_history') }}"
                                                                            value="{{ $invoiceSearch ?? '' }}">
                                                                        <button type="submit" class="btn btn-sm btn-primary ml-2">
                                                                            <i class="fa fa-search"></i>
                                                                        </button>
                                                                        @if(!empty($invoiceSearch))
                                                                            <a href="{{ route('admin.customers.show', $customer->id) }}?tab=invoice&product_search={{ $productSearch ?? '' }}&topay_search={{ $toPaySearch ?? '' }}&product_page={{ request('product_page', 1) }}&topay_page={{ request('topay_page', 1) }}"
                                                                            class="btn btn-sm btn-danger ml-1">
                                                                                <i class="fa fa-times"></i>
                                                                            </a>
                                                                        @endif
                                                                    </form>
                                                                    <div class="btn-group ml-2">
                                                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            {{ __t('export') }}
                                                                        </button>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <a class="dropdown-item" href="{{ route('admin.customers.export-invoices-history', ['customer' => $customer->id, 'type' => 'excel', 'invoice_search' => $invoiceSearch ?? '']) }}">{{ __t('excel') }}</a>
                                                                            <a class="dropdown-item" href="{{ route('admin.customers.export-invoices-history', ['customer' => $customer->id, 'type' => 'pdf', 'invoice_search' => $invoiceSearch ?? '']) }}">{{ __t('pdf') }}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-striped table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                           <th>{{ __t('invoice_id') }}</th>
                                                                            <th>{{ __t('date') }}</th>
                                                                            <th>{{ __t('total') }}</th>
                                                                            <th>{{ __t('total_paid') }}</th>
                                                                            <th>{{ __t('payment_type') }}</th>
                                                                            <th>{{ __t('status') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse ($invoices as $invoice)
                                                                            <tr>
                                                                                <td><a target="_blank" class="btn btn-link"
                                                                                        href="{{ route('admin.invoices.show', $invoice->id) }}">{{ make8digits($invoice->id) }}</a>
                                                                                </td>
                                                                                <td>
                                                                                    {{ date('F m, Y', strtotime($invoice->date)) }}
                                                                                    <br>
                                                                                    <small>{{ date('H:i:s A', strtotime($invoice->date)) }}</small>
                                                                                </td>
                                                                                <td>{{ currencySymbol() . ' ' . $invoice->total }}
                                                                                </td>
                                                                                <td>{{ currencySymbol() . ' ' . $invoice->total_paid }}
                                                                                </td>
                                                                                <td>{{ ucfirst($invoice->payment_type) }}
                                                                                </td>
                                                                                <td>{!! invoiceStatusBadge($invoice->status) !!}
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted">{{ __t('no_data_found') }}</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="d-flex justify-content-center">
                                                                {!! $invoices->links() !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- To Pay Tab --}}
                                                <div class="tab-pane fade {{ request('tab') == 'topay' ? 'show active' : '' }}" id="nav-topay" role="tabpanel" aria-labelledby="nav-topay-tab">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                                                <h6 class="text-muted mb-0">{{ __t('to_pay') }}</h6>
                                                                <div class="d-flex">
                                                                    <form action="" method="GET" class="d-flex" style="max-width: 350px;">
                                                                        <input type="hidden" name="tab" value="topay">
                                                                        <input type="hidden" name="product_search" value="{{ $productSearch ?? '' }}">
                                                                        <input type="hidden" name="invoice_search" value="{{ $invoiceSearch ?? '' }}">
                                                                        <input type="hidden" name="product_page" value="{{ request('product_page', 1) }}">
                                                                        <input type="hidden" name="invoice_page" value="{{ request('invoice_page', 1) }}">
                                                                        <input type="text" name="topay_search" class="form-control form-control-sm"
                                                                            placeholder="{{ __t('search_placeholder_invoice_history') }}"
                                                                            value="{{ $toPaySearch ?? '' }}">
                                                                        <button type="submit" class="btn btn-sm btn-primary ml-2">
                                                                            <i class="fa fa-search"></i>
                                                                        </button>
                                                                        @if(!empty($toPaySearch))
                                                                            <a href="{{ route('admin.customers.show', $customer->id) }}?tab=topay&product_search={{ $productSearch ?? '' }}&invoice_search={{ $invoiceSearch ?? '' }}&product_page={{ request('product_page', 1) }}&invoice_page={{ request('invoice_page', 1) }}"
                                                                            class="btn btn-sm btn-danger ml-1">
                                                                                <i class="fa fa-times"></i>
                                                                            </a>
                                                                        @endif
                                                                    </form>
                                                                    <div class="btn-group ml-2">
                                                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            {{ __t('export') }}
                                                                        </button>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <a class="dropdown-item" href="{{ route('admin.customers.export-topay-history', ['customer' => $customer->id, 'type' => 'excel', 'topay_search' => $toPaySearch ?? '']) }}">{{ __t('excel') }}</a>
                                                                            <a class="dropdown-item" href="{{ route('admin.customers.export-topay-history', ['customer' => $customer->id, 'type' => 'pdf', 'topay_search' => $toPaySearch ?? '']) }}">{{ __t('pdf') }}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-striped table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __t('invoice_id') }}</th>
                                                                            <th>{{ __t('date') }}</th>
                                                                            <th>{{ __t('total') }}</th>
                                                                            <th>{{ __t('total_paid') }}</th>
                                                                            <th>{{ __t('payment_type') }}</th>
                                                                            <th>{{ __t('status') }}</th>
                                                                            <th>{{ __t('action') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse ($not_paid_invoices as $invoice)
                                                                            <tr>
                                                                            <td><a target="_blank" class="btn btn-link"
                                                                                    href="{{ route('admin.invoices.show', $invoice->id) }}">{{ make8digits($invoice->id) }}</a>
                                                                            </td>
                                                                            <td>
                                                                                {{ date('F m, Y', strtotime($invoice->date)) }}
                                                                                <br>
                                                                                <small>{{ date('H:i:s A', strtotime($invoice->date)) }}</small>
                                                                            </td>
                                                                            <td>{{ currencySymbol() . ' ' . $invoice->total }}
                                                                            </td>
                                                                            <td>{{ currencySymbol() . ' ' . $invoice->total_paid }}
                                                                            </td>
                                                                            <td>{{ ucfirst($invoice->payment_type) }}</td>
                                                                            <td>{!! invoiceStatusBadge($invoice->status) !!}
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{ route('admin.invoices.makePayment', $invoice->id) }}"
                                                                                    title="{{ __t('make_payment') }}"
                                                                                    class="btn btn-sm btn-primary"><i
                                                                                        class="fa fa-money-bill"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="7" class="text-center text-muted">{{ __t('no_data_found') }}</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="d-flex justify-content-center">
                                                                {!! $not_paid_invoices->links() !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Product History Tab --}}
                                                <div class="tab-pane fade {{ request('tab') == 'product' ? 'show active' : '' }}" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                                                <h6 class="text-muted mb-0">{{ __t('product_history') }}</h6>
                                                                <div class="d-flex">
                                                                    <form action="" method="GET" class="d-flex" style="max-width: 350px;">
                                                                        <input type="hidden" name="tab" value="product">
                                                                        <input type="hidden" name="invoice_search" value="{{ $invoiceSearch ?? '' }}">
                                                                        <input type="hidden" name="topay_search" value="{{ $toPaySearch ?? '' }}">
                                                                        <input type="hidden" name="invoice_page" value="{{ request('invoice_page', 1) }}">
                                                                        <input type="hidden" name="topay_page" value="{{ request('topay_page', 1) }}">
                                                                        <input type="text" name="product_search" class="form-control form-control-sm"
                                                                            placeholder="{{ __t('search_placeholder_product_history') }}"
                                                                            value="{{ $productSearch ?? '' }}">
                                                                        <button type="submit" class="btn btn-sm btn-primary ml-2">
                                                                            <i class="fa fa-search"></i>
                                                                        </button>
                                                                        @if(!empty($productSearch))
                                                                            <a href="{{ route('admin.customers.show', $customer->id) }}?tab=product&invoice_search={{ $invoiceSearch ?? '' }}&topay_search={{ $toPaySearch ?? '' }}&invoice_page={{ request('invoice_page', 1) }}&topay_page={{ request('topay_page', 1) }}"
                                                                            class="btn btn-sm btn-danger ml-1">
                                                                                <i class="fa fa-times"></i>
                                                                            </a>
                                                                        @endif
                                                                    </form>
                                                                    <div class="btn-group ml-2">
                                                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            {{ __t('export') }}
                                                                        </button>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <a class="dropdown-item" href="{{ route('admin.customers.export-products-history', ['customer' => $customer->id, 'type' => 'excel', 'product_search' => $productSearch ?? '']) }}">{{ __t('excel') }}</a>
                                                                            <a class="dropdown-item" href="{{ route('admin.customers.export-products-history', ['customer' => $customer->id, 'type' => 'pdf', 'product_search' => $productSearch ?? '']) }}">{{ __t('pdf') }}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __t('product_id') }}</th>
                                                                            <th>{{ __t('product_name') }}</th>
                                                                            <th>{{ __t('sku') }}</th>
                                                                            <th>{{ __t('price') }}</th>
                                                                            <th>{{ __t('quantity') }}</th>
                                                                            <th>{{ __t('sub_total') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse ($products as $product)
                                                                        <tr>
                                                                            <td>
                                                                                <a target="_blank" class="btn btn-link" href="{{ route('admin.products.edit', $product['product_id']) }}">
                                                                                    {{ make8digits($product['product_id']) }}
                                                                                </a>
                                                                            </td>
                                                                            <td>{{ $product['product_name'] }}</td>
                                                                            <td>{{ $product['sku'] }}</td>
                                                                            <td>{{ currencySymbol() . ' ' . $product['price'] }}</td>
                                                                            <td>{{ $product['quantity'] }}</td>
                                                                            <td>{{ currencySymbol() . ' ' . ($product['price'] * $product['quantity']) }}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">{{ __t('no_data_found') }}</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>

                                                                </table>
                                                            </div>
                                                            <div class="d-flex justify-content-center">
                                                                {!! $products->links() !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .project-tab {
            padding: 5%;
            margin-top: -8%;
        }
        .project-tab #tabs {
            background: #007bff;
            color: #eee;
        }
        .project-tab #tabs h6.section-title {
            color: #eee;
        }
        .project-tab #tabs .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #0062cc;
            background-color: transparent;
            border-color: transparent transparent #f3f3f3;
            border-bottom: 3px solid !important;
            font-size: 16px;
            font-weight: bold;
        }
        .project-tab .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            color: #0062cc;
            font-size: 16px;
            font-weight: 600;
        }
        .project-tab .nav-link:hover {
            border: none;
        }
        .project-tab thead {
            background: #f3f3f3;
            color: #333;
        }
        .project-tab a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
        }
    </style>
@endpush

@push('script')
    @include('includes.scripts.country_state_city_auto_load', ['address_data' => $customer])
    @include('includes.scripts.country_state_city_auto_load_2', ['address_data' => $customer])
@endpush
