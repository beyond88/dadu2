<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_no',
        'borrower_name',
        'borrower_phone',
        'borrower_address',
        'loan_type',
        'opening_balance',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'loan_date',
        'due_date',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance'  => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'loan_date'        => 'date',
        'due_date'         => 'date',
    ];

    const TYPE_GIVEN  = 'given';   // আমরা দিয়েছি
    const TYPE_TAKEN  = 'taken';   // আমরা নিয়েছি

    const STATUS_ACTIVE         = 'active';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_FULLY_PAID     = 'fully_paid';
    const STATUS_WRITTEN_OFF    = 'written_off';

    public static function getTypes()
    {
        return [
            self::TYPE_GIVEN => __('custom.loan_given'),
            self::TYPE_TAKEN => __('custom.loan_taken'),
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE         => __('custom.active'),
            self::STATUS_PARTIALLY_PAID => __('custom.partially_paid'),
            self::STATUS_FULLY_PAID     => __('custom.fully_paid'),
            self::STATUS_WRITTEN_OFF    => __('custom.written_off'),
        ];
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_ACTIVE         => 'badge-warning',
            self::STATUS_PARTIALLY_PAID => 'badge-info',
            self::STATUS_FULLY_PAID     => 'badge-success',
            self::STATUS_WRITTEN_OFF    => 'badge-danger',
        ];
        return $badges[$this->status] ?? 'badge-secondary';
    }

    public function getTypeBadgeAttribute()
    {
        return $this->loan_type === self::TYPE_GIVEN ? 'badge-primary' : 'badge-danger';
    }

    public static function generateLoanNo()
    {
        $prefix = 'LN';
        $latest = self::withTrashed()->latest()->first();
        $number = $latest ? ($latest->id + 1) : 1;
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function recalculate()
    {
        $paid = $this->payments()->sum('amount');
        $this->paid_amount = $paid;
        $this->remaining_amount = $this->total_amount - $paid;

        if ($this->remaining_amount <= 0) {
            $this->status = self::STATUS_FULLY_PAID;
            $this->remaining_amount = 0;
        } elseif ($paid > 0) {
            $this->status = self::STATUS_PARTIALLY_PAID;
        } else {
            $this->status = self::STATUS_ACTIVE;
        }

        $this->save();
    }
}
