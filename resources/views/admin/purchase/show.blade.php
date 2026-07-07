@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __t('purchases') }}</a></li>
                <li class="breadcrumb-item active">{{ __t('view').' '.__t('purchase') }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="header-title">{{ __t('view').' '.__t('purchase') }}</h4>
                    </div>
                    <div class="col-lg-6 d-print-none ic-print-btn-head">
                        <button data-div-name="section-to-print-pshow" class="btn btn-primary section-print-btn"
                            type="button"><i class="fa fa-print"></i> {{ __t('print') }}</button>
                        @can('Add Purchase Payment')
                        <a class="btn btn-success mr-1" href="{{ route('admin.purchases.payment.create', $purchase->id) }}">
                            <i class="fa fa-money"></i> {{ __t('make_payment') }}
                        </a>
                        @endcan
                        @can('View Purchase Payment')
                        <a class="btn btn-info mr-1" href="{{ route('admin.purchases.payment.history', $purchase->id) }}">
                            <i class="fa fa-history"></i> {{ __t('payment_history') }}
                        </a>
                        @endcan
                        <a class="btn btn-secondary mr-2" href="{{ route('admin.purchases.index') }}"><i
                                class="fa fa-arrow-left"></i> {{ __t('back') }}</a>
                    </div>
                </div>
                <div id="section-to-print-pshow">
                    <div class="table-responsive">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table class="ic-purchase-print" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td><b>{{ __t('purchase_number') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->purchase_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('supplier') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->supplier->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('supplier_phone') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->supplier->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('warehouse') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->warehouse->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('company') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->company }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('date') }}</b></td>
                                            <td>:</td>
                                            <td>{{ date('Y-m-d', strtotime($purchase->date)) }}</td>
                                        </tr>
                                        {{-- Note & Short address hidden per request
                                        <tr>
                                            <td><b>{{ __t('note') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->notes }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('short_address') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->short_address }}</td>
                                        </tr>
                                        --}}
                                    </table>
                                </td>
                                <td>
                                    <table class="ic-purchase-print" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td><b>{{ __t('address_line_1') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->address_line_1 }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('address_line_2') }}</b></td>
                                            <td>:</td>
                                            <td>{{ $purchase->address_line_2 }}</td>
                                        </tr>
                                        {{-- Country, State & City hidden per request
                                        <tr>
                                            <td><b>{{ __t('country') }}</b></td>
                                            <td>:</td>
                                            <td>{{ optional($purchase->systemCountry)->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('state') }}</b></td>
                                            <td>:</td>
                                            <td>{{ optional($purchase->systemState)->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('city') }}</b></td>
                                            <td>:</td>
                                            <td>{{ optional($purchase->systemCity)->name }}</td>
                                        </tr>
                                        --}}
                                        <tr>
                                            <td><b>{{ __t('status') }} </b></td>
                                            <td>:</td>
                                            <td> @if($purchase->status == \App\Models\Purchase::STATUS_REQUESTED)
                                                <span class="badge badge-primary">{{ strtoupper($purchase->status)
                                                    }}</span>
                                                @elseif($purchase->status == \App\Models\Purchase::STATUS_CONFIRMED)
                                                <span class="badge badge-success">{{ strtoupper($purchase->status)
                                                    }}</span>
                                                @else
                                                <span class="badge badge-danger">{{ strtoupper($purchase->status)
                                                    }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ __t('received') }}</b></td>
                                            <td>:</td>
                                            <td>
                                                @if($purchase->received)
                                                <span class="badge badge-success">{{ strtoupper(__t('received'))
                                                    }}</span>
                                                @else
                                                <span class="badge badge-warning">{{ strtoupper(__t('not_received_yet'))
                                                    }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                @php
                                    // Totals across all purchased items (barrel + kg for weight-based, units otherwise).
                                    $totalBarrels = 0; $totalKg = 0; $totalUnits = 0; $labels = [];
                                    foreach ($purchase->purchaseItems as $pi) {
                                        if ($pi->product->is_weight_based) {
                                            $kpb = (float) $pi->product->kg_per_barrel;
                                            $totalBarrels += (float) $pi->quantity;
                                            $totalKg      += (float) $pi->quantity * $kpb;
                                            $labels[]     = $pi->product->barrel_label ?: __('custom.barrels');
                                        } else {
                                            $totalUnits += (float) $pi->quantity;
                                        }
                                    }
                                    // Use the actual unit label when all weight-based items share one; else generic.
                                    $barrelLabel = count(array_unique($labels)) === 1 ? $labels[0] : __('custom.barrels');
                                    $qtyParts = [];
                                    if ($totalBarrels > 0) {
                                        $qtyParts[] = (float) $totalBarrels . ' ' . $barrelLabel . ' (' . (float) $totalKg . ' kg)';
                                    }
                                    if ($totalUnits > 0) {
                                        $qtyParts[] = (float) $totalUnits;
                                    }
                                    $totalQtySummary = implode(' + ', $qtyParts);
                                @endphp
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __t('sl') }}</th>
                                            <th>{{ __t('sku') }}</th>
                                            <th>{{ __t('product_name') }}</th>
                                            <th>{{ __('custom.purchased_quantity') }}</th>
                                            <th class="text-right">{{ __('custom.buying_price_per_kg') }}</th>
                                            <th class="text-right">{{ __('custom.price_per_barrel', ['label' => __('custom.barrel')]) }}</th>
                                            <th>{{ __t('note') }}</th>
                                            <th class="text-right">{{ __('custom.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($purchase->purchaseItems as $key => $purchaseItems)
                                        @php
                                            $isWeight = $purchaseItems->product->is_weight_based;
                                            $kpb      = (float) $purchaseItems->product->kg_per_barrel;
                                            $label    = $purchaseItems->product->barrel_label ?: __('custom.barrels');
                                            $qty      = (float) $purchaseItems->quantity;
                                            $kg       = $isWeight && $kpb ? $qty * $kpb : 0;
                                            $pricePerKg = $isWeight && $kpb ? ($purchaseItems->price / $kpb) : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $key+1 }}</td>

                                            {{-- Show SKU from variation if exists, otherwise from product --}}
                                            <td>
                                                {{ $purchaseItems->variation ? $purchaseItems->variation->sku : $purchaseItems->product->sku }}
                                            </td>

                                            {{-- Show product name, and if it has a variant, show variant name --}}
                                            <td>
                                                {{ $purchaseItems->product->name }}
                                                @if($purchaseItems->product->is_variant == 1 && $purchaseItems->variation)
                                                    ({{ __("custom.variant") }} : {{ $purchaseItems->variation->name }})
                                                @endif
                                            </td>

                                            {{-- Purchased quantity: barrel + kg for weight-based products --}}
                                            <td>
                                                @if($isWeight)
                                                    {{ $qty }} {{ $label }} <small class="text-muted">({{ $kg }} kg)</small>
                                                @else
                                                    {{ $qty }}
                                                @endif
                                            </td>

                                            {{-- Buying price: per kg for weight-based, unit price otherwise --}}
                                            <td class="text-right">
                                                @if($isWeight)
                                                    {{ currencySymbol().make2decimal($pricePerKg) }} <small class="text-muted">/kg</small>
                                                @else
                                                    {{ currencySymbol().make2decimal($purchaseItems->price) }}
                                                @endif
                                            </td>

                                            {{-- Total price per barrel (the stored per-barrel price) --}}
                                            <td class="text-right">
                                                @if($isWeight)
                                                    {{ currencySymbol().make2decimal($purchaseItems->price) }} <small class="text-muted">/{{ $label }}</small>
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            <td>{{ $purchaseItems->note }}</td>
                                            <td class="text-right">{{
                                                currencySymbol().make2decimal($purchaseItems->sub_total) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">{{ __('custom.total_quantity') }}: </th>
                                            <th colspan="4">{{ $totalQtySummary }}</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-right">{{ __t('total') }}: </th>
                                            <th class="text-right">{{ currencySymbol().make2decimal($purchase->total) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- Payment Summary --}}
                    @php
                        $totalPaid = $purchase->payments->sum('amount');
                        $due = $purchase->total - $totalPaid;
                    @endphp
                    <div class="row mt-3 d-print-none">
                        <div class="col-md-4 ml-auto">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th>{{ __t('total') }}</th>
                                    <td class="text-right">{{ currencySymbol() }}{{ make2decimal($purchase->total) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th>{{ __t('total_paid') }}</th>
                                    <td class="text-right text-success font-weight-bold">{{ currencySymbol() }}{{ make2decimal($totalPaid) }}</td>
                                </tr>
                                <tr class="{{ $due > 0 ? 'table-danger' : 'table-success' }}">
                                    <th>{{ __t('due') }}</th>
                                    <td class="text-right font-weight-bold {{ $due > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ currencySymbol() }}{{ make2decimal($due) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($purchase->payments->count() > 0)
                    <div class="row mt-2 d-print-none">
                        <div class="col-12">
                            <h5 class="font-size-14 mb-2"><i class="fa fa-history text-primary mr-1"></i> {{ __t('payment_history') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __t('date') }}</th>
                                            <th>{{ __t('payment_type') }}</th>
                                            <th class="text-right">{{ __t('amount') }}</th>
                                            <th>{{ __t('notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchase->payments->sortByDesc('created_at') as $pmt)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pmt->date)->format('d M Y') }}</td>
                                            <td><span class="badge badge-soft-primary">{{ strtoupper(str_replace('_', ' ', $pmt->payment_type)) }}</span></td>
                                            <td class="text-right">{{ currencySymbol() }}{{ make2decimal($pmt->amount) }}</td>
                                            <td>{{ $pmt->notes ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')

@endpush

@push('script')

@endpush
