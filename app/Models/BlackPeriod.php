<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BlackPeriod extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'black_periods';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'date',
        'description'
    ];

}
