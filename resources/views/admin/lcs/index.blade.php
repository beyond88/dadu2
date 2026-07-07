@extends('admin.layouts.master')



@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.lcs') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.lcs') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-bordered dt-responsive nowrap', 'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;']) !!}
                    </div>
                </div>
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
    #dataTableBuilder_wrapper .dt-buttons .btn { margin: 0 !important; }
    .datepicker.lc-dp {
        z-index: 99999 !important;
        font-size: 14px;
        min-width: 300px;
        padding: 8px;
    }
    .datepicker.lc-dp table { width: 100%; }
    .datepicker.lc-dp td,
    .datepicker.lc-dp th { width: 40px; height: 36px; font-size: 13px; }
    .datepicker.lc-dp .datepicker-switch,
    .datepicker.lc-dp .prev,
    .datepicker.lc-dp .next { font-size: 14px; padding: 6px; }
</style>
@endpush

@push('script')
@include('includes.scripts.datatable')
<script>
$(document).ready(function () {
    var $btnBar = $('#dataTableBuilder_wrapper .dt-buttons');
    $btnBar.append(
        '<span class="lc-date-range" style="display:inline-flex;align-items:center;margin-left:8px;gap:4px;vertical-align:middle;height:100%;">' +
            '<input type="text" id="lc_start_date" class="form-control form-control-sm" placeholder="Start Date" style="width:115px;" autocomplete="off" readonly>' +
            '<span class="text-muted px-1">—</span>' +
            '<input type="text" id="lc_end_date" class="form-control form-control-sm" placeholder="End Date" style="width:115px;" autocomplete="off" readonly>' +
        '</span>'
    );

    $('#lc_start_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('lc-dp'); })
        .on('changeDate', function (e) {
            $('#lc_end_date').datepicker('setStartDate', e.date);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        })
        .on('clearDate', function () {
            $('#lc_end_date').datepicker('setStartDate', null);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

    $('#lc_end_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('lc-dp'); })
        .on('changeDate clearDate', function () {
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

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
