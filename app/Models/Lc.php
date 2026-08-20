<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lc extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dollar_price',
        'usd_rate',
        'lc_amount_bdt',
        'total_expense',
        'final_cost',
        'per_dollar_cost',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dollar_price' => 'decimal:2',
        'usd_rate' => 'decimal:4',
        'lc_amount_bdt' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'per_dollar_cost' => 'decimal:4',
    ];

    public function items()
    {
        return $this->hasMany(LcItem::class);
    }

    public function expenses()
    {
        return $this->hasMany(LcExpense::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
