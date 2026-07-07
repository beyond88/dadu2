@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.accounts') }}</a></li>
                <li class="breadcrumb-item active">{{ __('custom.account_list') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <h4 class="header-title">{{ __('custom.account_list') }}</h4>
                    </div>

                    <div class="col-lg-7 col-md-7 col-sm-7 text-right">
                    </div>
                </div>

                {!! $dataTable->table(['class' => 'nowrap']) !!}
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
    .datepicker.ac-dp {
        z-index: 99999 !important;
        font-size: 14px;
        min-width: 300px;
        padding: 8px;
    }
    .datepicker.ac-dp table { width: 100%; }
    .datepicker.ac-dp td,
    .datepicker.ac-dp th { width: 40px; height: 36px; font-size: 13px; }
    .datepicker.ac-dp .datepicker-switch,
    .datepicker.ac-dp .prev,
    .datepicker.ac-dp .next { font-size: 14px; padding: 6px; }
</style>
@endpush

@push('script')
@include('includes.scripts.datatable')
<script>
$(document).ready(function () {
    var $btnBar = $('#dataTableBuilder_wrapper .dt-buttons');
    $btnBar.append(
        '<span class="ac-date-range" style="display:inline-flex;align-items:center;margin-left:8px;gap:4px;vertical-align:middle;height:100%;">' +
            '<input type="text" id="ac_start_date" class="form-control form-control-sm" placeholder="Start Date" style="width:115px;" autocomplete="off" readonly>' +
            '<span class="text-muted px-1">—</span>' +
            '<input type="text" id="ac_end_date" class="form-control form-control-sm" placeholder="End Date" style="width:115px;" autocomplete="off" readonly>' +
        '</span>'
    );

    $('#ac_start_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('ac-dp'); })
        .on('changeDate', function (e) {
            $('#ac_end_date').datepicker('setStartDate', e.date);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        })
        .on('clearDate', function () {
            $('#ac_end_date').datepicker('setStartDate', null);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

    $('#ac_end_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('ac-dp'); })
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

