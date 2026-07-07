<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressTracking extends Model
{
    protected $table = 'progress_tracking';

    protected $fillable = [
        'type', 'reference_id', 'total', 'processed', 'status'
    ];
}

