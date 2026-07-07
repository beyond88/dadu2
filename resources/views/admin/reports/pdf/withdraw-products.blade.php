<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ static_asset('admin/css/pdf-style.css') }}" rel="stylesheet" type="text/css" />
    <title>{{ __('custom.export') }}</title>
</head>

<body>
    <p><b>{{ __('custom.withdrawal_product_report') }}:</b> {{ $report_range ?? '' }}</p>
    <p><b>{{ __('custom.total_qty') }}:</b> {{ $groupedWithdrawals->sum('total_quantity') }}</p>

    <table class="ic-main-table" width="100%" cellpadding="0" cellspacing="0" border="1">
        <thead>
            <tr>
                <th class="ic-table-td">{{ __('custom.sl') }}</th>
                <th class="ic-table-td">{{ __('custom.employee') }}</th>
                <th class="ic-table-td">{{ __('custom.company') }}</th>
                <th class="ic-table-td">{{ __('custom.item') }}</th>
                <th class="ic-table-td">{{ __('custom.quantity') }}</th>

                <th class="ic-table-td">{{ __('custom.withdraw_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedWithdrawals as $withdrawal)
                @php
                    $customer = $customers[$withdrawal->customer_id] ?? null;
                    $product = $products[$withdrawal->product_id] ?? null;
                @endphp
                <tr>
                    <td class="ic-table-td">{{ $loop->iteration }}</td>
                    <td class="ic-table-td">{{ $customer?->full_name ?? '-' }}</td>
                    <td class="ic-table-td">{{ $customer?->company?->name ?? '-' }}</td>
                    <td class="ic-table-td">{{ $product?->name ?? '-' }}</td>
                    <td class="ic-table-td">{{ $withdrawal->total_quantity }}</td>
                    <td class="ic-table-td">{{ formatDynamicDateTime($withdrawal->withdraw_date) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="ic-table-td text-right"><strong>{{ __('custom.total') }}</strong></td>
                <td class="ic-table-td"><strong>{{ $groupedWithdrawals->sum('total_quantity') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
