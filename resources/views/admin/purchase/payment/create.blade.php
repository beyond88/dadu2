@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.purchases.index') }}">{{ __t('purchases') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.purchases.show', $purchase->id) }}">#{{ $purchase->purchase_number }}</a></li>
                <li class="breadcrumb-item active">{{ __t('make_payment') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Payment Summary Card --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #5664d2 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 font-12">{{ __t('total') }}</p>
                        <h5 class="mb-0 font-weight-bold">{{ currencySymbol() }}{{ make2decimal($purchase->total) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #1cbb8c !important;">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 font-12">{{ __t('total_paid') }}</p>
                        <h5 class="mb-0 font-weight-bold text-success">{{ currencySymbol() }}{{ make2decimal($totalPaid) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid {{ $due > 0 ? '#ff3d60' : '#1cbb8c' }} !important;">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 font-12">{{ __t('due') }}</p>
                        <h5 class="mb-0 font-weight-bold {{ $due > 0 ? 'text-danger' : 'text-success' }}">{{ currencySymbol() }}{{ make2decimal($due) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">{{ __t('make_payment') }}</h4>
                    <a href="{{ route('admin.purchases.payment.history', $purchase->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-history"></i> {{ __t('payment_history') }}
                    </a>
                </div>

                @if($due <= 0)
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ __t('fully_paid') ?? 'This purchase is fully paid.' }}
                    </div>
                @endif

                <form action="{{ route('admin.purchases.payment.store', $purchase->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>{{ __t('date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>{{ __('custom.select_account') }} <span class="text-danger">*</span></label>
                            <select name="account_id" class="form-control select2" required>
                                <option value="">-- {{ __('custom.select_account') }} --</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ currencySymbol() }}{{ make2decimal($account->current_balance) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>{{ __t('payment_type') }} <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-control select2" required>
                                <option value="">-- Select Payment Type --</option>
                                <option value="cash"        {{ old('payment_type') == 'cash'        ? 'selected' : '' }}>Cash</option>
                                <option value="bank"        {{ old('payment_type') == 'bank'        ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque"      {{ old('payment_type') == 'cheque'      ? 'selected' : '' }}>Cheque</option>
                                <option value="mobile"      {{ old('payment_type') == 'mobile'      ? 'selected' : '' }}>Mobile Banking</option>
                                <option value="credit_card" {{ old('payment_type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="other"       {{ old('payment_type') == 'other'       ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_type') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>{{ __t('amount') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ currencySymbol() }}</span>
                                </div>
                                <input type="number" name="amount" id="amount" class="form-control"
                                    value="{{ old('amount', $due > 0 ? make2decimal($due) : '') }}"
                                    min="0.01" max="{{ $due }}" step="any" required>
                            </div>
                            <small class="text-muted">Due: {{ currencySymbol() }}{{ make2decimal($due) }}</small>
                            @error('amount') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>{{ __t('notes') }}</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Optional note…">
                            @error('notes') <p class="text-danger small mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary" {{ $due <= 0 ? 'disabled' : '' }}>
                            <i class="fa fa-save"></i> {{ __t('save_payment') }}
                        </button>
                        <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="btn btn-danger ml-2">
                            <i class="fa fa-times"></i> {{ __t('cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
