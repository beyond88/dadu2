<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ __('custom.pdf') }}</title>
    <style>
        .ic-table-money-relatad {
            padding: 8px 8px;
            color: #000;
            border-bottom: 1px solid #EBEBEB;
            text-align: right;
            white-space: nowrap;
        }

        .ic-table-money-relatad.text-right {
            text-align: right;
        }

        .ic-table-money-relatad.text-right.ic-table-font-big {
            font-size: 25px;
        }

        .ic-table-td-style {
            padding: 8px 8px;
            border-bottom: 1px solid #EBEBEB;

        }

        .ic-table-th {
            text-align: left;
            padding: 8px 8px;
            border-bottom: 1px solid #EBEBEB;

            font-size: 13px;
        }

        .ic-table-th.ic-table-th-text-right {
            text-align: right;
        }

        .ic-table-th.ic-table-th-text-center {
            text-align: center;
        }

        .ic-table-fixed-layout {
            table-layout: fixed;
        }

        .ic-table-tr-td-text {
            color: #8C8D8E;
            margin: 0
        }

        .ic-invoice-table-heads {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .ic-table-td {
            text-align: left;
        }

        .ic-table-td-right {
            text-align: right;
        }

        .ic-table-text-right {
            padding: 2px;
            color: #000;
            text-align: right;
        }

        .ic-table-inner-td {
            padding: 2px;
            color: #000;
            margin-bottom: 0;
        }

        .ic-top-table-heads {
            border-bottom: 1px solid #EBEBEB;
            padding-bottom: 10px;
        }

        .ic-table-app-name {
            text-align: left;
            font-size: 25px;
            color: #606770;
        }

        .text-right {
            text-align: right
        }

        /* report pdf */
        .ic-main-table {
            width: 100%;
        }

        .ic-table-td {
            padding: 5px;
            font-size: 14px;
        }

        .ic-custom-date {
            width: 15%;
        }

        .ic-custom-title {
            width: 25%;
        }

        .show-on-print {
            display: block !important;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

    </style>
</head>

<body>


<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ic-invoice-table-heads">
    <tr>
        <td class="ic-table-td" style="vertical-align: top;">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td class="ic-table-inner-td">
                        <P class="ic-table-inner-td"><b>{{ __('custom.employee_info') }}:</b></P>
                        @if($data->billing_info['name'])
                            <p class="ic-table-tr-td-text"> {{ $data->billing_info['name'] ?? '' }}</p>
                            <p class="ic-table-tr-td-text">{{ $data->billing_info['email'] ?? '' }}</p>
                            <p class="ic-table-tr-td-text">{{ $data->billing_info['phone_number'] ?? '' }}</p>
                            <p class="ic-table-tr-td-text">{{ $data->billing_info['address_line_1'] ?? '' }}</p>
                            <p class="ic-table-tr-td-text">{{ $data->billing_info['address_line_2'] ?? '' }}</p>
                            <p class="ic-table-tr-td-text">
                                {{ $data->billing_info['zip'] ? $data->billing_info['zip'].',1 ' : '' }}
                                {{ $data->billing_info['city'] ? $data->billing_info['city'].',2 ' : '' }}
                                {{ $data->billing_info['state'] ? $data->billing_info['state'].',3 ' : '' }}
                                {{ $data->billing_info['country'] ?? '' }}
                            </p>
                        @else
                            @if($data->customer_id != null && $data->customer_id != "")
                                <p class="ic-table-tr-td-text">{{ @$data->customerInfo['full_name'] ?? '' }}</p>
                                <p class="ic-table-tr-td-text">{{ @$data->customerInfo['email'] ?? '' }}</p>
                                <p class="ic-table-tr-td-text">{{ @$data->customerInfo['phone'] ?? '' }}</p>
                                <p class="ic-table-tr-td-text">
                                    {{ $data->customerInfo['address_line_1'] ? $data->customerInfo['address_line_1'].',4 ' : '' }}
                                    {{ $data->customerInfo['address_line_2'] ? $data->customerInfo['address_line_2'].'5' : '' }}
                                </p>
                                <p class="ic-table-tr-td-text">
                                    {{ $data->customerInfo['zipcode'] ? $data->customerInfo['zipcode'].', ' : '' }}
                                    {{ optional($data->customerInfo->systemCity)->name ? optional($data->customerInfo->systemCity)->name .',6 ' : '' }}
                                    {{ optional($data->customerInfo->systemState)->name ? optional($data->customerInfo->systemState)->name .',7 ' : '' }}
                                    {{ optional($data->customerInfo->systemCountry)->name ?optional($data->customerInfo->systemCountry)->name .',8 ' : '' }}
                                </p>
                            @else
                                <p class="ic-table-tr-td-text">{{ __('custom.walk_in_customer') }}</p>
                            @endif
                        @endif
                    </td>
                </tr>
            </table>
        </td>

        <td class="ic-table-td-right" valign="top" style="vertical-align: top;">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td class="ic-table-text-right">
                        <P class="ic-table-inner-td"><b>{{ __('custom.withdraw') }}:</b></P>
                        <p class="ic-table-tr-td-text">{{ __('custom.withdraw_id') }} : {{ make8digits($data->id)
                            }}</p>
                       
                        <p class="ic-table-tr-td-text">{{ __('custom.date') }}: {{ formatDynamicDateTime($data->date) }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ic-invoice-table-heads">
    <tr>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ic-invoice-table-heads">
    <tr>
        <td class="ic-table-td">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td class="ic-table-inner-td"><b>{{ __('custom.summary') }}</b></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ic-invoice-table-heads">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="ic-table-fixed-layout"
                   style="border:1px solid #EBEBEB;">
                <thead>
                <tr>

                    <th class="ic-table-th" width="40%">
                        {{ __('custom.name') }}</th>
                    <th class="ic-table-th">
                        {{ __('custom.quantity') }}</th>

                </tr>
                </thead>
                <tbody>
                @if (isset($data->items))
                    @foreach ($data->items as $item)
                        <tr>

                            <td width="40%" class="ic-table-td-style">
                                {{ $item->product_name ?? '' }}
                                @if($item->product->is_variant != null && $item->product->is_variant == 1 && isset($item->productStock))
                                    ({{optional(optional($item->productStock)->attribute)->name ?? ''}}
                                    : {{optional(optional($item->productStock)->attributeItem)->name ?? ''}})
                                @endif
                            </td>
                            <td class="ic-table-td-style">
                                {{ $item->quantity ?? '' }}
                            </td>

                        </tr>
                    @endforeach
                @endif

                </tbody>

            </table>
        </td>
    </tr>
</table>
<br>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td colspan="6">Note: {!! $data->notes ? $data->notes : (config('terms_and_conditions') != null ? config('terms_and_conditions') : '') !!}</td>
    </tr>
</table>
<div class="show-on-print">
    <p>{{ config('invoice_footer') }}</p>
</div>
</body>

</html>
