@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.edit_lc') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.lcs.index') }}">{{ __('custom.lcs') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.edit_lc') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <form action="{{ route('admin.lcs.update', $lc->id) }}" method="POST" class="ic-lc-form">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title mb-2">{{ __('custom.lc_information') }}</h4>

                        {{-- One USD rate applies to every LC row below --}}
                        <div class="row justify-content-end">
                            <div class="col-12 col-md-6">
                                <div class="ic-entry-wrap mb-3">
                                    <div class="form-row justify-content-end">
                                        <div class="form-group col-12 col-md-6">
                                            <label for="usd_rate">{{ __('custom.usd_rate') }} <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-right calc-input @error('usd_rate') is-invalid @enderror"
                                                   id="usd_rate" name="usd_rate" value="{{ old('usd_rate', $lc->usd_rate) }}" required autofocus>
                                            @error('usd_rate')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- LC rows: name + price ($), repeatable like the expenses below --}}
                        <div class="form-row d-none d-md-flex">
                            <div class="col-md-7"><label class="mb-1">{{ __('custom.lc_name') }}</label></div>
                            <div class="col-md-4"><label class="mb-1">{{ __('custom.price_usd') }}</label></div>
                            <div class="col-md-1"></div>
                        </div>

                        <div id="lc-repeater" class="ic-entry-wrap mb-2">
                            @php
                                $oldItems = old('items', $lc->items->toArray());
                                if (empty($oldItems)) {
                                    // LCs saved before the breakdown existed carry a single name/price.
                                    $oldItems = [['name' => $lc->name, 'dollar_price' => $lc->dollar_price]];
                                }
                            @endphp
                            @foreach($oldItems as $index => $item)
                                <div class="form-row lc-row">
                                    <div class="form-group col-12 col-md-7">
                                        <input type="text" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" class="form-control form-control-sm" placeholder="{{ __('custom.lc_name') }}">
                                    </div>
                                    <div class="form-group col-8 col-md-4">
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][dollar_price]" value="{{ $item['dollar_price'] ?? '' }}" class="form-control form-control-sm text-right calc-input lc-price" placeholder="{{ __('custom.price_usd') }}">
                                    </div>
                                    <div class="form-group col-4 col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm btn-block remove-lc"><i class="mdi mdi-delete"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-row mb-2">
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-success btn-sm" id="add-lc"><i class="mdi mdi-plus"></i> {{ __('custom.add_lc') }}</button>
                            </div>
                        </div>

                        <div class="form-row align-items-center mb-3">
                            <div class="col-12 col-md-7 text-md-right">
                                <label class="mb-0">{{ __('custom.total_lc_price') }}</label>
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" id="total-lc-price" class="form-control form-control-sm text-right ic-readbox" value="0.00" readonly>
                            </div>
                        </div>

                        @error('items')
                            <p class="text-danger small">{{ $message }}</p>
                        @enderror


                        {{-- Expenses entry panel --}}
                        <h4 class="mt-0 header-title mb-2">{{ __('custom.expenses') }}</h4>
                        {{-- Column labels, so the expense currency is as visible as the Price ($) one --}}
                        <div class="form-row d-none d-md-flex">
                            <div class="col-md-7"><label class="mb-1">{{ __('custom.expense_name') }}</label></div>
                            <div class="col-md-4"><label class="mb-1">{{ __('custom.expense_bdt') }}</label></div>
                            <div class="col-md-1"></div>
                        </div>

                        <div id="expense-repeater" class="ic-entry-wrap mb-2">
                            @php
                                // Standard LC charges, prefilled — leave an amount blank to skip that row.
                                $defaultExpenses = ['Bank charge', 'Document charge', 'Other payments', 'Insurance', 'Transportation', 'Duty tax & other charges'];
                                $oldExpenses = old('expenses', $lc->expenses->toArray());
                                if (empty($oldExpenses)) {
                                    $oldExpenses = array_map(fn ($name) => ['expense_name' => $name, 'amount' => ''], $defaultExpenses);
                                }
                            @endphp
                            @foreach($oldExpenses as $index => $expense)
                                <div class="form-row expense-row">
                                    <div class="form-group col-12 col-md-7">
                                        <input type="text" name="expenses[{{$index}}][expense_name]" value="{{ $expense['expense_name'] ?? '' }}" class="form-control form-control-sm" placeholder="{{ __('custom.expense_name') }}">
                                    </div>
                                    <div class="form-group col-8 col-md-4">
                                        <input type="number" step="0.01" min="0" name="expenses[{{$index}}][amount]" value="{{ $expense['amount'] ?? 0 }}" class="form-control form-control-sm text-right calc-input expense-amount" placeholder="{{ __('custom.amount_bdt') }}">
                                    </div>
                                    <div class="form-group col-4 col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm btn-block remove-expense"><i class="mdi mdi-delete"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3 text-right">
                            <button type="button" class="btn btn-success btn-sm" id="add-expense"><i class="mdi mdi-plus"></i> {{ __('custom.add_expense') }}</button>
                        </div>

                        {{-- Calculation breakdown as a read-only summary section --}}
                        <h4 class="mt-0 header-title mb-2">{{ __('custom.calculation_breakdown') }}</h4>
                        <div class="ic-summary">
                            <div class="form-row">
                                <div class="form-group col-6 col-md-4">
                                    <label>{{ __('custom.usd_rate') }}</label>
                                    <input type="text" id="calc-usd-rate-display" class="form-control form-control-sm text-right ic-readbox" value="0.00" readonly>
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label>{{ __('custom.lc_amount_bdt') }}</label>
                                    <input type="text" id="calc-lc-amount" class="form-control form-control-sm text-right ic-readbox" value="0.00" readonly>
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label>{{ __('custom.total_expense') }}</label>
                                    <input type="text" id="calc-total-expense" class="form-control form-control-sm text-right ic-readbox" value="0.00" readonly>
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label>{{ __('custom.final_cost') }}</label>
                                    <input type="text" id="calc-final-cost" class="form-control form-control-sm text-right ic-readbox ic-final" value="0.00" readonly>
                                </div>
                                <div class="form-group col-6 col-md-4">
                                    <label>{{ __('custom.per_dollar_actual_cost') }}</label>
                                    <input type="text" id="calc-per-dollar-cost" class="form-control form-control-sm text-right ic-readbox ic-per-dollar" value="0.0000" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 text-right">
                            <a href="{{ route('admin.lcs.index') }}" class="btn btn-secondary ic-form-action"><i class="mdi mdi-arrow-left"></i> {{ __('custom.cancel') }}</a>
                            <button type="submit" class="btn btn-primary ic-form-action"><i class="mdi mdi-save"></i> {{ __('custom.update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* LC form — same layout & input design as the transaction entry page */
        .ic-lc-form .card-body { padding: 14px 16px; }
        .ic-lc-form .header-title { font-size: 1rem; }
        .ic-lc-form label { margin-bottom: 1px; font-weight: 600; font-size: .72rem; color: #495057; text-transform: uppercase; letter-spacing: .3px; }
        .ic-lc-form .form-group { margin-bottom: 10px; }

        /* Clearly-bordered, compact inputs */
        .ic-lc-form .form-control {
            height: 32px;
            padding: 2px 8px;
            font-size: .85rem;
            border: 1px solid #b0b7c3;
            border-radius: 4px;
            background: #fff;
        }
        .ic-lc-form .form-control:focus {
            border-color: #4a7dff;
            box-shadow: 0 0 0 2px rgba(74, 125, 255, .18);
            background: #fbfcff;
        }

        /* Grouped entry panels like the transaction entry row */
        .ic-lc-form .ic-entry-wrap { padding: 10px 10px 0; background: #f4f6f9; border: 1px solid #dbe0e6; border-radius: 6px; }
        .ic-lc-form .expense-row { margin-bottom: 0; }

        /* Cancel / Save sit larger than the compact form inputs */
        .ic-lc-form .ic-form-action { padding: 8px 22px; font-size: .95rem; font-weight: 600; }

        /* Read-only calculation boxes (gray, like the transaction summary) */
        .ic-lc-form .form-control[readonly], .ic-lc-form .ic-readbox { background: #e9ecef; font-weight: 600; }
        .ic-lc-form .ic-summary label { color: #6c757d; }
        .ic-lc-form .ic-final { color: #2f57d6; font-weight: 700; }
        .ic-lc-form .ic-per-dollar { color: #1a9e5c; font-weight: 700; }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            let expenseIndex = {{ count($oldExpenses) }};
            let lcIndex = {{ count($oldItems) }};

            // Add LC row
            $('#add-lc').on('click', function() {
                const html = `
                    <div class="form-row lc-row">
                        <div class="form-group col-12 col-md-7">
                            <input type="text" name="items[${lcIndex}][name]" class="form-control form-control-sm" placeholder="{{ __('custom.lc_name') }}">
                        </div>
                        <div class="form-group col-8 col-md-4">
                            <input type="number" step="0.01" min="0" name="items[${lcIndex}][dollar_price]" class="form-control form-control-sm text-right calc-input lc-price" placeholder="{{ __('custom.price_usd') }}">
                        </div>
                        <div class="form-group col-4 col-md-1">
                            <button type="button" class="btn btn-danger btn-sm btn-block remove-lc"><i class="mdi mdi-delete"></i></button>
                        </div>
                    </div>
                `;
                $('#lc-repeater').append(html);
                lcIndex++;
                calculateBreakdown();
            });

            // Remove LC row — totals refresh immediately
            $(document).on('click', '.remove-lc', function() {
                if ($('.lc-row').length > 1) {
                    $(this).closest('.lc-row').remove();
                    calculateBreakdown();
                } else {
                    alert("{{ __('custom.at_least_one_lc_required') }}");
                }
            });

            // Add repeater
            $('#add-expense').on('click', function() {
                const html = `
                    <div class="form-row expense-row">
                        <div class="form-group col-12 col-md-7">
                            <input type="text" name="expenses[${expenseIndex}][expense_name]" class="form-control form-control-sm" placeholder="{{ __('custom.expense_name') }}">
                        </div>
                        <div class="form-group col-8 col-md-4">
                            <input type="number" step="0.01" min="0" name="expenses[${expenseIndex}][amount]" value="0" class="form-control form-control-sm text-right calc-input expense-amount" placeholder="{{ __('custom.amount_bdt') }}">
                        </div>
                        <div class="form-group col-4 col-md-1">
                            <button type="button" class="btn btn-danger btn-sm btn-block remove-expense"><i class="mdi mdi-delete"></i></button>
                        </div>
                    </div>
                `;
                $('#expense-repeater').append(html);
                expenseIndex++;
                calculateBreakdown();
            });

            // Remove repeater
            $(document).on('click', '.remove-expense', function() {
                if ($('.expense-row').length > 1) {
                    $(this).closest('.expense-row').remove();
                    calculateBreakdown();
                } else {
                    alert("{{ __('custom.at_least_one_expense_required') }}");
                }
            });

            // The USD rate drives every calculation, so it has to be entered
            // before any LC price can be typed in.
            let usdRateAlertOpen = false;

            function usdRateEntered($priceInput) {
                if ($('#usd_rate').val()) {
                    return true;
                }
                if (!usdRateAlertOpen) {
                    usdRateAlertOpen = true;
                    alert("{{ __('custom.enter_usd_rate_first') }}");
                    usdRateAlertOpen = false;
                    $priceInput.val('');
                    $('#usd_rate').trigger('focus');
                }
                return false;
            }

            $(document).on('focus input', '.lc-price', function() {
                usdRateEntered($(this));
            });

            // Trigger calculation
            $(document).on('input', '.calc-input', function() {
                calculateBreakdown();
            });

            function calculateBreakdown() {
                // Every LC row shares the one USD rate, so the calculation runs
                // on the total of the LC prices.
                let dollarPrice = 0;
                $('.lc-price').each(function() {
                    dollarPrice += parseFloat($(this).val()) || 0;
                });
                let usdRate = parseFloat($('#usd_rate').val()) || 0;

                $('#total-lc-price').val(dollarPrice.toFixed(2));

                let lcAmountBdt = dollarPrice * usdRate;

                let totalExpense = 0;
                $('.expense-amount').each(function() {
                    totalExpense += parseFloat($(this).val()) || 0;
                });

                let finalCost = lcAmountBdt + totalExpense;
                // Landed cost of one dollar = USD rate + the expense share it carries.
                let perDollarCost = dollarPrice > 0 ? (finalCost / dollarPrice) : usdRate;

                $('#calc-usd-rate-display').val(usdRate.toFixed(2));
                $('#calc-lc-amount').val(lcAmountBdt.toFixed(2));
                $('#calc-total-expense').val(totalExpense.toFixed(2));
                $('#calc-final-cost').val(finalCost.toFixed(2));
                $('#calc-per-dollar-cost').val(perDollarCost.toFixed(4));
            }

            // Init
            calculateBreakdown();
        });
    </script>
@endpush
