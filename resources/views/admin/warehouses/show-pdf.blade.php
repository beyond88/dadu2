<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('custom.warehouse_details') }}</title>
    <style>
        body {
            font-family: "Roboto", sans-serif;
            font-size: 12px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
            padding: 4px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-danger {
            color: red;
        }
        .text-success {
            color: green;
        }
    </style>
</head>

<body>
    <div style="width: 100%; text-align: center">
        <p>
            <b style="font-size: 26px">{{ $warehouse_details->name }}</b>
            <br>
            {{ __t('email') }}: {{ $warehouse_details->email }}
            <br>
            {{ __t('phone') }}: {{ $warehouse_details->phone }}
            <br>
            {{ __t('company') }}: {{ $warehouse_details->company_name }}
            <br>
            {{ __t('address') }}: {{ $warehouse_details->address_1 }} <br>
            {{ $warehouse_details->address_2 }}
        </p>
    </div>

    <div style="width: 100%;">
        <table border="1" cellpadding="2" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><strong>SL</strong></th>
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
                    $totalStock = 0;
                    $totalAmout = 0;
                @endphp
                @foreach ($product_stocks as $product_stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">
                            @if(optional($product_stock->product)->thumb && \Storage::exists(\App\Models\Product::FILE_STORE_PATH . '/' . optional($product_stock->product)->thumb))
                                <img src="{{ config('app.url') . '/storage/' . \App\Models\Product::FILE_STORE_PATH . '/' . optional($product_stock->product)->thumb }}"
                                     alt="{{ optional($product_stock->product)->name }}"
                                     style="width: 40px; height: 40px; object-fit: cover; margin-right: 5px;">
                            @endif
                            {{ optional($product_stock->product)->name }}
                            @if (optional($product_stock->product)->is_variant == 1)
                                <small>({{ __("custom.variant") }}: {{ optional($product_stock->variation)->name ?? '' }})</small>
                            @endif
                        </td>
                        <td>{{ $product_stock->variation->sku ?? optional($product_stock->product)->sku }}</td>
                        <td>{{ optional(optional($product_stock->product)->category)->name }}</td>
                        <td>{{ optional(optional($product_stock->product)->manufacturer)->name }}</td>
                        <td>
                            @if ($product_stock->quantity <= 0)
                                <span class="text-danger"><b>{{ $product_stock->quantity }}</b></span>
                                {{ optional(optional($product_stock->product)->weight_unit)->name }}
                            @else
                                <span class="text-success"><b>{{ $product_stock->quantity }}</b></span>
                                {{ optional(optional($product_stock->product)->weight_unit)->name }}
                            @endif
                        </td>
                        <td class="text-right">
                            {{ currencySymbol() . make2decimal(optional($product_stock->product)->price) }}</td>
                        <td class="text-right">
                            {{ currencySymbol() . optional($product_stock->product)->price * $product_stock->quantity }}
                        </td>
                    </tr>
                    @php
                        $totalStock += $product_stock->quantity;
                        $totalAmout += optional($product_stock->product)->price * $product_stock->quantity;
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">{{ __('custom.total') }}</th>
                    <th>{{ $totalStock }}</th>
                    <th></th>
                    <th>{{ currencySymbol() . $totalAmout }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
