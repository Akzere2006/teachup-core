<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        "user_id",
        "offer_id",
        "start_date",
        "end_date",
        "format",
    ];
}
