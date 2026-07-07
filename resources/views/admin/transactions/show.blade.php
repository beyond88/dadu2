@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">{{ __('custom.transactions') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.transaction_details') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0" style="color: white">{{ __('custom.transaction_details') }}</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">{{ __('custom.transaction_id') }}</th>
                        <td>#{{ $transaction->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.account') }}</th>
                        <td>{{ $transaction->account->name ?? 'N/A' }}</td>
                    </tr>
                    @if($transaction->account && $transaction->account->type == 'bank')
                    <tr>
                        <th>{{ __('custom.account_number') }}</th>
                        <td>{{ $transaction->account->account_number ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.bank_name') }}</th>
                        <td>{{ $transaction->account->bank_name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.branch_name') }}</th>
                        <td>{{ $transaction->account->branch_name ?? '---' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>{{ __('custom.type') }}</th>
                        <td>
                            @php
                                $badgeClass = 'badge-info';
                                if(in_array($transaction->type, ['add', 'transfer_in', 'invoice_payment', 'due_collection', 'opening_balance'])) $badgeClass = 'badge-success';
                                elseif(in_array($transaction->type, ['reduce', 'transfer_out'])) $badgeClass = 'badge-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.amount') }}</th>
                        <td><strong>{{ number_format($transaction->amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.balance_after') }}</th>
                        <td>{{ number_format($transaction->balance_after, 2) }}</td>
                    </tr>
                    @if($transaction->from_account_id)
                    <tr>
                        <th>{{ __('custom.from_account') }}</th>
                        <td>{{ $transaction->fromAccount->name ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    @if($transaction->to_account_id)
                    <tr>
                        <th>{{ __('custom.to_account') }}</th>
                        <td>{{ $transaction->toAccount->name ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>{{ __('custom.date') }}</th>
                        <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.note') }}</th>
                        <td>{{ $transaction->note ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('custom.created_by') }}</th>
                        <td>{{ $transaction->creator->name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">{{ __('custom.back') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
