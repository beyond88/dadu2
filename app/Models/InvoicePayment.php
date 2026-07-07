<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InvoicePayment
 */
class InvoicePayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'invoice_id',
        'date',
        'payment_type',
        'amount',
        'notes',
        'created_by',
        'bank_info',
    ];

    protected $casts = [
        'bank_info' => 'array',
    ];

    /**
     * getDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getDateAttribute($value)
    {
        return custom_datetime($value);
    }


    /**
     * getPaymentTypeAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getPaymentTypeAttribute($value): string
    {
        return (string) strtoupper($value ?? '');
    }

    /**
     * getBankNameAttribute
     *
     * @return string
     */
    public function getBankNameAttribute()
    {
        if ($this->bank_info && is_array($this->bank_info)) {
            return $this->bank_info['bank_name'] ?? '';
        }
        return '';
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
