@extends('admin.layouts.master')

@section('content')

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.product') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.edit_product') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card ic-compact-form">
            <div class="card-body">
                <h4 class="header-title">{{ __('custom.edit_product') }}</h4>
                <form class="form-validate edit-font" action="{{ route('admin.products.update', $product->id) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        <div class="form-group col-sm-12">
                            <label for="">{{ __('custom.name') }} <span class="error">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                            @error('name')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="form-group col-sm-6" style="display:none;">{{-- Category hidden per request (now optional) --}}
                            <label for="">{{ __('custom.category') }}</label>
                            <select name="category_id" class="form-control select2">
                                <option value="">{{ __('custom.select_category') }}</option>
                                @foreach ($categories as $item)
                                    <option {{ $product->category_id == $item->id ? 'selected' : '' }}
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

                        <div class="form-group col-sm-6" style="display:none;">{{-- Brand hidden per request --}}
                            <label for="">{{ __('custom.brand') }}</label>
                            <select name="brand_id" class="form-control select2">
                                <option value="">{{ __('custom.select_brand') }}</option>
                                @foreach ($brands as $item)
                                    <option value="{{ $item->id }}" @if($item->id == $product->brand_id) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-6" style="display:none;">{{-- Manufacturer hidden per request --}}
                            <label for="">{{ __('custom.manufacturer') }}</label>
                            <select name="manufacturer_id" class="form-control select2">
                                <option value="">{{ __('custom.select_manufacturer') }}</option>
                                @foreach ($manufacturers as $item)
                                    <option value="{{ $item->id }}" @if($item->id == $product->manufacturer_id) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('manufacturer_id')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-6" style="display:none;">{{-- Model hidden per request --}}
                            <label for="">{{ __('custom.model') }}</label>
                            <input type="text" name="model" class="form-control" value="{{ $product->model }}">
                            @error('model')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-sm-6" style="display:none;">{{-- Parts No hidden per request --}}
                            <label for="">{{ __('custom.parts_no') }}</label>
                            <input type="text" name="parts_no" class="form-control" value="{{ old('parts_no', $product->parts_no) }}">
                            @error('parts_no')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-6" style="display:none;">{{-- Weight Unit hidden per request --}}
                            <label for="">{{ __('custom.weight_unit') }}</label>
                            <select name="weight_unit_id" class="form-control select2">
                                <option value="">{{ __('custom.select_weight_unit') }}</option>
                                @foreach ($weight_units as $item)
                                    <option value="{{ $item->id }}" @if($item->id == $product->weight_unit_id) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('weight_unit_id')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-6" style="display:none;">{{-- Measurement Unit hidden per request --}}
                            <label for="" >{{ __('custom.measurement_unit') }}</label>
                            <select name="measurement_unit_id" class="form-control select2">
                                <option value="">{{ __('custom.select_measurement_unit') }}</option>
                                @foreach ($measurement_units as $item)
                                    <option value="{{ $item->id }}" @if($item->id == $product->measurement_unit_id) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('measurement_unit_id')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        @php
                            // Tax/Vat counts as enabled only when the product actually carries
                            // tax — included, or a custom rate was set. Otherwise the switch is off.
                            $taxEnabled = (bool) old('tax_enabled',
                                ($product->tax_status == \App\Models\Product::TAX_INCLUDED || (float) $product->custom_tax > 0) ? 1 : 0);
                            $taxStatusOld = old('tax_status',
                                $product->tax_status ?: \App\Models\Product::TAX_EXCLUDED);
                        @endphp
                        <div class="form-group col-sm-6">
                            <label class="d-block mb-2">{{ __('custom.tax') }}</label>
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="tax_enabled"
                                    name="tax_enabled" value="1" {{ $taxEnabled ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tax_enabled">{{ __('custom.tax') }}</label>
                            </div>

                            <div id="tax-status-options" style="{{ $taxEnabled ? '' : 'display:none;' }}">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="tax_include"
                                        value="{{ \App\Models\Product::TAX_INCLUDED }}" name="tax_status"
                                        class="custom-control-input"
                                        {{ $taxStatusOld == \App\Models\Product::TAX_INCLUDED ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="tax_include">{{ __('custom.include') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="tax_exclude"
                                        value="{{ \App\Models\Product::TAX_EXCLUDED }}" name="tax_status"
                                        class="custom-control-input"
                                        {{ $taxStatusOld == \App\Models\Product::TAX_EXCLUDED ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="tax_exclude">{{ __('custom.exclude') }}</label>
                                </div>
                            </div>

                            @error('tax_status')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="custom-tax" class="form-group col-sm-6"
                            style="{{ $taxEnabled && $taxStatusOld == \App\Models\Product::TAX_INCLUDED ? '' : 'display:none;' }}">
                            <label for="">{{ __('custom.custom_tax_amount') }} (%)</label>
                            <input type="number" name="custom_tax" class="form-control"
                                value="{{ old('custom_tax', $product->custom_tax) }}" min="0" step="any">
                            @error('custom_tax')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12" style="display:none;">{{-- Notes hidden per request --}}
                            <label for="">{{ __('custom.notes') }}</label>
                            <input type="text" name="notes" class="form-control" value="{{ $product->notes }}">
                            @error('notes')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12" style="display:none;">{{-- Description hidden per request --}}
                            <label for="">{{ __('custom.desc') }}</label>
                            <textarea class="form-control summernote" name="desc">{{ $product->desc }}</textarea>
                            @error('desc')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12">
                           <input type="hidden" name="is_variant" value="{{ $product->is_variant ? 1 : 0 }}">

                           <div class="col-sm-12 attribute_section">
                                <product-attribute-edit
                                    :attributes="{{ $attributes->toJson() }}"
                                    :product="{{ $product->toJson() }}"
                                    :variants="{{ $variants->toJson() }}"
                                    :validation-errors="{{ json_encode((object) $errors->messages()) }}">
                                </product-attribute-edit>
                            </div>


                            @error('attribute_data')
                                <p class="error">{{ $message }}</p>
                            @enderror

                            <div id="normal-fields-wrapper">
                                <div class="row">

                                    <div class="form-group col-sm-6">
                                        <label for="">Product Code <span class="error">*</span></label>
                                        <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                                        @error('sku')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="d-block">{{ __('custom.barrel_product') }}</label>
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="hidden" name="is_weight_based" value="0">
                                            <input type="checkbox" class="custom-control-input" id="is_weight_based"
                                                name="is_weight_based" value="1" {{ old('is_weight_based', $product->is_weight_based) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_weight_based">{{ __('custom.sold_by_weight_hint') }}</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6 barrel-only" style="{{ old('is_weight_based', $product->is_weight_based) ? '' : 'display:none;' }}">
                                        <label for="kg_per_barrel">{{ __('custom.kg_per_barrel') }}</label>
                                        <input type="number" name="kg_per_barrel" id="kg_per_barrel" class="form-control"
                                            value="{{ old('kg_per_barrel', $product->kg_per_barrel) }}" step="any" min="0" placeholder="e.g. 25">
                                        @error('kg_per_barrel')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6 barrel-only" style="{{ old('is_weight_based', $product->is_weight_based) ? '' : 'display:none;' }}">
                                        <label for="barrel_label">{{ __('custom.barrel_label') }} <span class="error">*</span></label>
                                        <input type="text" name="barrel_label" id="barrel_label" class="form-control"
                                            value="{{ old('barrel_label', $product->barrel_label ?: 'Barrel') }}" placeholder="{{ __('custom.barrel_label_placeholder') }}">
                                        @error('barrel_label')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('custom.buying_price') }}({{ currencySymbol() }})
                                            <small class="text-muted barrel-only" style="{{ old('is_weight_based', $product->is_weight_based) ? '' : 'display:none;' }}">{{ __('custom.per_kg') }}</small>
                                            <span class="error">*</span></label>
                                        <input type="number" name="buying_price" class="form-control"
                                            value="{{ old('buying_price', $product->buying_price) }}" step="any" min="0" required>
                                        @error('buying_price')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6 barrel-only" style="{{ old('is_weight_based', $product->is_weight_based) ? '' : 'display:none;' }}">
                                        <label for="selling_price_per_kg">{{ __('custom.selling_price') }}({{ currencySymbol() }})
                                            <small class="text-muted">{{ __('custom.per_kg') }}</small>
                                            <span class="error">*</span></label>
                                        <input type="number" name="selling_price_per_kg" id="selling_price_per_kg"
                                            class="form-control"
                                            value="{{ old('selling_price_per_kg', ($product->is_weight_based && $product->kg_per_barrel > 0) ? round($product->price / $product->kg_per_barrel, 4) : '') }}"
                                            step="any" min="0">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('custom.selling_price') }}({{ currencySymbol() }})
                                            <span class="error">*</span></label>
                                        <input type="number" name="price" id="price" class="form-control" value="{{ $product->price }}"
                                            step="any" required>
                                        @error('price')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-6" style="display:none;">{{-- Weight hidden per request --}}
                                        <label for="">{{ __('custom.weight') }}</label>
                                        <input type="number" step="any" name="weight" class="form-control"
                                            value="{{ $product->weight }}" min="0">
                                        @error('weight')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6" style="display:none;">{{-- Length / Width / Depth hidden per request --}}
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label for="" >{{ __('custom.length') }}</label>
                                                <input type="number" name="dimension_l" class="form-control" min="0"
                                                    step="any" value="{{ $product->dimension_l }}">
                                                @error('dimension_l')
                                                    <p class="error">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="form-group col-sm-3">
                                                <label for="" >{{ __('custom.width') }}</label>
                                                <input type="number" name="dimension_w" class="form-control" min="0"
                                                    value="{{ $product->dimension_w }}" step="any">
                                                @error('dimension_w')
                                                    <p class="error">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="form-group col-sm-3">
                                                <label for="" >{{ __('custom.depth') }}</label>
                                                <input type="number" name="dimension_d" class="form-control" min="0"
                                                    value="{{ $product->dimension_d }}" step="any">
                                                @error('dimension_d')
                                                    <p class="error">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group col-sm-6" style="display:none;">{{-- Status / Available For hidden per request --}}
                            <label class="d-block mb-3">{{ __('custom.status') }} <span class="error">*</span></label>
                            <div
                                class="custom-control custom-control custom-checkbox custom-radio custom-control-inline">
                                <input type="radio" id="status_yes" {{ old('status',$product->status) == \App\Models\Product::STATUS_ACTIVE ? 'checked' : '' }}  value="{{ \App\Models\Product::STATUS_ACTIVE }}"
                                    name="status" class="custom-control-input" checked="">
                                <label class="custom-control-label" for="status_yes">{{__('custom.active')}}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="status_no"
                                    value="{{ \App\Models\Product::STATUS_INACTIVE }}" name="status"
                                    class="custom-control-input" {{ old('status',$product->status) == \App\Models\Product::STATUS_INACTIVE ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="status_no">{{ __('custom.inactive') }}</label>
                            </div>

                            @error('status')
                                <p class="error">{{ $message }}</p>
                            @enderror

                            <label class="d-block mb-3 mt-3">{{ __('custom.available_for') }} <span
                                    class="error">*</span></label>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="available_for_customer"
                                    value="{{ \App\Models\Product::SALE_AVAILABLE_FOR['customer'] }}"
                                    name="available_for" class="custom-control-input" @if($product->available_for == \App\Models\Product::SALE_AVAILABLE_FOR['customer'] ) checked="" @endif>
                                <label class="custom-control-label"
                                    for="available_for_customer">{{ __('custom.customer') }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="available_for_warehouse"
                                    value="{{ \App\Models\Product::SALE_AVAILABLE_FOR['warehouse'] }}"
                                    name="available_for" class="custom-control-input" @if($product->available_for == \App\Models\Product::SALE_AVAILABLE_FOR['warehouse'] ) checked="" @endif>
                                <label class="custom-control-label"
                                    for="available_for_warehouse">{{ __('custom.warehouse') }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="available_for_all"
                                    value="{{ \App\Models\Product::SALE_AVAILABLE_FOR['all'] }}" name="available_for"
                                    class="custom-control-input" @if($product->available_for == \App\Models\Product::SALE_AVAILABLE_FOR['all'] ) checked="" @endif>
                                <label class="custom-control-label"
                                    for="available_for_all">{{ __('custom.both') }}</label>
                            </div>

                            @error('available_for')
                                <p class="error">{{ $message }}</p>
                            @enderror

                            <div class="custom-control custom-checkbox">
                                <label for="" class=" "></label><br>
                                <input class="form-check-input custom-control-input" type="checkbox" value="1"
                                    id="split_sale" name="split_sale" {{ $product->split_sale ? 'checked' : '' }}>
                                <label class="form-check-label custom-control-label checkbox-label" for="split_sale">
                                    {{ __('custom.is_split_sale') }}
                                </label>
                            </div>

                            <input type="hidden" name="is_batch_product" value="{{ $product->is_batch_product ? 1 : 0 }}">
                        </div>

                        <div class="col-sm-6" style="display:none;">
                            {{-- Sub Products --}}
                            @php $existingSubProducts = $product->subProducts ?? collect(); @endphp
                            <div class="card">
                                <div class="card-header">
                                    <input type="hidden" name="enable_sub_products" value="0">
                                    {{-- Enable Package toggle hidden per request --}}
                                    <div class="d-flex align-items-center" style="display:none !important;">
                                        <label class="switch mb-0">
                                            <input type="checkbox" id="enableSubProducts" name="enable_sub_products" value="1"
                                                {{ $existingSubProducts->isNotEmpty() ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <label for="enableSubProducts" class="mb-0 ml-2 font-weight-bold">
                                            Enable Package
                                        </label>
                                    </div>
                                </div>
                                <div id="subProductsSection" style="{{ $existingSubProducts->isNotEmpty() ? '' : 'display:none;' }}">
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
                                                @foreach($existingSubProducts as $i => $sub)
                                                <tr class="sub-product-row">
                                                    <td>
                                                        <select name="sub_products[{{ $i }}][product_id]" class="form-control sub-product-select" style="width:100%">
                                                            <option value="{{ $sub->id }}" selected>{{ $sub->name }} ({{ $sub->sku }})</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="sub_products[{{ $i }}][quantity]" class="form-control" value="{{ $sub->pivot->quantity }}" min="1">
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
                            @php $existingCarton = $product->carton; @endphp
                            <div class="card mt-2" style="box-shadow:none;">
                                <div class="card-header" style="border-bottom:none;background-color:transparent;">
                                    <input type="hidden" name="enable_carton" value="0">
                                    {{-- Enable Carton toggle hidden per request --}}
                                    <div class="d-flex align-items-center" style="display:none !important;">
                                        <label class="switch mb-0">
                                            <input type="checkbox" id="enableCarton" name="enable_carton" value="1"
                                                {{ $existingCarton ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <label for="enableCarton" class="mb-0 ml-2 font-weight-bold">
                                            Enable Carton
                                        </label>
                                    </div>
                                </div>
                                <div id="cartonSection" style="{{ $existingCarton ? '' : 'display:none;' }}">
                                    <div class="card-body pt-2">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <label class="mb-1" style="font-size:.8rem;font-weight:600;">Qty per Carton</label>
                                                <input type="number" name="carton_qty" id="cartonQty"
                                                       class="form-control form-control-sm"
                                                       value="{{ $existingCarton->qty_per_carton ?? 1 }}" min="1"
                                                       placeholder="e.g. 12">
                                            </div>
                                            <div class="col-sm-7">
                                                <label class="mb-1" style="font-size:.8rem;font-weight:600;">Carton Product</label>
                                                <select name="carton_product_id" id="cartonProductSelect" class="form-control form-control-sm" style="width:100%">
                                                    @if($existingCarton && $existingCarton->cartonProduct)
                                                        <option value="{{ $existingCarton->cartonProduct->id }}" selected>
                                                            {{ $existingCarton->cartonProduct->name }} ({{ $existingCarton->cartonProduct->sku }})
                                                        </option>
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

        /* Edit Product form — full width, same input design as the transaction entry page */
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
        .ic-compact-form form.form-validate .card { background: transparent; box-shadow: none; }
        .ic-compact-form .form-control[readonly] { background: #e9ecef; font-weight: 600; }
    </style>
@endpush

@push('script')
<script>
$(document).ready(function () {

    // Tax/Vat switch. Turning it off falls back to Exclude
    // (tax_status is required server side) and clears any custom rate.
    $('#tax_enabled').on('change', function () {
        var enabled = $(this).is(':checked');
        $('#tax-status-options').toggle(enabled);

        if (enabled) {
            $('#custom-tax').toggle($('#tax_include').is(':checked'));
        } else {
            $('#tax_exclude').prop('checked', true);
            $('#custom-tax').hide().find('input[name="custom_tax"]').val('');
        }
    });

    // Press Enter in any field to save the product. It clicks the primary
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

    // Confirm (Yes / No) before saving. "Yes" is focused, so after Enter opens
    // the popup, pressing Enter again confirms & updates.
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
                title: 'Update this product?',
                text: 'Do you want to save the changes?',
                showCancelButton: true,
                reverseButtons: true,
                focusConfirm: true,
                buttonsStyling: false,
                confirmButtonText: '<i class="fa fa-check"></i> Yes, update',
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
        } else if (confirm('Update this product?')) {
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
        // Backfill the per-kg field from the stored final price when missing so the
        // locked Selling Price stays consistent.
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

    // --- Set initial state based on existing product data ---
    @if($product->is_variant)
        lastClicked = 'variant';
        $('#isVariant').prop('checked', true);
    @elseif($product->is_batch_product)
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
                            if (s.product_id == {{ $product->id }}) return null;
                            return { id: s.product_id, text: buildStockLabel(s) };
                        }).filter(Boolean)
                    };
                }
            }
        });
    }

    // Init Select2 on pre-populated rows from existing relations
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
                        if (s.product_id == {{ $product->id }}) return null;
                        return { id: s.product_id, text: buildStockLabel(s) };
                    }).filter(Boolean)
                };
            }
        }
    });
});
</script>
@endpush
