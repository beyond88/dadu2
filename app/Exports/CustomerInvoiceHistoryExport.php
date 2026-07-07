<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Str;

class CustomerInvoiceHistoryExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function array(): array
    {
        $data = [];
        $sl = 1;

        foreach ($this->items as $invoice) {
            $r = [];
            $r[] = $sl;
            $r[] = make8digits($invoice->id);
            $r[] = date(config('date_formate'), strtotime($invoice->date));
            $r[] = $invoice->items->sum('quantity');
            $r[] = currencySymbol() . ' ' . $invoice->total;
            $r[] = currencySymbol() . ' ' . $invoice->total_paid;
            $r[] = currencySymbol() . ' ' . ($invoice->total - $invoice->total_paid);
            $r[] = Str::upper($invoice->status);
            $r[] = Str::upper($invoice->delivery_status);

            $data[] = $r;
            $sl++;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            __('custom.sl'),
            __('custom.invoice_id'),
            __('custom.date'),
            __('custom.total_qty'),
            __('custom.total'),
            __('custom.paid'),
            __('custom.due'),
            __('custom.status'),
            __('custom.delivery_status'),
        ];
    }
}
