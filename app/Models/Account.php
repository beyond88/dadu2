<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'account_number',
        'bank_name',
        'branch_name',
        'current_balance',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const TYPE_CASH = 'cash';
    const TYPE_BANK = 'bank';
    const TYPE_MOBILE_BANKING = 'mobile_banking';

    public static function getTypes()
    {
        return [
            self::TYPE_CASH => __('custom.cash'),
            self::TYPE_BANK => __('custom.bank'),
            self::TYPE_MOBILE_BANKING => __('custom.mobile_banking'),
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function addBalance($amount, $note = null, $referenceId = null, $referenceType = null, $transactionType = null)
    {
        return DB::transaction(function () use ($amount, $note, $referenceId, $referenceType, $transactionType) {
            $this->increment('current_balance', $amount);
            
            return $this->transactions()->create([
                'type' => $transactionType ?? Transaction::TYPE_ADD,
                'amount' => $amount,
                'note' => $note,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'balance_after' => $this->current_balance,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function reduceBalance($amount, $note = null, $referenceId = null, $referenceType = null, $allowNegative = false)
    {
        if (!$allowNegative && $this->current_balance < $amount) {
            throw new \Exception(__('custom.insufficient_balance'));
        }

        return DB::transaction(function () use ($amount, $note, $referenceId, $referenceType) {
            $this->decrement('current_balance', $amount);
            
            return $this->transactions()->create([
                'type' => Transaction::TYPE_REDUCE,
                'amount' => $amount,
                'note' => $note,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'balance_after' => $this->current_balance,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function transferTo(Account $toAccount, $amount, $note = null)
    {
        if ($this->current_balance < $amount) {
            throw new \Exception(__('custom.insufficient_balance'));
        }

        return DB::transaction(function () use ($toAccount, $amount, $note) {
            // Deduct from source account
            $this->decrement('current_balance', $amount);
            
            $transferOutTransaction = $this->transactions()->create([
                'type' => Transaction::TYPE_TRANSFER_OUT,
                'amount' => $amount,
                'to_account_id' => $toAccount->id,
                'note' => $note,
                'balance_after' => $this->current_balance,
                'created_by' => auth()->id(),
            ]);

            // Add to destination account
            $toAccount->increment('current_balance', $amount);
            
            $transferInTransaction = $toAccount->transactions()->create([
                'type' => Transaction::TYPE_TRANSFER_IN,
                'amount' => $amount,
                'from_account_id' => $this->id,
                'note' => $note,
                'balance_after' => $toAccount->current_balance,
                'created_by' => auth()->id(),
            ]);

            return [
                'transfer_out' => $transferOutTransaction,
                'transfer_in' => $transferInTransaction,
            ];
        });
    }

    public function recordOpeningBalance($amount)
    {
        return DB::transaction(function () use ($amount) {
            $this->update(['current_balance' => $amount]);
            
            return $this->transactions()->create([
                'type' => Transaction::TYPE_OPENING_BALANCE,
                'amount' => $amount,
                'balance_after' => $amount,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function recordInvoicePayment($amount, $invoiceId, $note = null)
    {
        return $this->addBalance($amount, $note, $invoiceId, 'invoice', Transaction::TYPE_INVOICE_PAYMENT);
    }

    public function recordDueCollection($amount, $invoiceId, $note = null)
    {
        return $this->addBalance($amount, $note, $invoiceId, 'invoice', Transaction::TYPE_DUE_COLLECTION);
    }
}
