@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.account_details') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">{{ __('custom.accounts') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.account_details') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.account_information') }}</h4>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.account_name') }}</label>
                                <p class="form-control-static">{{ $account->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.account_code') }}</label>
                                <p class="form-control-static">{{ $account->code ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.account_type') }}</label>
                                <p class="form-control-static">
                                    <span class="badge badge-info">
                                        {{ $account->getTypes()[$account->type] ?? $account->type }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($account->type !== 'cash')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.account_number') }}</label>
                                    <p class="form-control-static">{{ $account->account_number ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.bank_name') }}</label>
                                    <p class="form-control-static">{{ $account->bank_name ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.branch_name') }}</label>
                                    <p class="form-control-static">{{ $account->branch_name ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.current_balance') }}</label>
                                <p class="form-control-static">
                                    <strong class="text-success" style="font-size: 18px;">
                                        {{ number_format($account->current_balance, 2) }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.status') }}</label>
                                <p class="form-control-static">
                                    @if($account->is_active)
                                        <span class="badge badge-success">{{ __('custom.active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('custom.inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.created_at') }}</label>
                                <p class="form-control-static">{{ $account->created_at->format('d M Y H:i A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('custom.created_by') }}</label>
                                <p class="form-control-static">{{ $account->creator?->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($account->updated_at != $account->created_at)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.updated_at') }}</label>
                                    <p class="form-control-static">{{ $account->updated_at->format('d M Y H:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('custom.updated_by') }}</label>
                                    <p class="form-control-static">{{ $account->updater?->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                @can('Account Edit')
                                    <a href="{{ route('admin.accounts.edit', $account) }}" class="btn btn-warning">
                                        <i class="mdi mdi-pencil"></i> {{ __('custom.edit') }}
                                    </a>
                                @endcan
                                <a href="{{ route('admin.transactions.statement', $account) }}" class="btn btn-info">
                                    <i class="mdi mdi-file-document"></i> {{ __('custom.view_statement') }}
                                </a>
                                <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> {{ __('custom.back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.recent_transactions') }}</h4>
                    
                    @if($account->transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('custom.date') }}</th>
                                        <th>{{ __('custom.type') }}</th>
                                        <th>{{ __('custom.amount') }}</th>
                                        <th>{{ __('custom.balance_after') }}</th>
                                        <th>{{ __('custom.note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($account->transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <span class="badge badge-{{ 
                                                    $transaction->type == 'add' || $transaction->type == 'transfer_in' || $transaction->type == 'invoice_payment' || $transaction->type == 'due_collection' ? 'success' : 
                                                    ($transaction->type == 'reduce' || $transaction->type == 'transfer_out' ? 'danger' : 'info')
                                                }}">
                                                    {{ $transaction->type_label }}
                                                </span>
                                            </td>
                                            <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
                                            <td class="text-right">{{ number_format($transaction->balance_after, 2) }}</td>
                                            <td>{{ $transaction->note ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">{{ __('custom.no_transactions_found') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.quick_actions') }}</h4>
                    
                    <div class="list-group">
                        @can('Account Edit')
                            <a href="{{ route('admin.accounts.edit', $account) }}" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-pencil"></i> {{ __('custom.edit_account') }}
                            </a>
                        @endcan
                        
                        <a href="{{ route('admin.transactions.statement', $account) }}" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-file-document"></i> {{ __('custom.view_full_statement') }}
                        </a>
                        
                        <button type="button" class="list-group-item list-group-item-action toggle-status" 
                                data-id="{{ $account->id }}">
                            <i class="mdi {{ $account->is_active ? 'mdi-toggle-switch-off' : 'mdi-toggle-switch' }}"></i>
                            {{ $account->is_active ? __('custom.deactivate') : __('custom.activate') }}
                        </button>
                        
                        @can('Account Delete')
                            <button type="button" class="list-group-item list-group-item-action text-danger delete-account" 
                                    data-id="{{ $account->id }}">
                                <i class="mdi mdi-delete"></i> {{ __('custom.delete_account') }}
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
            // Delete Account
            $(document).on('click', '.delete-account', function() {
                var accountId = $(this).data('id');
                if(confirm('{{ __("custom.are_you_sure_to_delete_this_account") }}')) {
                    $.ajax({
                        url: '{{ route("admin.accounts.destroy", ":id") }}'.replace(':id', accountId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.location.href = '{{ route("admin.accounts.index") }}';
                        },
                        error: function(xhr) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                var accountId = $(this).data('id');
                $.ajax({
                    url: '{{ route("admin.accounts.toggle-status", ":id") }}'.replace(':id', accountId),
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
