@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.product') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.add_product') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card ic-compact-form">
                <div class="card-body">
                    <h4 class="header-title">{{ __('custom.add_product') }}</h4>
                    <form class="form-validate edit-font" action="{{ route('admin.products.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="form-group col-sm-12">
                                <label for="">{{ __('custom.name') }} <span class="error">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>



                            {{-- <div class="form-group col-sm-6">
                            <label for="">{{__('custom.sku')}} <span class="error">*</span></label>
                            <input type="text"
                                   name="sku"
                                   class="form-control" value="{{ $skuSetting['auto'] == 'yes' ? $skuSetting['generated_sku'] : '' }}"
                                   {{ $skuSetting['editable'] == 'no' ? 'readonly' : '' }}
                                   required>
                            @error('sku')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}

                            {{-- ===== Fields hidden per request (Category, Brand, Manufacturer, Model, Parts No, Weight Unit, Measurement Unit) ===== --}}
                            {{--
                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.category') }}</label>
                                <select name="category_id" class="form-control select2">
                                    <option value="">{{ __('custom.select_category') }}</option>
                                    @foreach ($categories as $item)
                                        <option {{ old('category_id') == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                        @foreach ($item->subCategory as $subCategory)
                                            @include('admin.product_categories.child-categories', [
                                                'sub_category' => $subCategory,
                                            ])
                                        @endforeach
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.brand') }}</label>
                                <select name="brand_id" class="form-control select2">
                                    <option value="">{{ __('custom.select_brand') }}</option>
                                    @foreach ($brands as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.manufacturer') }}</label>
                                <select name="manufacturer_id" class="form-control select2">
                                    <option value="">{{ __('custom.select_manufacturer') }}</option>
                                    @foreach ($manufacturers as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('manufacturer_id')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.model') }}</label>
                                <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                                @error('model')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.parts_no') }}</label>
                                <input type="text" name="parts_no" class="form-control" value="{{ old('parts_no') }}">
                                @error('parts_no')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.weight_unit') }}</label>
                                <select name="weight_unit_id" class="form-control select2">
                                    <option value="">{{ __('custom.select_weight_unit') }}</option>
                                    @foreach ($weight_units as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('weight_unit_id')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6">
                                        <label for=""
                                            >{{ __('custom.measurement_unit') }}</label>
                                        <select name="measurement_unit_id" class="form-control select2">
                                            <option value="">{{ __('custom.select_measurement_unit') }}</option>
                                            @foreach ($measurement_units as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('measurement_unit_id')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                            </div>
                            --}}

                            {{-- Tax/Vat hidden per request (default: included) --}}
                            <input type="hidden" name="tax_status" value="{{ \App\Models\Product::TAX_INCLUDED }}">

                            {{-- Custom tax amount hidden per request
                            <div id="custom-tax" class="form-group col-sm-6">
                                <label for="">{{ __('custom.custom_tax_amount') }} (%)</label>
                                <input type="number" name="custom_tax" class="form-control"
                                    value="{{ old('custom_tax') }}" min="0" step="any">
                                @error('custom_tax')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                            --}}
                            {{-- Notes hidden per request
                            <div class="form-group col-sm-12">
                                <label for="">{{ __('custom.notes') }}</label>
                                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                                @error('notes')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                            --}}

                            {{-- Description hidden per request --}}
                           <div class="form-group col-sm-12">
                            <input type="hidden" name="is_variant" value="0">

                                {{-- Is variant product toggle hidden per request --}}
                                <div class="d-flex align-items-center" style="display:none !important;">
                                    <label class="switch mb-0">
                                        <input type="checkbox" id="isVariant" name="is_variant" value="1"
                                            {{ old('is_variant', 0) == 1 ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <label for="isVariant" class="mb-0 ml-2">
                                        {{ __('custom.is_variant_product') }}
                                    </label>
                                </div>

                                @error('is_variant')
                                    <p class="error">{{ $message }}</p>
                                @enderror



                            <div class="col-sm-12 attribute_section">
                                {{-- <label for="">{{ __('custom.attributes') }}</label> --}}
                            <product-attribute-add
                            :attributes="{{ $attributes }}"
                            :product-id="{{ $nextId }}"
                            :validation-errors="{{json_encode((object) $errors->messages()) }}"
                            :old-variants="{{ json_encode(old('variants', [])) }}"
                        ></product-attribute-add>




                            </div>
                            @error('attribute_data')
                            <p class="error">{{ $message }}</p>
                            @enderror

                        <div id="normal-fields-wrapper">
                         <div class="row">

                             <div class="form-group col-sm-6">
                                <label for="">Product Code <span class="error">*</span></label>
                                <input type="text" name="sku" class="form-control"
                                    value="{{ old('sku') }}" required>
                                @error('sku')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>



                            <div class="form-group col-sm-6">
                                <label class="d-block">{{ __('custom.barrel_product') }}</label>
                                <div class="custom-control custom-switch mt-2">
                                    {{-- Barrel Product is always ON — every product is sold by weight, so the switch is locked. --}}
                                    <input type="hidden" name="is_weight_based" value="1">
                                    <input type="checkbox" class="custom-control-input" id="is_weight_based"
                                        name="is_weight_based" value="1" checked disabled onclick="return false;">
                                    <label class="custom-control-label" for="is_weight_based">{{ __('custom.sold_by_weight_hint') }}</label>
                                </div>
                            </div>

                            <div class="form-group col-sm-6 barrel-only" style="{{ old('is_weight_based', 1) ? '' : 'display:none;' }}">
                                <label for="kg_per_barrel">{{ __('custom.kg_per_barrel') }} <span class="error">*</span></label>
                                <input type="number" name="kg_per_barrel" id="kg_per_barrel" class="form-control"
                                    value="{{ old('kg_per_barrel') }}" step="any" min="0.01" placeholder="e.g. 25" required>
                                @error('kg_per_barrel')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6 barrel-only" style="{{ old('is_weight_based', 1) ? '' : 'display:none;' }}">
                                <label for="barrel_label">{{ __('custom.barrel_label') }} <span class="error">*</span></label>
                                <input type="text" name="barrel_label" id="barrel_label" class="form-control"
                                    value="{{ old('barrel_label', 'Barrel') }}" placeholder="{{ __('custom.barrel_label_placeholder') }}">
                                @error('barrel_label')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.buying_price') }}({{ currencySymbol() }})
                                    <small class="text-muted barrel-only" style="{{ old('is_weight_based', 1) ? '' : 'display:none;' }}">{{ __('custom.per_kg') }}</small>
                                    <span class="error">*</span></label>
                                <input type="number" name="buying_price" class="form-control"
                                    value="{{ old('buying_price') }}" step="any" min="0" required>
                                @error('buying_price')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6 barrel-only" style="{{ old('is_weight_based', 1) ? '' : 'display:none;' }}">
                                <label for="selling_price_per_kg">{{ __('custom.selling_price') }}({{ currencySymbol() }})
                                    <small class="text-muted">{{ __('custom.per_kg') }}</small>
                                    <span class="error">*</span></label>
                                <input type="number" name="selling_price_per_kg" id="selling_price_per_kg"
                                    class="form-control" value="{{ old('selling_price_per_kg') }}" step="any" min="0">
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="">{{ __('custom.selling_price') }}({{ currencySymbol() }})
                                    <span class="error">*</span></label>
                                <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}"
                                    step="any" required>
                                @error('price')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Weight hidden per request --}}
                            <input type="hidden" name="weight" value="{{ old('weight') }}">
                             <div class="col-sm-6">

                                <div class="row">
                                    {{-- Length / Width / Depth hidden per request --}}
                                    <input type="hidden" name="dimension_l" value="{{ old('dimension_l') }}">
                                    <input type="hidden" name="dimension_w" value="{{ old('dimension_w') }}">
                                    <input type="hidden" name="dimension_d" value="{{ old('dimension_d') }}">
                           </div>


                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-group col-sm-6 mt-3" style="display:none;">


                            <div class="form-group col-sm-6">
                                {{-- Status hidden per request (default: active) --}}
                                <input type="hidden" name="status" value="{{ \App\Models\Product::STATUS_ACTIVE }}">

                                {{-- Available For hidden per request (default: both) --}}
                                <input type="hidden" name="available_for" value="{{ \App\Models\Product::SALE_AVAILABLE_FOR['all'] }}">


                                {{-- Is Split sale hidden per request
                                <div class="custom-control custom-checkbox">
                                    <label for="" class=" "></label><br>
                                    <input class="form-check-input custom-control-input" type="checkbox" value="1"
                                        id="split_sale" name="split_sale">
                                    <label class="form-check-label custom-control-label checkbox-label" for="split_sale">
                                        {{ __('custom.is_split_sale') }}
                                    </label>
                                </div>
                                --}}



                                <input type="hidden" name="is_batch_product" value="0">
                                {{-- Is batch product hidden per request
                                <div class="custom-control custom-checkbox">
                                    <label for="" class=" "></label><br>
                                    <input class="form-check-input custom-control-input" type="checkbox" value="1"
                                        id="isBatch" name="is_batch_product"  {{ old('is_batch_product', 0) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label custom-control-label checkbox-label" for="isBatch">
                                        {{ __('custom.is_batch_product') }}
                                    </label>
                                </div>
                                --}}

                                @error('is_batch_product')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6 mt-3" style="display:none;">
                            {{-- Sub Products --}}
                            <div class="card" style="box-shadow: none;">
                                <div class="card-header" style="border-bottom: none;background-color: transparent;">
                                    <input type="hidden" name="enable_sub_products" value="0">
                                    {{-- Enable Package toggle hidden per request --}}
                                    <div class="d-flex align-items-center" style="display:none !important;">
                                        <label class="switch mb-0">
                                            <input type="checkbox" id="enableSubProducts" name="enable_sub_products" value="1"
                                                {{ old('enable_sub_products') ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <label for="enableSubProducts" class="mb-0 ml-2 font-weight-bold">
                                            Enable Package
                                        </label>
                                    </div>
                                </div>
                                <div id="subProductsSection" style="{{ old('enable_sub_products') ? '' : 'display:none;' }}">
                                    <div class="card-body">
                                        <table class="table table-bordered" id="subProductsTable">
                                            <thead>
                                                <tr>
                                                    <th>Sub Product</th>
                                                    <th style="width:120px">Qty to Deduct</th>
                                                    <th style="width:50px"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="subProductsBody">
                                                @foreach(old('sub_products', []) as $i => $sp)
                                                @php $oldSubProduct = \App\Models\Product::find($sp['product_id']); @endphp
                                                <tr class="sub-product-row">
                                                    <td>
                                                        <select name="sub_products[{{ $i }}][product_id]" class="form-control sub-product-select" style="width:100%">
                                                            @if($oldSubProduct)
                                                                <option value="{{ $oldSubProduct->id }}" selected>{{ $oldSubProduct->name }} ({{ $oldSubProduct->sku }})</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="sub_products[{{ $i }}][quantity]" class="form-control" value="{{ $sp['quantity'] ?? 1 }}" min="1">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-sub-product"><i class="fa fa-times"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-secondary btn-sm" id="addSubProduct">
                                            <i class="fa fa-plus"></i> Add Sub Product
                                        </button>
                                    </div>
                                </div>
                                
                            </div>

                            {{-- Enable Carton --}}
                            <div class="card mt-2" style="box-shadow:none;">
                                <div class="card-header" style="border-bottom:none;background-color:transparent;">
                                    <input type="hidden" name="enable_carton" value="0">
                                    {{-- Enable Carton toggle hidden per request --}}
                                    <div class="d-flex align-items-center" style="display:none !important;">
                                        <label class="switch mb-0">
                                            <input type="checkbox" id="enableCarton" name="enable_carton" value="1"
                                                {{ old('enable_carton') ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <label for="enableCarton" class="mb-0 ml-2 font-weight-bold">
                                            Enable Carton
                                        </label>
                                    </div>
                                </div>
                                <div id="cartonSection" style="{{ old('enable_carton') ? '' : 'display:none;' }}">
                                    <div class="card-body pt-2">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <label class="mb-1" style="font-size:.8rem;font-weight:600;">Qty per Carton</label>
                                                <input type="number" name="carton_qty" id="cartonQty"
                                                       class="form-control form-control-sm"
                                                       value="{{ old('carton_qty', 1) }}" min="1"
                                                       placeholder="e.g. 12">
                                            </div>
                                            <div class="col-sm-7">
                                                <label class="mb-1" style="font-size:.8rem;font-weight:600;">Carton Product</label>
                                                <select name="carton_product_id" id="cartonProductSelect" class="form-control form-control-sm" style="width:100%">
                                                    @if(old('carton_product_id'))
                                                        @php $oldCarton = \App\Models\Product::find(old('carton_product_id')); @endphp
                                                        @if($oldCarton)
                                                            <option value="{{ $oldCarton->id }}" selected>{{ $oldCarton->name }} ({{ $oldCarton->sku }})</option>
                                                        @endif
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>

                        <div class="form-group mt-3 text-right">
                            <div>
                                <button class="btn btn-primary waves-effect waves-lightml-2" type="submit">
                                    <i class="fa fa-save"></i> <span>{{ __('custom.submit') }}</span>
                                </button>

                                <input type="hidden" id="is_submit_with_stock" name="is_submit_with_stock">
                                <button class="btn btn-info waves-effect waves-lightml-2" type="submit"
                                    id="submit_with_stock">
                                    <i class="fa fa-save"></i> <span>{{ __('custom.submit_with_stock') }}</span>
                                </button>
                                <a class="btn btn-danger waves-effect" href="{{ route('admin.products.index') }}">
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
        .ic-swal-popup { border-radius: 18px !important; padding: 30px 30px 26px !important; box-shadow: 0 30px 70px rgba(16,24,40,.28), 0 6px 16px rgba(16,24,40,.08) !important; }
        .swal2-container.swal2-backdrop-show { background: rgba(15,23,42,.55) !important; }
        .ic-swal-title { font-size: 1.28rem !important; font-weight: 700 !important; letter-spacing: -.01em; color: #1a202c !important; margin: 14px 0 4px !important; }
        .ic-swal-html { font-size: .95rem !important; color: #5b6472 !important; line-height: 1.6 !important; }
        .ic-swal-icon { transform: scale(.86); margin: 8px auto 4px !important; }
        .ic-swal-confirm, .ic-swal-cancel { display: inline-flex; align-items: center; justify-content: center; min-width: 120px; margin: 10px 6px 0; padding: 11px 26px; border: 0; border-radius: 12px; font-size: .92rem; font-weight: 600; cursor: pointer; transition: transform .12s ease, box-shadow .12s ease, filter .12s ease; }
        .ic-swal-confirm i { margin-right: 6px; }
        .ic-swal-confirm--ok { color: #fff; background: linear-gradient(135deg,#38a169 0%,#2f855a 100%); box-shadow: 0 8px 20px rgba(47,133,90,.32); }
        .ic-swal-cancel { color: #4a5568; background: #edf2f7; }
        .ic-swal-confirm:hover, .ic-swal-cancel:hover { transform: translateY(-1px); filter: brightness(1.03); }
        .ic-swal-confirm:active, .ic-swal-cancel:active { transform: translateY(0); }
        .ic-swal-confirm:focus, .ic-swal-cancel:focus { outline: none; box-shadow: 0 0 0 4px rgba(56,161,105,.25); }

        /* Add Product form — full width, same input design as the transaction entry page */
        .ic-compact-form .card-body { padding: 14px 16px; }
        .ic-compact-form .header-title { font-size: 1rem; }
        .ic-compact-form label { margin-bottom: 1px; font-weight: 600; font-size: .72rem; color: #495057; text-transform: uppercase; letter-spacing: .3px; }
        .ic-compact-form .form-group { margin-bottom: 10px; }
        /* checkbox / radio / switch labels stay normal-case */
        .ic-compact-form .custom-control-label { text-transform: none; font-size: .85rem; font-weight: 500; color: #212529; letter-spacing: 0; }

        /* Clearly-bordered, compact inputs */
        .ic-compact-form .form-control {
            height: 32px;
            padding: 2px 8px;
            font-size: .85rem;
            border: 1px solid #b0b7c3;
            border-radius: 4px;
            background: #fff;
        }
        .ic-compact-form textarea.form-control { height: auto; }
        .ic-compact-form select.form-control { height: 32px; }
        .ic-compact-form .form-control:focus {
            border-color: #4a7dff;
            box-shadow: 0 0 0 2px rgba(74, 125, 255, .18);
            background: #fbfcff;
        }

        /* Select2 sized to match the bordered inputs */
        .ic-compact-form .select2-container--default .select2-selection--single {
            height: 32px;
            border: 1px solid #b0b7c3;
            border-radius: 4px;
        }
        .ic-compact-form .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px; padding-left: 8px; font-size: .85rem; }
        .ic-compact-form .select2-container--default .select2-selection--single .select2-selection__arrow { height: 30px; }

        /* Shorter description editor so it doesn't dominate the page */
        .ic-compact-form .jodit-wysiwyg,
        .ic-compact-form .note-editable,
        .ic-compact-form .jodit-workplace {
            min-height: 70px !important;
            max-height: 140px;
        }

        /* White card with a gray inset panel — matches the transaction entry page */
        .ic-compact-form > .card-body { background: #fff; }
        .ic-compact-form form.form-validate {
            background: #f4f6f9;
            border: 1px solid #dbe0e6;
            border-radius: 8px;
            padding: 18px 18px 6px;
        }
        /* Nested cards (sub-products / carton) stay clean on the gray panel */
        .ic-compact-form form.form-validate .card { background: transparent; }
        .ic-compact-form .form-control[readonly] { background: #e9ecef; font-weight: 600; }
    </style>
@endpush

@push('script')
<script>
$(document).ready(function () {

    // Press Enter in any field to create the product. It clicks the primary
    // Submit button, so the browser's built-in required-field validation runs
    // first — the form only submits when every required field is filled.
    $('form.form-validate').on('keydown', 'input:not([type=hidden]):not(textarea)', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            // While the confirm popup is open, let SweetAlert handle Enter.
            if (typeof Swal !== 'undefined' && Swal.isVisible()) return;
            // Let select2 search boxes handle Enter for choosing an option.
            if ($(e.target).closest('.select2-container, .select2-search').length) return;
            e.preventDefault();
            $(this).closest('form').find('button[type=submit]').first().click();
        }
    });

    // Confirm (Yes / No) before actually creating the product. "Yes" is focused,
    // so after Enter opens the popup, pressing Enter again confirms & creates.
    var icProductConfirmed = false;
    $('form.form-validate').on('submit', function (e) {
        if (icProductConfirmed) { return; } // second pass — allow the real submit
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
                title: 'Create this product?',
                text: 'Do you want to save this product?',
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
                    // Guarantee Enter confirms while the popup is open.
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
                    icProductConfirmed = true;
                    formEl.submit();
                }
            });
        } else if (confirm('Create this product?')) {
            icProductConfirmed = true;
            formEl.submit();
        }
    });

    // Barrel / sold-by-weight product: show kg-per-barrel + "per kg" hints.
    // When sold by weight, the Selling Price field is locked and auto-computed
    // as (Selling Price per kg x Kg per Barrel); the computed total is saved.
    function recalcSellingPrice() {
        var isBarrel = $('#is_weight_based').is(':checked');
        if (isBarrel) {
            var perKg = parseFloat($('#selling_price_per_kg').val()) || 0;
            var kg    = parseFloat($('#kg_per_barrel').val()) || 0;
            $('#price').val(perKg && kg ? +(perKg * kg).toFixed(4) : '').prop('readonly', true);
        } else {
            $('#price').prop('readonly', false);
        }
    }

    function toggleBarrelFields() {
        var isBarrel = $('#is_weight_based').is(':checked');
        $('.barrel-only').toggle(isBarrel);
        // On edit (or after a validation error) backfill the per-kg field from the
        // stored final price so the locked Selling Price stays consistent.
        if (isBarrel && !$('#selling_price_per_kg').val()) {
            var price = parseFloat($('#price').val()) || 0;
            var kg    = parseFloat($('#kg_per_barrel').val()) || 0;
            if (price && kg) {
                $('#selling_price_per_kg').val(+(price / kg).toFixed(4));
            }
        }
        recalcSellingPrice();
    }
    $('#is_weight_based').on('change', toggleBarrelFields);
    $('#selling_price_per_kg, #kg_per_barrel').on('input', recalcSellingPrice);
    toggleBarrelFields();

    let lastClicked = null; // track last clicked checkbox

    function toggleAttributeSection() {
        if (lastClicked === 'variant') {
            $('#isBatch').prop('checked', false); // uncheck batch
            $('.attribute_section').show();
            $('#normal-fields-wrapper').hide();
            $('#normal-fields-wrapper :input').prop('required', false);
        } else if (lastClicked === 'batch') {
            $('#isVariant').prop('checked', false); // uncheck variant
            $('.attribute_section').hide();
            $('#normal-fields-wrapper').show();
            $('#normal-fields-wrapper :input').prop('required', false);
        } else {
            $('.attribute_section').hide();
            $('#normal-fields-wrapper').show();
            $('#normal-fields-wrapper :input[data-required="true"]').prop('required', true);
        }
    }

    // Store which inputs are originally required
    $('#normal-fields-wrapper :input[required]').each(function () {
        $(this).attr('data-required', 'true');
    });

    // --- Restore old values on page load ---
    @if(old('is_variant'))
        lastClicked = 'variant';
        $('#isVariant').prop('checked', true);
    @elseif(old('is_batch_product'))
        lastClicked = 'batch';
        $('#isBatch').prop('checked', true);
    @endif

    // Initial load
    toggleAttributeSection();

    // Track which checkbox is clicked last
    $('#isVariant').click(function () {
        lastClicked = $(this).is(':checked') ? 'variant' : null;
        toggleAttributeSection();
    });

    $('#isBatch').click(function () {
        lastClicked = $(this).is(':checked') ? 'batch' : null;
        toggleAttributeSection();
    });

    // Sub Products
    $('#enableSubProducts').on('change', function () {
        $('#subProductsSection').toggle(this.checked);
    });

    $('#addSubProduct').on('click', function () {
        addSubProductRow();
    });

    $(document).on('click', '.remove-sub-product', function () {
        $(this).closest('tr').remove();
        reindexSubProducts();
    });

    function addSubProductRow(productId, productLabel, qty) {
        var idx = $('#subProductsBody tr').length;
        var row = '<tr class="sub-product-row">' +
            '<td>' +
            '<select name="sub_products[' + idx + '][product_id]" class="form-control sub-product-select" style="width:100%">' +
            (productId ? '<option value="' + productId + '" selected>' + productLabel + '</option>' : '') +
            '</select>' +
            '</td>' +
            '<td><input type="number" name="sub_products[' + idx + '][quantity]" class="form-control" value="' + (qty || 1) + '" min="1"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm remove-sub-product"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
        $('#subProductsBody').append(row);
        initSelect2OnRow($('#subProductsBody tr:last .sub-product-select'));
    }

    function reindexSubProducts() {
        $('#subProductsBody tr').each(function (i) {
            $(this).find('[name^="sub_products["]').each(function () {
                var name = $(this).attr('name').replace(/sub_products\[\d+\]/, 'sub_products[' + i + ']');
                $(this).attr('name', name);
            });
        });
    }

    function buildStockLabel(s) {
        var sku = (s.product && s.product.is_variant == 1 && s.variation) ? s.variation.sku : (s.product ? s.product.sku : '');
        var name = s.product ? s.product.name : '';
        var variant = (s.product && s.product.is_variant == 1 && s.variation) ? ' (' + s.variation.name + ')' : '';
        var warehouse = s.warehouse ? ' - ' + s.warehouse.name : '';
        return '(' + sku + ') ' + name + variant + warehouse;
    }

    function initSelect2OnRow(el) {
        el.select2({
            placeholder: 'Search product by name or SKU',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: function (params) {
                    return '/admin/api/product-stock/search/name-sku/' + encodeURIComponent(params.term);
                },
                dataType: 'json',
                delay: 300,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (s) {
                            return { id: s.product_id, text: buildStockLabel(s) };
                        })
                    };
                }
            }
        });
    }

    // Init Select2 on any rows restored via old()
    $('.sub-product-select').each(function () { initSelect2OnRow($(this)); });

    // ── Enable Carton ──────────────────────────────────────
    $('#enableCarton').on('change', function () {
        $('#cartonSection').toggle(this.checked);
    });

    $('#cartonProductSelect').select2({
        placeholder: 'Search carton product by name or SKU',
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: function (params) {
                return '/admin/api/product-stock/search/name-sku/' + encodeURIComponent(params.term);
            },
            dataType: 'json',
            delay: 300,
            processResults: function (data) {
                return {
                    results: $.map(data, function (s) {
                        return { id: s.product_id, text: buildStockLabel(s) };
                    })
                };
            }
        }
    });
});
</script>
@endpush

