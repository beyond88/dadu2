<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbOperationLog extends Model
{
    use HasFactory;

    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED  = 'failed';

    protected $fillable = [
        'user_id',
        'action',
        'status',
        'file_name',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
