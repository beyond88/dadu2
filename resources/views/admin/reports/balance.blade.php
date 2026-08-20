@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.balance_report') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.balance_report') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.category_wise_balance') }}</h4>
                    <div class="row">
                        <div class="col-md-9 col-lg-10">
                            <form action="{{ route('admin.reports.balance') }}">
                                <div class="row input-daterange">
                                    <div class="col-md-4 col-lg-4 mb-2">
                                        <div class="form-group mb-lg-0">
                                            <input type="text" name="from_date" value="{{ request()->from_date }}"
                                                   id="from_date" class="form-control" placeholder="From Date"
                                                   autocomplete="off" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 mb-2">
                                        <div class="form-group mb-lg-0">
                                            <input type="text" name="to_date" value="{{ request('to_date', now()->toDateString()) }}"
                                                   id="to_date"
                                                   class="form-control" placeholder="To Date" autocomplete="off"
                                                   required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="mdi mdi-filter"></i> {{ __('custom.generate') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br>

                    @if ($report_range)
                        <div class="text-right">
                            <hr>
                            <button type="button" data-div-name="section-to-print-balance"
                                    class="btn btn-warning btn-sm section-print-btn"><i class="fa fa-print"></i> {{ __('custom.print') }}</button>
                            <a href="{{ route('admin.reports.export.balance', ['type'=>'pdf', 'from_date'=>request()->from_date, 'to_date'=>request()->to_date]) }}"
                               class="btn btn-pdf btn-sm"> <i class="fa fa-file-pdf"></i> {{ __('custom.pdf') }}</a>
                            <a href="{{ route('admin.reports.export.balance', ['type'=>'csv', 'from_date'=>request()->from_date, 'to_date'=>request()->to_date]) }}"
                               class="btn btn-success btn-sm"> <i class="fa fa-file-csv"></i> {{ __('custom.csv') }}</a>
                            <a href="{{ route('admin.reports.export.balance', ['type'=>'excel', 'from_date'=>request()->from_date, 'to_date'=>request()->to_date]) }}"
                               class="btn btn-excel btn-sm"> <i class="fa fa-file-csv"></i> {{ __('custom.excel') }}</a>
                        </div>
                        <div id="section-to-print-balance">
                            <p class="mb-0"><b>{{ __('custom.balance_report') }}:</b> {{ $report_range ?? '' }}</p>
                            @php $colCount = count($categories) + 1; @endphp
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap balance-report-table mb-0">
                                    <thead>
                                    {{-- Title banner --}}
                                    <tr>
                                        <th colspan="{{ $colCount }}" class="text-center balance-title">
                                            {{ __('custom.balance_report_heading') }}
                                        </th>
                                    </tr>
                                    {{-- Column header --}}
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
                                                <td colspan="{{ $colCount }}" class="text-center balance-month-banner">
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

                                    {{-- Ledger rows pulled from Expenses (Salary, Bonus, EXP., REP. ...) --}}
                                    @foreach ($expenseRows as $expense)
                                        <tr>
                                            <td>{{ $expense['name'] }}</td>
                                            <td colspan="{{ $colCount - 1 }}" class="text-right">
                                                {{ $expense['total'] > 0 ? number_format($expense['total'], 0) : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    {{-- Total --}}
                                    <tr>
                                        <th class="text-right">{{ __('custom.total') }} :</th>
                                        @foreach ($categories as $category)
                                            <th class="text-right">{{ number_format($categoryTotals[$category->id] ?? 0, 0) }}</th>
                                        @endforeach
                                    </tr>
                                    {{-- AVG. label row --}}
                                    <tr>
                                        <th class="text-right balance-avg" colspan="{{ $colCount }}">{{ __('custom.avg') }}:</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <style>
        .balance-report-table {
            font-size: 12px;
            border-color: #000 !important;
        }
        .balance-report-table th,
        .balance-report-table td {
            border: 1px solid #000 !important;
            padding: 3px 6px !important;
            vertical-align: middle;
        }
        .balance-title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .balance-month-banner {
            font-weight: 700;
            letter-spacing: 4px;
        }
        .balance-avg {
            color: #d00;
            font-weight: 700;
        }
    </style>
@endpush

@push('script')

@endpush
