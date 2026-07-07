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
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.manufacturer') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('custom.manufacturer_list') }}</li>
            </ol>
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
      <form action="{{ route('admin.manufacturers.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label for="import_file">{{ __('custom.import') }} (Excel/CSV)</label>
            <input type="file" name="import_file" id="import_file" class="form-control" required accept=".xlsx,.csv">
          </div>
          <a href="{{ static_asset('sample_manufacturers_import.xlsx') }}" class="btn btn-link">{{ __('custom.download_sample') }}</a>

        </div>
        <div class="text-end ml-3">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('custom.cancel') }}</button>
          <button type="submit" class="btn btn-success">{{ __('custom.import') }}</button>
        </div>
      </form>
      <table class="table table-bordered table-sm mt-4">
        <tbody>
          <tr>
            <td>{{__('custom.name')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.desc')}}</td>
            <th><span class="border border-warning text-warning fs-6 fw-normal p-1">This Field is not required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.image')}}</td>
            <th><span class="border border-warning text-warning fs-6 fw-normal p-1">This Field is not required</span></th>
          </tr>
          <tr>
            <td>{{__('custom.status')}}</td>
            <th><span class="border border-success text-success fs-6 fw-normal p-1">This Field is required</span></th>
          </tr>
        </tbody>
      </table>
      </form>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="header-title mb-0">{{ __('custom.manufacturer_list') }}</h4>

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
    .datepicker.mf-dp {
        z-index: 99999 !important;
        font-size: 14px;
        min-width: 300px;
        padding: 8px;
    }
    .datepicker.mf-dp table { width: 100%; }
    .datepicker.mf-dp td,
    .datepicker.mf-dp th { width: 40px; height: 36px; font-size: 13px; }
    .datepicker.mf-dp .datepicker-switch,
    .datepicker.mf-dp .prev,
    .datepicker.mf-dp .next { font-size: 14px; padding: 6px; }
</style>
@endpush

@push('script')
@include('includes.scripts.datatable')
<script>
$(document).ready(function () {
    var $btnBar = $('#dataTableBuilder_wrapper .dt-buttons');
    $btnBar.append(
        '<span class="mf-date-range" style="display:inline-flex;align-items:center;margin-left:8px;gap:4px;vertical-align:middle;height:100%;">' +
            '<input type="text" id="mf_start_date" class="form-control form-control-sm" placeholder="Start Date" style="width:115px;" autocomplete="off" readonly>' +
            '<span class="text-muted px-1">—</span>' +
            '<input type="text" id="mf_end_date" class="form-control form-control-sm" placeholder="End Date" style="width:115px;" autocomplete="off" readonly>' +
        '</span>'
    );

    $('#mf_start_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('mf-dp'); })
        .on('changeDate', function (e) {
            $('#mf_end_date').datepicker('setStartDate', e.date);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        })
        .on('clearDate', function () {
            $('#mf_end_date').datepicker('setStartDate', null);
            window.LaravelDataTables['dataTableBuilder'].ajax.reload();
        });

    $('#mf_end_date').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, clearBtn: true, orientation: 'bottom' })
        .on('show', function () { $('.datepicker.dropdown-menu').addClass('mf-dp'); })
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
