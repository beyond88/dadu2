<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;
    protected $fillable = ['type','store_name','store_url','consumer_key','consumer_secret','is_connected','is_webhook_enabled'];

    
}
