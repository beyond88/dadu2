@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.loan_transaction_history') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.loans.index') }}">{{ __('custom.loan_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.transaction_history') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> {{ __('custom.back') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.loans.transactions') }}" method="GET" class="row align-items-end">
                        <div class="col-md-3">
                            <label>{{ __('custom.select_loan') }}</label>
                            <select name="loan_id" class="form-control">
                                <option value="">-- {{ __('custom.all') }} --</option>
                                @foreach($loans as $loan)
                                    <option value="{{ $loan->id }}" {{ request('loan_id') == $loan->id ? 'selected' : '' }}>
                                        {{ $loan->loan_no }} - {{ $loan->borrower_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>{{ __('custom.account') }}</label>
                            <select name="account_id" class="form-control">
                                <option value="">-- {{ __('custom.all') }} --</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>{{ __('custom.from_date') }}</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>{{ __('custom.to_date') }}</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="mdi mdi-filter"></i> {{ __('custom.filter') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <strong>{{ __('custom.total_paid_in_filter') }}:</strong>
                        {{ currencySymbol() }}{{ number_format($payments->sum('amount'), 2) }}
                        &nbsp;|&nbsp;
                        <strong>{{ __('custom.records') }}:</strong> {{ $payments->total() }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('custom.loan_no') }}</th>
                                    <th>{{ __('custom.borrower_name') }}</th>
                                    <th>{{ __('custom.amount') }}</th>
                                    <th>{{ __('custom.account') }}</th>
                                    <th>{{ __('custom.payment_method') }}</th>
                                    <th>{{ __('custom.payment_date') }}</th>
                                    <th>{{ __('custom.reference_no') }}</th>
                                    <th>{{ __('custom.note') }}</th>
                                    <th>{{ __('custom.created_by') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $i => $payment)
                                <tr>
                                    <td>{{ $payments->firstItem() + $i }}</td>
                                    <td>
                                        <a href="{{ route('admin.loans.show', $payment->loan_id) }}" class="font-weight-bold text-primary">
                                            {{ $payment->loan->loan_no ?? '-' }}
                                        </a>
                                    </td>
                                    <td>{{ $payment->loan->borrower_name ?? '-' }}</td>
                                    <td class="text-right">
                                        <strong class="text-success">{{ currencySymbol() }}{{ number_format($payment->amount, 2) }}</strong>
                                    </td>
                                    <td>{{ $payment->account->name ?? '-' }}</td>
                                    <td><span class="badge badge-secondary">{{ $payment->payment_method }}</span></td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                    <td>{{ $payment->note ?? '-' }}</td>
                                    <td>{{ $payment->creator->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="mdi mdi-information-outline" style="font-size:2rem;"></i><br>
                                        {{ __('custom.no_data_found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
