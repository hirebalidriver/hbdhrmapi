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
        'booking_id',
    ];

    protected $casts = [
        'date' => 'datetime:d M Y',
    ];


    public function booking() {
        return $this->hasOne(Bookings::class, 'id', 'booking_id');
    }
    public function guide() {
        return $this->hasOne(Guides::class, 'id', 'guide_id');
    }
}
