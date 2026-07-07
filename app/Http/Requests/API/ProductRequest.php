<?php

namespace App\Http\Requests\API;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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

        $rules = [
            'category_id' => ['bail', 'nullable', 'numeric','exists:product_categories,id'],
            'name' => ['bail', 'required', 'min:1', 'max:200', Rule::unique('products')->ignore($this->id)],
            'parts_no' => ['bail', 'nullable', 'max:200'],
            'brand_id' => ['bail', 'nullable', 'numeric','exists:brands,id'],
            'manufacturer_id' => ['bail', 'nullable', 'numeric','exists:manufacturers,id'],
            'model' => ['bail', 'nullable', 'max:200'],
            'price' => ['bail', 'nullable', 'regex:/^(\d+(\.\d*)?)|(\.\d+)$/'],
            'weight' => ['bail','nullable', 'numeric', 'between:0,99999999.99'],
            'weight_unit_id' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99','exists:weight_units,id'],
            'dimension_l' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
            'dimension_w' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
            'dimension_d' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
            'measurement_unit_id' => ['bail', 'nullable', 'numeric','exists:measurement_units,id'],
            'notes' => ['bail', 'nullable', 'max:255'],
            'desc' => ['bail', 'nullable', 'max:10000'],
            'is_variant' => ['bail', 'required', 'boolean'],
            'split_sale' => ['bail', 'nullable', 'boolean'],
            'attribute_data' => ['bail', 'nullable'],
            'tax_status' => ['bail', 'required'],
            'custom_tax' => ['bail', 'nullable', 'numeric', 'between:0,99999999.99'],
            'status' => ['bail', 'required', Rule::in([Product::STATUS_ACTIVE, Product::STATUS_INACTIVE])],
            'available_for' => ['bail', 'required', Rule::in(Product::SALE_AVAILABLE_FOR)],
            'customer_buying_price' => ['bail', 'nullable','regex:/^(\d+(\.\d*)?)|(\.\d+)$/'],
        ];

        // if not variant product, require main SKU/barcode/price
        if (!$this->boolean('is_variant')) {
            $rules['sku'] = ['bail', 'required', 'min:1', 'max:200', Rule::unique('products')->ignore($this->id)];
            $rules['barcode'] = ['bail', 'nullable', 'min:1', 'max:200', Rule::unique('products')->ignore($this->id)];
            $rules['price'] = ['bail', 'required', 'regex:/^(\d+(\.\d*)?)|(\.\d+)$/'];
            $rules['customer_buying_price'] = ['bail', 'nullable','regex:/^(\d+(\.\d*)?)|(\.\d+)$/'];
            $rules['weight'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['dimension_l'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['dimension_w'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['dimension_d'] = ['nullable','numeric','between:0,99999999.99'];
        }

        // variant validation
        if ($this->boolean('is_variant')) {
            $rules['variants'] = ['required', 'array', 'min:1'];
            $rules['variants.*.id'] = ['nullable', 'numeric'];
            $rules['variants.*.name'] = ['bail', 'required', 'string', 'min:1', 'max:200'];
            $rules['variants.*.sku'] = ['bail', 'required', 'min:1', 'max:200'];
            $rules['variants.*.barcode'] = ['bail', 'nullable', 'min:1', 'max:200'];
            $rules['variants.*.price'] = ['bail', 'required', 'numeric', 'min:0'];
            $rules['variants.*.customer_buying_price'] = ['nullable','numeric'];
            $rules['variants.*.weight'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.dimension_l'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.dimension_w'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.dimension_d'] = ['nullable','numeric','between:0,99999999.99'];
            $rules['variants.*.attribute_items'] = ['required','array','min:1'];
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

        if (!$this->boolean('is_variant')) {
            $attributes['sku'] = 'SKU';
            $attributes['barcode'] = 'Barcode';
            $attributes['price'] = 'Price';
            $attributes['customer_buying_price'] = 'Customer Buying Price';
            $attributes['weight'] = 'Weight';
            $attributes['dimension_l'] = 'Length';
            $attributes['dimension_w'] = 'Width';
            $attributes['dimension_d'] = 'Depth';
        }

        if ($this->boolean('is_variant')) {
            foreach ($this->variants ?? [] as $index => $variant) {
                $number = $index + 1;
                $attributes["variants.$index.name"] = "Variant #$number Name";
                $attributes["variants.$index.sku"] = "Variant #$number SKU";
                $attributes["variants.$index.barcode"] = "Variant #$number Barcode";
                $attributes["variants.$index.price"] = "Variant #$number Price";
                $attributes["variants.$index.customer_buying_price"] = "Variant #$number Customer Buying Price";
                $attributes["variants.$index.weight"] = "Variant #$number Weight";
                $attributes["variants.$index.dimension_l"] = "Variant #$number Length";
                $attributes["variants.$index.dimension_w"] = "Variant #$number Width";
                $attributes["variants.$index.dimension_d"] = "Variant #$number Depth";
                $attributes["variants.$index.attribute_items"] = "Variant #$number Attribute Items";
            }
        }

        return $attributes;
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'status' => false,
            'message' => $errors->first(),
            'data' => $errors->messages() ,
        ], 422);

        throw new HttpResponseException($response);
    }
}
