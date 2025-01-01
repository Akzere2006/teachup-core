<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'offer_id',
        'day_of_week',
        'from_hour',
        'to_hour',
        'format',
    ];
}
