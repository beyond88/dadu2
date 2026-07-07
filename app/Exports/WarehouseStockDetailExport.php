<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WarehouseStockDetailExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $product_stocks;

    public function __construct($product_stocks)
    {
        $this->product_stocks = $product_stocks;
    }

    public function array(): array
    {
        $data = [];
        $sl = 1;

        foreach ($this->product_stocks as $stock) {
            $product = $stock->product;
            $data[] = [
                $sl++,
                $product ? $product->name . ($product->is_variant == 1 ? ' (' . optional($stock->attribute)->name . ':' . optional($stock->attributeItem)->name . ')' : '') : '',
                $product?->sku,
                $product?->category?->name,
                $product?->manufacturer?->name,
                $stock->quantity ? $stock->quantity : '0',
                $product ? currencySymbol() . make2decimal($product->price) : '',
                $product ? currencySymbol() . ($product->price * $stock->quantity) : ''
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            '#',
            __('custom.product'),
            __('custom.sku'),
            __('custom.category'),
            __('custom.manufacturer'),
            __('custom.quantity'),
            __('custom.price'),
            __('custom.total'),
        ];
    }
}
