<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /*return [
            'alert_quantity' => ['bail', 'required', 'numeric', 'digits_between:0,8'],
            'warehouse_stock' => ['bail', 'required', 'array'],
        ];*/

        if ($this->is_variant){

            return [
                'alert_quantity' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
                'warehouse_stock' => ['bail', 'required', 'array'],
                'supplier_id' => ['sometimes','required','numeric'],
                'warehouse_stock.*.note' => ['nullable', 'string', 'max:500'],
                'warehouse_stock.*.warehouse' =>  ['bail', 'required', 'exists:warehouses,id'],
                'warehouse_stock.*.quantity.*.*' =>  ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
                'warehouse_stock.*.price.*.*'                         => ['bail','nullable','numeric'],
                'warehouse_stock.*.customer_buying_price.*.*'         => ['bail','nullable','numeric'],
//                'warehouse_stock.*.sku.*.*'                           => ['bail', 'required', 'min:1'],
//                'warehouse_stock.*.barcode.*.*'                       => ['bail', 'required', 'min:1'],
//                'warehouse_stock.*.barcode_image.*.*'                 => ['nullable'],
            ];

        }else{
            return [
                'alert_quantity'                                    => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
                'warehouse_stock'                                   => ['bail', 'required', 'array'],
                'warehouse_stock.*.note'                            => ['nullable', 'string', 'max:500'],
                'warehouse_stock.*.warehouse'                       => ['bail', 'required', 'exists:warehouses,id'],
                'warehouse_stock.*.quantity'                        => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
                'supplier_id'                                       => ['sometimes','required','numeric'],
                'warehouse_stock.*.price'                           => ['bail','nullable','numeric'],
                'warehouse_stock.*.customer_buying_price'           => ['bail','nullable','numeric'],
//                'warehouse_stock.*.sku'                             => ['bail', 'required', 'min:1'],
//                'warehouse_stock.*.barcode'                         => ['bail', 'required', 'min:1'],
//                'warehouse_stock.*.barcode_image'                   => ['nullable'],
            ];
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Barrel (weight-based) products are stocked in whole barrels — a
            // fractional barrel adjustment is not allowed, just like in purchase.
            $product = Product::find($this->route('product_stock'));
            if (!$product || !$product->is_weight_based) {
                return;
            }
            $label     = $product->barrel_label ?: 'barrel';
            $kgPerBarrel = (float) $product->kg_per_barrel;
            foreach ((array) $this->input('warehouse_stock', []) as $index => $row) {
                if (!isset($row['quantity']) || is_array($row['quantity'])) {
                    continue; // skip missing or nested (variant) quantities
                }
                $qty = $row['quantity'];
                if ($qty === null || $qty === '') {
                    continue;
                }
                // The frontend converts KG to barrels. Ensure the result is a whole number.
                if (abs((float) $qty - round((float) $qty)) > 1e-6) {
                    $validator->errors()->add(
                        "warehouse_stock.$index.quantity",
                        "KG পরিমাণ {$kgPerBarrel} kg এর গুণিতক হতে হবে যাতে পূর্ণ {$label} হয় (fractional {$label} allowed নয়)।"
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'warehouse_stock.*.warehouse.required' => 'Warehouse field is required',
            'warehouse_stock.*.warehouse.exist' => 'Selected Warehouse is invalid',
            'warehouse_stock.*.quantity.numeric' => 'Quantity must be a number',
            'warehouse_stock.*.quantity.digits_between' => 'Quantity must be between 0 and 8 digits.',

            'warehouse_stock.*.quantity.*.*.numeric' => 'Quantity must be a number',
            'warehouse_stock.*.quantity.*.*.digits_between' => 'Quantity must be between 0 and 8 digits.'

        ];
    }
}
