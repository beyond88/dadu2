<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LcExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'lc_id',
        'expense_name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function lc()
    {
        return $this->belongsTo(Lc::class);
    }
}
