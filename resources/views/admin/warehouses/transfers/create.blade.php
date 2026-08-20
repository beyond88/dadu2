@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.warehouses.index') }}">Warehouse</a></li>
                <li class="breadcrumb-item active">Transfer</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">Warehouse Stock Transfer</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.warehouse-transfers.store') }}" method="POST" id="transferForm">
                    @csrf

                    <div class="row mb-3">
                        {{-- Source Warehouse --}}
                        <div class="col-sm-5">
                            <label class="font-weight-bold">From Warehouse <span class="text-danger">*</span></label>
                            <select name="from_warehouse_id" id="fromWarehouse" class="form-control select2" required>
                                <option value="">— Select Source Warehouse —</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-2 d-flex align-items-end justify-content-center pb-1">
                            <i class="fas fa-arrow-right fa-lg text-muted"></i>
                        </div>

                        {{-- Destination Warehouse --}}
                        <div class="col-sm-5">
                            <label class="font-weight-bold">To Warehouse <span class="text-danger">*</span></label>
                            <select name="to_warehouse_id" id="toWarehouse" class="form-control select2" required>
                                <option value="">— Select Destination Warehouse —</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Products Table --}}
                    <div class="table-responsive mb-2" style="overflow:visible;">
                        <table class="table table-bordered table-sm" id="itemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product (Name / SKU)</th>
                                    <th style="width:130px;" class="text-center">Available Qty</th>
                                    <th style="width:170px;" class="text-center">Transfer Qty (KG)</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row" id="row-0">
                                    <td style="position:relative;">
                                        <input type="text" class="form-control form-control-sm product-search"
                                               placeholder="Search by name or SKU..."
                                               autocomplete="off" data-row="0" />
                                        <input type="hidden" name="items[0][product_stock_id]" class="product-stock-id" />
                                        <div class="search-dropdown" data-row="0"
                                             style="display:none;position:absolute;z-index:9999;left:8px;right:8px;top:100%;background:#fff;border:1px solid #ced4da;border-radius:4px;max-height:200px;overflow-y:auto;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="available-qty badge badge-secondary px-3 py-2">—</span>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]"
                                               class="form-control form-control-sm qty-input text-center"
                                               min="1" value="1" />
                                        <small class="qty-barrel-hint text-muted d-block text-center mt-1"></small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="padding:2px 8px;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="addRow">
                        <i class="fa fa-plus"></i> Add Product
                    </button>

                    {{-- Notes --}}
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional transfer notes">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-exchange-alt"></i> Transfer Stock
                    </button>
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary ml-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function () {
    let rowIndex = 1;

    // ── Add row ──────────────────────────────────────────────────────
    $('#addRow').on('click', function () {
        const row = `
        <tr class="item-row" id="row-${rowIndex}">
            <td style="position:relative;">
                <input type="text" class="form-control form-control-sm product-search"
                       placeholder="Search by name or SKU..."
                       autocomplete="off" data-row="${rowIndex}" />
                <input type="hidden" name="items[${rowIndex}][product_stock_id]" class="product-stock-id" />
                <div class="search-dropdown" data-row="${rowIndex}"
                     style="display:none;position:absolute;z-index:9999;left:8px;right:8px;top:100%;background:#fff;border:1px solid #ced4da;border-radius:4px;max-height:200px;overflow-y:auto;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                </div>
            </td>
            <td class="text-center">
                <span class="available-qty badge badge-secondary px-3 py-2">—</span>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]"
                       class="form-control form-control-sm qty-input text-center"
                       min="1" value="1" />
                <small class="qty-barrel-hint text-muted d-block text-center mt-1"></small>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="padding:2px 8px;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>`;
        $('#itemsBody').append(row);
        rowIndex++;
    });

    // ── Remove row ────────────────────────────────────────────────────
    $(document).on('click', '.remove-row', function () {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    // ── Product search ────────────────────────────────────────────────
    let searchTimer = null;

    $(document).on('input', '.product-search', function () {
        const input = $(this);
        const rowId = input.data('row');
        const q = input.val().trim();
        const dropdown = $(`.search-dropdown[data-row="${rowId}"]`);
        const fromWarehouse = $('#fromWarehouse').val();

        if (!fromWarehouse) {
            showAlert('Please select the source warehouse first.');
            input.val('');
            return;
        }

        clearTimeout(searchTimer);

        if (q.length < 1) {
            dropdown.hide().empty();
            return;
        }

        searchTimer = setTimeout(function () {
            $.get(`/admin/app/api/product-stocks/name-sku/search/${encodeURIComponent(q)}/${fromWarehouse}`)
                .done(function (res) {
                    dropdown.empty();
                    const results = res.data || [];
                    if (!results.length) {
                        dropdown.html('<div style="padding:8px 12px;color:#888;font-size:.8rem;">No products found.</div>').show();
                        return;
                    }
                    results.forEach(function (ps) {
                        const name  = ps.product?.name ?? '';
                        const sku   = ps.product?.sku ?? '';
                        const avail = ps.quantity ?? 0;
                        const item  = $('<div>')
                            .css({padding:'6px 12px', cursor:'pointer', fontSize:'.8rem', borderBottom:'1px solid #f5f5f5'})
                            .html(`<strong>${name}</strong> <span class="text-muted">(${sku})</span> — <span class="text-success">Avail: ${avail}</span>`)
                            .hover(function(){ $(this).css('background','#f0f4ff'); },
                                   function(){ $(this).css('background','#fff'); })
                            .on('mousedown', function (e) {
                                e.preventDefault();
                                input.val(`${name} (${sku})`);
                                input.closest('tr').find('.product-stock-id').val(ps.id);
                                input.closest('tr').find('.available-qty')
                                    .text(avail)
                                    .removeClass('badge-secondary badge-danger')
                                    .addClass(avail > 0 ? 'badge-success' : 'badge-danger');
                                // Weight based products are transferred in whole barrels,
                                // so the kg entered must be a multiple of kg_per_barrel.
                                const kgPerBarrel = parseFloat(ps.product?.kg_per_barrel) || 0;
                                const qtyInput = input.closest('tr').find('.qty-input');

                                qtyInput.attr('max', avail).data('kg-per-barrel', kgPerBarrel);

                                if (kgPerBarrel > 0) {
                                    qtyInput.attr({min: kgPerBarrel, step: kgPerBarrel})
                                            .val(avail >= kgPerBarrel ? kgPerBarrel : 0);
                                } else {
                                    qtyInput.attr({min: 1, step: 1}).val(Math.min(1, avail));
                                }
                                validateQty(qtyInput);
                                dropdown.hide().empty();
                            });
                        dropdown.append(item);
                    });
                    dropdown.show();
                });
        }, 300);
    });

    // Hide dropdown on blur
    $(document).on('blur', '.product-search', function () {
        const rowId = $(this).data('row');
        setTimeout(function () {
            $(`.search-dropdown[data-row="${rowId}"]`).hide();
        }, 200);
    });

    // ── Validate qty on input ─────────────────────────────────────────
    // Returns true when the row's kg is a whole number of barrels and within stock.
    function validateQty($input) {
        const max         = parseFloat($input.attr('max'));
        const kgPerBarrel = parseFloat($input.data('kg-per-barrel')) || 0;
        const val         = parseFloat($input.val()) || 0;
        const $hint       = $input.closest('td').find('.qty-barrel-hint');

        $input.closest('td').find('.qty-error').remove();
        $input.removeClass('is-invalid');
        $hint.text('');

        let error = null;

        if (val < 1) {
            error = kgPerBarrel > 0 ? `Minimum ${kgPerBarrel} kg (1 barrel).` : 'Minimum 1.';
        } else if (!isNaN(max) && val > max) {
            error = `Max available: ${max} kg.`;
        } else if (kgPerBarrel > 0 && Math.abs(val % kgPerBarrel) > 0.0001) {
            // e.g. 30 kg with a 25 kg barrel = 1.2 barrel — not allowed.
            const lower = Math.floor(val / kgPerBarrel) * kgPerBarrel;
            const upper = lower + kgPerBarrel;
            error = `Must be a multiple of ${kgPerBarrel} kg (1 barrel). Try ${lower > 0 ? lower + ' or ' : ''}${upper} kg.`;
        }

        if (error) {
            $input.addClass('is-invalid');
            $input.after(`<div class="qty-error invalid-feedback d-block" style="font-size:.75rem;">${error}</div>`);
            return false;
        }

        if (kgPerBarrel > 0) {
            const barrels = val / kgPerBarrel;
            $hint.text(`= ${barrels} barrel (1 barrel = ${kgPerBarrel} kg)`);
        }
        return true;
    }

    $(document).on('input', '.qty-input', function () {
        validateQty($(this));
    });

    // ── Reset product rows when source warehouse changes ──────────────
    $('#fromWarehouse').on('change', function () {
        $('.product-search').val('');
        $('.product-stock-id').val('');
        $('.available-qty').text('—').removeClass('badge-success badge-danger').addClass('badge-secondary');
        $('.qty-input').removeAttr('max').attr({min: 1, step: 1}).data('kg-per-barrel', 0)
            .removeClass('is-invalid').val(1);
        $('.qty-error').remove();
        $('.qty-barrel-hint').text('');
    });

    // ── Submit guard ─────────────────────────────────────────────────
    $('#transferForm').on('submit', function (e) {
        let valid = true;
        $('.item-row').each(function () {
            const stockId = $(this).find('.product-stock-id').val();
            if (!stockId) {
                valid = false;
                $(this).find('.product-search').addClass('is-invalid');
            }
            if (!validateQty($(this).find('.qty-input'))) {
                valid = false;
            }
        });
        if (!valid) {
            e.preventDefault();
            showAlert('Please fix the highlighted errors before submitting.');
        }
    });

    function showAlert(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire('Warning', msg, 'warning');
        } else {
            alert(msg);
        }
    }
});
</script>
@endpush
