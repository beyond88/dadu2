<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ static_asset('admin/css/pdf-style.css') }}" rel="stylesheet" type="text/css" />
    <title>{{ __('custom.product_history') }}</title>
</head>

<body>
    <p><b>{{ __('custom.employee') }}:</b> {{ $customer->full_name }}</p>
    <p><b>{{ __('custom.product_history') }}</b></p>
    
    <table class="ic-main-table" width="100%" cellpadding="0" cellspacing="0" border="1" >
        <thead>
            <tr>
                <th class="ic-table-td">{{ __('custom.sl') }}</th>
                <th class="ic-table-td">{{ __('custom.product_id') }}</th>
                <th class="ic-table-td">{{ __('custom.product_name') }}</th>
                <th class="ic-table-td">{{ __('custom.sku') }}</th>
                <th class="ic-table-td">{{ __('custom.price') }}</th>
                <th class="ic-table-td">{{ __('custom.quantity') }}</th>
                <th class="ic-table-td">{{ __('custom.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td class="ic-table-td">{{ ++$loop->index }}</td>
                    <td class="ic-table-td">{{ make8digits($product['product_id']) }}</td>
                    <td class="ic-table-td">{{ $product['product_name'] }}</td>
                    <td class="ic-table-td">{{ $product['sku'] }}</td>
                    <td class="ic-table-td">{{ currencySymbol() . ' ' . $product['price'] }}</td>
                    <td class="ic-table-td">{{ $product['quantity'] }}</td>
                    <td class="ic-table-td">{{ currencySymbol() . ' ' . ($product['price'] * $product['quantity']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
