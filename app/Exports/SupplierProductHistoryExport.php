<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SupplierProductHistoryExport implements FromArray, WithHeadings, ShouldAutoSize
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

        foreach ($this->items as $product) {
            $r = [];
            $r[] = $sl;
            $r[] = make8digits($product['product_id']);
            $r[] = $product['product_name'];
            $r[] = $product['sku'];
            $r[] = currencySymbol() . ' ' . $product['price'];
            $r[] = $product['quantity'];
            $r[] = currencySymbol() . ' ' . ($product['price'] * $product['quantity']);

            $data[] = $r;
            $sl++;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            __('custom.sl'),
            __('custom.product_id'),
            __('custom.product_name'),
            __('custom.sku'),
            __('custom.price'),
            __('custom.quantity'),
            __('custom.sub_total'),
        ];
    }
}
