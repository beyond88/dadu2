<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'from_account_id',
        'to_account_id',
        'note',
        'reference_id',
        'reference_type',
        'balance_after',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    const TYPE_ADD = 'add';
    const TYPE_REDUCE = 'reduce';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_INVOICE_PAYMENT = 'invoice_payment';
    const TYPE_DUE_COLLECTION = 'due_collection';
    const TYPE_OPENING_BALANCE = 'opening_balance';

    public static function getTypes()
    {
        return [
            self::TYPE_ADD => __('custom.add'),
            self::TYPE_REDUCE => __('custom.reduce'),
            self::TYPE_TRANSFER_IN => __('custom.transfer_in'),
            self::TYPE_TRANSFER_OUT => __('custom.transfer_out'),
            self::TYPE_INVOICE_PAYMENT => __('custom.invoice_payment'),
            self::TYPE_DUE_COLLECTION => __('custom.due_collection'),
            self::TYPE_OPENING_BALANCE => __('custom.opening_balance'),
        ];
    }

    public function getTypeLabelAttribute()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [$fromDate, $toDate]);
    }
}
