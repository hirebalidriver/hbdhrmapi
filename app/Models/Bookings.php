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
        'status',
        'ref_id',
        'name',
        'phone',
        'hotel',
        'status_payment',
        'collect',
        'option_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'datetime:d M Y',
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
