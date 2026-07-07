<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('custom.balance_report') }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 9px;
        }
        .title { font-size: 13px; font-weight: bold; text-align: center; letter-spacing: 1px; }
        .month-banner { font-weight: bold; text-align: center; letter-spacing: 3px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .avg { color: #d00; font-weight: bold; text-align: right; }
    </style>
</head>

<body>
    @php $colCount = count($categories) + 1; @endphp
    <table>
        <thead>
            <tr>
                <th class="title" colspan="{{ $colCount }}">{{ __('custom.balance_report_heading') }}</th>
            </tr>
            <tr>
                <th class="text-center">{{ __('custom.date') }}</th>
                @foreach ($categories as $category)
                    <th class="text-center">{{ strtoupper($category->name) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $currentMonth = null; @endphp
            @foreach ($dates as $date)
                @php $carbonDate = \Carbon\Carbon::parse($date); $monthKey = $carbonDate->format('Y-m'); @endphp
                @if ($monthKey !== $currentMonth)
                    @php $currentMonth = $monthKey; @endphp
                    <tr>
                        <td class="month-banner" colspan="{{ $colCount }}">
                            {{ strtoupper($carbonDate->format('F')) }} .... {{ $carbonDate->format('Y') }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>{{ $carbonDate->format('j/n/y') }}</td>
                    @foreach ($categories as $category)
                        @php $value = $rows[$date][$category->id] ?? 0; @endphp
                        <td class="text-right">{{ $value > 0 ? number_format($value, 0) : '' }}</td>
                    @endforeach
                </tr>
            @endforeach

            @foreach ($expenseRows as $expense)
                <tr>
                    <td>{{ $expense['name'] }}</td>
                    <td class="text-right" colspan="{{ $colCount - 1 }}">
                        {{ $expense['total'] > 0 ? number_format($expense['total'], 0) : '' }}
                    </td>
                </tr>
            @endforeach

            <tr>
                <th class="text-right">{{ __('custom.total') }} :</th>
                @foreach ($categories as $category)
                    <th class="text-right">{{ number_format($categoryTotals[$category->id] ?? 0, 0) }}</th>
                @endforeach
            </tr>
            <tr>
                <th class="avg" colspan="{{ $colCount }}">{{ __('custom.avg') }}:</th>
            </tr>
        </tbody>
    </table>
</body>

</html>
