@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.stock_change_over_period_report') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.stock_change_over_period_report') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                        <h4 class="mt-0 header-title">{{ __('custom.stock_change_over_period_report') }}</h4>

                        <div class="row">
                            <div class="col-sm-9">
                                <form action="{{ route('admin.report.stock-change-over-period') }}">
                                    <div class="row input-daterange">
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group mb-lg-0">
                                                <select name="warehouse" class="form-control">
                                                    <option value="">{{ __('custom.all_warehouses') }}</option>
                                                    @foreach($warehouses as $id => $name)
                                                        <option value="{{ $id }}" {{ request()->warehouse == $id ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group mb-lg-0">
                                                <input type="text" name="from_date" value="{{ request()->from_date }}"
                                                    id="from_date" class="form-control" placeholder="{{ __('custom.from_date') }}"
                                                    autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group mb-lg-0">
                                                <input type="text" name="to_date" value="{{ request()->to_date }}" id="to_date"
                                                    class="form-control" placeholder="{{ __('custom.to_date') }}" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="mdi mdi-filter"></i> {{ __('custom.generate') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-sm-3">
                                <form action="{{ route('admin.report.stock-change-over-period') }}">
                                    <div class="input-daterange">
                                        <input type="hidden" name="q" value="all-time">
                                        @if(request()->warehouse)
                                            <input type="hidden" name="warehouse" value="{{ request()->warehouse }}">
                                        @endif

                                        <button type="submit" class="btn btn-secondary w-100">
                                            <i class="mdi mdi-filter"></i> {{ __('custom.all_time') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br>

                    {{-- REPORT TABLE --}}

                    <div class="text-right">
                        <hr>
                        @php
                            $exportParams = [
                                'type' => '',
                                'from_date' => request()->from_date,
                                'to_date' => request()->to_date,
                                'warehouse' => request()->warehouse,
                            ];
                        @endphp
                        <a href="{{ route('admin.report.stock-change-over-period', array_merge($exportParams, ['type' => 'pdf'])) }}"
                           class="btn btn-pdf btn-sm"> <i class="fa fa-file-pdf"></i> {{ __('custom.pdf') }}</a>
                        <a href="{{ route('admin.report.stock-change-over-period', array_merge($exportParams, ['type' => 'csv'])) }}"
                           class="btn btn-success btn-sm"> <i class="fa fa-file-csv"></i> {{ __('custom.csv') }}</a>
                            <a href="{{ route('admin.report.stock-change-over-period', array_merge($exportParams, ['type' => 'excel'])) }}"
                           class="btn btn-excel btn-sm"> <i class="fa fa-file-excel"></i> {{ __('custom.excel') }}</a>
                    </div>

                        <div id="section-to-print-purchases">
                            <p class="mb-0"><b>{{ __('custom.stock_change_over_period_report') }}:</b> {{ $report_range ?? '' }}</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('custom.sl') }}</th>

                                            <th>{{ __('custom.product') }}</th>
                                            <th>{{ __('custom.sku') }}</th>
                                            <th>{{ __('custom.warehouse') }}</th>
                                            <th>{{ __('custom.start_qty') }}</th>
                                            <th>{{ __('custom.inbound') }}</th>
                                            <th>{{ __('custom.outbound') }}</th>
                                            <th>{{ __('custom.end_qty') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stockSummary as $summary)
                                            <tr>
                                                <td><strong>{{ $loop->iteration }}</strong></td>
                                                <td><strong>{{ $summary['product'] }}</strong></td>
                                                <td><strong>{{ $summary['sku'] }}</strong></td>
                                                <td><strong>{{ $summary['warehouse'] }}</strong></td>
                                                <td class="text-right"><strong>{{ $summary['start_quantity'] }}</strong></td>
                                                <td class="text-right text-success"><strong>{{ $summary['total_inbound'] }}</strong></td>
                                                <td class="text-right text-danger"><strong>{{ $summary['total_outbound'] }}</strong></td>
                                                <td class="text-right"><strong>{{ $summary['end_quantity'] }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">{{ __('custom.no_data_found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <br>


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
@endpush

@push('script')
@endpush
