<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    // status : 0 = pending, 1 = success, 2 = reject

    protected $dates = ['date'];

    protected $fillable = [
        'id',
        'booking_id',
        'guide_id',
        'user_id',
        'price',
        'status',
        'date',
    ];

    public function booking() {
        return $this->hasOne(Bookings::class, 'id', 'booking_id');
    }
    public function cost() {
        return $this->hasMany(Bills::class, 'booking_id', 'booking_id');
    }
}
