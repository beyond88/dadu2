<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ make8digits($data->id) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "hindsiliguri", "DejaVu Sans", sans-serif; font-size:11px; color:#000; }
        .text-center { text-align:center; }
        .text-right  { text-align:right; }
        .text-left   { text-align:left; }
        .font-bold   { font-weight:bold; }

        /* Header */
        .company-name { font-size:22px; font-style:italic; font-weight:bold; text-align:center; margin-bottom:2px; }
        .company-addr { font-size:10px; text-align:center; margin-bottom:2px; }
        .doc-title    { font-size:12px; text-align:center; text-decoration:underline; font-style:italic; margin-bottom:2px; }
        .doc-sub      { font-size:10px; text-align:center; margin-bottom:6px; }

        /* Info table */
        .info-table   { width:100%; margin-bottom:8px; }
        .info-table td { padding:2px 4px; font-size:10px; vertical-align:top; }
        .customer-block { width:55%; }
        .invoice-meta   { width:45%; text-align:right; }
        .meta-box { border:1px solid #000; padding:5px 12px; display:inline-block; min-width:90px; text-align:center; }

        /* Items table — uniform single thin black grid */
        .items-table { width:100%; border-collapse:collapse; margin-bottom:6px; font-size:10px; }
        .items-table th {
            border:0.5px solid #000; padding:4px 5px; text-align:center;
            background:#f5f5f5; font-weight:bold; font-size:10px;
        }
        .items-table td { border:0.5px solid #000; padding:3px 5px; }
        .items-table td.right { text-align:right; }
        .items-table td.center { text-align:center; }
        .free-row { background:#fffde7; }
        .free-badge { font-size:8px; font-weight:bold; color:#e65100; }

        /* Summary */
        .summary-table { width:100%; border-collapse:collapse; margin-bottom:8px; }
        .summary-table td { padding:3px 6px; font-size:11px; }
        .summary-row td { border-top:1px solid #999; }
        .total-row td { border-top:2px solid #000; font-weight:bold; font-size:12px; }

        /* Right-side financial summary */
        .finance-wrap { width:100%; }
        .finance-left  { width:55%; vertical-align:top; }
        .finance-right { width:45%; vertical-align:top; }

        .finance-box { border:1px solid #ccc; border-collapse:collapse; width:100%; font-size:11px; }
        .finance-box td { padding:3px 8px; }
        .finance-box .label { text-align:right; width:60%; }
        .finance-box .value { text-align:right; border-left:1px solid #ccc; min-width:80px; font-weight:bold; }
        .finance-divider { border-top:1px solid #ccc; }

        /* Category breakdown */
        .cat-table { border-collapse:collapse; width:100%; font-size:10px; margin-top:6px; }
        .cat-table td { padding:2px 8px; }
        .cat-table .cat-label { text-align:right; }
        .cat-table .cat-value { text-align:right; border-left:1px solid #ccc; min-width:80px; }
        .cat-total td { border-top:1px solid #000; font-weight:bold; }

        /* Footer notes */
        .footer-note { border:1px solid #ccc; padding:4px 8px; font-size:9px; margin-top:4px; }

        .show-on-print { display:block; position:fixed; bottom:0; left:50%; transform:translateX(-50%); }
    </style>
</head>
<body>

@php
    /* ── Customer ──────────────────────────────────── */
    $customer       = $data->customerInfo;

    /* ── Customer display name ──────────────────────── */
    $billingName    = $data->billing_info['name'] ?? null;
    $customerName   = $customer?->company ?: ($billingName ?: ($customer?->full_name ?? __('custom.walk_in_customer')));
    $customerPhone  = collect([$data->billing_info['phone'] ?? null, $customer?->phone])
        ->first(fn ($p) => strlen(preg_replace('/\D/', '', (string) $p)) >= 6) ?? '';
    $customerAddr   = $data->billing_info['address_line_1'] ?? $customer?->address_line_1 ?? '';
@endphp

{{-- ── HEADER ─────────────────────────────────────────────────── --}}
<div class="company-name">{{ config('site_title') ?? config('app.name') }}</div>
@if(config('store_address'))
<div class="company-addr">{{ config('store_address') }}</div>
@endif
<div class="doc-title">Delivery Challan with Invoice</div>
@if(config('booking_info'))
<div class="doc-sub" style="font-weight:bold;">{!! nl2br(e(config('booking_info'))) !!}</div>
@endif
@if(config('store_mobile') || config('store_website'))
<div class="doc-sub">
    @if(config('store_mobile')) Mob. : {{ config('store_mobile') }} @endif
    @if(config('store_website')) &nbsp;|&nbsp; {{ config('store_website') }} @endif
</div>
@endif

{{-- ── CUSTOMER + DATE BLOCK ───────────────────────────────────── --}}
<table class="info-table">
    <tr>
        <td class="customer-block">
            <div style="font-size:11px;">
                <strong>{{ $customerName }}</strong><br>
                @if($customerAddr) {{ $customerAddr }}<br> @endif
                @if($customerPhone) মোবাইল : {{ to_bangla_number($customerPhone) }} @endif
            </div>
        </td>
        <td class="invoice-meta">
            <table style="margin-left:auto; border-collapse:separate; border-spacing:0;">
                <tr>
                    <td style="padding:4px 6px; text-align:right;">Date :</td>
                    <td style="border:1px solid #000; padding:5px 12px; text-align:center; min-width:80px;">{{ \Carbon\Carbon::parse($data->date)->format('d-m-y') }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 6px; text-align:right;">SI No :</td>
                    <td style="border:1px solid #000; padding:5px 12px; text-align:center; min-width:80px; font-weight:bold; font-size:13px;">{{ make8digits($data->id) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── ITEMS TABLE ─────────────────────────────────────────────── --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:8%;">CODE</th>
            <th style="width:28%; text-align:left;">Name of the Item</th>
            <th style="width:5%;">Ctr</th>
            <th style="width:5%;">Pc</th>
            <th style="width:6%;">Total</th>
            <th style="width:10%;">Price</th>
            <th style="width:12%; text-align:right;">Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($data->items as $index => $item)
        @php
            $code    = $item->product?->parts_no ?: ($item->sku ?? '—');
            $carton  = $item->product?->carton;
            $qpc     = $carton ? (int)$carton->qty_per_carton : 0;
            $ctr     = ($qpc > 0) ? floor($item->quantity / $qpc) : '';
            $pc      = $item->quantity;
            $total   = $item->quantity_display;
            $unitLabel = $item->unit_label;
        @endphp
        <tr @if($item->is_free) class="free-row" @endif>
            <td class="center">{{ $code }}</td>
            <td>
                {{ $item->product_name }}
                @if($item->is_free) <span class="free-badge">[FREE]</span> @endif
                @if($item->product?->is_variant && isset($item->productStock))
                    <small>({{ optional($item->productStock->variation)->name ?? '' }})</small>
                @endif
            </td>
            <td class="center">{{ $ctr }}</td>
            <td class="center">{{ $pc }}</td>
            <td class="center">{{ $total }} {{ $unitLabel }}</td>
            <td class="right">@if(!$item->is_free){{ make2decimal($item->price) }}@endif</td>
            <td class="right">@if(!$item->is_free){{ make2decimal($item->sub_total) }}@endif</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="border:0.5px solid #000; text-align:right; padding:4px 8px; font-weight:bold;">Total</td>
            <td style="border:0.5px solid #000; text-align:right; padding:4px 8px; font-weight:bold;">{{ make2decimal($data->total) }}</td>
        </tr>
    </tfoot>
</table>

{{-- ── FINANCIAL SUMMARY + CATEGORY BREAKDOWN ─────────────────── --}}
<table style="width:100%; margin-top:6px;">
    <tr>
        {{-- Left: notes / signature area. vertical-align:bottom drops the
             signature to the bottom of the (tall) row, leaving blank room
             above the line to actually sign. --}}
        <td style="width:52%; vertical-align:bottom; padding-right:10px;">
            @if($data->notes)
            <div style="font-size:9px; color:#555; margin-bottom:8px;">
                {!! nl2br(e($data->notes)) !!}
            </div>
            @endif

            {{-- Signature --}}
            <div style="width:60%;">
                <div style="border-top:1px solid #000; padding-top:3px; font-size:11px; font-weight:bold; font-style:italic; text-align:center;">
                    &nbsp;
                </div>
            </div>
        </td>

        {{-- Right: Total Bill / Total Paid / Total Due --}}
        <td style="width:48%; vertical-align:top;">
            <table style="width:100%; border-collapse:collapse; font-size:11px;">
                <tr>
                    <td style="text-align:right; padding:3px 6px; width:55%;">Total Bill :</td>
                    <td style="border:1px solid #000; text-align:right; padding:3px 8px; min-width:80px; font-weight:bold;">
                        {{ make2decimal($data->total) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right; padding:3px 6px;">Total Paid :</td>
                    <td style="border:1px solid #000; text-align:right; padding:3px 8px; font-weight:bold;">
                        {{ make2decimal($data->total_paid) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right; padding:3px 6px; font-weight:bold;">Total Due :</td>
                    <td style="border:1px solid #000; text-align:right; padding:3px 8px; font-weight:bold;">
                        {{ make2decimal(calculateDue($data->total, $data->total_paid)) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── FOOTER NOTES ────────────────────────────────────────────── --}}
@if(config('terms_and_conditions'))
<div class="footer-note" style="margin-top:8px;">
    {!! config('terms_and_conditions') !!}
</div>
@endif

<div class="show-on-print">
    <p>{{ config('invoice_footer') }}</p>
</div>

</body>
</html>
