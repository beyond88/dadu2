@extends('admin.layouts.master')
@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __t('sales') }}</a></li>
                    <li class="breadcrumb-item active">{{ __t('return') . ' ' . __t('sales') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('includes.messages.validation')
                <form action="{{ route('admin.sales-return.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $sales->id }}">
                    {{--                <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}"> --}}

                    <div class="card-body">
                        <h4 class="header-title">{{ __t('return') . ' ' . __t('sales') }}</h4>

                        @php
                            $customer = (object) $sales->customer;
                            $billing_info = (object) $sales->billing_info;
                            $shipping_info = (object) $sales->shipping_info;
                        @endphp
                        <div class="row">
                            <div class="col-lg-6 col-md-8 col-xl-6 ml-auto">
                                <table class="ic-purchase-print" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="text-right"><b>{{ __t('sale_number') }}</b></td>
                                        <td>:</td>
                                        <td class="text-right">{{ make8digits($sales->id) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>{{ __t('sale_date') }}</b></td>
                                        <td>:</td>
                                        <td class="text-right">{{ custom_date($sales->date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>{{ __t('customer_name') }}</b></td>
                                        <td>:</td>
                                        <td class="text-right">{{ optional($customer)->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>{{ __t('customer_phone') }}</b></td>
                                        <td>:</td>
                                        <td class="text-right">{{ optional($customer)->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>{{ __t('customer_email') }}</b></td>
                                        <td>:</td>
                                        <td class="text-right">{{ optional($customer)->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>{{ __t('warehouse') }}</b></td>
                                        <td>:</td>
                                        <td class="text-right">{{ $warehouse->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <h4 class="mt-0 header-title">{{ __t('billing_info') }}</h4>
                                <table class="ic-purchase-print" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><b>{{ __t('name') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($billing_info)->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('email') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($billing_info)->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('phone') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($billing_info)->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('address_line_1') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($billing_info)->address_line_1 }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('address_line_2') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($billing_info)->address_line_2 }}</td>
                                    </tr>
                                    {{-- Country, State, City, Zip hidden per request
                                    <tr>
                                        <td><b>{{ __t('country') }}</b></td>
                                        <td>:</td>

                                        <td>{{ !empty($billing_info->country) ? \App\Models\SystemCountry::findOrFail($billing_info->country)->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('state') }}</b></td>
                                        <td>:</td>
                                        <td>{{ !empty($billing_info->state) ? \App\Models\SystemState::findOrFail($billing_info->state)->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('city') }}</b></td>
                                        <td>:</td>
                                        <td>{{ !empty($billing_info->city) ? \App\Models\SystemCity::findOrFail($billing_info->city)->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('zip') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($billing_info)->zip }}</td>
                                    </tr>
                                    --}}
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <h4 class="mt-0 header-title">{{ __t('shipping_info') }}</h4>
                                <table class="ic-purchase-print" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><b>{{ __t('name') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($shipping_info)->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('email') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($shipping_info)->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('phone') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($shipping_info)->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('address_line_1') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($shipping_info)->address_line_1 }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('address_line_2') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($shipping_info)->address_line_2 }}</td>
                                    </tr>
                                    {{-- Country, State, City, Zip hidden per request
                                    <tr>
                                        <td><b>{{ __t('country') }}</b></td>
                                        <td>:</td>
                                        <td>{{ !empty($shipping_info->country) ? \App\Models\SystemCountry::findOrFail($shipping_info->country)->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('state') }}</b></td>
                                        <td>:</td>
                                        <td>{{ !empty($shipping_info->state) ? \App\Models\SystemState::findOrFail($shipping_info->state)->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('city') }}</b></td>
                                        <td>:</td>
                                        <td>{{ !empty($shipping_info->city) ? \App\Models\SystemCity::findOrFail($shipping_info->city)->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{ __t('zip') }}</b></td>
                                        <td>:</td>
                                        <td>{{ optional($shipping_info)->zip }}</td>
                                    </tr>
                                    --}}
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="date" class="pt-2">{{ __t('return_date') }} <span
                                            class="error">*</span></label>

                                    <div class="form-group">
                                        <input type="text" class="form-control datepicker-autoclose" name="return_date"
                                            id="date" value="{{ old('return_date') ?? date('Y-m-d') }}" required
                                            placeholder="{{ __t('date') }}" autocomplete="off">
                                    </div>
                                    @error('date')
                                        <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="return_note" class="pt-2">{{ __t('return_note') }} </label>
                                    <textarea name="return_note" id="return_note" class="form-control" placeholder="{{ __t('note') }}">{{ old('return_note') }}</textarea>

                                    @error('return_note')
                                        <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="warehouse" class="pt-2">{{ __t('warehouse') }} </label>
                                    <select class="form-control" name="warehouse_id" id="warehouse">
                                        <option value="">{{ __t('select_warehouse') }}</option>
                                        @foreach ($warehouses as $house)
                                            <option value="{{ $house->id }}"
                                                {{ $house->id == $warehouse->id ? 'selected' : '' }}>{{ $house->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('warehouse_id')
                                        <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered ic-table-return">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="align-middle">{{ __t('sl') }}</th>
                                                <th rowspan="2" class="align-middle">{{ __t('sku') }}</th>
                                                <th rowspan="2" class="align-middle">{{ __t('product_name') }}</th>
                                                <th colspan="3" class="text-center">{{ __t('sales') }}</th>
                                                <th class="text-center">{{ __t('available') }}</th>
                                                <th colspan="5" class="text-center">Return / Damage / Loss</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __t('quantity') }}</th>
                                                <th>{{ __t('price') }}
                                                    <small>({{ __('custom.with_tax_and_discount') }})</small>
                                                </th>
                                                <th>{{ __t('sub_total') }}</th>
                                                <th>{{ __t('quantity') }}</th>
                                                <th>Return Qty <small class="text-success">(Good)</small></th>
                                                <th>Damage Qty <small class="text-warning">(Bad)</small></th>
                                                <th>Lost Qty <small class="text-danger">(Unknown)</small></th>
                                                <th>{{ __t('price') }}</th>
                                                <th>{{ __t('sub_total') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $saleTotal = 0;
                                            @endphp
                                            @foreach ($sales->items as $key => $item)
                                                <input type="hidden" name="invoice_details_id[]"
                                                    value="{{ $item->id }}">
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        {{ $item->sku }}
                                                        <input type="hidden" name="product_id[]"
                                                            value="{{ $item->product_id }}">
                                                        <input type="hidden" name="product_stock_id[]"
                                                            value="{{ $item->product_stock_id }}">
                                                        <input type="hidden" name="attribute_id[]"
                                                            value="{{ optional($item->productStock)->attribute_id }}">
                                                        <input type="hidden" name="attribute_item_id[]"
                                                            value="{{ optional($item->productStock)->attribute_item_id }}">
                                                        <input type="hidden" name="product_sku[]"
                                                            value="{{ $item->sku }}">
                                                        <input type="hidden" name="price[]"
                                                            value="{{ $item->price }}">
                                                        <input type="hidden" name="discount[]"
                                                            value="{{ $item->discount }}">
                                                        <input type="hidden" name="discount_type[]"
                                                            value="{{ $item->discount_type }}">
                                                    </td>
                                                    <td>
                                                        {{ $item->product_name }}
                                                        @if ($item->product->is_variant != null && $item->product->is_variant == 1 && isset($item->productStock))
                                                            ({{ optional(optional($item->productStock)->attribute)->name ?? '' }}
                                                            :
                                                            {{ optional(optional($item->productStock)->attributeItem)->name ?? '' }})
                                                        @endif
                                                        <input type="hidden" name="product_name[]"
                                                            value="{{ $item->product_name }}">
                                                    </td>
                                                    <td>
                                                        {{ $item->quantity }}
                                                        <input type="hidden" value="{{ $item->quantity }}"
                                                            id="sale_quantity_{{ $item->id }}">
                                                    </td>
                                                    <td>
                                                        @php
                                                            $itemPrice = $item->price;
                                                            $itemDiscount = $item->discount;
                                                            $price = 0;
                                                            $tax = 0;
                                                            if ($item->quantity > 0 && $item->tax > 0) {
                                                                $tax = ($item->price * $item->tax) / 100;
                                                            }
                                                            if ($item->discount_type == 'percent') {
                                                                $price =
                                                                    $itemPrice -
                                                                    ($itemPrice * $itemDiscount) / 100 +
                                                                    $tax;
                                                            } else {
                                                                $price = $itemPrice - $itemDiscount + $tax;
                                                            }
                                                        @endphp
                                                        {{ $price }}

                                                        <input type="hidden" value="{{ $price }}"
                                                            id="sale_price_{{ $price }}">
                                                    </td>
                                                    <td>{{ $price * $item->quantity }}</td>

                                                    <td>
                                                        @php
                                                            $availableQty = $item->quantity
                                                                - $item->salesReturnItems->sum('return_qty')
                                                                - $item->salesReturnItems->sum('damage_qty')
                                                                - $item->salesReturnItems->sum('lost_qty');
                                                            $isWb = (bool) optional($item->product)->is_weight_based;
                                                            $kgpb = (float) optional($item->product)->kg_per_barrel;
                                                            $wbLabel = optional($item->product)->barrel_label ?: __('custom.barrels');
                                                            $availableDisplay = $isWb && $kgpb ? $availableQty * $kgpb : $availableQty;
                                                        @endphp
                                                        <input type="text" readonly class="form-control"
                                                            id="available_qty_{{ $item->id }}"
                                                            value="{{ $availableDisplay }}">
                                                        @if($isWb)
                                                            <small class="text-muted d-block">kg (1 {{ $wbLabel }} = {{ $kgpb }} kg)</small>
                                                        @endif
                                                        @if ($availableQty == 0)
                                                            <span class="text-danger">{{ __t('no_available_quantity') }}</span>
                                                        @endif
                                                    </td>

                                                    {{-- Good return --}}
                                                    <td>
                                                        @if($isWb)
                                                            {{-- weight-based: enter KG, auto-convert to barrels --}}
                                                            <input type="number" step="any" min="0" class="form-control ic-sale-return-qty damage-loss-qty ic-wb-extra-kg"
                                                                rel="{{ $item->id }}" id="return_qty_{{ $item->id }}" value="0"
                                                                data-kgpb="{{ $kgpb }}" data-qtytarget="return_qty_hidden_{{ $item->id }}"
                                                                data-barrelspan="return_qty_barrel_{{ $item->id }}"
                                                                placeholder="{{ __('custom.weight_kg') }}">
                                                            <input type="hidden" name="return_qty[]" id="return_qty_hidden_{{ $item->id }}" value="0">
                                                            <small class="text-muted d-block">= <span id="return_qty_barrel_{{ $item->id }}">0</span> {{ $wbLabel }} (1 {{ $wbLabel }} = {{ $kgpb }} kg)</small>
                                                        @else
                                                            <input type="number" min="0" class="form-control ic-sale-return-qty damage-loss-qty"
                                                                rel="{{ $item->id }}" name="return_qty[]"
                                                                id="return_qty_{{ $item->id }}" value="0">
                                                        @endif
                                                    </td>
                                                    {{-- Damage --}}
                                                    <td>
                                                        @if($isWb)
                                                            <input type="number" step="any" min="0" class="form-control damage-loss-qty ic-wb-extra-kg"
                                                                rel="{{ $item->id }}" id="damage_qty_{{ $item->id }}" value="0"
                                                                data-kgpb="{{ $kgpb }}" data-qtytarget="damage_qty_hidden_{{ $item->id }}"
                                                                data-barrelspan="damage_qty_barrel_{{ $item->id }}"
                                                                placeholder="{{ __('custom.weight_kg') }}">
                                                            <input type="hidden" name="damage_qty[]" id="damage_qty_hidden_{{ $item->id }}" value="0">
                                                            <small class="text-muted d-block">= <span id="damage_qty_barrel_{{ $item->id }}">0</span> {{ $wbLabel }}</small>
                                                        @else
                                                            <input type="number" min="0" class="form-control damage-loss-qty"
                                                                rel="{{ $item->id }}" name="damage_qty[]"
                                                                id="damage_qty_{{ $item->id }}" value="0">
                                                        @endif
                                                    </td>
                                                    {{-- Lost --}}
                                                    <td>
                                                        @if($isWb)
                                                            <input type="number" step="any" min="0" class="form-control damage-loss-qty ic-wb-extra-kg"
                                                                rel="{{ $item->id }}" id="lost_qty_{{ $item->id }}" value="0"
                                                                data-kgpb="{{ $kgpb }}" data-qtytarget="lost_qty_hidden_{{ $item->id }}"
                                                                data-barrelspan="lost_qty_barrel_{{ $item->id }}"
                                                                placeholder="{{ __('custom.weight_kg') }}">
                                                            <input type="hidden" name="lost_qty[]" id="lost_qty_hidden_{{ $item->id }}" value="0">
                                                            <small class="text-muted d-block">= <span id="lost_qty_barrel_{{ $item->id }}">0</span> {{ $wbLabel }}</small>
                                                        @else
                                                            <input type="number" min="0" class="form-control damage-loss-qty"
                                                                rel="{{ $item->id }}" name="lost_qty[]"
                                                                id="lost_qty_{{ $item->id }}" value="0">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small id="row_error_{{ $item->id }}" class="text-danger" style="display:none;font-size:.75rem;"></small>
                                                        @if($isWb)
                                                            @php $wbPerKg = $kgpb ? $price / $kgpb : $price; @endphp
                                                            <input type="text" class="form-control"
                                                                id="return_price_{{ $item->id }}"
                                                                value="{{ $wbPerKg }}" readonly>
                                                            <small class="text-muted d-block">{{ __('custom.per_kg') }}</small>
                                                            <input type="hidden" name="return_price[]" value="{{ $price }}">
                                                        @else
                                                            <input type="text" class="form-control" name="return_price[]"
                                                                id="return_price_{{ $item->id }}"
                                                                value="{{ $price }}" readonly>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control sub_total"
                                                            name="return_sub_total[]"
                                                            id="return_subtotal_{{ $item->id }}" readonly>
                                                    </td>
                                                </tr>
                                                @php
                                                    $saleTotal += $price;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-right">{{ __t('total') }}:</th>
                                                <th class="text-right">{{ $saleTotal }}</th>
                                                <th colspan="5" class="text-right">{{ __t('total') }}:</th>
                                                <th class="text-right">
                                                    <input name="total" class="form-control form-control-sm total" readonly>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 mt-3">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-save"></i> <span>{{ __t('submit') }}</span>
                                </button>
                                <a class="btn btn-danger" href="{{ route('admin.sales-return.createable_list') }}">
                                    <i class="fa fa-times"></i> <span>{{ __t('cancel') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
$(document).ready(function () {

    // Validate that return_qty + damage_qty + lost_qty <= available_qty per row
    $(document).on('input', '.damage-loss-qty', function () {
        const itemId   = $(this).attr('rel');
        const available = parseFloat($('#available_qty_' + itemId).val()) || 0;
        const returnQty = parseFloat($('#return_qty_' + itemId).val())  || 0;
        const damageQty = parseFloat($('#damage_qty_'  + itemId).val())  || 0;
        const lostQty   = parseFloat($('#lost_qty_'    + itemId).val())  || 0;
        const total     = returnQty + damageQty + lostQty;

        if (total > available) {
            $(this).addClass('is-invalid');
            $('#row_error_' + itemId).text(
                'Total (Return + Damage + Lost = ' + total + ') exceeds available qty (' + available + ').'
            ).show();
        } else {
            $('.damage-loss-qty[rel="' + itemId + '"]').removeClass('is-invalid');
            $('#row_error_' + itemId).hide();
        }
    });

    // ── Weight-based: KG entered must resolve to WHOLE barrels/drums ──
    // (server-side validation also enforces this)
    function fractionalBarrels($el) {
        var kgpb = parseFloat($el.data('kgpb')) || 0;
        var kg   = parseFloat($el.val()) || 0;
        if (!kgpb || !kg) return false;
        var b = kg / kgpb;
        return Math.abs(b - Math.round(b)) > 1e-9;
    }
    function refreshWbInt($el) {
        var bad  = fractionalBarrels($el);
        var $td  = $el.closest('td');
        var $err = $td.find('.wb-int-error');
        $el.toggleClass('is-invalid', bad);
        if (bad) {
            if (!$err.length) {
                $err = $('<small class="wb-int-error d-block text-danger" style="font-size:.75rem;"></small>');
                $td.append($err);
            }
            $err.text('Quantity must be whole barrels/drums (no fractional).');
        } else {
            $err.remove();
        }
    }
    $(document).on('input change keyup', '.ic-wb-extra-kg', function () { refreshWbInt($(this)); });

    // Block form submit if any row exceeds available qty OR has fractional barrels
    $('form').on('submit', function (e) {
        let valid = true;
        $('[id^="available_qty_"]').each(function () {
            const itemId    = this.id.replace('available_qty_', '');
            const available = parseFloat($(this).val()) || 0;
            const returnQty = parseFloat($('#return_qty_' + itemId).val())  || 0;
            const damageQty = parseFloat($('#damage_qty_'  + itemId).val())  || 0;
            const lostQty   = parseFloat($('#lost_qty_'    + itemId).val())  || 0;
            if ((returnQty + damageQty + lostQty) > available) {
                valid = false;
            }
        });
        let fractional = false;
        $('.ic-wb-extra-kg').each(function () {
            if (fractionalBarrels($(this))) { fractional = true; refreshWbInt($(this)); }
        });
        if (!valid || fractional) {
            e.preventDefault();
            e.stopPropagation();

            // Bring the first highlighted field into view for quick correction.
            var $firstBad = $('.is-invalid').first();
            if ($firstBad.length) {
                $('html, body').animate({ scrollTop: $firstBad.offset().top - 150 }, 300);
                setTimeout(function () { $firstBad.trigger('focus'); }, 320);
            }

            // Exceeding available qty is the more critical error — show it first.
            var opts = !valid ? {
                icon: 'error',
                title: 'Quantity exceeds available',
                html: 'One or more rows exceed the <b>available quantity</b>.<br>Please fix the highlighted field(s).'
            } : {
                icon: 'warning',
                title: 'Whole units only',
                html: 'One or more quantities are not whole <b>barrels/drums</b>.<br>Please enter an amount equal to whole units.'
            };

            if (typeof Swal !== 'undefined') {
                var isWarn = opts.icon === 'warning';
                Swal.fire($.extend({
                    confirmButtonText: 'Got it',
                    buttonsStyling: false,
                    focusConfirm: true,
                    showClass: { popup: 'swal2-show ic-swal-pop-in' },
                    customClass: {
                        popup: 'ic-swal-popup',
                        title: 'ic-swal-title',
                        htmlContainer: 'ic-swal-html',
                        icon: 'ic-swal-icon',
                        confirmButton: 'ic-swal-confirm ' + (isWarn ? 'ic-swal-confirm--warn' : 'ic-swal-confirm--err')
                    }
                }, opts));
            } else {
                alert(opts.title + '\n\n' + opts.html.replace(/<[^>]+>/g, ''));
            }
        }
    });
});
</script>
@endpush


@push('style')
<style>
    /* ── Polished validation popup (SweetAlert2) ─────────────────────── */
    .ic-swal-popup {
        border-radius: 18px !important;
        padding: 32px 32px 26px !important;
        box-shadow: 0 30px 70px rgba(16, 24, 40, .28), 0 6px 16px rgba(16, 24, 40, .08) !important;
    }
    .swal2-container.swal2-backdrop-show {
        background: rgba(15, 23, 42, .55) !important;
    }
    .ic-swal-title {
        font-size: 1.3rem !important;
        font-weight: 700 !important;
        letter-spacing: -.01em;
        color: #1a202c !important;
        margin: 14px 0 4px !important;
    }
    .ic-swal-html {
        font-size: .95rem !important;
        color: #5b6472 !important;
        line-height: 1.6 !important;
    }
    .ic-swal-html b { color: #2d3748; font-weight: 700; }

    /* Icon: slightly smaller & softer */
    .ic-swal-icon { transform: scale(.86); margin: 8px auto 4px !important; border-width: 3px !important; }

    /* Branded pill confirm button */
    .ic-swal-confirm {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 134px;
        margin-top: 10px;
        padding: 11px 32px;
        border: 0;
        border-radius: 12px;
        font-size: .92rem;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
    }
    .ic-swal-confirm:hover  { transform: translateY(-1px); filter: brightness(1.04); }
    .ic-swal-confirm:active { transform: translateY(0); }
    .ic-swal-confirm:focus  { outline: none; box-shadow: 0 0 0 4px rgba(59, 130, 246, .28) !important; }
    .ic-swal-confirm--err   { background: linear-gradient(135deg, #3b82f6 0%, #2b6cb0 100%); box-shadow: 0 8px 20px rgba(43, 108, 176, .35); }
    .ic-swal-confirm--warn  { background: linear-gradient(135deg, #f6ad55 0%, #dd6b20 100%); box-shadow: 0 8px 20px rgba(221, 107, 32, .32); }

    /* Gentle pop-in animation */
    @keyframes icSwalPopIn {
        0%   { opacity: 0; transform: translateY(8px) scale(.96); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .ic-swal-pop-in { animation: icSwalPopIn .22s cubic-bezier(.21, 1.02, .73, 1) both; }
</style>
@endpush
