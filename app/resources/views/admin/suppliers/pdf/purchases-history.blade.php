<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ static_asset('admin/css/pdf-style.css') }}" rel="stylesheet" type="text/css" />
    <title>{{ __('custom.purchase_history') }}</title>
</head>

<body>
    <p><b>{{ __('custom.supplier') }}:</b> {{ $supplier->first_name }} {{ $supplier->last_name }}</p>
    <p><b>{{ __('custom.purchase_history') }}</b></p>

    <table class="ic-main-table" width="100%" cellpadding="0" cellspacing="0" border="1" >
        <thead>
            <tr>
                <th class="ic-table-td">{{ __('custom.sl') }}</th>
                <th class="ic-table-td">{{ __('custom.purchase_number') }}</th>
                <th class="ic-table-td">{{ __('custom.date') }}</th>
                <th class="ic-table-td">{{ __('custom.total') }}</th>
                <th class="ic-table-td">{{ __('custom.total_unique_product') }}</th>
                <th class="ic-table-td">{{ __('custom.status') }}</th>
                <th class="ic-table-td">{{ __('custom.received') }}</th>
                <th class="ic-table-td">{{ __('custom.missing_item') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td class="ic-table-td">{{ ++$loop->index }}</td>
                    <td class="ic-table-td">{{ make8digits($purchase->purchase_number) }}</td>
                    <td class="ic-table-td">
                        {{ date(config('date_formate'), strtotime($purchase->date)) }}
                    </td>
                    <td class="ic-table-td">{{ currencySymbol() . ' ' . $purchase->total }}</td>
                    <td class="ic-table-td">{{ $purchase->purchaseItems->count() }}</td>
                    <td class="ic-table-td">{{ \Illuminate\Support\Str::upper($purchase->status) }}</td>
                    <td class="ic-table-td">
                        @if ($purchase->received)
                            {{ __('custom.received') }}
                        @else
                            {{ __('custom.not_received_yet') }}
                        @endif
                    </td>
                    <td class="ic-table-td">
                        @if ($purchase->received)
                            @php
                                $purchaseItemQty = $purchase->purchaseItems->sum('quantity');
                                $purchaseReceiveItemQty = 0;
                                foreach ($purchase->purchaseItems as $purchaseItems) {
                                    $purchaseReceiveItemQty += $purchaseItems->receiveItems->sum('quantity');
                                }
                            @endphp
                            @if ($purchaseItemQty != $purchaseReceiveItemQty)
                                {{ __('custom.missing') }}
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
