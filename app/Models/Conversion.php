<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $fillable = [
        'source_currency',
        'target_currency',
        'value',
        'converted_value',
        'rate'
    ];
}