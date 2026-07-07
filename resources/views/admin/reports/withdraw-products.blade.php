@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.withdrawal_product_report') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.withdrawal_product_report') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.withdrawal_product_report') }}</h4>

                    {{-- FILTER SECTION --}}
                    <div class="row">
                        <div class="col-sm-10">
                            <form action="{{ route('admin.report.withdraw-products') }}">
                                <div class="row input-daterange">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group mb-lg-0">

                                         <select name="customer[]"  class="form-control select2" multiple="multiple">
                                            <option value="" disabled>{{ __('custom.select_employee') }}</option>
                                            @foreach($allCustomers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ collect(request('customer'))->contains($customer->id) ? 'selected' : '' }}>
                                                    {{ $customer->full_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-group mb-lg-0">
                                            <input type="text" name="from_date" value="{{ request()->from_date }}"
                                                   id="from_date" class="form-control" placeholder="{{ __('custom.from_date') }}"
                                                   autocomplete="off" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-group mb-lg-0">
                                            <input type="text" name="to_date" value="{{ request()->to_date }}"
                                                   id="to_date"
                                                   class="form-control" placeholder="{{ __('custom.to_date') }}" autocomplete="off"
                                                   required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3 col-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="mdi mdi-filter"></i> {{ __('custom.generate') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-sm-2">
                            <form action="{{ route('admin.report.withdraw-products') }}">
                                <div class="input-daterange">
                                    <input type="hidden" name="customer" value="{{ is_array(request('customer')) ? implode(',', request('customer')) : request('customer') }}">
                                    <input type="hidden" name="q" value="all-time">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="mdi mdi-filter"></i> {{ __('custom.all_time') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <br>

                    {{-- REPORT TABLE --}}

                    @php
                        $exportParams = [
                            'type' => '',
                            'from_date' => request()->from_date,
                            'to_date' => request()->to_date,
                            'customer' => request()->customer,
                        ];
                    @endphp
                    <div class="text-right">
                        <hr>
                        {{-- <button type="button" data-div-name="section-to-print-sales"
                                class="btn btn-warning btn-sm section-print-btn"><i class="fa fa-print"></i> {{
                    __('custom.print') }}</button> --}}
                       <a href="{{ route('admin.report.withdraw-products', array_merge($exportParams, ['type' => 'pdf'])) }}"
                        class="btn btn-pdf btn-sm">
                            <i class="fa fa-file-pdf"></i> {{ __('custom.pdf') }}
                        </a>

                        <a href="{{ route('admin.report.withdraw-products', array_merge($exportParams, ['type' => 'csv'])) }}"
                        class="btn btn-success btn-sm">
                            <i class="fa fa-file-csv"></i> {{ __('custom.csv') }}
                        </a>

                        <a href="{{ route('admin.report.withdraw-products', array_merge($exportParams, ['type' => 'excel'])) }}"
                        class="btn btn-excel btn-sm">
                            <i class="fa fa-file-excel"></i> {{ __('custom.excel') }}
                        </a>
                    </div>

                        <div id="section-to-print-purchases">
                            <p class="mb-0"><b>{{ __('custom.withdrawal_product_report') }}:</b> {{ $report_range ?? '' }}</p>
                            <div class="table-responsive">
                                 {{-- Report Table --}}
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('custom.sl') }}</th>
                                            <th>{{ __('custom.employee') }}</th>
                                            <th>{{ __('custom.company') }}</th>
                                            <th>{{ __('custom.item') }}</th>
                                            <th>{{ __('custom.quantity') }}</th>
                                            <th>{{ __('custom.withdraw_date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($groupedWithdrawals as $withdrawal)
                                            @php
                                                $employee = $customers[$withdrawal->customer_id] ?? null;
                                                $item = $products[$withdrawal->product_id] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $employee?->full_name ?? '-' }}</td>
                                                <td>{{ $employee?->company?->name ?? '-' }}</td>
                                                <td>{{ $item?->name ?? '-' }}</td>
                                                <td>{{ $withdrawal->total_quantity }}</td>
                                                <td>{{ formatDynamicDateTime($withdrawal->withdraw_date) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No data found for selected filters.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </div>
                        </div>


                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
@endpush


@push('script')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "{{ __('custom.select_employees') }}",
            width: '100%' // Optional: ensures full width
        });
    });
</script>
@endpush


