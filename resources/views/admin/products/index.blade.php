@extends('admin.layouts.master')

@section('content')
@if ($errors->any())
<div class="alert alert-danger mt-2">
  <ul class="mb-0">
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.products') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.products') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- Summary Cards -->
                <div class="row mb-4">
                    @php
                        $totalStockValue = \App\Models\Product::all()->sum(function($product) {
                            return $product->stock_value;
                        });
                        $totalProducts = \App\Models\Product::count();
                        $totalStockQuantity = \App\Models\Product::sum('stock');
                    @endphp
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white mb-2">{{ __('custom.total_stock_value') }}</h6>
                                        <h3 class="text-white mb-0">{{ currencySymbol() }}{{ number_format($totalStockValue, 2) }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="mdi mdi-currency-usd fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white mb-2">{{ __('custom.total_products') }}</h6>
                                        <h3 class="text-white mb-0">{{ number_format($totalProducts) }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="mdi mdi-package-variant fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white mb-2">{{ __('custom.total_stock_quantity') }}</h6>
                                        <h3 class="text-white mb-0">{{ number_format($totalStockQuantity) }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="mdi mdi-archive fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <h4 class="header-title">{{ __('custom.product_list') }}</h4>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7 text-right">
                        <!-- Barcode Download -->
                        <a class="btn btn-sm btn-primary mb-4" href="javascript:void(0)" id="download_barcode">
                            {{ __('custom.download_all_barcode') }}
                        </a>
                        <form action="{{ route('admin.products.barcode.download.zip') }}" method="post" id="download_form" style="display: none">
                            @csrf
                            <input type="text" name="product_ids" id="product_ids">
                        </form>
                    </div>
                </div>

                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">{{ __('custom.import') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label for="file">{{ __('custom.import') }} (Excel/CSV)</label>
            <input type="file" name="import_file" id="import_file" class="form-control" required accept=".xlsx,.csv">
            </div>
            <a href="{{ static_asset('sample_products_import.xlsx') }}" class="btn btn-link">{{ __('custom.download_sample') }}</a>
        </div>
        <div class="text-end ml-3">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('custom.cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('custom.import') }}</button>
        </div>
      </form>
      <table class="table table-bordered table-sm mt-4">
        <tbody>
          <tr>
            <td>{{__('custom.name')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.sku')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.barcode')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.price')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.status')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.category')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>Others fields</td>
            <th><span class="border border-warning text-warning fs-6 fw-normal p-1">Others fields are not required</span></th>
          </tr>

        </tbody>
      </table>
      </form>
    </div>
  </div>
</div>
@endsection

@push('style')
@include('includes.styles.datatable')
<style>
    #dataTableBuilder_wrapper .dt-buttons {
        display: inline-flex !important;
        align-items: center !important;
        flex-wrap: wrap;
        gap: 2px;
    }
    #dataTableBuilder_wrapper .dt-buttons .btn {
        margin: 0 !important;
    }
    .datepicker.pd-dp {
        z-index: 99999 !important;
        font-size: 14px;
        min-width: 300px;
        padding: 8px;
    }
    .datepicker.pd-dp table { width: 100%; }
    .datepicker.pd-dp td,
    .datepicker.pd-dp th { width: 40px; height: 36px; font-size: 13px; }
    .datepicker.pd-dp .datepicker-switch,
    .datepicker.pd-dp .prev,
    .datepicker.pd-dp .next { font-size: 14px; padding: 6px; }
</style>
@endpush

@push('script')
@include('includes.scripts.datatable')
<script src="{{ static_asset('admin/js/bulk_barcode_download.js') }}"></script>
<script>
    // Copy product code to clipboard (delegated — rows are rendered by DataTables).
    $(document).on('click', '.ic-copy-code', function () {
        var code = String($(this).data('code') || '');
        var $icon = $(this).find('i');
        function flash() {
            $icon.removeClass('mdi-content-copy text-muted').addClass('mdi-check text-success');
            setTimeout(function () { $icon.removeClass('mdi-check text-success').addClass('mdi-content-copy text-muted'); }, 1200);
        }
        function fallbackCopy(text) {
            var t = document.createElement('textarea');
            t.value = text; t.style.position = 'fixed'; t.style.opacity = '0';
            document.body.appendChild(t); t.focus(); t.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(t);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(flash).catch(function () { fallbackCopy(code); flash(); });
        } else {
            fallbackCopy(code); flash();
        }
    });
$(document).ready(function () {
    var $btnBar = $('#dataTableBuilder_wrapper .dt-buttons');
    $btnBar.append(
        '<span class="pd-date-range" style="display:inline-flex;align-items:center;margin-left:8px;gap:4px;vertical-align:middle;height:100%;">' +
            '<input type="text" id="pd_start_date" class="form-control form-control-sm" placeholder="Start Date" style="width:115px;" autocomplete="off" readonly>' +
            '<span class="text-muted px-1">—</span>' +
            '<input type="text" id="pd_end_date" class="form-control form-control-sm" placeholder="End Date" style="width:115px;" autocomplete="off" readonly>' +
        '</span>'
    );

    $('#pd_start_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('pd-dp'); })
        .on('changeDate', function (e) {
            $('#pd_end_date').datepicker('setStartDate', e.date);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        })
        .on('clearDate', function () {
            $('#pd_end_date').datepicker('setStartDate', null);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

    $('#pd_end_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('pd-dp'); })
        .on('changeDate clearDate', function () {
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

    // Override print button to open in a new tab
    window.LaravelDataTables['dataTableBuilder'].buttons('.buttons-print').action(function (e, dt) {
        var url = dt.ajax.url() || '';
        var params = dt.ajax.params();
        params.action = 'print';
        var fullUrl = (url.indexOf('?') > -1 ? url + '&' : url + '?') + $.param(params);
        window.open(fullUrl, '_blank');
    });
});
</script>
@endpush
