<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // dd($this->all());
        $rules = [
            'category_id' => ['bail', 'nullable', 'numeric'],
            'name' => ['bail', 'required', 'min:1', 'max:200', Rule::unique('products')->ignore($this->product)],
            'parts_no' => ['bail', 'nullable', 'max:200'],
            'brand_id' => ['bail', 'nullable', 'numeric'],
            'manufacturer_id' => ['bail', 'nullable', 'numeric'],
            'model' => ['bail', 'nullable', 'max:200'],
            'weight_unit_id' => ['bail', 'nullable', 'numeric'],
            'measurement_unit_id' => ['bail', 'nullable', 'numeric'],
            'notes' => ['bail', 'nullable', 'max:255'],
            'desc' => ['bail', 'nullable', 'max:10000'],
            'is_variant' => ['bail', 'nullable','in:0,1'],
            'is_batch_product' => ['bail', 'nullable','in:0,1'],
            'split_sale' => ['bail', 'nullable', 'boolean'],
            'tax_status' => ['bail', 'required'],
            'custom_tax' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
            'status' => ['bail', 'required', Rule::in([Product::STATUS_ACTIVE, Product::STATUS_INACTIVE])],
            'available_for' => ['bail', 'required', Rule::in(Product::SALE_AVAILABLE_FOR)],
            'customer_buying_price' => ['bail', 'nullable','regex:/^(\d+(\.\d*)?)|(\.\d+)$/'],
            'buying_price' => ['bail', 'nullable','regex:/^(\d+(\.\d*)?)|(\.\d+)$/'],
            'is_weight_based'        => ['bail', 'nullable', 'in:0,1'],
            'kg_per_barrel'          => ['bail', 'nullable', 'required_if:is_weight_based,1', 'numeric', 'gt:0'],
            'barrel_label'           => ['bail', 'nullable', 'required_if:is_weight_based,1', 'string', 'max:255'],
            'store_ids'              => ['bail', 'nullable', 'array'],
            'enable_sub_products'    => ['bail', 'nullable', 'boolean'],
            'sub_products'           => ['bail', 'nullable', 'array'],
            'sub_products.*.product_id' => ['bail', 'required_with:sub_products', 'integer', 'exists:products,id'],
            'sub_products.*.quantity'   => ['bail', 'required_with:sub_products', 'integer', 'min:1'],
            'enable_carton'          => ['bail', 'nullable', 'boolean'],
            'carton_qty'             => ['bail', 'nullable', 'integer', 'min:1'],
            'carton_product_id'      => ['bail', 'nullable', 'integer', 'exists:products,id'],
        ];

       // Only validate main SKU/price/barcode if NOT a variant product
        if ($this->is_variant != 1) {
            $rules['sku'] = ['bail', 'required', 'min:1', 'max:200', Rule::unique('products')->ignore($this->product)];
            $rules['barcode'] = ['bail', 'nullable', 'min:1', 'max:200', Rule::unique('products')->ignore($this->product)];
            $rules['price'] = ['bail', 'required', 'regex:/^(\d+(\.\d*)?)|(\.\d+)$/'];
            $rules['customer_buying_price'] = ['bail', 'nullable','regex:/^(\d+(\.\d*)?)|(\.\d+)$/'];
            $rules['buying_price'] = ['bail', 'required','regex:/^(\d+(\.\d*)?)|(\.\d+)$/'];
            $rules['weight'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['dimension_l'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['dimension_w'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['dimension_d'] = ['nullable','numeric','between:0,99999999.99'];
        }

        // If product is variant, validate variants array
        if ($this->is_variant == 1) {
            $rules['variants'] = ['required', 'array', 'min:1'];

            $rules['variants.*.sku'] = [
                'bail',
                'required',
                'min:1',
                'max:200',
                Rule::unique('products', 'sku')->ignore($this->product),
            ];
            $rules['variants.*.barcode'] = [
                'bail',
                'nullable',
                'digits:10',
                Rule::unique('products', 'barcode')->ignore($this->product),
            ];
            $rules['variants.*.id'] = ['nullable', 'numeric'];
            $rules['variants.*.name'] = ['bail', 'required', 'string', 'min:1', 'max:200'];
            $rules['variants.*.price'] = ['bail', 'required', 'numeric', 'min:0'];
            $rules['variants.*.customer_buying_price'] = ['nullable','numeric'];
            $rules['variants.*.weight'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.dimension_l'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.dimension_w'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.dimension_d'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.attribute_items'] = ['required', 'array', 'min:1']; // must include attribute items
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'category_id' => 'Category',
            'name' => 'Product Name',
            'parts_no' => 'Parts No',
        ];

        // Main product fields only if not variant
        if ($this->is_variant != 1) {
            $attributes['sku'] = 'SKU';
            $attributes['barcode'] = 'Barcode';
            $attributes['price'] = 'Price';
            $attributes['customer_buying_price'] = 'Customer Buying Price';
            $attributes['weight'] = 'Weight';
            $attributes['dimension_l'] = 'Length';
            $attributes['dimension_w'] = 'Width';
            $attributes['dimension_d'] = 'Depth';
        }

        // Variant fields
        if ($this->is_variant == 1) {
            foreach ($this->variants ?? [] as $index => $variant) {
                $number = $index + 1;
                $attributes["variants.$index.sku"] = "Variant #$number SKU";
                $attributes["variants.$index.barcode"] = "Variant #$number Barcode";
                $attributes["variants.$index.price"] = "Variant #$number Price";
                $attributes["variants.$index.customer_buying_price"] = "Variant #$number Customer Buying Price";
                $attributes["variants.$index.weight"] = "Variant #$number Weight";
                $attributes["variants.$index.dimension_l"] = "Variant #$number Length";
                $attributes["variants.$index.dimension_w"] = "Variant #$number Width";
                $attributes["variants.$index.dimension_d"] = "Variant #$number Depth";
            }
        }

        return $attributes;
    }
}
