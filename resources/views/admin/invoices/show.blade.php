@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">{{ __('custom.show_invoice') }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.invoice') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.show_invoice') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- ── INVOICE CONTENT (matches PDF exactly) ─────────────── --}}
            <div class="card-body" id="print-invoice">
                @php
                    /* Customer */
                    $customer    = $invoice->customerInfo;

                    /* Customer display */
                    $billingName  = $invoice->billing_info['name'] ?? null;
                    $customerName = $customer?->company ?: ($billingName ?: ($customer?->full_name ?? __('custom.walk_in_customer')));
                    $customerPhone= collect([$invoice->billing_info['phone'] ?? null, $customer?->phone])
                        ->first(fn ($p) => strlen(preg_replace('/\D/', '', (string) $p)) >= 6) ?? '';
                    $customerAddr = $invoice->billing_info['address_line_1'] ?? $customer?->address_line_1 ?? '';
                @endphp

                {{-- Company Header --}}
                <div class="text-center mb-2">
                    @if(config('is_logo_show_in_invoice') == 'yes' && site_logo())
                        <div class="mb-1"><img src="{{ site_logo() }}" style="height:50px;" alt="logo"></div>
                    @endif
                    <h4 class="mb-0" style="font-style:italic; font-weight:700; letter-spacing:1px;">
                        {{ config('site_title') ?? config('app.name') }}
                    </h4>
                    @if(config('store_address'))
                        <div style="font-size:.8rem; color:#555;">{{ config('store_address') }}</div>
                    @endif
                    <div style="font-size:.85rem; font-style:italic; text-decoration:underline; margin-top:2px;">
                        Delivery Challan with Invoice
                    </div>
                    @if(config('booking_info'))
                    <div style="font-size:.8rem; margin-top:2px;">
                        {!! nl2br(e(config('booking_info'))) !!}
                    </div>
                    @endif
                    @if(config('store_mobile') || config('store_website'))
                    <div style="font-size:.78rem; color:#666;">
                        @if(config('store_mobile')) Mob. : {{ config('store_mobile') }} @endif
                        @if(config('store_website')) &nbsp;|&nbsp; {{ config('store_website') }} @endif
                    </div>
                    @endif
                </div>

                {{-- Customer + Date block --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
                    <tr>
                        <td style="width:55%; vertical-align:top; padding-right:10px;">
                            <div style="font-size:.875rem;">
                                <strong>{{ $customerName }}</strong><br>
                                @if($customerAddr) {{ $customerAddr }}<br> @endif
                                @if($customerPhone) মোবাইল : {{ to_bangla_number($customerPhone) }} @endif
                            </div>
                        </td>
                        <td style="width:45%; vertical-align:top;">
                            <table style="margin-left:auto; border-collapse:collapse; font-size:.875rem;">
                                <tr>
                                    <td style="padding:2px 8px; text-align:right;">Date :</td>
                                    <td>
                                        <span style="border:1px solid #000; padding:2px 10px; display:inline-block; min-width:90px; text-align:right;">
                                            {{ \Carbon\Carbon::parse($invoice->date)->format('d-m-y') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:2px 8px; text-align:right;">SI No :</td>
                                    <td>
                                        <span style="border:1px solid #000; padding:2px 10px; display:inline-block; min-width:90px; text-align:right; font-weight:700; font-size:1rem;">
                                            {{ make8digits($invoice->id) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                {{-- Items Table --}}
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="border-collapse:collapse; font-size:.8125rem; margin-bottom:0;">
                    <thead>
                        <tr style="background:#f5f5f5;">
                            <th style="border:1px solid #999; padding:5px 6px; text-align:center; width:8%;">CODE</th>
                            <th style="border:1px solid #999; padding:5px 6px; text-align:left; width:28%;">Name of the Item</th>
                            <th style="border:1px solid #999; padding:5px 6px; text-align:center; width:5%;">Ctr</th>
                            <th style="border:1px solid #999; padding:5px 6px; text-align:center; width:5%;">Pc</th>
                            <th style="border:1px solid #999; padding:5px 6px; text-align:center; width:6%;">Total</th>
                            <th style="border:1px solid #999; padding:5px 6px; text-align:right; width:10%;">Price</th>
                            <th style="border:1px solid #999; padding:5px 6px; text-align:right; width:12%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            @php
                                $code   = $item->product?->parts_no ?: ($item->sku ?? '—');
                                $carton = $item->product?->carton;
                                $qpc    = $carton ? (int)$carton->qty_per_carton : 0;
                                $ctr    = ($qpc > 0) ? floor($item->quantity / $qpc) : '';
                                $pc     = $item->quantity;
                            @endphp
                            <tr @if($item->is_free) style="background:#fffde7;" @endif>
                                <td style="border:1px solid #999; padding:4px 6px; text-align:center;">{{ $code }}</td>
                                <td style="border:1px solid #999; padding:4px 6px;">
                                    {{ $item->product_name }}
                                    @if($item->is_free)
                                        <span style="background:#f57f17;color:#fff;font-size:.65rem;padding:1px 5px;border-radius:3px;margin-left:4px;font-weight:700;">FREE</span>
                                    @endif
                                    @if($item->product?->is_variant && isset($item->productStock))
                                        <small class="text-muted">({{ optional($item->productStock->variation)->name ?? '' }})</small>
                                    @endif
                                </td>
                                <td style="border:1px solid #999; padding:4px 6px; text-align:center;">{{ $ctr }}</td>
                                <td style="border:1px solid #999; padding:4px 6px; text-align:center;">{{ $pc }}</td>
                                <td style="border:1px solid #999; padding:4px 6px; text-align:center;">{{ $item->quantity_display }} {{ $item->unit_label }}</td>
                                <td style="border:1px solid #999; padding:4px 6px; text-align:right;">
                                    @if(!$item->is_free){{ currencySymbol().make2decimal($item->price) }}@if($item->unit_label)<small class="text-muted">/{{ $item->unit_label }}</small>@endif @endif
                                </td>
                                <td style="border:1px solid #999; padding:4px 6px; text-align:right;">
                                    @if(!$item->is_free){{ currencySymbol().make2decimal($item->sub_total) }}@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="border:1px solid #999; padding:5px 8px; text-align:right; font-weight:700;">Total</td>
                            <td style="border:1px solid #999; padding:5px 8px; text-align:right; font-weight:700;">
                                {{ currencySymbol().make2decimal($invoice->total) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Financial Summary + Category Breakdown --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;">
                    <tr>
                        {{-- Left: notes + signature --}}
                        <td style="width:52%; vertical-align:top; padding-right:12px; font-size:.8rem; color:#555;">
                            @if($invoice->notes)
                                {!! nl2br(e($invoice->notes)) !!}
                            @elseif(config('terms_and_conditions'))
                                {!! config('terms_and_conditions') !!}
                            @endif

                            {{-- Signature --}}
                            <div style="margin-top:60px; width:60%;">
                                <div style="border-top:1px solid #000; padding-top:4px; font-size:.85rem; font-weight:700; font-style:italic; color:#000; text-align:center;">
                                    &nbsp;
                                </div>
                            </div>
                        </td>

                        {{-- Right: Total Bill / Total Paid / Total Due --}}
                        <td style="width:48%; vertical-align:top;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:.875rem;">
                                <tr>
                                    <td style="text-align:right; padding:4px 8px; width:55%;">Total Bill :</td>
                                    <td style="border:1px solid #000; text-align:right; padding:4px 10px; font-weight:700; min-width:100px;">
                                        {{ make2decimal($invoice->total) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right; padding:4px 8px;">Total Paid :</td>
                                    <td style="border:1px solid #000; text-align:right; padding:4px 10px; font-weight:700;">
                                        {{ make2decimal($invoice->total_paid) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right; padding:4px 8px; font-weight:700;">Total Due :</td>
                                    <td style="border:1px solid #000; text-align:right; padding:4px 10px; font-weight:700;">
                                        {{ make2decimal(calculateDue($invoice->total, $invoice->total_paid)) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                @if($invoice->status == 'pending')
                <div class="mt-3 d-print-none">
                    <label>{{ __('custom.payment_url') }} — {{ __('custom.amount') }}: {{ currencySymbol() }}{{ $invoice->last_paid }}</label>
                    <div class="ic-copy-url">
                        <input type="text" class="form-control" id="live-invoice-pay" value="{{ $invoice->token }}" disabled>
                        <button type="button" class="btn btn-primary copy-url-btn" data-copy-id="live-invoice-pay">
                            {{ __('custom.copy_url') }}
                        </button>
                    </div>
                </div>
                @endif

                <div class="show-on-print mt-3">
                    <p>{{ config('invoice_footer') }}</p>
                </div>
            </div>{{-- end #print-invoice --}}

            {{-- Action Buttons --}}
            <div class="card-body d-print-none">
                <div class="row">
                    <div class="col-lg-6 col-sm-6">
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-dark waves-effect waves-light">
                            <i class="fa fa-arrow-left"></i> <span>{{ __('custom.back') }}</span>
                        </a>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <div class="btn-group float-right" role="group">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-info dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-print"></i> {{ __('custom.print') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item" href="#" onclick="window.print()">A4 Print</a>
                                    <a class="dropdown-item" href="{{ route('admin.invoice.print', $invoice->id) }}">POS Print</a>
                                </div>
                            </div>
                            <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="btn btn-primary">
                                <i class="fa fa-download"></i> <span>{{ __('custom.download') }}</span>
                            </a>
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
    .show-on-print { display: none; }

    @media print {
        .d-print-none { display: none !important; }
        .show-on-print {
            display: block !important;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        .page-title-box { display: none !important; }
    }
</style>
@endpush

@push('script')
<script src="https://cdn.apidelv.com/libs/awesome-functions/awesome-functions.min.js"></script>
@endpush
