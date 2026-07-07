@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.bank_details') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.banks.index') }}">{{ __('custom.banks') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.bank_details') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.bank_information') }}</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.bank_name') }}</label>
                                <p class="form-control-static">{{ $bank->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.account_name') }}</label>
                                <p class="form-control-static">{{ $bank->account_name ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.account_number') }}</label>
                                <p class="form-control-static">{{ $bank->account_number ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.branch_name') }}</label>
                                <p class="form-control-static">{{ $bank->branch_name ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.phone') }}</label>
                                <p class="form-control-static">{{ $bank->phone ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.email') }}</label>
                                <p class="form-control-static">{{ $bank->email ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.contact_person') }}</label>
                                <p class="form-control-static">{{ $bank->contact_person ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.contact_person_phone') }}</label>
                                <p class="form-control-static">{{ $bank->contact_person_phone ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.address') }}</label>
                                <p class="form-control-static">{{ $bank->address ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.notes') }}</label>
                                <p class="form-control-static">{{ $bank->notes ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.status') }}</label>
                                <p class="form-control-static">
                                    @if($bank->is_active)
                                        <span class="badge badge-success">{{ __('custom.active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('custom.inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.created_at') }}</label>
                                <p class="form-control-static">{{ $bank->created_at->format('d M Y H:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.created_by') }}</label>
                                <p class="form-control-static">{{ $bank->creator?->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($bank->updated_at != $bank->created_at)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.updated_at') }}</label>
                                    <p class="form-control-static">{{ $bank->updated_at->format('d M Y H:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.updated_by') }}</label>
                                    <p class="form-control-static">{{ $bank->updater?->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                @can('Bank Edit')
                                    <a href="{{ route('admin.banks.edit', $bank) }}" class="btn btn-warning">
                                        <i class="mdi mdi-pencil"></i> {{ __('custom.edit') }}
                                    </a>
                                @endcan
                                <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> {{ __('custom.back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.quick_actions') }}</h4>
                    
                    <div class="list-group">
                        @can('Bank Edit')
                            <a href="{{ route('admin.banks.edit', $bank) }}" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-pencil"></i> {{ __('custom.edit_bank') }}
                            </a>
                        @endcan
                        
                        <button type="button" class="list-group-item list-group-item-action toggle-status" 
                                data-id="{{ $bank->id }}">
                            <i class="mdi {{ $bank->is_active ? 'mdi-toggle-switch-off' : 'mdi-toggle-switch' }}"></i>
                            {{ $bank->is_active ? __('custom.deactivate') : __('custom.activate') }}
                        </button>
                        
                        @can('Bank Delete')
                            <button type="button" class="list-group-item list-group-item-action text-danger delete-bank" 
                                    data-id="{{ $bank->id }}">
                                <i class="mdi mdi-delete"></i> {{ __('custom.delete_bank') }}
                            </button>
                        @endcan
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
                            window.location.href = '{{ route("admin.banks.index") }}';
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
