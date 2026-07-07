@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">{{ __('custom.make_payment') }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.invoice') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.make_payment') }}</li>
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
                                {{ config('site_title') ?? config('app.name') }}
                            </h3>
                        </div>
                        <hr>
                        <div>
                            <div class="table-responsive">
                                <table cellpadding="0" cellspaceing="0" border="0" width="100%">
                                    <tr>
                                        <td>
                                            <address class="ic-invoice-addess">
                                                <strong>{{ __('custom.billed_to') }}:</strong><br>
                                                <p class="mb-0">{{ $invoice->billing_info['name'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->billing_info['email'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->billing_info['phone_number'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->billing_info['address_line_1'] ?? '' }}, {{
                                                    $invoice->billing_info['address_line_2'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->billing_info['zip'] ?? '' }},{{
                                                    $invoice->billing_info['city'] ?? '' }},{{
                                                    $invoice->billing_info['state'] ?? '' }},{{
                                                    $invoice->billing_info['country'] ?? '' }}</p>
                                            </address>
                                        </td>
                                        <td>
                                            <address class="ic-invoice-addess">
                                                <strong>{{ __('custom.shipped_to') }}:</strong>
                                                <p class="mb-0">{{ $invoice->shipping_info['name'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->shipping_info['email'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->shipping_info['phone_number'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->shipping_info['address_line_1'] ?? '' }}
                                                    ,{{
                                                    $invoice->shipping_info['address_line_2'] ?? '' }}</p>
                                                <p class="mb-0">{{ $invoice->shipping_info['zip'] ?? '' }}, {{
                                                    $invoice->shipping_info['city'] ?? '' }}, {{
                                                    $invoice->shipping_info['state'] ?? '' }}, {{
                                                    $invoice->shipping_info['country'] ?? '' }}</p>
                                            </address>
                                        </td>
                                        <td>
                                            <address class="ic-invoice-addess ic-right-content">
                                                <strong>{{ __('custom.invoice') }}:</strong>
                                                <p class="mb-0">{{ __('custom.invoice_id') }} # {{
                                                    make8digits($invoice->id)
                                                    }}</p>
                                                <p class="mb-0">{{ __('custom.date') }}: {{ custom_date($invoice->date)
                                                    }}
                                                </p>
                                                <p class="mb-0">{{ __('custom.total') }}: {{
                                                    currencySymbol().make2decimal($invoice->total) }}</p>
                                                <p class="mb-0">{{ __('custom.status') }}: {{
                                                    \App\Models\Invoice::INVOICE_ALL_STATUS[$invoice->status] }}</p>
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
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td><strong>{{ __('custom.sku') }}</strong></td>
                                                <td><strong>{{ __('custom.name') }}</strong></td>
                                                <td><strong>{{ __('custom.quantity') }}</strong></td>
                                                <td>
                                                    <strong>{{ __('custom.price') }}</strong>
                                                </td>
                                                <td><strong>{{ __('custom.discount') }}</strong>
                                                <td><strong>{{ __('custom.sub_total') }}</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($invoice->items)
                                            @foreach ($invoice->items as $item)
                                            <tr>
                                                <td>{{ $item->sku ?? '' }}</td>
                                                <td width="40%">{{ $item->product_name ?? '' }}
                                                    @if($item->product->is_variant == 1 && isset($item->productStock))
                                                        ({{optional(optional($item->productStock)->attribute)->name ?? ''}} : {{optional(optional($item->productStock)->attributeItem)->name ?? ''}})
                                                    @endif
                                                </td>
                                                <td>{{ $item->quantity_display }} {{ $item->unit_label }}</td>
                                                <td>{{ currencySymbol().make2decimal($item->price) ?? '' }}</td>
                                                <td>{{ $item->discount ?? 0 }} @if($item->discount_type ==
                                                    \App\Models\Invoice::DISCOUNT_PERCENT)
                                                    %
                                                    @endif</td>
                                                <td>{{ currencySymbol().make2decimal($item->sub_total) ?? '' }}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            <tr>
                                                <td class="thick-line"></td>
                                                <td class="thick-line"></td>
                                                <td class="thick-line"></td>
                                                <td class="thick-line"></td>
                                                <td class="thick-line">
                                                    <strong>{{ __('custom.discount') }}</strong>
                                                </td>
                                                <td class="thick-line">{{
                                                    currencySymbol().make2decimal($invoice->discount_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line">
                                                    <strong>{{ __('custom.tax') }}</strong>
                                                </td>
                                                <td class="no-line">{{
                                                    currencySymbol().make2decimal($invoice->tax_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line">
                                                    <strong>{{ __('custom.total') }}</strong>
                                                </td>
                                                <td class="no-line">
                                                    <p class="mb-0"><b>{{ currencySymbol().make2decimal($invoice->total)
                                                            }}</b></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line">
                                                    <strong>{{ __('custom.total_paid') }}</strong>
                                                </td>
                                                <td class="no-line">
                                                    <p class="mb-0"><b>{{
                                                            currencySymbol().make2decimal($invoice->total_paid) }}</b>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line"></td>
                                                <td class="no-line">
                                                    <strong>{{ __('custom.total_due') }}</strong>
                                                </td>
                                                <td class="no-line">
                                                    <p class="mb-0"><b>{{
                                                            currencySymbol().make2decimal(calculateDue($invoice->total,
                                                            $invoice->total_paid)) }}</b></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-print-none row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="d-flex d-sm-block justify-content-between justify-content-sm-start">
                                            <a href="{{ route('admin.invoices.index') }}"
                                                class="btn btn-dark waves-effect waves-light"><i
                                                    class="fa fa-arrow-left"></i>
                                                <span>{{ __('custom.back') }}</span></a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="ic-print-header">
                                            <a href="#" data-div-name="print-invoice"
                                                class="btn btn-info waves-effect waves-light section-print-btn"><i
                                                    class="fa fa-print"></i>
                                                <span>{{ __('custom.print') }}</span></a>
                                            <a href="{{ route('admin.invoices.download', $invoice->id) }}"
                                                class="btn btn-primary waves-effect waves-light"><i
                                                    class="fa fa-download"></i>
                                                <span>{{ __('custom.download') }}</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- end row -->

                </div>

                <div class="row d-print-none">
                    <div class="col-sm-8 mt-3">
                        @php $due = max(0, $invoice->total - $invoice->total_paid); @endphp

                        {{-- Due summary --}}
                        <div class="alert alert-info py-2 mb-3">
                            <strong>{{ __('custom.total_due') }}:</strong>
                            {{ currencySymbol() }}{{ make2decimal($due) }}
                            @if(isset($customer) && $customer && $customer->opening_balance > 0)
                                &nbsp;|&nbsp;
                                <strong>Customer Available Balance:</strong>
                                {{ currencySymbol() }}{{ make2decimal($customer->opening_balance) }}
                            @endif
                        </div>

                        <form id="combinedPaymentForm" action="{{ route('admin.invoices.makePaymentPost', $invoice->id) }}" method="POST">
                            @csrf

                            {{-- ── Cash ──────────────────────────────────────── --}}
                            <div class="card mb-2" style="border:1px solid #dee2e6;">
                                <div class="card-header py-2 d-flex align-items-center" style="background:#f8f9fa;">
                                    <img src="{{ static_asset('admin/images/cash.png') }}" style="height:24px;margin-right:8px;" alt="cash">
                                    <strong>Cash</strong>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label class="small mb-1">Cash Amount</label>
                                        <input id="cashAmount" name="cash_amount" type="number" min="0" step="any"
                                               value="0" class="form-control payment-input" />
                                    </div>
                                </div>
                            </div>

                            {{-- ── Bank ──────────────────────────────────────── --}}
                            <div class="card mb-2" style="border:1px solid #dee2e6;">
                                <div class="card-header py-2 d-flex align-items-center" style="background:#f8f9fa;">
                                    <img src="{{ static_asset('admin/images/bank.png') }}" style="height:24px;margin-right:8px;" alt="bank">
                                    <strong>Bank / Online</strong>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Bank Amount</label>
                                        <input id="bankAmount" name="bank_amount" type="number" min="0" step="any"
                                               value="0" class="form-control payment-input" />
                                    </div>
                                </div>
                            </div>

                            {{-- ── Customer Balance ───────────────────────────── --}}
                            @if(isset($customer) && $customer && $customer->opening_balance > 0)
                            <div class="card mb-3" style="border:1px solid #dee2e6;">
                                <div class="card-header py-2 d-flex align-items-center justify-content-between" style="background:#f8f9fa;">
                                    <strong>Customer Available Balance</strong>
                                    <span class="badge badge-success">{{ currencySymbol() }}{{ make2decimal($customer->opening_balance) }} available</span>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label class="small mb-1">Balance Amount to Use</label>
                                        <input id="balanceAmount" name="balance_amount" type="number" min="0"
                                               max="{{ $customer->opening_balance }}" step="any"
                                               value="0" class="form-control payment-input" />
                                    </div>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="balance_amount" value="0" />
                            @endif

                            {{-- ── Total being paid ──────────────────────────── --}}
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded" style="background:#e9ecef;">
                                <strong>Total Paying Now:</strong>
                                <strong id="totalPayingDisplay" class="text-primary">{{ currencySymbol() }}0.00</strong>
                            </div>

                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fa fa-check-circle"></i> {{ __('custom.make_payment') }}
                            </button>
                        </form>
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
<script>
$(document).ready(function () {
    var currencySymbol = '{{ currencySymbol() }}';

    function updateTotal() {
        var cash    = parseFloat($('#cashAmount').val())    || 0;
        var bank    = parseFloat($('#bankAmount').val())    || 0;
        var balance = parseFloat($('#balanceAmount').val()) || 0;
        $('#totalPayingDisplay').text(currencySymbol + (cash + bank + balance).toFixed(2));
    }

    $('#bankAmount').on('input', updateTotal);

    $('.payment-input').on('input', updateTotal);

    // Form validation before submit
    $('#combinedPaymentForm').on('submit', function (e) {
        var cash    = parseFloat($('#cashAmount').val())    || 0;
        var bank    = parseFloat($('#bankAmount').val())    || 0;
        var balance = parseFloat($('#balanceAmount').val()) || 0;

        if (cash + bank + balance <= 0) {
            e.preventDefault();
            alert('Please enter at least one payment amount.');
            return false;
        }
    });

    updateTotal();
});
</script>
@endpush
