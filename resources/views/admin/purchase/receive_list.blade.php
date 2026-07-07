@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __t('purchases') }}</a></li>
                <li class="breadcrumb-item active">{{ __t('purchase_receive_list') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">{{ __t('purchase_receive_list') }}</h4>
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
    .datepicker.pr-dp {
        z-index: 99999 !important;
        font-size: 14px;
        min-width: 300px;
        padding: 8px;
    }
    .datepicker.pr-dp table { width: 100%; }
    .datepicker.pr-dp td,
    .datepicker.pr-dp th { width: 40px; height: 36px; font-size: 13px; }
    .datepicker.pr-dp .datepicker-switch,
    .datepicker.pr-dp .prev,
    .datepicker.pr-dp .next { font-size: 14px; padding: 6px; }
</style>
@endpush

@push('script')
@include('includes.scripts.datatable')
<script>
$(document).ready(function () {
    var $btnBar = $('#dataTableBuilder_wrapper .dt-buttons');
    $btnBar.append(
        '<span class="pr-date-range" style="display:inline-flex;align-items:center;margin-left:8px;gap:4px;vertical-align:middle;height:100%;">' +
            '<input type="text" id="pr_start_date" class="form-control form-control-sm" placeholder="Start Date" style="width:115px;" autocomplete="off" readonly>' +
            '<span class="text-muted px-1">—</span>' +
            '<input type="text" id="pr_end_date" class="form-control form-control-sm" placeholder="End Date" style="width:115px;" autocomplete="off" readonly>' +
        '</span>'
    );

    $('#pr_start_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('pr-dp'); })
        .on('changeDate', function (e) {
            $('#pr_end_date').datepicker('setStartDate', e.date);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        })
        .on('clearDate', function () {
            $('#pr_end_date').datepicker('setStartDate', null);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

    $('#pr_end_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('pr-dp'); })
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