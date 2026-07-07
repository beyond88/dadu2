@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">{{ __('custom.customers') }}</a></li>
                <li class="breadcrumb-item active">{{ __t('payment_history') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        {{-- Summary Cards --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #5664d2 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 font-12">{{ __t('total_invoiced') ?? 'Total Invoiced' }}</p>
                        <h5 class="mb-0 font-weight-bold">{{ currencySymbol() }}{{ make2decimal($totalInvoiced) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #1cbb8c !important;">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 font-12">{{ __t('total_received') ?? 'Total Received' }}</p>
                        <h5 class="mb-0 font-weight-bold text-success">{{ currencySymbol() }}{{ make2decimal($totalPaid) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid {{ $due > 0 ? '#ff3d60' : '#1cbb8c' }} !important;">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 font-12">{{ __t('total_due') }}</p>
                        <h5 class="mb-0 font-weight-bold {{ $due > 0 ? 'text-danger' : 'text-success' }}">{{ currencySymbol() }}{{ make2decimal($due) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">
                        {{ __t('payment_history') }} — {{ $customer->full_name }}
                    </h4>
                    <div>
                        @if($due > 0)
                        <a href="{{ route('admin.customers.payment.create', $customer->id) }}" class="btn btn-sm btn-primary mr-2">
                            <i class="fa fa-plus"></i> {{ __t('make_payment') }}
                        </a>
                        @endif
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __t('back') }}
                        </a>
                    </div>
                </div>

                @if($payments->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-credit-card fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No payments recorded for this customer yet.</p>
                        @if($due > 0)
                        <a href="{{ route('admin.customers.payment.create', $customer->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> {{ __t('receive_payment') ?? 'Receive Payment' }}
                        </a>
                        @endif
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __t('sl') }}</th>
                                <th>{{ __t('date') }}</th>
                                <th>{{ __t('invoice_number') ?? 'Invoice Number' }}</th>
                                <th>{{ __t('payment_type') }}</th>
                                <th class="text-right">{{ __t('amount') }}</th>
                                <th>{{ __t('notes') }}</th>
                                <th class="text-center d-print-none">{{ __t('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $i => $payment)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</td>
                                <td>
                                    @if($payment->invoice_id)
                                    <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}">
                                        {{ make8digits($payment->invoice_id) }}
                                    </a>
                                    @else
                                    —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-2 py-1">
                                        {{ strtoupper(str_replace('_', ' ', $payment->payment_type)) }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold">{{ currencySymbol() }}{{ make2decimal($payment->amount) }}</td>
                                <td>{{ $payment->notes ?? '—' }}</td>
                                <td class="text-center d-print-none">
                                    <a href="{{ route('admin.invoices.delete_payment', $payment->id) }}" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this payment?')">
                                            <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light font-weight-bold">
                                <td colspan="4" class="text-right">{{ __t('total_received') ?? 'Total Received' }}:</td>
                                <td class="text-right text-success">{{ currencySymbol() }}{{ make2decimal($totalPaid) }}</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="table-light font-weight-bold">
                                <td colspan="4" class="text-right">{{ __t('total_due') }}:</td>
                                <td class="text-right {{ $due > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ currencySymbol() }}{{ make2decimal($due) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
