<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * AttributePlatform
 */
class AttributePlatform extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'attribute_id',
        'platform_data',
        'platform_id',
        'ecommerce_id',
        'is_active',
        
    ];
    protected $casts = [
        'platform_data' => 'array',
    ];

    /**
     * attribute
     *
     * @return void
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

}
