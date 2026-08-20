@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.loan_management') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.loan_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.loan_list') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                @can('Loan Create')
                <a href="{{ route('admin.loans.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> {{ __('custom.add_loan') }}
                </a>
                @endcan
                @can('Loan Transaction History')
                <a href="{{ route('admin.loans.transactions') }}" class="btn btn-info ml-1">
                    <i class="mdi mdi-history"></i> {{ __('custom.transaction_history') }}
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card" style="border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="mdi mdi-arrow-down-circle-outline" style="font-size: 2.5rem; color: #dc3545;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted" style="font-size:12px;">{{ __('custom.total_loan_taken') }}</p>
                            <h4 class="mb-0 font-weight-bold text-danger">{{ currencySymbol() }}{{ number_format($totalTaken, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" style="border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="mdi mdi-clock-outline" style="font-size: 2.5rem; color: #ffc107;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted" style="font-size:12px;">{{ __('custom.total_remaining') }}</p>
                            <h4 class="mb-0 font-weight-bold text-warning">{{ currencySymbol() }}{{ number_format($totalRemaining, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.loans.index') }}" method="GET" class="row align-items-end">
                        <div class="col-md-4">
                            <label>{{ __('custom.search') }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('custom.loan_no_or_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('custom.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="">-- {{ __('custom.all') }} --</option>
                                @foreach($loanStatuses as $key => $val)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block"><i class="mdi mdi-filter"></i> {{ __('custom.filter') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.loan_list') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th class="ic-action-col">{{ __('custom.action') }}</th>
                                    <th>{{ __('custom.loan_no') }}</th>
                                    <th>{{ __('custom.borrower_name') }}</th>
                                    <th>{{ __('custom.opening_balance') }}</th>
                                    <th>{{ __('custom.paid_amount') }}</th>
                                    <th>{{ __('custom.remaining_amount') }}</th>
                                    <th>{{ __('custom.loan_date') }}</th>
                                    <th>{{ __('custom.due_date') }}</th>
                                    <th>{{ __('custom.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $i => $loan)
                                <tr>
                                    <td>{{ $loans->firstItem() + $i }}</td>
                                    <td class="ic-action-col">
                                        <div class="ic-action-inline">
                                            <a class="btn btn-sm btn-outline-success ic-act-btn" href="{{ route('admin.loans.show', $loan->id) }}">
                                                <i class="fa fa-money-bill-wave"></i> {{ __('custom.make_payment') }}
                                            </a>
                                            @can('Loan Edit')
                                            <a class="btn btn-sm btn-outline-primary ic-act-btn" href="{{ route('admin.loans.edit', $loan->id) }}">
                                                <i class="fa fa-edit"></i> {{ __('custom.edit') }}
                                            </a>
                                            @endcan
                                            @can('Loan Delete')
                                            @if($loan->payments()->count() === 0)
                                            <form action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST" id="delete-form-{{ $loan->id }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data"
                                                    data-from-name="{{ $loan->loan_no }}"
                                                    data-from-id="{{ $loan->id }}" type="button">
                                                    <i class="mdi mdi-trash-can-outline"></i> {{ __('custom.delete') }}
                                                </button>
                                            </form>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                    <td><strong>{{ $loan->loan_no }}</strong></td>
                                    <td>
                                        {{ $loan->borrower_name }}
                                        @if($loan->borrower_phone)
                                        <br><small class="text-muted"><i class="mdi mdi-phone"></i> {{ $loan->borrower_phone }}</small>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ currencySymbol() }}{{ number_format($loan->opening_balance, 2) }}</td>
                                    <td class="text-right text-success">{{ currencySymbol() }}{{ number_format($loan->paid_amount, 2) }}</td>
                                    <td class="text-right {{ $loan->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        <strong>{{ currencySymbol() }}{{ number_format($loan->remaining_amount, 2) }}</strong>
                                    </td>
                                    <td>{{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($loan->due_date)
                                            <span class="{{ $loan->due_date->isPast() && $loan->status !== 'fully_paid' ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $loan->due_date->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $loan->status_badge }}">
                                            {{ $loanStatuses[$loan->status] ?? $loan->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="mdi mdi-information-outline" style="font-size:2rem;"></i><br>
                                        {{ __('custom.no_data_found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $loans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
