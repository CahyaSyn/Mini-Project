<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosingPeriode extends Model
{
    protected $fillable = [
        'period',
        'is_locked',
        'locked_by',
        'locked_at',
    ];
}
