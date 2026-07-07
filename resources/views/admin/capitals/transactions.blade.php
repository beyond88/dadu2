@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('custom.capital_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.transactions') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.capitals.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('custom.back') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.capitals.transactions') }}">
                <div class="row">
                    <div class="col-md-3">
                        <select name="capital_id" class="form-control">
                            <option value="">{{ __('custom.all_capitals') }}</option>
                            @foreach($capitals as $capital)
                                <option value="{{ $capital->id }}" {{ request('capital_id') == $capital->id ? 'selected' : '' }}>
                                    {{ $capital->capital_no }} - {{ $capital->investor_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="account_id" class="form-control">
                            <option value="">{{ __('custom.all_accounts') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control" placeholder="{{ __('custom.from_date') }}" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" placeholder="{{ __('custom.to_date') }}" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">{{ __('custom.filter') }}</button>
                        <a href="{{ route('admin.capitals.transactions') }}" class="btn btn-secondary">{{ __('custom.reset') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Transactions List --}}
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ __('custom.capital_transactions') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('custom.date') }}</th>
                            <th>{{ __('custom.capital_no') }}</th>
                            <th>{{ __('custom.investor_name') }}</th>
                            <th>{{ __('custom.account') }}</th>
                            <th>{{ __('custom.amount') }}</th>
                            <th>{{ __('custom.payment_method') }}</th>
                            <th>{{ __('custom.reference_no') }}</th>
                            <th>{{ __('custom.note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->payment_date->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.capitals.show', $transaction->capital_id) }}">
                                        {{ $transaction->capital->capital_no ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $transaction->capital->investor_name ?? '-' }}</td>
                                <td>{{ $transaction->account->name ?? '-' }}</td>
                                <td>{{ currencySymbol() }} {{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                                <td>{{ $transaction->reference_no ?? '-' }}</td>
                                <td>{{ $transaction->note ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('custom.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
