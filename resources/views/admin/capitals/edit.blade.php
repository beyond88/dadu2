@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('custom.capital_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.edit_capital') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.capitals.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('custom.back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="header-title">{{ __('custom.edit_capital') }} - {{ $capital->capital_no }}</h4>
            <form action="{{ route('admin.capitals.update', $capital->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="investor_name">{{ __('custom.investor_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="investor_name" id="investor_name" class="form-control @error('investor_name') is-invalid @enderror" value="{{ old('investor_name', $capital->investor_name) }}" required>
                            @error('investor_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="investor_phone">{{ __('custom.investor_phone') }}</label>
                            <input type="text" name="investor_phone" id="investor_phone" class="form-control phone @error('investor_phone') is-invalid @enderror" value="{{ old('investor_phone', $capital->investor_phone) ?: '+880' }}">
                            @error('investor_phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="investor_address">{{ __('custom.investor_address') }}</label>
                            <textarea name="investor_address" id="investor_address" class="form-control @error('investor_address') is-invalid @enderror" rows="3">{{ old('investor_address', $capital->investor_address) }}</textarea>
                            @error('investor_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="note">{{ __('custom.note') }}</label>
                            <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $capital->note) }}</textarea>
                            @error('note')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_amount">{{ __('custom.total_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ currencySymbol() }}</span>
                                </div>
                                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="{{ $capital->total_amount }}" readonly>
                            </div>
                            <small class="text-muted">{{ __('custom.cannot_edit_amount_with_payments') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="capital_date">{{ __('custom.capital_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="capital_date" id="capital_date" class="form-control @error('capital_date') is-invalid @enderror" value="{{ old('capital_date', $capital->capital_date->format('Y-m-d')) }}" required>
                            @error('capital_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="due_date">{{ __('custom.due_date') }}</label>
                            <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $capital->due_date ? $capital->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ __('custom.update') }}
                    </button>
                    <a href="{{ route('admin.capitals.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times"></i> {{ __('custom.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
