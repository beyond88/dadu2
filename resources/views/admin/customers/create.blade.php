@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">
                            {{ user_type() == 'admin' ? __('custom.customer') : __('custom.employee') }}
                        </a></li>
                    <li class="breadcrumb-item active">
                        {{ user_type() == 'admin' ? __('custom.add_customer') : __('custom.add_employee') }}
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">
                        {{ user_type() == 'admin' ? __('custom.add_customer') : __('custom.add_employee') }}</h4>

                    <form class="form-validate" action="{{ route('admin.customers.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.first_name') }} <span class="error">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}"
                                    required>
                                @error('first_name')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.customer_code') }}</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                                    placeholder="{{ __('custom.enter_customer_code') }}">
                                @error('code')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.last_name') }}</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                                @error('last_name')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.email') }}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.phone') }} <span class="error">*</span></label>
                                <input type="text" name="phone" class="form-control phone"
                                    value="{{ old('phone') ?? '+880' }}" required>
                                @error('phone')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.company') }} </label>
                                <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                                @error('company')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.designation') }}</label>
                                <input type="text" name="designation" class="form-control"
                                    value="{{ old('designation') }}">
                                @error('designation')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.opening_balance') }}</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control"
                                    value="{{ old('opening_balance') ?? 0 }}"
                                    placeholder="e.g. 5000">
                                <small class="text-muted">Prepaid credit the customer holds. It is only spent when applied as a payment on an invoice — unpaid invoices stay as Total Due.</small>
                                @error('opening_balance')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-sm-12">
                                <label for="" class="text-muted">{{ __('custom.address') }}</label>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('custom.address_line_1') }}</label>
                                        <input type="text" name="address_line_1" class="form-control"
                                            value="{{ old('address_line_1') }}">
                                        @error('address_line_1')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('custom.address_line_2') }}</label>
                                        <input type="text" name="address_line_2" class="form-control"
                                            value="{{ old('address_line_2') }}">
                                        @error('address_line_2')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 d-none">
                                        <div class="form-group ic-select-gray-bg">
                                            <label for="#">{{ __('custom.country') }} </label>
                                            <select id="country" name="country" class="form-control select2">
                                                <option value="">{{ __('custom.select') }}
                                                    {{ __('custom.country') }}
                                                </option>
                                                @foreach ($countries as $country)
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
                                    <div class="col-lg-6 d-none">
                                        <div class="form-group ic-select-gray-bg">
                                            <label for="#">{{ __('custom.state') }} </label>
                                            <select id="state" name="state" class="form-control select2">
                                                <option value="">{{ __('custom.select') }} {{ __('custom.state') }}
                                                </option>
                                            </select>

                                            @error('state')
                                                <p class="error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 d-none">
                                        <div class="form-group ic-select-gray-bg">
                                            <label for="#">{{ __('custom.city') }}</label>
                                            <select id="city" name="city" class="form-control select2">
                                                <option value="">{{ __('custom.select') }} {{ __('custom.city') }}
                                                </option>
                                            </select>

                                            @error('city')
                                                <p class="error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6 d-none">
                                        <label for="">{{ __('custom.zipcode') }}</label>
                                        <input type="number" name="zipcode" class="form-control"
                                            value="{{ old('zipcode') }}" maxlength="12">
                                        @error('zipcode')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-12 d-none">
                                        <label for="short_address">{{ __t('short_address') }}
                                            <small>({{ __t('short_address_note') }})</small></label>
                                        <textarea name="short_address" class="form-control" id="short_address" placeholder="{{ __t('short_address') }}">{{ old('short_address') }}</textarea>
                                        @error('short_address')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <label for="" class="text-muted">{{ __('custom.billing_address') }}</label>
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="billingSameAsAddress" name="billing_same">
                                            <label class="form-check-label checkbox-label" for="billingSameAsAddress">
                                                {{ __('custom.billing_address_same') }}
                                            </label>
                                        </div>

                                        @error('is_variant')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-sm-6 b_hide_same">
                                        <label for="">{{ __('custom.first_name') }}</label>
                                        <input type="text" name="b_first_name" class="form-control"
                                            value="{{ old('b_first_name') }}">
                                        @error('b_first_name')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6 b_hide_same">
                                        <label for="">{{ __('custom.last_name') }}</label>
                                        <input type="text" name="b_last_name" class="form-control"
                                            value="{{ old('b_last_name') }}">
                                        @error('b_last_name')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6 b_hide_same">
                                        <label for="">{{ __('custom.email') }}</label>
                                        <input type="email" name="b_email" class="form-control"
                                            value="{{ old('b_email') }}">
                                        @error('b_email')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6 b_hide_same">
                                        <label for="">{{ __('custom.phone') }}</label>
                                        <input type="text" name="b_phone" class="form-control phone"
                                            value="{{ old('b_phone') ?? '+880' }}">
                                        @error('b_phone')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-sm-6 b_hide_same">
                                        <label for="">{{ __('custom.address_line_1') }}</label>
                                        <input type="text" name="b_address_line_1" class="form-control"
                                            value="{{ old('b_address_line_1') }}">
                                        @error('b_address_line_1')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6 b_hide_same">
                                        <label for="">{{ __('custom.address_line_2') }}</label>
                                        <input type="text" name="b_address_line_2" class="form-control"
                                            value="{{ old('b_address_line_2') }}">
                                        @error('b_address_line_2')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 b_hide_same d-none">
                                        <div class="form-group ic-select-gray-bg">
                                            <label for="#">{{ __('custom.country') }}</label>
                                            <select id="country2" name="b_country" class="form-control select2">
                                                <option value="">{{ __('custom.select') }}
                                                    {{ __('custom.country') }}
                                                </option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('b_country')
                                                <p class="error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 b_hide_same d-none">
                                        <div class="form-group ic-select-gray-bg">
                                            <label for="#">{{ __('custom.state') }}</label>
                                            <select id="state2" name="b_state" class="form-control select2">
                                                <option value="">{{ __('custom.select') }} {{ __('custom.state') }}
                                                </option>
                                            </select>

                                            @error('b_state')
                                                <p class="error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 b_hide_same d-none">
                                        <div class="form-group ic-select-gray-bg">
                                            <label for="#"> {{ __('custom.city') }}</label> <select id="city2"
                                                name="b_city" class="form-control select2">
                                                <option value="">{{ __('custom.select') }} {{ __('custom.city') }}
                                                </option>
                                            </select>

                                            @error('b_city')
                                                <p class="error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 b_hide_same d-none">
                                        <label for="">{{ __('custom.zipcode') }}</label>
                                        <input type="number" name="b_zipcode" class="form-control" maxlength="12"
                                            value="{{ old('b_zipcode') }}">
                                        @error('zipcode')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-12 b_hide_same d-none">
                                        <label for="b_short_address">{{ __t('short_address') }}
                                            <small>({{ __t('short_address_note') }})</small></label>
                                        <textarea name="b_short_address" class="form-control" id="b_short_address"
                                            placeholder="{{ __t('short_address') }}">{{ old('b_short_address') }}</textarea>
                                        @error('b_short_address')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="form-group col-md-12 col-lg-12 col-xl-6 d-none">
                                <label for="">{{ __('custom.avatar') }}</label>
                                <small class="font-12">{{ __('custom.image_support_message') }}</small>
                                <div class="form-group">
                                    <input type="file" id="uploadFile" class="f-input form-control" name="avatar"
                                        value="{{ old('avatar') }}">
                                </div>
                                @error('avatar')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="form-group col-sm-6">
                                <label class="d-block mb-3">{{ __('custom.status') }} <span
                                        class="error">*</span></label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="status_yes"
                                        value="{{ \App\Models\Customer::STATUS_ACTIVE }}" name="status"
                                        class="custom-control-input" checked="">
                                    <label class="custom-control-label"
                                        for="status_yes">{{ __('custom.active') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="status_no"
                                        value="{{ \App\Models\Customer::STATUS_INACTIVE }}" name="status"
                                        class="custom-control-input">
                                    <label class="custom-control-label"
                                        for="status_no">{{ __('custom.inactive') }}</label>
                                </div>

                                @error('status')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group">
                            <div>
                                <button class="btn btn-primary waves-effect waves-lightml-2" type="submit">
                                    <i class="fa fa-save"></i> <span>{{ __('custom.submit') }}</span>
                                </button>
                                <a class="btn btn-danger waves-effect" href="{{ route('admin.customers.index') }}">
                                    <i class="fa fa-times"></i> <span>{{ __('custom.cancel') }}</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    /* ── Polished confirm popup (SweetAlert2) ────────────────────────── */
    .ic-swal-popup {
        border-radius: 18px !important;
        padding: 30px 30px 26px !important;
        box-shadow: 0 30px 70px rgba(16, 24, 40, .28), 0 6px 16px rgba(16, 24, 40, .08) !important;
    }
    .swal2-container.swal2-backdrop-show { background: rgba(15, 23, 42, .55) !important; }
    .ic-swal-title {
        font-size: 1.28rem !important; font-weight: 700 !important;
        letter-spacing: -.01em; color: #1a202c !important; margin: 14px 0 4px !important;
    }
    .ic-swal-html { font-size: .95rem !important; color: #5b6472 !important; line-height: 1.6 !important; }
    .ic-swal-icon { transform: scale(.86); margin: 8px auto 4px !important; }
    .ic-swal-confirm, .ic-swal-cancel {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 120px; margin: 10px 6px 0; padding: 11px 26px;
        border: 0; border-radius: 12px; font-size: .92rem; font-weight: 600; cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
    }
    .ic-swal-confirm i { margin-right: 6px; }
    .ic-swal-confirm--ok { color: #fff; background: linear-gradient(135deg, #38a169 0%, #2f855a 100%); box-shadow: 0 8px 20px rgba(47, 133, 90, .32); }
    .ic-swal-cancel { color: #4a5568; background: #edf2f7; }
    .ic-swal-confirm:hover, .ic-swal-cancel:hover { transform: translateY(-1px); filter: brightness(1.03); }
    .ic-swal-confirm:active, .ic-swal-cancel:active { transform: translateY(0); }
    .ic-swal-confirm:focus, .ic-swal-cancel:focus { outline: none; box-shadow: 0 0 0 4px rgba(56, 161, 105, .25); }
</style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {


            $('#billingSameAsAddress').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.b_hide_same').hide();
                    console.log('click')
                } else {
                    $('.b_hide_same').show();

                }
            });





        })
    </script>
    @include('includes.scripts.country_state_city_auto_load')
    @include('includes.scripts.country_state_city_auto_load_2')

<script>
$(function () {
    var $form = $('form.form-validate');
    var confirmed = false;

    // Press Enter on any text field to save (browser blocks it if a required field is empty).
    $form.on('keydown', 'input:not([type=file]):not([type=radio]):not([type=checkbox]):not(.select2-search__field)', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            $form.trigger('submit');
        }
    });

    // Ask for confirmation (Yes / No) just before creating the customer.
    // "Yes" is focused by default, so pressing Enter again confirms and creates.
    $form.on('submit', function (e) {
        if (confirmed) { return; } // second pass — let the real submit through
        e.preventDefault();

        var formEl = this;
        // Respect the browser's required-field validation first.
        if (typeof formEl.reportValidity === 'function' && !formEl.reportValidity()) {
            return;
        }

        if (typeof Swal !== 'undefined') {
            var icEnterHandler = null;
            Swal.fire({
                icon: 'question',
                title: 'Create this customer?',
                text: 'Do you want to save this customer?',
                showCancelButton: true,
                reverseButtons: true,
                focusConfirm: true,
                buttonsStyling: false,
                confirmButtonText: '<i class="fa fa-check"></i> Yes, create',
                cancelButtonText: 'No',
                customClass: {
                    popup: 'ic-swal-popup',
                    title: 'ic-swal-title',
                    htmlContainer: 'ic-swal-html',
                    icon: 'ic-swal-icon',
                    confirmButton: 'ic-swal-confirm ic-swal-confirm--ok',
                    cancelButton: 'ic-swal-cancel'
                },
                onOpen: function () {
                    var btn = Swal.getConfirmButton();
                    if (btn) { btn.focus(); }
                    icEnterHandler = function (ev) {
                        if ((ev.key === 'Enter' || ev.keyCode === 13) && Swal.isVisible()) {
                            ev.preventDefault();
                            ev.stopPropagation();
                            Swal.clickConfirm();
                        }
                    };
                    document.addEventListener('keydown', icEnterHandler, true);
                },
                onClose: function () {
                    if (icEnterHandler) {
                        document.removeEventListener('keydown', icEnterHandler, true);
                        icEnterHandler = null;
                    }
                }
            }).then(function (result) {
                if (result.value || result.isConfirmed) {
                    confirmed = true;
                    formEl.submit();
                }
            });
        } else if (confirm('Create this customer?')) {
            confirmed = true;
            formEl.submit();
        }
    });
});
</script>
@endpush
