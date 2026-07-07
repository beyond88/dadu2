@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.banks') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.banks') }}</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="float-right d-none d-block">
                    @can('Bank Create')
                        <a href="{{ route('admin.banks.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> {{ __('custom.add_bank') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.bank_list') }}</h4>
                    
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('admin.banks.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="{{ __('custom.search_banks') }}" 
                                           value="{{ request()->search }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <div class="float-right">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.banks.index') }}" 
                                       class="btn {{ !request()->status ? 'btn-primary' : 'btn-secondary' }}">
                                        {{ __('custom.all') }}
                                    </a>
                                    <a href="{{ route('admin.banks.index', ['status' => 'active']) }}" 
                                       class="btn {{ request()->status == 'active' ? 'btn-primary' : 'btn-secondary' }}">
                                        {{ __('custom.active') }}
                                    </a>
                                    <a href="{{ route('admin.banks.index', ['status' => 'inactive']) }}" 
                                       class="btn {{ request()->status == 'inactive' ? 'btn-primary' : 'btn-secondary' }}">
                                        {{ __('custom.inactive') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Banks Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('custom.sl') }}</th>
                                    <th>{{ __('custom.name') }}</th>
                                    <th>{{ __('custom.account_name') }}</th>
                                    <th>{{ __('custom.account_number') }}</th>
                                    <th>{{ __('custom.branch_name') }}</th>
                                    <th>{{ __('custom.phone') }}</th>
                                    <th>{{ __('custom.email') }}</th>
                                    <th>{{ __('custom.status') }}</th>
                                    <th>{{ __('custom.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banks as $bank)
                                    <tr>
                                        <td>{{ $banks->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $bank->name }}</strong>
                                            @if($bank->contact_person)
                                                <br><small>{{ __('custom.contact_person') }}: {{ $bank->contact_person }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $bank->account_name ?: '-' }}</td>
                                        <td>{{ $bank->account_number ?: '-' }}</td>
                                        <td>{{ $bank->branch_name ?: '-' }}</td>
                                        <td>{{ $bank->phone ?: '-' }}</td>
                                        <td>{{ $bank->email ?: '-' }}</td>
                                        <td>
                                            @if($bank->is_active)
                                                <span class="badge badge-success">{{ __('custom.active') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('custom.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.banks.show', $bank) }}" 
                                                   class="btn btn-sm btn-info" title="{{ __('custom.view') }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                @can('Bank Edit')
                                                    <a href="{{ route('admin.banks.edit', $bank) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('custom.edit') }}">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('Bank Delete')
                                                    <button type="button" class="btn btn-sm btn-danger delete-bank" 
                                                            data-id="{{ $bank->id }}" title="{{ __('custom.delete') }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @endcan
                                                <button type="button" class="btn btn-sm {{ $bank->is_active ? 'btn-secondary' : 'btn-success' }} toggle-status" 
                                                        data-id="{{ $bank->id }}" title="{{ $bank->is_active ? __('custom.deactivate') : __('custom.activate') }}">
                                                    <i class="mdi {{ $bank->is_active ? 'mdi-toggle-switch-off' : 'mdi-toggle-switch' }}"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('custom.no_banks_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">
                                {{ __('custom.showing', ['from' => $banks->firstItem(), 'to' => $banks->lastItem(), 'total' => $banks->total()]) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            {{ $banks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Delete Bank
            $(document).on('click', '.delete-bank', function() {
                var bankId = $(this).data('id');
                if(confirm('{{ __("custom.are_you_sure_to_delete_this_bank") }}')) {
                    $.ajax({
                        url: '{{ route("admin.banks.destroy", ":id") }}'.replace(':id', bankId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                var bankId = $(this).data('id');
                $.ajax({
                    url: '{{ route("admin.banks.toggle-status", ":id") }}'.replace(':id', bankId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
