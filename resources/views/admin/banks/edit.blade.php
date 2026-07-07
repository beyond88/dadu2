@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.edit_bank') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.banks.index') }}">{{ __('custom.banks') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.edit_bank') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.edit_bank_information') }}</h4>
                    
                    <form action="{{ route('admin.banks.update', $bank) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('custom.bank_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $bank->name) }}" 
                                           placeholder="{{ __('custom.enter_bank_name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account_name">{{ __('custom.account_name') }}</label>
                                    <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                           id="account_name" name="account_name" value="{{ old('account_name', $bank->account_name) }}" 
                                           placeholder="{{ __('custom.enter_account_name') }}">
                                    @error('account_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account_number">{{ __('custom.account_number') }}</label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                           id="account_number" name="account_number" value="{{ old('account_number', $bank->account_number) }}" 
                                           placeholder="{{ __('custom.enter_account_number') }}">
                                    @error('account_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_name">{{ __('custom.branch_name') }}</label>
                                    <input type="text" class="form-control @error('branch_name') is-invalid @enderror" 
                                           id="branch_name" name="branch_name" value="{{ old('branch_name', $bank->branch_name) }}" 
                                           placeholder="{{ __('custom.enter_branch_name') }}">
                                    @error('branch_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">{{ __('custom.phone') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $bank->phone) }}" 
                                           placeholder="{{ __('custom.enter_phone_number') }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('custom.email') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $bank->email) }}" 
                                           placeholder="{{ __('custom.enter_email_address') }}">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person">{{ __('custom.contact_person') }}</label>
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                           id="contact_person" name="contact_person" value="{{ old('contact_person', $bank->contact_person) }}" 
                                           placeholder="{{ __('custom.enter_contact_person_name') }}">
                                    @error('contact_person')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_phone">{{ __('custom.contact_person_phone') }}</label>
                                    <input type="text" class="form-control @error('contact_person_phone') is-invalid @enderror" 
                                           id="contact_person_phone" name="contact_person_phone" value="{{ old('contact_person_phone', $bank->contact_person_phone) }}" 
                                           placeholder="{{ __('custom.enter_contact_person_phone') }}">
                                    @error('contact_person_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">{{ __('custom.address') }}</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" 
                                              placeholder="{{ __('custom.enter_bank_address') }}">{{ old('address', $bank->address) }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">{{ __('custom.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="{{ __('custom.enter_notes') }}">{{ old('notes', $bank->notes) }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $bank->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            {{ __('custom.active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-save"></i> {{ __('custom.update') }}
                                    </button>
                                    <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> {{ __('custom.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
