<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackgroundJob extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class',
        'method',
        'parameters',
        'output',
        'retries',
        'delay',
        'priority',
        'status',
    ];
}
