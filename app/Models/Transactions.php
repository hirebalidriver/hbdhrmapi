<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function booking() {
        return $this->hasOne(Bookings::class, 'id', 'booking_id');
    }
}
