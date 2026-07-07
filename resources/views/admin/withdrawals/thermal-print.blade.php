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
    <title>{{ __('custom.withdraw') }}: {{ make8digits($invoice->id) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
</head>
<body style="align-items: center">
<div class="no_print" style="overflow: hidden;">
    <a class="no_print btn backBtn" href="{{ $authUser ? route('customer.withdrawals.index') : route('admin.withdrawals.index') }}">Back to Withdraw</a>
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
                    <strong>{{ __('custom.withdraw') }} # {{ make8digits($invoice->id) }}</strong>
                </span>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <table width="100%" cellpadding="2" cellspacing="0" border="0">
                <tbody>
                <tr>
                    <td colspan="4">Date: {{ formatDynamicDateTime($invoice->date) }}</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr style="">
        <td width="40%" style="border: 1px solid #333; text-align: left"><strong>{{ __('custom.product') }}</strong></td>

        <td width="15%" style="border: 1px solid #333; border-left: 0 none;text-align: center"><strong>{{ __('custom.qty') }}</strong></td>
        <td width="15%" style="border: 1px solid #333; border-left: 0 none;text-align: center"><strong>{{ __('custom.batch') }}</strong></td>

        </td>
    </tr>
{{--    Loop area--}}
    @if ($invoice->items)
        @foreach ($invoice->items as $item)
        <tr>
            <td class="" style="border-left: 1px solid #333; border-right: 1px solid #333; text-align: left">
                {{ $item->product_name ?? '' }}
                @if($item->product->is_variant == 1 && isset($item->productStock))
                    ({{optional(optional($item->productStock)->attribute)->name ?? ''}} : {{optional(optional($item->productStock)->attributeItem)->name ?? ''}})
                @endif
            </td>

            <td class="" style="border-right: 1px solid #333; text-align: center">
                {{ $item->quantity }}
            </td>
            <td class="" style="border-right: 1px solid #333; text-align: center">
                {{ $item->productStock->batch ?? '' }}
            </td>

        </tr>
        @endforeach
    @endif
{{--    Loop Area End--}}

    <tr>
        <td colspan="2" style="border-top: 1px solid #333;text-align: right; font-weight: bold"></td>
        <td colspan="2" style="border-top: 1px solid #333;text-align: right; font-weight: bold"></td>
    </tr>
    <tr>

    </tbody>
</table>

    <span style="text-align: left; font-weight: bold">{{ __('custom.note') }} : {{ $invoice->notes }}</span>
<div class="no_print" style="overflow: hidden;">
    <br/>
    <a class="no_print btn backBtn" href="{{ $authUser ? route('customer.withdrawals.index') : route('admin.withdrawals.index') }}">Back to Withdraw</a>
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
