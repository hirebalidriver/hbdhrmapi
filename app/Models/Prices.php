<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    use HasFactory;

    protected $fillable = [
        "tour_id",
        "people",
        "people_end",
        "type",
        "price",
        "is_active",
    ];
}
