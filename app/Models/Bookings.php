<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'guide_id',
        'time',
        'supplier',
        'date',
        'note',
        'status', //0 = all, 1 = success, 2 = cancel
        'ref_id',
        'name',
        'phone',
        'hotel',
        'status_payment',
        'collect',
        'option_id',
        'created_by',
        'country',
        'adult',
        'child',
        'price',
        'down_payment',
        'guide_fee'
    ];

    protected $casts = [
        'date' => 'datetime:d M Y',
        'time' => 'datetime:H:i',
    ];

    public function packages() {
        return $this->hasOne(Packages::class, 'id', 'package_id');
    }

    public function guides() {
        return $this->hasOne(Guides::class, 'id', 'guide_id');
    }

    public function options() {
        return $this->hasOne(Tours::class, 'id', 'option_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
