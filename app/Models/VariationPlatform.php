<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'variation_id', 'platform_id', 'platform_data', 'ecommerce_id'
    ];


    protected $casts = [
        'platform_data' => 'array',
    ];

    public function variation()
    {

        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
