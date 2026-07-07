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
    <p style="text-align: center;"><b>{{ __('custom.stock_change_over_period_report') }}:</b> {{ $report_range ?? '' }}</p>
    <p style="text-align: center;"><b>{{ __('custom.total_products') }}:</b> {{ count($stockSummary) }}</p>

    <table class="ic-main-table" width="100%" cellpadding="0" cellspacing="0" border="1">
        <thead>
            <tr>
                <th class="ic-table-td">{{ __('custom.sl') }}</th>
                <th class="ic-table-td">{{ __('custom.product') }}</th>
                <th class="ic-table-td">{{ __('custom.sku') }}</th>
                <th class="ic-table-td">{{ __('custom.warehouse') }}</th>
                <th class="ic-table-td">{{ __('custom.start_qty') }}</th>
                <th class="ic-table-td">{{ __('custom.inbound') }}</th>
                <th class="ic-table-td">{{ __('custom.outbound') }}</th>
                <th class="ic-table-td">{{ __('custom.end_qty') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockSummary as $summary)
                <tr>
                    <td class="ic-table-td">{{ $loop->iteration }}</td>
                    <td class="ic-table-td"><strong>{{ $summary['product'] }}</strong></td>
                    <td class="ic-table-td"><strong>{{ $summary['sku'] }}</strong></td>
                    <td class="ic-table-td"><strong>{{ $summary['warehouse'] }}</strong></td>
                    <td class="ic-table-td" style="text-align: right;"><strong>{{ $summary['start_quantity'] }}</strong></td>
                    <td class="ic-table-td" style="text-align: right; color: #28a745;"><strong>{{ $summary['total_inbound'] }}</strong></td>
                    <td class="ic-table-td" style="text-align: right; color: #dc3545;"><strong>{{ $summary['total_outbound'] }}</strong></td>
                    <td class="ic-table-td" style="text-align: right;"><strong>{{ $summary['end_quantity'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('custom.no_data_found') }}</td>
                </tr>
            @endforelse
        </tbody>

    </table>
</body>

</html>
