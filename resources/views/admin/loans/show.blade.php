@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.loan_details') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.loans.index') }}">{{ __('custom.loan_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ $loan->loan_no }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                @can('Loan Edit')
                <a href="{{ route('admin.loans.edit', $loan->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> {{ __('custom.edit') }}
                </a>
                @endcan
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary ml-1">
                    <i class="mdi mdi-arrow-left"></i> {{ __('custom.back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Loan Details --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                    <h5 class="mb-0 text-white"><i class="mdi mdi-bank-transfer"></i> {{ __('custom.loan_info') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">{{ __('custom.loan_no') }}</td>
                            <td><strong>{{ $loan->loan_no }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('custom.borrower_name') }}</td>
                            <td><strong>{{ $loan->borrower_name }}</strong></td>
                        </tr>
                        @if($loan->borrower_phone)
                        <tr>
                            <td class="text-muted">{{ __('custom.phone') }}</td>
                            <td>{{ $loan->borrower_phone }}</td>
                        </tr>
                        @endif
                        @if($loan->borrower_address)
                        <tr>
                            <td class="text-muted">{{ __('custom.address') }}</td>
                            <td>{{ $loan->borrower_address }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">{{ __('custom.loan_date') }}</td>
                            <td>{{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('custom.due_date') }}</td>
                            <td>
                                @if($loan->due_date)
                                    <span class="{{ $loan->due_date->isPast() && $loan->status !== 'fully_paid' ? 'text-danger font-weight-bold' : '' }}">
                                        {{ $loan->due_date->format('d M Y') }}
                                        @if($loan->due_date->isPast() && $loan->status !== 'fully_paid')
                                            <span class="badge badge-danger ml-1">{{ __('custom.overdue') }}</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('custom.status') }}</td>
                            <td><span class="badge {{ $loan->status_badge }}">{{ \App\Models\Loan::getStatuses()[$loan->status] ?? $loan->status }}</span></td>
                        </tr>
                        @if($loan->note)
                        <tr>
                            <td class="text-muted">{{ __('custom.note') }}</td>
                            <td>{{ $loan->note }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">{{ __('custom.created_by') }}</td>
                            <td>{{ $loan->creator->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Balance Summary --}}
            <div class="card">
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-4">
                            <p class="text-muted mb-0" style="font-size:11px;">{{ __('custom.opening_balance') }}</p>
                            <h5 class="text-primary">{{ currencySymbol() }}{{ number_format($loan->opening_balance, 2) }}</h5>
                        </div>
                        <div class="col-4">
                            <p class="text-muted mb-0" style="font-size:11px;">{{ __('custom.paid') }}</p>
                            <h5 class="text-success">{{ currencySymbol() }}{{ number_format($loan->paid_amount, 2) }}</h5>
                        </div>
                        <div class="col-4">
                            <p class="text-muted mb-0" style="font-size:11px;">{{ __('custom.remaining') }}</p>
                            <h5 class="{{ $loan->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ currencySymbol() }}{{ number_format($loan->remaining_amount, 2) }}
                            </h5>
                        </div>
                    </div>
                    @if($loan->total_amount > 0)
                    <div class="mt-2">
                        @php
                            $paidPercent = ($loan->paid_amount / $loan->total_amount) * 100;
                        @endphp
                        <div class="progress" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ min($paidPercent, 100) }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($paidPercent, 1) }}% {{ __('custom.paid') }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Add Payment Form --}}
            @can('Loan Payment')
            @if($loan->status !== 'fully_paid')
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="mdi mdi-cash-plus"></i> {{ __('custom.add_payment') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.loans.payment.store', $loan->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.account') }} <span class="text-danger">*</span></label>
                                    <select name="account_id" class="form-control @error('account_id') is-invalid @enderror" required>
                                        <option value="">-- {{ __('custom.select_account') }} --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ currencySymbol() }}{{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.amount') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ currencySymbol() }}</span>
                                        </div>
                                        <input type="number" name="amount" value="{{ old('amount', $loan->remaining_amount) }}"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            step="0.01" min="0.01" max="{{ $loan->remaining_amount }}" required>
                                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <small class="text-muted">{{ __('custom.max') }}: {{ currencySymbol() }}{{ number_format($loan->remaining_amount, 2) }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.payment_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}"
                                        class="form-control @error('payment_date') is-invalid @enderror" required>
                                    @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.payment_method') }}</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="cash">{{ __('custom.cash') }}</option>
                                        <option value="bank_transfer">{{ __('custom.bank') }}</option>
                                        <option value="mobile_banking">{{ __('custom.mobile_banking') }}</option>
                                        <option value="cheque">{{ __('custom.cheque') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.reference_no') }}</label>
                                    <input type="text" name="reference_no" value="{{ old('reference_no') }}"
                                        class="form-control" placeholder="{{ __('custom.reference_no') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('custom.note') }}</label>
                                    <input type="text" name="note" value="{{ old('note') }}"
                                        class="form-control" placeholder="{{ __('custom.note') }}">
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-cash-plus"></i> {{ __('custom.add_payment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            @endcan

            {{-- Payment History --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="mdi mdi-history"></i> {{ __('custom.payment_history') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('custom.payment_date') }}</th>
                                    <th>{{ __('custom.amount') }}</th>
                                    <th>{{ __('custom.account') }}</th>
                                    <th>{{ __('custom.payment_method') }}</th>
                                    <th>{{ __('custom.reference_no') }}</th>
                                    <th>{{ __('custom.note') }}</th>
                                    <th>{{ __('custom.created_by') }}</th>
                                    @can('Loan Payment Delete')
                                    <th>{{ __('custom.action') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loan->payments as $i => $payment)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</td>
                                    <td class="text-right">
                                        <strong class="text-success">{{ currencySymbol() }}{{ number_format($payment->amount, 2) }}</strong>
                                    </td>
                                    <td>{{ $payment->account->name ?? '-' }}</td>
                                    <td><span class="badge badge-secondary">{{ $payment->payment_method }}</span></td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                    <td>{{ $payment->note ?? '-' }}</td>
                                    <td>{{ $payment->creator->name ?? '-' }}</td>
                                    @can('Loan Payment Delete')
                                    <td>
                                        <form action="{{ route('admin.loans.payment.destroy', [$loan->id, $payment->id]) }}" method="POST"
                                            onsubmit="return confirm('{{ __('custom.are_you_sure') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endcan
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">
                                        {{ __('custom.no_payments_yet') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
