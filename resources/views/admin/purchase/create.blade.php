@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.purchases') }}</a></li>
                <li class="breadcrumb-item active">{{ __t('add').' '.__t('purchase') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">{{ __t('add').' '.__t('purchase') }}</h4>

                @include('includes.messages.validation')

                <form class="form-validate" action="{{ route('admin.purchases.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-5">

                        {{-- Row 1: Receive/Purchase No · Date · Purchase Type --}}
                        <div class="form-group col-sm-4">
                            <label for="purchase_number">{{ __t('purchase_number') ?? 'Receive No' }}</label>
                            <input type="text" class="form-control font-weight-bold" id="purchase_number"
                                name="purchase_number" value="{{ old('purchase_number', $purchaseNumber ?? '') }}" readonly>
                            @error('purchase_number')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="date">{{ __t('date') }} <span class="error">*</span></label>
                            <input type="text" class="form-control datepicker-autoclose" name="date" id="date"
                                value="{{ old('date') ?? date('Y-m-d') }}" required placeholder="{{ __t('date') }}" autocomplete="off">

                            @error('date')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Payment type: Cash = paid immediately (deducts the single Cash account); Credit = due/pending --}}
                        <div class="form-group col-sm-4">
                            <label class="d-block mb-2">{{ __t('payment_type') ?? 'Purchase Type' }}</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="payment_type_credit" name="payment_type" value="credit"
                                    class="custom-control-input" {{ old('payment_type') === 'credit' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="payment_type_credit">{{ __t('credit') ?? 'Credit' }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="payment_type_cash" name="payment_type" value="cash"
                                    class="custom-control-input" {{ old('payment_type', 'cash') === 'cash' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="payment_type_cash">{{ __t('cash') ?? 'Cash' }}</label>
                            </div>
                            @error('payment_type')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Row 2: Trader (Supplier) · Warehouse --}}
                        <div class="form-group col-sm-6">
                            <label for="supplier">Trader <span class="error">*</span></label>
                            <select name="supplier" id="supplier" class="form-control select2" required="true">
                                <option value="">- {{ __t('select') }} Trader -
                                </option>
                                @foreach($suppliers as $supplier)
                                <option {{ old('supplier')==$supplier->id ? 'selected' : '' }}
                                    value="{{ $supplier->id }}" data-address="{{ $supplier->address_line_1 }}">{{ $supplier->full_name }}</option>
                                @endforeach
                            </select>

                            @error('supplier')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="warehouse">{{ __('custom.warehouse') }} <span class="error">*</span></label>
                            <select name="warehouse" id="warehouse" class="form-control" required>
                                <option value="">- {{ __('custom.select') }} {{ __('custom.warehouse') }} -</option>
                                @foreach($warehouses as $id => $name)
                                <option value="{{ $id }}" {{ old('warehouse') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Company hidden per request (kept in code, not rendered) --}}
                        <div class="form-group col-sm-6 d-none">
                            <label for="company">{{ __t('company') }}</label>
                            <input type="text" class="form-control" name="company" id="company"
                                value="{{ old('company') }}" placeholder="{{ __t('company') }}">
                        </div>

                        <div class="col-sm-12">
                            <label for="address_line_1" class="text-muted">{{ __('custom.address') }}</label>
                            <div class="row">
                                {{-- Single address field — auto-fills from the selected Trader's address (stays editable) --}}
                                <div class="form-group col-sm-12">
                                    <input type="text" name="address_line_1" id="address_line_1" class="form-control"
                                        value="{{ old('address_line_1') }}" placeholder="{{ __('custom.address') }}">
                                    @error('address_line_1')
                                    <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Address Line 2 hidden per request (kept in code, not rendered) --}}
                                <div class="form-group col-sm-6 d-none">
                                    <label for="">{{__('custom.address_line_2')}}</label>
                                    <input type="text" name="address_line_2" class="form-control"
                                        value="{{ old('address_line_2') }}">
                                </div>

                                {{-- Country / State / City / Zip Code hidden per request --}}
                                {{--
                                <div class="col-lg-6">
                                    <div class="form-group ic-select-gray-bg">
                                        <label for="#">{{ __('custom.country') }}</label>
                                        <select id="country" name="country" class="form-control select2">
                                            <option value="">{{ __('custom.select') }} {{ __('custom.country') }}
                                            </option>
                                            @foreach($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('country')
                                        <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group ic-select-gray-bg">
                                        <label for="#">{{ __('custom.state') }} </label>
                                        <select id="state" name="state" class="form-control select2">
                                            <option value="">{{ __('custom.select') }} {{ __('custom.state') }}</option>
                                        </select>

                                        @error('state')
                                        <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group ic-select-gray-bg">
                                        <label for="#">{{ __('custom.city') }} </label>
                                        <select id="city" name="city" class="form-control select2">
                                            <option value="">{{ __('custom.select') }} {{ __('custom.city') }}</option>
                                        </select>

                                        @error('city')
                                        <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label for="">{{__('custom.zipcode')}} </label>
                                    <input type="number" name="zipcode" class="form-control"
                                        value="{{ old('zipcode') }}"
                                        maxlength="12">
                                    @error('zipcode')
                                    <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                                --}}
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="row">
                                {{-- Short Address / Note hidden per request --}}
                                {{--
                                <div class="form-group col-sm-12">
                                    <label for="short_address">{{ __t('short_address') }} <small>({{ __t('short_address_note') }})</small></label>
                                    <textarea name="short_address" class="form-control" id="short_address"
                                              placeholder="{{ __t('short_address') }}">{{ old('short_address') }}</textarea>
                                    @error('short_address')
                                    <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label for="note">{{ __t('note') }}</label>
                                    <textarea name="note" class="form-control" id="note"
                                              placeholder="{{ __t('note') }}">{{ old('note') }}</textarea>
                                    @error('note')
                                    <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                                --}}
                            </div>
                        </div>
                    </div>


                    <purchase-add
                        currency_symbol="{{currencySymbol()}}"
                    ></purchase-add>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div>
                                    <button class="btn btn-primary waves-effect waves-lightml-2" type="submit">
                                        <i class="fa fa-save"></i> <span>{{ __('custom.submit') }}</span>
                                    </button>
                                    <a class="btn btn-danger waves-effect" href="{{ route('admin.purchases.index') }}">
                                        <i class="fa fa-times"></i> <span>{{ __('custom.cancel') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
@endpush

@push('script')

@include('includes.scripts.country_state_city_auto_load')

<script>
    $(function () {
        // Auto-fill the Address field from the selected Trader's (supplier's) address.
        // Convenience default only — it never overwrites a value the user typed/edited:
        // it fills when the field is empty or still holds the previously auto-filled value.
        var $supplier = $('#supplier');
        var $address  = $('#address_line_1');

        function applyTraderAddress() {
            var addr = $supplier.find('option:selected').data('address') || '';
            var current = $address.val();
            if (current === '' || current === $address.data('autofilled')) {
                $address.val(addr);
                $address.data('autofilled', addr);
            }
        }

        // select2 fires the native change event on selection.
        $supplier.on('change', applyTraderAddress);
    });
</script>

@endpush
