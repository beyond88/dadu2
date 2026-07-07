<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Str;

class SupplierPurchaseHistoryExport implements FromArray, WithHeadings, ShouldAutoSize
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

        foreach ($this->items as $purchase) {
            $purchaseItemQty = $purchase->purchaseItems->sum('quantity');
            $purchaseReceiveItemQty = 0;
            foreach ($purchase->purchaseItems as $purchaseItems) {
                $purchaseReceiveItemQty += $purchaseItems->receiveItems->sum('quantity');
            }
            $missing = ($purchase->received && $purchaseItemQty != $purchaseReceiveItemQty) ? __('custom.missing') : '';
            $received = $purchase->received ? __('custom.received') : __('custom.not_received_yet');

            $r = [];
            $r[] = $sl;
            $r[] = make8digits($purchase->purchase_number);
            $r[] = date(config('date_formate'), strtotime($purchase->date));
            $r[] = currencySymbol() . ' ' . $purchase->total;
            $r[] = $purchase->purchaseItems->count();
            $r[] = Str::upper($purchase->status);
            $r[] = $received;
            $r[] = $missing;

            $data[] = $r;
            $sl++;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            __('custom.sl'),
            __('custom.purchase_number'),
            __('custom.date'),
            __('custom.total'),
            __('custom.total_unique_product'),
            __('custom.status'),
            __('custom.received'),
            __('custom.missing_item'),
        ];
    }
}
