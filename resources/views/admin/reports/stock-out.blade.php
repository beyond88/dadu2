@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.stock_out_report') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.stock_out_report') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.stock_out_report') }}</h4>

                    {{-- FILTER SECTION --}}
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="{{ route('admin.report.stock-out') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select name="category" class="form-control select2">
                                                <option value="">{{ __('custom.select_category') }}</option>
                                                @foreach($categories as $id => $name)
                                                    <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select name="brand" class="form-control select2">
                                                <option value="">{{ __('custom.select_brand') }}</option>
                                                @foreach($brands as $id => $name)
                                                    <option value="{{ $id }}" {{ request('brand') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="mdi mdi-filter"></i> {{ __('custom.generate') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <br>

                    {{-- REPORT TABLE --}}
                    <div id="section-to-print-stock-out">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped nowrap">
                                <thead>
                                <tr>
                                    <th>SL#</th>
                                    <th>{{ __('custom.product') }}</th>
                                    <th>{{ __('custom.sku') }}</th>
                                    <th>{{ __('custom.category') }}</th>
                                    <th>{{ __('custom.brand') }}</th>
                                    <th>{{ __('custom.stock') }}</th>
                                    <th>{{ __('custom.status') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product->id) }}" target="_blank">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ optional($product->category)->name }}</td>
                                        <td>{{ optional($product->brand)->name }}</td>
                                        <td>
                                            <span class="badge badge-danger">{{ $product->allStock->sum('quantity') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">{{ __('custom.out_of_stock') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('custom.no_data_found') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <link href="{{ static_asset('admin/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('script')
    <script src="{{ static_asset('admin/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
