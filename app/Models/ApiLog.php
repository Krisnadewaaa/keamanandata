<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_logs';
    protected $fillable = [
        'method',
        'endpoint',
        'status_code',
        'ip_address',
        'user_agent',
        'user_id',
        'role',
        'duration_ms',
    ];
}
