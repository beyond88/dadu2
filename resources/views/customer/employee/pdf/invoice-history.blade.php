<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ static_asset('admin/css/pdf-style.css') }}" rel="stylesheet" type="text/css" />
    <title>{{ __('custom.invoice_history') }}</title>
</head>

<body>
    <p><b>{{ __('custom.employee') }}:</b> {{ $customer->full_name }}</p>
    <p><b>{{ __('custom.invoice_history') }}</b></p>
    
    <table class="ic-main-table" width="100%" cellpadding="0" cellspacing="0" border="1" >
        <thead>
            <tr>
                <th class="ic-table-td">{{ __('custom.sl') }}</th>
                <th class="ic-table-td">{{ __('custom.invoice_id') }}</th>
                <th class="ic-table-td">{{ __('custom.date') }}</th>
                <th class="ic-table-td">{{ __('custom.total') }}</th>
                <th class="ic-table-td">{{ __('custom.total_paid') }}</th>
                <th class="ic-table-td">{{ __('custom.due') }}</th>
                <th class="ic-table-td">{{ __('custom.status') }}</th>
                <th class="ic-table-td">{{ __('custom.delivery_status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td class="ic-table-td">{{ ++$loop->index }}</td>
                    <td class="ic-table-td">{{ make8digits($invoice->id) }}</td>
                    <td class="ic-table-td">
                        {{ date(config('date_formate'), strtotime($invoice->date)) }}
                    </td>
                    <td class="ic-table-td">{{ currencySymbol() . ' ' . $invoice->total }}</td>
                    <td class="ic-table-td">{{ currencySymbol() . ' ' . $invoice->total_paid }}</td>
                    <td class="ic-table-td">{{ currencySymbol() . ' ' . ($invoice->total - $invoice->total_paid) }}</td>
                    <td class="ic-table-td">{{ \Illuminate\Support\Str::upper($invoice->status) }}</td>
                    <td class="ic-table-td">{{ \Illuminate\Support\Str::upper($invoice->delivery_status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
