<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
      
        return [
            'date' => ['required'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'due_date' => ['nullable'],
            'customer_id' => ['nullable', 'numeric'],
            'walkin_customer' => ['nullable'],
            'is_delivered' => ['nullable'],
            'billing' => ['nullable', 'array'],
            'shipping' => ['nullable', 'array'],
            'tax' => ['numeric'],
            'discount' => ['numeric'],
            'discount_type' => ['nullable', 'string', Rule::in([Invoice::DISCOUNT_FIXED, Invoice::DISCOUNT_PERCENT])],
            'payment_type'                  => ['nullable', 'string'],
            'total_paid'                    => ['nullable', 'numeric', 'min:0'],
            'cash_amount'                   => ['nullable', 'numeric', 'min:0'],
            'bank_amount'                   => ['nullable', 'numeric', 'min:0'],
            'balance_amount'                => ['nullable', 'numeric', 'min:0'],
            'bank_info'                     => ['nullable'],
            'additional_charge_name'        => ['nullable', 'string', 'max:255'],
            'additional_charge_amount'      => ['nullable', 'numeric', 'min:0'],
            'notes'                         => ['nullable', 'max:2000'],
            'status'                        => ['nullable', Rule::in(array_keys(Invoice::INVOICE_ALL_STATUS))],
            'enable_free_items'             => ['nullable'],
            'free_items'                    => ['nullable', 'array'],
            'free_items.*.product_id'       => ['nullable', 'integer', 'exists:products,id'],
            'free_items.*.product_stock_id' => ['nullable', 'integer'],
            'free_items.*.product_name'     => ['nullable', 'string'],
            'free_items.*.quantity'         => ['nullable', 'integer', 'min:1'],
            'items' => ['array'],
            'items.*.id' => ['nullable'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.variation' => ['nullable', 'array'],           // ✅ Accept variation as array
            'items.*.variation.id' => ['nullable', 'exists:variations,id'], // ✅ Validate variation id
            'items.*.variation.name' => ['nullable', 'string'],   // ✅ for variation products      
            'items.*.is_variant' => ['nullable'],
            'items.*.split_sale' => ['nullable'],
            'items.*.sku' => ['nullable'],
            'items.*.batch' => ['nullable'],
            'items.*.stock' => ['nullable'],
            'items.*.tax_status' => ['nullable'],
            'items.*.custom_tax' => ['nullable'],
            'items.*.discount' => ['nullable'],
            'items.*.discount_type' => ['nullable'],
            'items.*.name' => ['required'],
            'items.*.quantity' => ['required'],
            'items.*.price' => ['required'],
            // Sold-by-weight (barrel/kg) line fields. Without these rules
            // $request->validated() would strip them and a kg sale would be
            // saved as barrels.
            'items.*.unit' => ['nullable', 'string'],
            'items.*.is_weight_based' => ['nullable'],
            'items.*.kg_per_barrel' => ['nullable', 'numeric'],
            'items.*.barrel_label' => ['nullable', 'string'],
            'items.*.price_per_barrel' => ['nullable', 'numeric'],
            'items.*.backorders_allowed' => ['nullable'],
        ];
    }
}
