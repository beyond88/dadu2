<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Capital extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'capital_no',
        'investor_name',
        'investor_phone',
        'investor_address',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'capital_date',
        'due_date',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount'     => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'capital_date'     => 'date',
        'due_date'         => 'date',
    ];

    const STATUS_ACTIVE         = 'active';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_FULLY_PAID     = 'fully_paid';
    const STATUS_CLOSED         = 'closed';

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE         => __('custom.active'),
            self::STATUS_PARTIALLY_PAID => __('custom.partially_paid'),
            self::STATUS_FULLY_PAID     => __('custom.fully_paid'),
            self::STATUS_CLOSED         => __('custom.closed'),
        ];
    }

    public function payments()
    {
        return $this->hasMany(CapitalPayment::class);
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
            self::STATUS_CLOSED         => 'badge-danger',
        ];
        return $badges[$this->status] ?? 'badge-secondary';
    }

    public static function generateCapitalNo()
    {
        $prefix = 'CAP';
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
