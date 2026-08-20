@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('custom.capital_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.list') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.capitals.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ __('custom.add_capital') }}
                </a>
                <a href="{{ route('admin.capitals.transactions') }}" class="btn btn-info">
                    <i class="fa fa-list"></i> {{ __('custom.transactions') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>{{ __('custom.total_capital') }}</h5>
                    <h3>{{ currencySymbol() }} {{ number_format($totalCapital, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>{{ __('custom.total_paid') }}</h5>
                    <h3>{{ currencySymbol() }} {{ number_format($totalPaid, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>{{ __('custom.total_remaining') }}</h5>
                    <h3>{{ currencySymbol() }} {{ number_format($totalRemaining, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.capitals.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('custom.search') }}..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">{{ __('custom.all_status') }}</option>
                            @foreach($capitalStatuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">{{ __('custom.filter') }}</button>
                        <a href="{{ route('admin.capitals.index') }}" class="btn btn-secondary">{{ __('custom.reset') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Capital List --}}
    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="ic-action-col">{{ __('custom.action') }}</th>
                            <th>{{ __('custom.capital_no') }}</th>
                            <th>{{ __('custom.investor_name') }}</th>
                            <th>{{ __('custom.investor_phone') }}</th>
                            <th>{{ __('custom.total_amount') }}</th>
                            <th>{{ __('custom.paid') }}</th>
                            <th>{{ __('custom.remaining') }}</th>
                            <th>{{ __('custom.capital_date') }}</th>
                            <th>{{ __('custom.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($capitals as $capital)
                            <tr>
                                <td class="ic-action-col">
                                    <div class="ic-action-inline">
                                        {{-- Add Capital --}}
                                        <a class="btn btn-sm btn-outline-primary ic-act-btn" href="{{ route('admin.capitals.show', [$capital->id, 'action' => 'add_capital']) }}">
                                            <i class="fa fa-plus"></i> {{ __('custom.add_capital') }}
                                        </a>

                                        {{-- Make Payment --}}
                                        <a class="btn btn-sm btn-outline-success ic-act-btn" href="{{ route('admin.capitals.show', [$capital->id, 'action' => 'make_payment']) }}">
                                            <i class="fa fa-money-bill-wave"></i> {{ __('custom.make_payment') }}
                                        </a>

                                        {{-- Edit --}}
                                        <a class="btn btn-sm btn-outline-primary ic-act-btn" href="{{ route('admin.capitals.edit', $capital->id) }}">
                                            <i class="fa fa-edit"></i> {{ __('custom.edit') }}
                                        </a>

                                        {{-- Delete --}}
                                        @if($capital->payments()->count() == 0)
                                            <form action="{{ route('admin.capitals.destroy', $capital->id) }}" method="POST" id="delete-form-{{ $capital->id }}">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-outline-danger ic-act-btn delete-list-data"
                                                        data-from-name="{{ $capital->id }}"
                                                        data-from-id="{{ $capital->id }}"
                                                        type="button">
                                                    <i class="mdi mdi-trash-can-outline"></i> {{ __('custom.delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $capital->capital_no }}</td>
                                <td>{{ $capital->investor_name }}</td>
                                <td>{{ $capital->investor_phone ?? '-' }}</td>
                                <td>{{ currencySymbol() }} {{ number_format($capital->total_amount, 2) }}</td>
                                <td>{{ currencySymbol() }} {{ number_format($capital->paid_amount, 2) }}</td>
                                <td>{{ currencySymbol() }} {{ number_format($capital->remaining_amount, 2) }}</td>
                                <td>{{ $capital->capital_date->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge {{ $capital->status_badge }}">
                                        {{ $capitalStatuses[$capital->status] ?? $capital->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('custom.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $capitals->links() }}
        </div>
    </div>
@endsection
