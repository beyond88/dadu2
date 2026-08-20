@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.customer_ledger_report') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.customer_ledger_report') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- Filters --}}
                    <form action="{{ route('admin.report.customer-ledger') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.customer') }} <span class="text-danger">*</span></label>
                                    <select name="customer_id" class="form-control select2" required>
                                        <option value="">{{ __('custom.select_customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->full_name }}{{ $customer->phone ? ' — ' . $customer->phone : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('custom.from_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" required>
                                    @error('from_date') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('custom.to_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->toDateString()) }}" required>
                                    @error('to_date') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="mdi mdi-filter"></i> {{ __('custom.generate') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(!$ledger)
                        <hr>
                        <p class="text-muted mb-0">{{ __('custom.select_a_customer_and_date_range') }}</p>
                    @else
                        @php
                            $exportParams = [
                                'customer_id' => $ledger['customer']->id,
                                'from_date'   => $ledger['from'],
                                'to_date'     => $ledger['to'],
                            ];
                        @endphp
                        <div class="text-right">
                            <hr>
                            {{-- Both open in a new tab and use the statement layout, not the screen markup. --}}
                            <a href="{{ route('admin.report.customer-ledger.export', $exportParams + ['print' => 1]) }}"
                               target="_blank" rel="noopener" class="btn btn-warning btn-sm">
                                <i class="fa fa-print"></i> {{ __('custom.print') }}
                            </a>
                            <a href="{{ route('admin.report.customer-ledger.export', $exportParams) }}"
                               target="_blank" rel="noopener" class="btn btn-pdf btn-sm">
                                <i class="fa fa-file-pdf"></i> {{ __('custom.pdf') }}
                            </a>
                        </div>

                        <div>
                            {{-- Statement header --}}
                            <div class="text-center mb-3">
                                <h4 class="mb-1">{{ config('site_title') ?? config('app.name') }}</h4>
                                @if(config('store_address'))
                                    <p class="mb-1">{{ config('store_address') }}</p>
                                @endif
                                <p class="mb-1"><strong>{{ __('custom.statement_of_account') }}</strong></p>
                                <p class="mb-0">
                                    {{ __('custom.statement_from_to', ['from' => custom_date($ledger['from']), 'to' => custom_date($ledger['to'])]) }}
                                </p>
                            </div>

                            <p class="mb-0"><b>{{ __('custom.customer') }}:</b> {{ $ledger['customer']->full_name }}</p>
                            @if($ledger['customer']->address_line_1)
                                <p class="mb-0"><b>{{ __('custom.address') }}:</b> {{ $ledger['customer']->address_line_1 }}</p>
                            @endif
                            @if($ledger['customer']->phone)
                                <p class="mb-2"><b>{{ __('custom.phone') }}:</b> {{ $ledger['customer']->phone }}</p>
                            @endif

                            {{-- Summary --}}
                            <div class="row mb-2">
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="card mb-0"><div class="card-body p-2">
                                        <p class="text-muted mb-1 font-12">{{ __('custom.opening_balance') }}</p>
                                        <h5 class="mb-0">{{ currencySymbol() . make2decimal($ledger['opening_balance']) }}</h5>
                                    </div></div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="card mb-0"><div class="card-body p-2">
                                        <p class="text-muted mb-1 font-12">{{ __('custom.total_invoiced') }}</p>
                                        <h5 class="mb-0">{{ currencySymbol() . make2decimal($ledger['total_invoiced']) }}</h5>
                                    </div></div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="card mb-0"><div class="card-body p-2">
                                        <p class="text-muted mb-1 font-12">{{ __('custom.total_paid') }}</p>
                                        <h5 class="mb-0 text-success">{{ currencySymbol() . make2decimal($ledger['total_paid']) }}</h5>
                                    </div></div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="card mb-0"><div class="card-body p-2">
                                        <p class="text-muted mb-1 font-12">{{ __('custom.total_due') }}</p>
                                        <h5 class="mb-0 text-danger">{{ currencySymbol() . make2decimal($ledger['closing_due']) }}</h5>
                                    </div></div>
                                </div>
                            </div>

                            {{-- Ledger --}}
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('custom.date') }}</th>
                                            <th>{{ __('custom.description') }}</th>
                                            <th class="text-right">{{ __('custom.debit') }}</th>
                                            <th class="text-right">{{ __('custom.credit') }}</th>
                                            <th class="text-right">{{ __('custom.balance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($rows->onFirstPage())
                                            <tr>
                                                <td class="text-center">/ /</td>
                                                <td>{{ __('custom.opening_balance') }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal(0) }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal(0) }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($ledger['opening_due']) }}</td>
                                            </tr>
                                        @endif

                                        @forelse($rows as $row)
                                            <tr>
                                                <td class="text-center">{{ date('d/m/Y', strtotime($row['date'])) }}</td>
                                                <td>{{ $row['description'] }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($row['debit']) }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($row['credit']) }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($row['running_due']) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('custom.no_transactions_in_range') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($rows->onLastPage() && $rows->count())
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="2" class="text-right">{{ __('custom.total') }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($ledger['total_invoiced']) }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($ledger['total_paid']) }}</td>
                                                <td class="text-right">{{ currencySymbol() . make2decimal($ledger['closing_due']) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($rows->hasPages())
                            <div class="mt-2">{{ $rows->links() }}</div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
