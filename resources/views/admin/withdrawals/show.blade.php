@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.show_withdraw') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.withdraw') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.show_withdraw') }}</li>
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

                            <div>
                                <div class="table-responsive ic-responsive-invoice">
                                    <table width="100%" cellpadding="0" cellspaceing="0">
                                        <tr>
                                            <td style="vertical-align: top;">
                                                <address class="ic-invoice-addess">
                                                    <strong>{{ __('custom.employee_info') }}:</strong><br>
                                                    @if($invoice->billing_info['name'])
                                                        <p class="mb-0">{{ $invoice->billing_info['name'] ?? '' }}</p>
                                                        <p class="mb-0">{{ $invoice->billing_info['email'] ?? '' }}</p>
                                                        <p class="mb-0">{{ $invoice->billing_info['phone_number'] ?? '' }}</p>
                                                        <p class="mb-0">
                                                            {{ $invoice->billing_info['address_line_1'] ? $invoice->billing_info['address_line_1'].', ' : '' }}
                                                            {{ $invoice->billing_info['address_line_2'] ? $invoice->billing_info['address_line_2'] : '' }}</p>
                                                        <p class="mb-0">{{ $invoice->billing_info['zip'] ? $invoice->billing_info['zip'].', ' : '' }}{{
                                                    $invoice->billing_info['city'] ? $invoice->billing_info['city'].', ' : '' }}{{
                                                    $invoice->billing_info['state'] ? $invoice->billing_info['state'].', ' : '' }}{{
                                                    $invoice->billing_info['country'] ?? '' }}</p>
                                                    @else
                                                        @if($invoice->customer_id != null && $invoice->customer_id != "")
                                                            <p class="mb-0">{{ @$invoice->customerInfo['full_name'] ?? '' }}</p>
                                                            <p class="mb-0">{{ @$invoice->customerInfo['email'] ?? '' }}</p>
                                                            <p class="mb-0">{{ @$invoice->customerInfo['phone'] ?? '' }}</p>
                                                            <p class="mb-0">
                                                                {{ $invoice->customerInfo['address_line_1'] ? $invoice->customerInfo['address_line_1'].', ' : '' }}
                                                                {{ $invoice->customerInfo['address_line_2'] ? $invoice->customerInfo['address_line_2'] : '' }}
                                                            </p>
                                                            <p class="mb-0">
                                                                {{ $invoice->customerInfo['zipcode'] ? $invoice->customerInfo['zipcode'].', ' : '' }}
                                                                {{ optional($invoice->customerInfo->systemCity)->name ? optional($invoice->customerInfo->systemCity)->name .', ' : '' }}
                                                                {{ optional($invoice->customerInfo->systemState)->name ? optional($invoice->customerInfo->systemState)->name .', ' : '' }}
                                                                {{ optional($invoice->customerInfo->systemCountry)->name ?optional($invoice->customerInfo->systemCountry)->name .', ' : '' }}
                                                            </p>
                                                        @else
                                                            <p class="mb-0">{{ __('custom.walk_in_customer') }}</p>
                                                        @endif
                                                    @endif
                                                </address>
                                            </td>

                                            <td style="vertical-align: top;">
                                                <address class="ic-invoice-addess ic-right-content">
                                                    <strong>{{ __('custom.withdraw') }}:</strong>
                                                    <p class="mb-0">{{ __('custom.withdraw_id') }} # <span id="invoice_number">{{ make8digits($invoice->id) }}</span></p>
                                                    <p class="mb-0">{{ __('custom.date') }}: {{ formatDynamicDateTime($invoice->date)
                                                    }}
                                                    </p>

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
                                                    <td><strong>{{ __('custom.name') }}</strong></td>
                                                    <td><strong>{{ __('custom.quantity') }}</strong></td>
                                                    <td><strong>{{ __('custom.batch') }}</strong></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($invoice->items)
                                                    @foreach ($invoice->items as $item)
                                                        <tr>
                                                            <td width="50%">
                                                                {{ $item->product_name ?? '' }}
                                                                @if($item->product->is_variant && isset($item->productStock))
                                                                    ({{ optional($item->productStock->attribute)->name ?? '' }}: {{ optional($item->productStock->attributeItem)->name ?? '' }})
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->quantity ?? '' }}</td>
                                                            <td>{{ $item->productStock->batch ?? '' }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>



                                </div>
                            </div>
                        </div>


                    </div> <!-- end row -->
                    <div class="show-on-print">
                        <p>{{ config('invoice_footer') }}</p>
                    </div>
                </div>


                <div class="card-body">
                    <div class="d-print-none row">
                        <div class="col-lg-6 col-sm-6">
                            <div class="d-flex d-sm-block justify-content-between justify-content-sm-start">
                                <a href="{{ route('admin.withdrawals.index') }}"
                                   class="btn btn-dark waves-effect waves-light"><i
                                        class="fa fa-arrow-left"></i>
                                    <span>{{ __('custom.back') }}</span></a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6">

                            <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-print"></i> {{ __('custom.print') }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item" href="#" onclick="window.print()">A4 Print</a>
                                        <a class="dropdown-item" href="{{ route('admin.withdrawals.print', $invoice->id) }}">POS Print</a>
                                    </div>
                                </div>
                                <a href="{{ route('admin.withdrawals.download', $invoice->id) }}" class="btn btn-primary">
                                    <i class="fa fa-download"></i> <span>{{ __('custom.download') }}</span>
                                </a>
                                {{--                            <button type="button" id="generatePDF" class="btn btn-primary">--}}
                                {{--                                <i class="fa fa-download"></i> <span>{{ __('custom.download') }}</span>--}}
                                {{--                            </button>--}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <style>
        .show-on-print {
            display: none;
        }
        .print-class .show-on-print{
            display: block !important;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        @media  print {
            .show-on-print {
                display: block !important;
                position: fixed;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
            }

        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.apidelv.com/libs/awesome-functions/awesome-functions.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" ></script>
@endpush
