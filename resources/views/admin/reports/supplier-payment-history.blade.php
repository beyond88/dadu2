@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.supplier_payment_history') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.supplier_payment_history') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.payment_history') }}</h4>
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.report.supplier-payment-history') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('custom.supplier') }}</label>
                                    <select name="supplier_id" class="form-control select2">
                                        <option value="">{{ __('custom.all_suppliers') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }} - {{ $supplier->phone ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('custom.from_date') }}</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('custom.to_date') }}</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->toDateString()) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('custom.payment_type') }}</label>
                                    <select name="payment_type" class="form-control">
                                        <option value="">{{ __('custom.all_types') }}</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="btn-group w-100">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-filter"></i> {{ __('custom.filter') }}
                                        </button>
                                        <a href="{{ route('admin.report.supplier-payment-history') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh"></i> {{ __('custom.reset') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Export Buttons -->
                    <div class="text-right mb-3">
                        <a href="{{ route('admin.report.supplier-payment-history.export', array_merge(request()->all(), ['type' => 'pdf'])) }}" 
                           class="btn btn-danger btn-sm">
                            <i class="mdi mdi-file-pdf"></i> {{ __('custom.pdf') }}
                        </a>
                        <a href="{{ route('admin.report.supplier-payment-history.export', array_merge(request()->all(), ['type' => 'excel'])) }}" 
                           class="btn btn-success btn-sm">
                            <i class="mdi mdi-file-excel"></i> {{ __('custom.excel') }}
                        </a>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="text-white">{{ __('custom.total_transactions') }}</h5>
                                    <h3 class="text-white mb-0">{{ number_format($summary->total_transactions ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="text-white">{{ __('custom.total_paid') }}</h5>
                                    <h3 class="text-white mb-0">{{ currencySymbol() }}{{ number_format($summary->total_amount ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="text-white">{{ __('custom.total_invoices') }}</h5>
                                    <h3 class="text-white mb-0">{{ number_format($summary->total_invoices ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="text-white">{{ __('custom.average_payment') }}</h5>
                                    <h3 class="text-white mb-0">
                                        {{ currencySymbol() }}{{ number_format(($summary->total_amount ?? 0) / ($summary->total_transactions ?: 1), 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Summary (if specific supplier selected) -->
                    @if($supplierSummary)
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title">{{ $supplierSummary['supplier']->name }} - {{ __('custom.summary') }}</h5>
                                <div class="row">
                                    <div class="col-md-2">
                                        <small class="text-muted">{{ __('custom.total_purchases') }}</small>
                                        <h6>{{ currencySymbol() }}{{ number_format($supplierSummary['total_purchases'], 2) }}</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">{{ __('custom.total_paid') }}</small>
                                        <h6 class="text-success">{{ currencySymbol() }}{{ number_format($supplierSummary['total_paid'], 2) }}</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">{{ __('custom.total_due') }}</small>
                                        <h6 class="text-danger">{{ currencySymbol() }}{{ number_format($supplierSummary['total_due'], 2) }}</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">{{ __('custom.total_invoices') }}</small>
                                        <h6>{{ $supplierSummary['total_invoices'] }}</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">{{ __('custom.paid_invoices') }}</small>
                                        <h6 class="text-success">{{ $supplierSummary['paid_invoices'] }}</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">{{ __('custom.unpaid_invoices') }}</small>
                                        <h6 class="text-danger">{{ $supplierSummary['total_invoices'] - $supplierSummary['paid_invoices'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payments Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('custom.sl') }}</th>
                                    <th>{{ __('custom.date') }}</th>
                                    <th>{{ __('custom.supplier') }}</th>
                                    <th>{{ __('custom.purchase_number') }}</th>
                                    <th>{{ __('custom.payment_type') }}</th>
                                    <th>{{ __('custom.amount') }}</th>
                                    <th>{{ __('custom.notes') }}</th>
                                    <th>{{ __('custom.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payments->firstItem() + $loop->index }}</td>
                                        <td>{{ formatDate($payment->date) }}</td>
                                        <td>
                                            <strong>{{ $payment->purchase->supplier->name ?? 'N/A' }}</strong>
                                            @if($payment->purchase->supplier->phone)
                                                <br><small class="text-muted">{{ $payment->purchase->supplier->phone }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.purchases.show', $payment->purchase_id) }}">
                                                {{ $payment->purchase->purchase_number ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($payment->payment_type)<span class="badge badge-info">{{ $payment->payment_type }}</span>@else<span class="text-muted">-</span>@endif
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                {{ currencySymbol() }}{{ number_format($payment->amount, 2) }}
                                            </strong>
                                        </td>
                                        <td>{{ $payment->notes ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.purchases.show', $payment->purchase_id) }}" 
                                               class="btn btn-sm btn-info" title="{{ __('custom.view_purchase') }}">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            @can('Delete Purchase Payment')
                                                <button type="button" class="btn btn-sm btn-danger delete-payment" 
                                                        data-id="{{ $payment->id }}" title="{{ __('custom.delete') }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('custom.no_payments_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="5" class="text-right">{{ __('custom.total') }}:</td>
                                    <td class="text-right">{{ currencySymbol() }}{{ number_format($payments->sum('amount'), 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">
                                {{ __('custom.showing', ['from' => $payments->firstItem(), 'to' => $payments->lastItem(), 'total' => $payments->total()]) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            {{ $payments->links() }}
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
            $('.select2').select2({
                width: '100%'
            });

            // Delete Payment
            $(document).on('click', '.delete-payment', function() {
                var paymentId = $(this).data('id');
                if(confirm('{{ __("custom.are_you_sure_to_delete_this_payment") }}')) {
                    $.ajax({
                        url: '{{ url("admin/purchase-payments") }}/' + paymentId,
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
        });
    </script>
@endpush
