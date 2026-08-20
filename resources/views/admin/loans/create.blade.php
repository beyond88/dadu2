@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.add_loan') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.loans.index') }}">{{ __('custom.loan_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.add_loan') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="mdi mdi-bank-transfer"></i> {{ __('custom.new_loan') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.loans.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.borrower_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="borrower_name" value="{{ old('borrower_name') }}"
                                        class="form-control @error('borrower_name') is-invalid @enderror"
                                        placeholder="{{ __('custom.borrower_name') }}" required>
                                    @error('borrower_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.borrower_phone') }}</label>
                                    <input type="text" name="borrower_phone" value="{{ old('borrower_phone') ?: '+880' }}"
                                        class="form-control phone @error('borrower_phone') is-invalid @enderror"
                                        placeholder="{{ __('custom.borrower_phone') }}">
                                    @error('borrower_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('custom.borrower_address') }}</label>
                            <textarea name="borrower_address" class="form-control @error('borrower_address') is-invalid @enderror"
                                rows="2" placeholder="{{ __('custom.borrower_address') }}">{{ old('borrower_address') }}</textarea>
                            @error('borrower_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.to_account') }} <span class="text-danger">*</span></label>
                                    <select name="account_id" class="form-control @error('account_id') is-invalid @enderror" required>
                                        <option value="">-- {{ __('custom.select_account') }} --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ currencySymbol() }} {{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.opening_balance') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ currencySymbol() }}</span>
                                        </div>
                                        <input type="number" name="opening_balance" value="{{ old('opening_balance') }}"
                                            class="form-control @error('opening_balance') is-invalid @enderror"
                                            placeholder="0.00" step="0.01" min="0.01" required>
                                        @error('opening_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.loan_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="loan_date" value="{{ old('loan_date', date('Y-m-d')) }}"
                                        class="form-control @error('loan_date') is-invalid @enderror" required>
                                    @error('loan_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.due_date') }}</label>
                                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                                        class="form-control @error('due_date') is-invalid @enderror">
                                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('custom.note') }}</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror"
                                rows="3" placeholder="{{ __('custom.note') }}">{{ old('note') }}</textarea>
                            @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="text-right">
                            <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary mr-2">
                                <i class="mdi mdi-arrow-left"></i> {{ __('custom.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-content-save"></i> {{ __('custom.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
