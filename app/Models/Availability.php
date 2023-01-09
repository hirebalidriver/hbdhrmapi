<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'guide_id',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'datetime:d M Y',
    ];
}
