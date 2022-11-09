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
    ];

    // public function setDateAttribute( $value ) {
    //     $this->attributes['date'] = (new Carbon($value))->format('d M Y');
    //   }
    protected $casts = [
        'date' => 'datetime:d M Y',
    ];

    public function packages() {
        return $this->hasOne(Packages::class, 'id', 'package_id');
    }

    public function guides() {
        return $this->hasOne(Guides::class, 'id', 'guide_id');
    }
}
