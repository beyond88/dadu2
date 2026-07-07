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
    <p><b>{{ __('custom.transaction_history_report') }}:</b> {{ $report_range ?? '' }}</p>
    <p><b>{{ __('custom.total_movements') }}:</b> {{ $history->count() }}</p>

    <table class="ic-main-table" width="100%" cellpadding="0" cellspacing="0" border="1">
        <thead>
            <tr>
                <th class="ic-table-td">{{ __('custom.sl') }}</th>
                <th class="ic-table-td">{{ __('custom.date_time') }}</th>
                <th class="ic-table-td">{{ __('custom.product') }}</th>
                <th class="ic-table-td">{{ __('custom.sku') }}</th>
                <th class="ic-table-td">{{ __('custom.warehouse') }}</th>
                <th class="ic-table-td">{{ __('custom.employee_source') }}</th>
                <th class="ic-table-td">{{ __('custom.type') }}</th>
                <th class="ic-table-td">{{ __('custom.quantity') }}</th>
                <th class="ic-table-td">{{ __('custom.reference') }}</th>
                <th class="ic-table-td">{{ __('custom.stock_after') }}</th>
                <th class="ic-table-td">{{ __('custom.comments') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($history as $item)
                <tr>
                    <td class="ic-table-td">{{ $loop->iteration }}</td>
                    <td class="ic-table-td">{{ \Carbon\Carbon::parse($item->date_time)->format('d/m/Y h:i A') }}</td>
                    <td class="ic-table-td">{{ $item->product ?? '-' }}</td>
                    <td class="ic-table-td">{{ $item->sku ?? '-' }}</td>
                    <td class="ic-table-td">{{ $item->warehouse ?? '-' }}</td>
                    <td class="ic-table-td">{{ $item->employee_source ?? '-' }}</td>
                    <td class="ic-table-td">{{ $item->type ?? '-' }}</td>
                    <td class="ic-table-td">{{ $item->quantity ?? '0' }}</td>
                    <td class="ic-table-td">{{ $item->type == 'Withdrawal' ? make8digits($item->reference) : $item->reference }}</td>
                    <td class="ic-table-td">{{ $item->stock_after ?? '-' }}</td>
                    <td class="ic-table-td">{{ $item->comments ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('custom.no_data_found') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="ic-table-td text-right"><strong>{{ __('custom.total') }}</strong></td>
                <td class="ic-table-td"><strong>{{ $history->sum('quantity') }}</strong></td>
                <td colspan="3" class="ic-table-td"></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
