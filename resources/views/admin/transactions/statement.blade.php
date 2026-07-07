@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.account_statement') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">{{ __('custom.accounts') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.account_statement') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.accounts.show', $account) }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> {{ __('custom.back_to_account') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5 class="mt-0">{{ __('custom.account') }}: {{ $account->name }}</h5>
                            <p class="text-muted mb-0">{{ __('custom.current_balance') }}: <strong>{{ number_format($account->current_balance, 2) }}</strong></p>
                        </div>
                        <div class="col-md-8">
                            <form action="{{ route('admin.transactions.statement', $account) }}" method="GET" class="form-inline justify-content-end">
                                <div class="form-group mr-2">
                                    <label class="mr-2">{{ __('custom.from_date') }}</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label class="mr-2">{{ __('custom.to_date') }}</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label class="mr-2">{{ __('custom.type') }}</label>
                                    <select name="type" class="form-control">
                                        <option value="">{{ __('custom.all_types') }}</option>
                                        @foreach($transactionTypes as $type => $label)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter"></i> {{ __('custom.filter') }}</button>
                                <a href="{{ route('admin.transactions.statement', $account) }}" class="btn btn-secondary ml-1"><i class="mdi mdi-refresh"></i></a>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('custom.sl') }}</th>
                                    <th>{{ __('custom.date') }}</th>
                                    <th>{{ __('custom.type') }}</th>
                                    <th>{{ __('custom.reference') }}</th>
                                    <th>{{ __('custom.amount') }}</th>
                                    <th>{{ __('custom.balance_after') }}</th>
                                    <th>{{ __('custom.note') }}</th>
                                    <th>{{ __('custom.created_by') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                                        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $transaction->type == 'add' || $transaction->type == 'transfer_in' || $transaction->type == 'invoice_payment' || $transaction->type == 'due_collection' ? 'success' : 
                                                ($transaction->type == 'reduce' || $transaction->type == 'transfer_out' ? 'danger' : 'info')
                                            }}">
                                                {{ $transaction->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($transaction->reference_id && $transaction->reference_type)
                                                <small class="text-muted">{{ strtoupper($transaction->reference_type) }} #{{ $transaction->reference_id }}</small>
                                            @elseif($transaction->type == 'transfer_out' && $transaction->toAccount)
                                                <small class="text-muted">{{ __('custom.to_account') }}: {{ $transaction->toAccount->name }}</small>
                                            @elseif($transaction->type == 'transfer_in' && $transaction->fromAccount)
                                                <small class="text-muted">{{ __('custom.from_account') }}: {{ $transaction->fromAccount->name }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <span class="{{ $transaction->amount > 0 && ($transaction->type == 'add' || $transaction->type == 'transfer_in' || $transaction->type == 'invoice_payment') ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-right"><strong>{{ number_format($transaction->balance_after, 2) }}</strong></td>
                                        <td>{{ $transaction->note ?: '-' }}</td>
                                        <td>{{ $transaction->creator?->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('custom.no_transactions_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
