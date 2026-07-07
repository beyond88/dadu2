@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">{{ __('custom.show_draft_invoice') }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.draft_invoice') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.show_draft_invoice') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body" id="print-invoice">
                <div class="row">
                    <div class="col-12">
                        <div class="invoice-title">
                            <h3 class="mt-0">
                                @if(config('is_logo_show_in_invoice') == 'yes')
                                <img src="{{ site_logo() }}" class="ic-logo-height" alt="logo">
                                @endif
                            </h3>
                        </div>
                        <hr>
                        <div>
                            <div class="table-responsive ic-responsive-invoice">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <address class="ic-invoice-addess">
                                                <strong>{{ __('custom.billed_to') }}:</strong><br>
                                                @if(isset($invoice->billing_info['name']) && $invoice->billing_info['name'])
                                                <p class="mb-0">{{ $invoice->billing_info['name'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->billing_info['email'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->billing_info['phone_number'] ?? '' }}</p>
                                                <p class="mb-0">
                                                    {{ isset($invoice->billing_info['address_line_1']) && $invoice->billing_info['address_line_1'] ? $invoice->billing_info['address_line_1'].', ' : '' }}
                                                    {{ $invoice->billing_info['address_line_2'] ?? '' }}</p>
                                                <p class="mb-0">
                                                    {{ isset($invoice->billing_info['zip']) && $invoice->billing_info['zip'] ? $invoice->billing_info['zip'].', ' : '' }}
                                                    {{ isset($invoice->billing_info['city']) && $invoice->billing_info['city'] ? $invoice->billing_info['city'].', ' : '' }}
                                                    {{ isset($invoice->billing_info['state']) && $invoice->billing_info['state'] ? $invoice->billing_info['state'].', ' : '' }}
                                                    {{ $invoice->billing_info['country'] ?? '' }}
                                                </p>
                                                @else
                                                    <p class="mb-0">{{ __('custom.walk_in_customer') }}</p>
                                                @endif
                                            </address>
                                        </td>
                                        <td>
                                            <address class="ic-invoice-addess">
                                                <strong>{{ __('custom.shipped_to') }}:</strong>
                                                @if(isset($invoice->shipping_info['name']) && $invoice->shipping_info['name'])
                                                <p class="mb-0">{{ $invoice->shipping_info['name'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->shipping_info['email'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->shipping_info['phone_number'] ?? '' }}</p>
                                                <p class="mb-0">
                                                    {{ isset($invoice->shipping_info['address_line_1']) && $invoice->shipping_info['address_line_1'] ? $invoice->shipping_info['address_line_1'].', ' : '' }}
                                                    {{ $invoice->shipping_info['address_line_2'] ?? '' }}
                                                </p>
                                                <p class="mb-0">
                                                    {{ isset($invoice->shipping_info['zip']) && $invoice->shipping_info['zip'] ? $invoice->shipping_info['zip'].', ' : '' }}
                                                    {{ isset($invoice->shipping_info['city']) && $invoice->shipping_info['city'] ? $invoice->shipping_info['city'].', ' : '' }}
                                                    {{ isset($invoice->shipping_info['state']) && $invoice->shipping_info['state'] ? $invoice->shipping_info['state'].', ' : '' }}
                                                    {{ $invoice->shipping_info['country'] ?? '' }}
                                                </p>
                                                @else
                                                    <p class="mb-0">{{ __('custom.walk_in_customer') }}</p>
                                                @endif
                                            </address>
                                        </td>
                                        <td>
                                            <address class="ic-invoice-addess ic-right-content">
                                                <strong>{{ __('custom.draft_invoice') }}:</strong>
                                                <p class="mb-0">{{ __('custom.draft_invoice_id') }} # <span id="invoice_number">{{ make8digits($invoice->id) }}</span></p>
                                                <p class="mb-0">{{ __('custom.date') }}: {{ custom_date($invoice->date) }}</p>
                                                <p class="mb-0">{{ __('custom.total') }}: {{ currencySymbol().make2decimal($invoice->total) }}</p>
                                                <p class="mb-0">{{ __('custom.status') }}: <span class="badge badge-warning">{{ __('custom.draft') }}</span></p>
                                            </address>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div>
                            <div class="p-2">
                                <h3 class="font-16"><strong>{{ __('custom.summary') }}</strong></h3>
                            </div>
                            <div class="">
                                <div class="table-responsive">
                                    <table width="100%" class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th><strong>#</strong></th>
                                                <th><strong>Qty</strong></th>
                                                {{-- <th><strong>Product No</strong></th> --}}
                                                <th><strong>Product Name</strong></th>
                                                <th><strong>Brand</strong></th>
                                                <th><strong>Part No</strong></th>
                                                <th><strong>Model</strong></th>
                                                <th><strong>Unit Price</strong></th>
                                                <th><strong>D. %</strong></th>
                                                <th><strong>Total Price</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($invoice->items)
                                            @foreach ($invoice->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->quantity_display }} {{ $item->unit_label }}</td>
                                                {{-- <td>{{ $item->productStock?->variation  ? $item->productStock->variation?->sku : $item->product?->sku }}</td> --}}
                                                <td width="20%">
                                                    {{ $item->product_name ?? '' }}
                                                    @if($item->product?->is_variant != null && $item->product->is_variant == 1 && isset($item->productStock))
                                                        ({{ __("custom.variant") }}: {{optional(optional($item->productStock)->variation)->name ?? ''}})
                                                    @endif
                                                </td>
                                                <td>{{ $item->product?->brand?->name ?? '' }}</td>
                                                <td>{{ $item->product?->parts_no ?? '' }}</td>
                                                <td>{{ $item->product?->model ?? '' }}</td>
                                                <td>{{ currencySymbol().make2decimal($item->price) ?? '' }}</td>
                                                <td>{{ $item->discount ?? 0 }} @if($item->discount_type == \App\Models\Invoice::DISCOUNT_PERCENT)%@endif</td>
                                                <td>{{ currencySymbol().make2decimal($item->sub_total) ?? '' }}</td>
                                            </tr>
                                            @endforeach
                                            @endif

                                            <tr>
                                                <td class="thick-line text-right" colspan="8">
                                                    <strong>{{ __('custom.discount') }}</strong>
                                                </td>
                                                <td class="thick-line">{{ currencySymbol().make2decimal($invoice->discount_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-line text-right" colspan="8">
                                                    <strong>{{ __('custom.tax') }}</strong>
                                                </td>
                                                <td class="no-line">{{ currencySymbol().make2decimal($invoice->tax_amount) }}</td>
                                            </tr>
                                            @if($invoice->additional_charge_amount > 0)
                                            <tr>
                                                <td class="no-line text-right" colspan="8">
                                                    <strong>{{ $invoice->additional_charge_name ?? __('custom.additional_charge') }}</strong>
                                                </td>
                                                <td class="no-line">{{ currencySymbol().make2decimal($invoice->additional_charge_amount) }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="no-line text-right" colspan="8">
                                                    <strong>{{ __('custom.total') }}</strong>
                                                </td>
                                                <td class="no-line">
                                                    <p class="mb-0"><b>{{ currencySymbol().make2decimal($invoice->total) }}</b></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <label for="">{{ __('custom.payment_info') }}</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <th>{{ __('custom.date') }}</th>
                                            <th>{{ __('custom.payment_type') }}</th>
                                            <th>{{ __('custom.amount') }}</th>
                                            <th>{{ __('custom.bank_name') }}</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ custom_date($invoice->date) }}</td>
                                                <td>{{ ucfirst($invoice->payment_type) }}</td>
                                                <td>{{ currencySymbol().make2decimal($invoice->total_paid) }}</td>
                                                <td>{{ in_array(strtolower($invoice->payment_type), ['bank', 'online']) && is_array($invoice->bank_info) && isset($invoice->bank_info['bank_name']) ? (\App\Models\Account::find($invoice->bank_info['bank_name'])?->name ?? '-') : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p>
                                    {{ __('custom.note') }}: {{ $invoice->notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="d-print-none row">
                    <div class="col-lg-6 col-sm-6">
                        <div class="d-flex d-sm-block justify-content-between justify-content-sm-start">
                            <a href="{{ route('admin.draft-invoices.index') }}"
                               class="btn btn-dark waves-effect waves-light">
                                <i class="fa fa-arrow-left"></i>
                                <span>{{ __('custom.back') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <div class="btn-group float-right" role="group">
                            <a href="{{ route('admin.draft-invoices.download', $invoice->id) }}"
                               class="btn btn-primary mr-2">
                                <i class="fa fa-download"></i> <span>{{ __('custom.download') }}</span>
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-info">
                                <i class="fa fa-print"></i> <span>{{ __('custom.print') }}</span>
                            </button>
                        </div>
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
