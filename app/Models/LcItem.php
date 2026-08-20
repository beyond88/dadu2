<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LcItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lc_id',
        'name',
        'dollar_price',
    ];

    protected $casts = [
        'dollar_price' => 'decimal:2',
    ];

    public function lc()
    {
        return $this->belongsTo(Lc::class);
    }
}
