<?php

namespace App\Traits;

/**
 * Shared display helpers for invoice / draft-invoice line items so the sold
 * unit (kg vs barrel) and quantity render identically everywhere
 * (view / edit / pdf / thermal / email / draft).
 *
 * Requires the using model to expose a `unit` attribute, a `quantity`
 * attribute and a `product()` relation.
 */
trait SoldUnitLabel
{
    /**
     * Label shown next to the quantity: "KG" for kg sales, the product's
     * barrel label for sold-by-weight products, otherwise the weight unit.
     */
    public function getUnitLabelAttribute(): string
    {
        if ($this->unit === 'kg') {
            return __('custom.kg');
        }

        $product = $this->product;
        if ($product && $product->is_weight_based) {
            return $product->barrel_label ?: 'Barrel';
        }

        return optional(optional($product)->weight_unit)->name ?? '';
    }

    /**
     * Quantity formatted for display with trailing zeros trimmed (45, 1.8, …).
     */
    public function getQuantityDisplayAttribute(): string
    {
        return rtrim(rtrim(number_format((float) $this->quantity, 2, '.', ''), '0'), '.');
    }
}
