<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePlatform extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'platform_id',
        'platform_data',
        'ecommerce_id',
        'is_active',

    ];
    protected $casts = [
        'platform_data' => 'array',
    ];
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'invoice_platform', 'platform_id', 'invoice_id', 'id');
    }
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
