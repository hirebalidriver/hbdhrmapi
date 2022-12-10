<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balances extends Model
{
    use HasFactory;

    protected $fillable = [
        'guide_id',
        'trx_id',
        'balance',
        'in',
        'out',
        'fee',
        'lock',
        'type',
    ];
}
