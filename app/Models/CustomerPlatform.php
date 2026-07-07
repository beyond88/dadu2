<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPlatform extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'platform_data',
        'platform_id',
        'ecommerce_id',
        'is_active'
    ];
    protected $casts = [
        'platform_data' => 'array',
    ];
    public function platform(){
        return $this->belongsTo(Platform::class,'platform_id');
    }
    
}
