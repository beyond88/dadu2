@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.warehouse_stock_report') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.warehouse') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('custom.warehouse_stock_report') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.warehouse_stock_report') }}</h4>

                    {{-- FILTER SECTION --}}
                    <form action="{{ route('admin.report.warehouse-stock') }}" id="warehouseStockFilterForm">
                        <div class="row input-daterange">
                            <div class="col-md-6 col-lg-3 mb-2">
                                <div class="form-group mb-lg-0">
                                    <select name="warehouse" id="reportPurchaseWarehouses" class="form-control" required>
                                        <option value="">{{ __('custom.select_warehouse') }}</option>
                                        <option value="all" {{ request('warehouse') === 'all' ? 'selected' : '' }}>
                                            {{ __('custom.all') }}</option>
                                        @foreach($warehouses as $key => $value)
                                            <option {{ request('warehouse') == $key ? 'selected' : '' }}
                                                value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 mb-2">
                                <div class="form-group mb-lg-0">
                                    <input type="text" name="from_date" value="{{ $fromDate }}"
                                           id="from_date" class="form-control" placeholder="From Date"
                                           autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 mb-2">
                                <div class="form-group mb-lg-0">
                                    {{-- To date always defaults to today --}}
                                    <input type="text" name="to_date" value="{{ $toDate }}"
                                           id="to_date" class="form-control" placeholder="To Date"
                                           autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 mb-2">
                                {{-- Page size lives with the bottom pagination bar --}}
                                <input type="hidden" name="per_page" id="perPageInput" value="{{ $perPage }}">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-filter"></i> {{ __('custom.generate') }}</button>
                            </div>
                        </div>
                    </form>

                    <br>

                    @if($histories)
                        <div class="text-right">
                            <hr>
                            <button type="button" data-div-name="section-to-print-warehouse-stock"
                                    class="btn btn-warning btn-sm section-print-btn"><i class="fa fa-print"></i>
                                {{ __('custom.print') }}</button>
                        </div>

                        <div id="section-to-print-warehouse-stock">
                            <p class="mb-0">
                                <b>{{ __('custom.warehouse') }}:</b> {{ $selectedWarehouse ? $selectedWarehouse->name : __('custom.all') }}<br>
                                <b>{{ __('custom.warehouse_stock_report') }}:</b> {{ $report_range }}
                            </p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ __('custom.sl') }}</th>
                                        @if($allWarehouses)
                                            <th>{{ __('custom.warehouse') }}</th>
                                        @endif
                                        <th>{{ __('custom.product') }}</th>
                                        <th>{{ __('custom.type') }}</th>
                                        <th>{{ __('custom.action_from') }}</th>
                                        <th>{{ __('custom.quantity') }}</th>
                                        <th style="width:220px;">{{ __('custom.note') }}</th>
                                        <th>{{ __('custom.created_at') }}</th>
                                        <th>{{ __('custom.created_by') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($histories as $history)
                                        <tr>
                                            <td>{{ $histories->firstItem() + $loop->index }}</td>
                                            @if($allWarehouses)
                                                {{-- Names come from the same list that fills the dropdown, so no extra query per row. --}}
                                                <td>{{ $warehouses[$history->warehouse_id] ?? '-' }}</td>
                                            @endif
                                            <td>{{ optional($history->product)->name }}</td>
                                            <td>{{ $history->type == \App\Models\ProductStockHistory::TYPE_IN ? 'In' : 'Out' }}</td>
                                            <td>{!! stockHistoryActionBadge($history, true) !!}</td>
                                            <td>{{ $history->quantity }} {{ optional(optional($history->product)->weight_unit)->name ?? '' }}</td>
                                            @php
                                                $note      = $history->note ?? '';
                                                $noteLimit = 60;
                                            @endphp
                                            <td class="note-cell">
                                                <div class="note-body">
                                                    @if($note === '')
                                                        -
                                                    @elseif(mb_strlen($note) <= $noteLimit)
                                                        {{ $note }}
                                                    @else
                                                        <span class="note-short">{{ mb_substr($note, 0, $noteLimit) }}…</span>
                                                        <span class="note-full d-none">{{ $note }}</span>
                                                        <a href="#" class="note-toggle" style="font-size:.75rem;">{{ __('custom.see_more') }}</a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d-m-Y h:i A') : '' }}</td>
                                            <td>{{ optional($history->createdBy)->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $allWarehouses ? 9 : 8 }}" class="text-center">{{ __('custom.no_data_found') }}</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    @if($histories->count())
                                        <tfoot>
                                        <tr>
                                            {{-- Totals are for the whole filtered range, not just this page --}}
                                            <td colspan="{{ $allWarehouses ? 5 : 4 }}" class="text-right"><strong>{{ __('custom.total') }} (In / Out):</strong></td>
                                            <td colspan="4"><strong>{{ $totalIn }} / {{ $totalOut }}</strong></td>
                                        </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>

                        {{-- Bottom bar mirrors the datatable on the warehouse storage store/out page:
                             length menu + "showing x to y of z" on the left, pager on the right. --}}
                        @if($histories->total())
                            <div class="row align-items-center mt-2 dataTables_wrapper">
                                <div class="col-sm-12 col-md-6 d-flex flex-wrap align-items-center">
                                    <div class="dataTables_length">
                                        <label class="mb-0">{{ __('custom.show') }}
                                            <select id="perPageSelect"
                                                    class="custom-select custom-select-sm form-control form-control-sm d-inline-block w-auto mx-1">
                                                @foreach([25, 50, 100, 200, 500] as $size)
                                                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                                @endforeach
                                            </select>
                                            entries
                                        </label>
                                    </div>
                                    <div class="dataTables_info ml-3">
                                        Showing {{ $histories->firstItem() }} to {{ $histories->lastItem() }}
                                        of {{ $histories->total() }} entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers float-md-right">
                                        {!! $histories->links() !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
<style>
    .note-cell { max-width: 240px; }

    /* The inner box caps the width so long URLs wrap instead of running
       over the next column; each part sits on its own line. */
    .note-cell .note-body {
        max-width: 240px;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .note-cell .note-short,
    .note-cell .note-full,
    .note-cell .note-toggle { display: block; }

    @media print {
        /* Print the whole note, no truncation */
        .note-short, .note-toggle { display: none !important; }
        .note-full { display: block !important; }
    }
</style>
@endpush

@push('script')
<script>
    // Long notes stay collapsed until "See more" is clicked.
    $(document).on('click', '.note-toggle', function (e) {
        e.preventDefault();
        var $cell = $(this).closest('.note-cell');
        var expanded = $cell.find('.note-full').hasClass('d-none');

        $cell.find('.note-short').toggleClass('d-none', expanded);
        $cell.find('.note-full').toggleClass('d-none', !expanded);
        $(this).text(expanded ? '{{ __('custom.see_less') }}' : '{{ __('custom.see_more') }}');
    });

    // Switching warehouse reloads straight away — otherwise the table below
    // would keep showing the previously filtered warehouse's rows.
    $(document).on('change', '#reportPurchaseWarehouses', function () {
        if ($(this).val()) {
            $('#warehouseStockFilterForm').submit();
        } else {
            window.location = '{{ route('admin.report.warehouse-stock') }}';
        }
    });

    // Changing the page size re-runs the filter from page 1.
    $(document).on('change', '#perPageSelect', function () {
        $('#perPageInput').val($(this).val());
        $('#warehouseStockFilterForm').submit();
    });
</script>
@endpush
