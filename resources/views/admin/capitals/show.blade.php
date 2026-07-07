@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('custom.capital_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.capital_details') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.capitals.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('custom.back') }}
                </a>
                <a href="{{ route('admin.capitals.edit', $capital->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> {{ __('custom.edit') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Capital Details Card --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('custom.capital_details') }} - {{ $capital->capital_no }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">{{ __('custom.investor_name') }}:</th>
                            <td>{{ $capital->investor_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.investor_phone') }}:</th>
                            <td>{{ $capital->investor_phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.investor_address') }}:</th>
                            <td>{{ $capital->investor_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.total_amount') }}:</th>
                            <td><strong>{{ currencySymbol() }} {{ number_format($capital->total_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.paid_amount') }}:</th>
                            <td class="text-success">{{ currencySymbol() }} {{ number_format($capital->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.remaining_amount') }}:</th>
                            <td class="text-danger"><strong>{{ currencySymbol() }} {{ number_format($capital->remaining_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.capital_date') }}:</th>
                            <td>{{ $capital->capital_date->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.due_date') }}:</th>
                            <td>{{ $capital->due_date ? $capital->due_date->format('d-m-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.status') }}:</th>
                            <td>
                                <span class="badge {{ $capital->status_badge }}">
                                    {{ \App\Models\Capital::getStatuses()[$capital->status] ?? $capital->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('custom.note') }}:</th>
                            <td>{{ $capital->note ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ __('custom.payment_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 25px;">
                        @php
                            $percentage = $capital->total_amount > 0 ? ($capital->paid_amount / $capital->total_amount) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                    <div class="text-center">
                        <h4>{{ currencySymbol() }} {{ number_format($capital->paid_amount, 2) }} / {{ currencySymbol() }} {{ number_format($capital->total_amount, 2) }}</h4>
                        <p class="text-muted">{{ __('custom.paid_percentage') }}</p>
                    </div>
                </div>
            </div>

            {{-- Add Payment Form --}}
            @if(request('action') == 'make_payment' && $capital->remaining_amount > 0 && $capital->status != 'fully_paid')
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">{{ __('custom.add_payment') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.capitals.payment.store', $capital->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="account_id">{{ __('custom.from_account') }} <span class="text-danger">*</span></label>
                                <select name="account_id" id="account_id" class="form-control @error('account_id') is-invalid @enderror" required>
                                    <option value="">{{ __('custom.select_account') }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ currencySymbol() }} {{ number_format($account->current_balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="amount">{{ __('custom.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ currencySymbol() }}</span>
                                    </div>
                                    <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" max="{{ $capital->remaining_amount }}" required>
                                </div>
                                <small class="text-muted">{{ __('custom.max_amount') }}: {{ currencySymbol() }} {{ number_format($capital->remaining_amount, 2) }}</small>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="payment_date">{{ __('custom.payment_date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="payment_method">{{ __('custom.payment_method') }}</label>
                                <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                    <option value="cash">{{ __('custom.cash') }}</option>
                                    <option value="bank_transfer">{{ __('custom.bank_transfer') }}</option>
                                    <option value="check">{{ __('custom.check') }}</option>
                                    <option value="mobile_banking">{{ __('custom.mobile_banking') }}</option>
                                </select>
                                @error('payment_method')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="reference_no">{{ __('custom.reference_no') }}</label>
                                <input type="text" name="reference_no" id="reference_no" class="form-control @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}">
                                @error('reference_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="payment_note">{{ __('custom.note') }}</label>
                                <textarea name="note" id="payment_note" class="form-control @error('note') is-invalid @enderror" rows="2">{{ old('note') }}</textarea>
                                @error('note')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-save"></i> {{ __('custom.pay_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Add Capital Amount Form --}}
            @if(request('action') == 'add_capital')
                <div class="card mt-3" id="add-capital-form">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('custom.add_capital_amount') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.capitals.add-amount', $capital->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="add_account_id">{{ __('custom.to_account') }} <span class="text-danger">*</span></label>
                                <select name="account_id" id="add_account_id" class="form-control @error('account_id') is-invalid @enderror" required>
                                    <option value="">{{ __('custom.select_account') }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ currencySymbol() }} {{ number_format($account->current_balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="add_amount">{{ __('custom.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ currencySymbol() }}</span>
                                    </div>
                                    <input type="number" step="0.01" name="amount" id="add_amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                                </div>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="add_date">{{ __('custom.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="add_date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="add_note">{{ __('custom.note') }}</label>
                                <textarea name="note" id="add_note" class="form-control @error('note') is-invalid @enderror" rows="2">{{ old('note') }}</textarea>
                                @error('note')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-plus"></i> {{ __('custom.add_amount') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Payment History --}}
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">{{ __('custom.payment_history') }}</h5>
        </div>
        <div class="card-body">
            @if($capital->payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('custom.date') }}</th>
                                <th>{{ __('custom.account') }}</th>
                                <th>{{ __('custom.amount') }}</th>
                                <th>{{ __('custom.payment_method') }}</th>
                                <th>{{ __('custom.reference_no') }}</th>
                                <th>{{ __('custom.note') }}</th>
                                <th>{{ __('custom.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($capital->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                    <td>{{ $payment->account->name ?? '-' }}</td>
                                    <td>{{ currencySymbol() }} {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                    <td>{{ $payment->note ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('admin.capitals.payment.destroy', [$capital->id, $payment->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('custom.confirm_delete_payment') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('custom.delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted">{{ __('custom.no_payments_yet') }}</p>
            @endif
        </div>
    </div>
@endsection
