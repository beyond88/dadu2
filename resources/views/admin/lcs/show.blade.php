@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.lc_details') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.lcs.index') }}">{{ __('custom.lcs') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.lc_details') }}</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="float-right d-none d-md-block">
                    <a href="{{ route('admin.lcs.index') }}" class="btn btn-secondary mr-2">
                        <i class="mdi mdi-arrow-left"></i> {{ __('custom.back') }}
                    </a>
                    <a href="{{ route('admin.lcs.edit', $lc->id) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil"></i> {{ __('custom.edit_lc') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title mb-4">{{ __('custom.expense_breakdown') }}</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('custom.expense_name') }}</th>
                                    <th class="text-right">{{ __('custom.amount_bdt') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lc->expenses as $index => $expense)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $expense->expense_name }}</td>
                                    <td class="text-right">{{ number_format($expense->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">{{ __('custom.total_expense') }}</th>
                                    <th class="text-right">{{ number_format($lc->total_expense, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-light">
                    <h4 class="mt-0 header-title mb-4">{{ __('custom.lc_summary') }}</h4>
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>{{ __('custom.lc_name') }}</td>
                            <td class="text-right"><strong>{{ $lc->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.price_usd') }}</td>
                            <td class="text-right"><strong>${{ number_format($lc->dollar_price, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.usd_rate') }}</td>
                            <td class="text-right"><strong>{{ number_format($lc->usd_rate, 4) }}</strong></td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.lc_amount_bdt') }}</td>
                            <td class="text-right"><strong>{{ number_format($lc->lc_amount_bdt, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.total_expense') }}</td>
                            <td class="text-right"><strong>{{ number_format($lc->total_expense, 2) }}</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td><h5>{{ __('custom.final_cost') }}</h5></td>
                            <td class="text-right"><h5 class="text-primary">{{ number_format($lc->final_cost, 2) }}</h5></td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.per_dollar_actual_cost') }}</td>
                            <td class="text-right"><strong class="text-success">{{ number_format($lc->per_dollar_cost, 4) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <p class="mb-1 text-muted">{{ __('custom.created_by') }}: <strong>{{ $lc->creator->name ?? 'N/A' }}</strong></p>
                    <p class="mb-1 text-muted">{{ __('custom.created_at') }}: <strong>{{ $lc->created_at->format('d M, Y h:i A') }}</strong></p>
                    @if($lc->updated_by)
                        <p class="mb-0 text-muted">{{ __('custom.updated_by') }}: <strong>{{ $lc->updater->name ?? 'N/A' }}</strong></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
