@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.transfer_balance') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">{{ __('custom.transactions') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.transfer_balance') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.transfer_balance_information') }}</h4>
                    
                    <form action="{{ route('admin.transactions.transfer') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_account_id">{{ __('custom.from_account') }} <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('from_account_id') is-invalid @enderror" 
                                            id="from_account_id" name="from_account_id" required>
                                        <option value="">{{ __('custom.select_from_account') }}</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" 
                                                    data-balance="{{ $account->current_balance }}"
                                                    {{ old('from_account_id', request('from_account_id')) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('from_account_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small id="from-balance-display" class="form-text text-muted" style="display: none;">
                                        {{ __('custom.available_balance') }}: <span id="from-balance-amount"></span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_account_id">{{ __('custom.to_account') }} <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('to_account_id') is-invalid @enderror" 
                                            id="to_account_id" name="to_account_id" required>
                                        <option value="">{{ __('custom.select_to_account') }}</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" 
                                                    data-balance="{{ $account->current_balance }}"
                                                    {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }} ({{ number_format($account->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_account_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">{{ __('custom.transfer_amount') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" 
                                           placeholder="{{ __('custom.enter_transfer_amount') }}" required>
                                    @error('amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small id="amount-validation" class="form-text text-danger" style="display: none;"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note">{{ __('custom.note') }}</label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" 
                                              id="note" name="note" rows="3" 
                                              placeholder="{{ __('custom.enter_transfer_note') }}">{{ old('note') }}</textarea>
                                    @error('note')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary" id="transfer-btn">
                                        <i class="mdi mdi-swap-horizontal"></i> {{ __('custom.transfer') }}
                                    </button>
                                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
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

@push('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            var fromBalance = 0;

            // Show balance when from account is selected
            $('#from_account_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                fromBalance = parseFloat(selectedOption.data('balance')) || 0;
                
                if (fromBalance > 0) {
                    $('#from-balance-amount').text(fromBalance.toFixed(2));
                    $('#from-balance-display').show();
                } else {
                    $('#from-balance-display').hide();
                }

                validateTransfer();
            });

            // Validate on amount change
            $('#amount').on('input', function() {
                validateTransfer();
            });

            // Validate on to account change
            $('#to_account_id').on('change', function() {
                validateTransfer();
            });

            function validateTransfer() {
                var amount = parseFloat($('#amount').val()) || 0;
                var fromAccountId = $('#from_account_id').val();
                var toAccountId = $('#to_account_id').val();
                var errorMsg = '';
                var isValid = true;

                // Check same account
                if (fromAccountId && toAccountId && fromAccountId == toAccountId) {
                    errorMsg = '{{ __("custom.cannot_transfer_to_same_account") }}';
                    isValid = false;
                }

                // Check sufficient balance
                if (amount > 0 && amount > fromBalance) {
                    errorMsg = '{{ __("custom.insufficient_balance_for_transfer") }}';
                    isValid = false;
                }

                if (errorMsg) {
                    $('#amount-validation').text(errorMsg).show();
                    $('#transfer-btn').prop('disabled', true);
                } else {
                    $('#amount-validation').hide();
                    $('#transfer-btn').prop('disabled', false);
                }

                return isValid;
            }

            // Trigger change if old values exist
            if ($('#from_account_id').val()) {
                $('#from_account_id').trigger('change');
            }
        });
    </script>
@endpush
