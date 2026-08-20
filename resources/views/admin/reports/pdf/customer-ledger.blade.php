<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('custom.customer_ledger_report') }}</title>
    <style>
        /* "hindsiliguri" is the font registered in render_mpdf; the rest are the
           browser fallbacks used when this same view is opened for printing. */
        * { margin: 0; padding: 0; }
        body {
            font-family: "hindsiliguri", "Hind Siliguri", "Noto Sans Bengali", sans-serif;
            font-size: 11px; color: #000;
        }
        @media print { body { margin: 0; } }
        @page { margin: 12mm; }

        .center { text-align: center; }
        .right  { text-align: right; }

        .company { font-size: 18px; font-weight: bold; font-style: italic; }
        .addr    { font-size: 11px; margin-top: 3px; }
        .title   { font-size: 12px; margin-top: 3px; }
        .range   { font-size: 11px; font-weight: bold; margin-top: 6px; }

        .party { margin: 14px 0 10px 0; font-size: 11px; }
        .party td { padding: 1px 0; vertical-align: top; }
        .party .lbl { text-align: right; padding-right: 4px; white-space: nowrap; }

        .stmt { width: 100%; border-collapse: collapse; }
        .stmt th, .stmt td { border: 0.8px solid #000; padding: 4px 6px; font-size: 11px; }
        .stmt th { font-style: italic; font-weight: normal; text-align: center; }
        .stmt td.num { text-align: right; }
        .stmt td.dt  { text-align: center; white-space: nowrap; }
    </style>
</head>

<body>

@php
    $money = fn ($v) => currencySymbol() . make2decimal($v);
    $day   = fn ($d) => $d ? date('d/m/Y', strtotime($d)) : '';
@endphp

<div class="center">
    <div class="company">{{ config('site_title') ?? config('app.name') }}</div>
    @if(config('store_address'))
        <div class="addr">{{ config('store_address') }}</div>
    @endif
    <div class="title">{{ __('custom.statement_of_account') }}</div>
    <div class="range">
        {{ __('custom.statement_from') }} {{ $day($ledger['from']) }} -{{ __('custom.to') }}- {{ $day($ledger['to']) }}
    </div>
</div>

<table class="party">
    <tr>
        <td class="lbl">{{ __('custom.customer_name') }} :</td>
        <td>{{ $ledger['customer']->full_name }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ __('custom.address') }} :</td>
        <td>{{ $ledger['customer']->address_line_1 }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ __('custom.telephone') }} :</td>
        <td>{{ $ledger['customer']->phone }}</td>
    </tr>
</table>

<table class="stmt">
    <tr>
        <th width="12%">{{ __('custom.date') }}</th>
        <th width="40%">{{ __('custom.description') }}</th>
        <th width="16%">{{ __('custom.debit') }}</th>
        <th width="16%">{{ __('custom.credit') }}</th>
        <th width="16%">{{ __('custom.balance') }}</th>
    </tr>

    <tr>
        <td class="dt">/ /</td>
        <td>{{ __('custom.opening_balance') }}</td>
        <td class="num">{{ $money(0) }}</td>
        <td class="num">{{ $money(0) }}</td>
        <td class="num">{{ $money($ledger['opening_due']) }}</td>
    </tr>

    @foreach($ledger['rows'] as $row)
        <tr>
            <td class="dt">{{ $day($row['date']) }}</td>
            <td>{{ $row['description'] }}</td>
            <td class="num">{{ $money($row['debit']) }}</td>
            <td class="num">{{ $money($row['credit']) }}</td>
            <td class="num">{{ $money($row['running_due']) }}</td>
        </tr>
    @endforeach
</table>

@if($autoPrint ?? false)
    {{-- Only when opened in a browser tab for printing; mPDF never sets this. --}}
    <script>window.addEventListener('load', function () { window.print(); });</script>
@endif

</body>

</html>
