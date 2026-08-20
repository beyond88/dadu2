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
     * The date column is a varchar holding both "Y-m-d" and "Y-m-d H:i:s", so a
     * plain string comparison would drop a payment timestamped on the last day of
     * a range ("2026-05-22 14:29:05" sorts after "2026-05-22"). These scopes
     * compare it as a real date instead, and are the only place that quirk is
     * handled.
     */
    public function scopeDateFrom($query, $from)
    {
        return $from ? $query->whereRaw('DATE(`date`) >= ?', [$from]) : $query;
    }

    public function scopeDateTo($query, $to)
    {
        return $to ? $query->whereRaw('DATE(`date`) <= ?', [$to]) : $query;
    }

    public function scopeDateAfter($query, $date)
    {
        return $date ? $query->whereRaw('DATE(`date`) > ?', [$date]) : $query;
    }

    public function scopeOrderByDate($query, $direction = 'asc')
    {
        return $query->orderByRaw('DATE(`date`) ' . ($direction === 'desc' ? 'desc' : 'asc'))
            ->orderBy('id', $direction === 'desc' ? 'desc' : 'asc');
    }

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
