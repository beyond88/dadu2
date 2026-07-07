@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.edit_loan') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.loans.index') }}">{{ __('custom.loan_management') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.loans.show', $loan->id) }}">{{ $loan->loan_no }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.edit') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-pencil"></i> {{ __('custom.edit_loan') }} - {{ $loan->loan_no }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.borrower_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="borrower_name" value="{{ old('borrower_name', $loan->borrower_name) }}"
                                        class="form-control @error('borrower_name') is-invalid @enderror" required>
                                    @error('borrower_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.borrower_phone') }}</label>
                                    <input type="text" name="borrower_phone" value="{{ old('borrower_phone', $loan->borrower_phone) }}"
                                        class="form-control @error('borrower_phone') is-invalid @enderror">
                                    @error('borrower_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('custom.borrower_address') }}</label>
                            <textarea name="borrower_address" class="form-control" rows="2">{{ old('borrower_address', $loan->borrower_address) }}</textarea>
                        </div>

                        <div class="row">
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.loan_type') }} <span class="text-danger">*</span></label>
                                    <select name="loan_type" class="form-control @error('loan_type') is-invalid @enderror" required>
                                        @foreach($loanTypes as $key => $val)
                                            <option value="{{ $key }}" {{ old('loan_type', $loan->loan_type) == $key ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                    @error('loan_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.opening_balance') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ currencySymbol() }}</span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ number_format($loan->opening_balance, 2) }}" readonly>
                                    </div>
                                    <small class="text-muted">{{ __('custom.opening_balance_readonly') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.loan_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="loan_date" value="{{ old('loan_date', $loan->loan_date ? $loan->loan_date->format('Y-m-d') : '') }}"
                                        class="form-control @error('loan_date') is-invalid @enderror" required>
                                    @error('loan_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('custom.due_date') }}</label>
                                    <input type="date" name="due_date" value="{{ old('due_date', $loan->due_date ? $loan->due_date->format('Y-m-d') : '') }}"
                                        class="form-control @error('due_date') is-invalid @enderror">
                                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('custom.note') }}</label>
                            <textarea name="note" class="form-control" rows="3">{{ old('note', $loan->note) }}</textarea>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn btn-secondary mr-2">
                                <i class="mdi mdi-arrow-left"></i> {{ __('custom.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> {{ __('custom.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
