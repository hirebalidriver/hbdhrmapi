<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inclusions extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'note',
    ];
}
