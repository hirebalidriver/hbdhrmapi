<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'package_id',
        'guide_id',
        'time',
        'supplier',
        'date',
        'date_end',
        'is_custom',
        'custom',
        'note',
        'status', //0 = pending, 1 = cancel, 2 = confirm, 3 = need approve, 4 = completed, 5 = reject, 6 = need approve guide, 7 = approved, 8 = guide rejected
        'ref_id',
        'name',
        'email',
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
        'guide_fee',
        'bill_total',
        'susuk_hbd',
        'susuk_guide',
        'tiket_total',
        'additional_price',
        'note_price',
        'is_multi_days',
        'paypalEmail',
        'adult_price',
        'child_price',
        'order_id',
    ];

    protected $casts = [
        'date' => 'datetime:d M Y',
        'date_end' => 'datetime:d M Y',
        'time' => 'datetime:H:i',
    ];

    public function packages() {
        return $this->hasOne(Packages::class, 'id', 'package_id');
    }

    public function guides() {
        return $this->hasOne(Guides::class, 'id', 'guide_id');
    }

    public function options() {
        return $this->hasOne(Tours::class, 'id', 'tour_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function trx() {
        return $this->hasOne(Transactions::class, 'booking_id', 'id');
    }

    public function notification() {
        return $this->hasOne(Notification::class, 'booking_id', 'id');
    }
}
