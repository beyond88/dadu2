@php
$authUser = auth()->guard('customer')->check();

@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('custom.invoice') }}: {{ make8digits($invoice->id) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
</head>
<body style="align-items: center">
<div class="no_print" style="overflow: hidden;">
    <a class="no_print btn backBtn" href="{{ $authUser ? route('customer.invoices.index') : route('admin.invoices.index') }}">Back to Invoice</a>
    <a onclick="location.reload();" class="no_print btn reprintBtn" href="javascript:">Print Again</a>
</div>
<table width="" cellspacing="0" cellpadding="2" border="0">
    <tbody>
    <tr>
        <td colspan="4" style="text-align: center">
            <h1>{{ config('site_title') ?? config('app.name') }}</h1>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: center">
            <span style="font-size: 12px; display: flex; justify-content: space-between;">
                    <strong>Customer#
                        @if($invoice->billing_info['name'])
                            {{ $invoice->billing_info['name'] ?? '' }}
                        @else
                            {{ __('custom.walk_in_customer') }}
                        @endif
                    </strong>
                    <strong>Invoice # {{ make8digits($invoice->id) }}</strong>
                </span>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <table width="100%" cellpadding="2" cellspacing="0" border="0">
                <tbody>
                <tr>
                    <td colspan="4">Invoice Date: {{ custom_date($invoice->date) }}</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr style="">
        <td width="40%" style="border: 1px solid #333; text-align: left"><strong>{{ __('custom.product') }}</strong></td>
        <td width="20%" style="border: 1px solid #333; border-left: 0 none;text-align: center"><strong>{{ __('custom.price') }}</strong></td>
        <td width="15%" style="border: 1px solid #333; border-left: 0 none;text-align: center"><strong>{{ __('custom.qty') }}</strong></td>
        <td width="15%" style="border: 1px solid #333; border-left: 0 none;text-align: center"><strong>{{ __('custom.batch') }}</strong></td>
        <td width="20%" style="border: 1px solid #333; border-left: 0 none;text-align: right"><strong>{{ __('custom.total') }}</strong>
        </td>
    </tr>
{{--    Loop area--}}
    @if ($invoice->items)
        @foreach ($invoice->items as $item)
        <tr @if($item->is_free) style="background:#fffde7;" @endif>
            <td style="border-left:1px solid #333;border-right:1px solid #333;text-align:left;">
                {{ $item->product_name ?? '' }}
                @if($item->is_free) <strong>[FREE]</strong> @endif
                @if($item->product?->is_variant != null && $item->product->is_variant == 1 && isset($item->productStock))
                    ({{ __("custom.variant") }}: {{optional(optional($item->productStock)->variation)->name ?? ''}})
                @endif
                <div style="font-size:10px;color:#555;">
                    @if($item->product?->brand?->name) <span>{{ __('custom.brand') }}: {{ $item->product->brand->name }}</span> @endif
                    @if($item->product?->parts_no) <span>{{ __('custom.parts_no') }}: {{ $item->product->parts_no }}</span> @endif
                    @if($item->product?->model) <span>{{ __('custom.model') }}: {{ $item->product->model }}</span> @endif
                </div>
            </td>
            <td style="border-right:1px solid #333;text-align:center;">
                @if($item->is_free) FREE @else {{ currencySymbol().make2decimal($item->price) }} @endif
            </td>
            <td style="border-right:1px solid #333;text-align:center;">
                {{ $item->quantity_display }} {{ $item->unit_label }}
            </td>
            <td style="border-right:1px solid #333;text-align:center;">
                {{ $item->productStock->batch ?? '' }}
            </td>
            <td style="border-right:1px solid #333;text-align:right;">
                @if($item->is_free) — @else {{ currencySymbol().make2decimal($item->sub_total) }} @endif
            </td>
        </tr>
        @endforeach
    @endif
{{--    Loop Area End--}}

    <tr>
        <td colspan="3" style="border-top: 1px solid #333;text-align: right; font-weight: bold">{{ __('custom.discount') }}</td>
        <td colspan="3" style="border-top: 1px solid #333;text-align: right; font-weight: bold">{{ currencySymbol().make2decimal($invoice->discount_amount) }}</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right; font-weight: bold">{{ __('custom.tax') }}</td>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ currencySymbol().make2decimal($invoice->tax_amount) }}</td>
    </tr>
    @if($invoice->additional_charge_amount > 0)
    <tr>
        <td colspan="2" style="text-align: right; font-weight: bold">{{ $invoice->additional_charge_name ?? __('custom.additional_charge') }}</td>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ currencySymbol().make2decimal($invoice->additional_charge_amount) }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ __('custom.total') }}</td>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ currencySymbol().make2decimal($invoice->total) }}</td>
    </tr>
    @php
        $thermalPaymentLabels = ['cash'=>'Cash','bank'=>'Bank','online'=>'Online','balance'=>'Current Balance','combined'=>'Combined'];
    @endphp
    @foreach($invoice->payments->where('amount', '>', 0) as $tp)
    <tr>
        <td colspan="3" style="text-align:right;font-weight:bold;">
            {{ $thermalPaymentLabels[strtolower($tp->payment_type)] ?? ucfirst($tp->payment_type) }}
        </td>
        <td colspan="3" style="text-align:right;font-weight:bold;">
            {{ currencySymbol().make2decimal($tp->amount) }}
        </td>
    </tr>
    @endforeach
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ __('custom.total_paid') }}</td>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ currencySymbol().make2decimal($invoice->total_paid) }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ __('custom.sale_return_amount') }}</td>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ currencySymbol().make2decimal($invoice->saleReturns()->sum('return_total_amount')) }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ __('custom.total_due') }}</td>
        <td colspan="3" style="text-align: right; font-weight: bold">{{ currencySymbol().make2decimal(calculateDue($invoice->total, $invoice->total_paid)) }}</td>
    </tr>
    </tbody>
</table>

    <span style="text-align: left; font-weight: bold">{{ __('custom.note') }} : {{ $invoice->notes }}</span>
<div class="no_print" style="overflow: hidden;">
    <br/>
    <a class="no_print btn backBtn" href="{{ $authUser ? route('customer.invoices.index') : route('admin.invoices.index') }}">Back to Invoice</a>
    <a onclick="location.reload();" class="no_print btn reprintBtn" href="javascript:">Print Again</a>
</div>
<style type="text/css">
    .btn {
        padding: 10px;
        text-decoration: none;
        color: #fff;
        display: inline-block;
    }

    .backBtn {
        background-color: #1221ff;
    }

    .ic-border-bottom {
        border-bottom: 1px solid #dedede;
    }

    .reprintBtn {
        background-color: #09ff88;
    }

    @media print {
        @page {
            margin: 0;
        }

        table {
            width: 2.3in;
            margin-bottom: 20px;
        }

        table td {
            border-collapse: collapse;
        }

        .no_print {
            display: none
        }
        * {
            font-size: 13px !important;
            word-wrap: break-word;
            font-family: 'Roboto', sans-serif;
        }
        h1{
            font-size: 20px !important;
        }
    }
</style>
<script type="text/javascript">
    window.print();
</script>

</body>
</html>
