@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.edit_account') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">{{ __('custom.accounts') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.edit_account') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card ic-acc-form">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.edit_account_information') }}</h4>

                    <form id="account-form" action="{{ route('admin.accounts.update', $account) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="ic-entry-wrap">
                            <div class="form-row">
                                <div class="form-group col-12 col-md-4">
                                    <label for="name">{{ __('custom.account_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $account->name) }}"
                                           placeholder="{{ __('custom.enter_account_name') }}" required autofocus>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label for="code">{{ __('custom.account_code') }}</label>
                                    <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code', $account->code) }}"
                                           placeholder="{{ __('custom.enter_account_code') }}">
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label for="type">{{ __('custom.account_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm @error('type') is-invalid @enderror"
                                            id="type" name="type" required>
                                        <option value="">{{ __('custom.select_account_type') }}</option>
                                        @foreach($accountTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $account->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row" id="bank-details-row">
                                <div class="form-group col-12 col-md-4">
                                    <label for="account_number">{{ __('custom.account_number') }}</label>
                                    <input type="text" class="form-control form-control-sm @error('account_number') is-invalid @enderror"
                                           id="account_number" name="account_number" value="{{ old('account_number', $account->account_number) }}"
                                           placeholder="{{ __('custom.enter_account_number') }}">
                                    @error('account_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label for="bank_name">{{ __('custom.bank_name') }}</label>
                                    <input type="text" class="form-control form-control-sm @error('bank_name') is-invalid @enderror"
                                           id="bank_name" name="bank_name" value="{{ old('bank_name', $account->bank_name) }}"
                                           placeholder="{{ __('custom.enter_bank_name') }}">
                                    @error('bank_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label for="branch_name">{{ __('custom.branch_name') }}</label>
                                    <input type="text" class="form-control form-control-sm @error('branch_name') is-invalid @enderror"
                                           id="branch_name" name="branch_name" value="{{ old('branch_name', $account->branch_name) }}"
                                           placeholder="{{ __('custom.enter_branch_name') }}">
                                    @error('branch_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <div class="form-group col-6 col-md-4">
                                    <label>{{ __('custom.current_balance') }}</label>
                                    <input type="text" class="form-control form-control-sm text-right ic-readbox" value="{{ number_format($account->current_balance, 2) }}" readonly>
                                    <small class="form-text text-muted">{{ __('current_balance_cannot_be_edited') }}</small>
                                </div>
                                <div class="form-group col-6 col-md-4 align-self-start">
                                    <label class="d-block">&nbsp;</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            {{ __('custom.active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> {{ __('custom.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-save"></i> {{ __('custom.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Edit Account form — full width, same input design/structure as the transaction entry page */
        .ic-acc-form .card-body { padding: 14px 16px; }
        .ic-acc-form .header-title { margin-bottom: 10px; font-size: 1rem; }
        .ic-acc-form label { margin-bottom: 1px; font-weight: 600; font-size: .72rem; color: #495057; text-transform: uppercase; letter-spacing: .3px; }
        .ic-acc-form .form-group { margin-bottom: 10px; }

        /* Clearly-bordered, compact inputs */
        .ic-acc-form .form-control {
            height: 32px;
            padding: 2px 8px;
            font-size: .85rem;
            border: 1px solid #b0b7c3;
            border-radius: 4px;
            background: #fff;
        }
        .ic-acc-form select.form-control { height: 32px; }
        .ic-acc-form .form-control:focus {
            border-color: #4a7dff;
            box-shadow: 0 0 0 2px rgba(74, 125, 255, .18);
            background: #fbfcff;
        }
        .ic-acc-form .form-control[readonly], .ic-acc-form .ic-readbox { background: #e9ecef; font-weight: 600; }

        /* Grouped entry panel like the transaction entry row */
        .ic-entry-wrap { padding: 12px 12px 4px; background: #f4f6f9; border: 1px solid #dbe0e6; border-radius: 6px; }
        .ic-acc-form .custom-control-label { text-transform: none; font-size: .85rem; font-weight: 500; color: #212529; }
    </style>
@endpush

@push('script')
    <script>
        $(function () {
            // Focus the Account Name field on load
            $('#name').trigger('focus');

            var $form = $('#account-form');
            var confirmed = false;

            // Press Enter (or click Update) -> validate -> confirmation -> save
            $form.on('submit', function (e) {
                if (confirmed) return; // second pass: let it submit
                e.preventDefault();

                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }

                Swal.fire({
                    title: @json(__('custom.are_you_sure')),
                    html: '<div style="font-size:.9rem">' + @json(__('custom.update')) + ' &mdash; <strong>' +
                          $('<div>').text($('#name').val()).html() + '</strong></div>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: @json(__('custom.yes')),
                    cancelButtonText: @json(__('custom.no')),
                    confirmButtonColor: '#28a745',
                    reverseButtons: true,
                    focusConfirm: true,
                    width: 340
                }).then(function (result) {
                    if (result.value) {
                        confirmed = true;
                        $form[0].submit();
                    }
                });
            });
        });
    </script>
@endpush
